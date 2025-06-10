<?php

namespace App\Http\Services;
use App\Constant\StatusConstant;
use App\Http\Requests\SaleRequest;
use App\Models\Sale;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;

class SaleService
{
    public function __construct(private readonly OrderLogService $orderLogService)
    {

    }

    /**
     * Validate the sale request against product types and quantities.
     *
     * @param SaleRequest $request
     * @param Collection $products
     * @return JsonResponse|void
     */
    public function validateSale(SaleRequest $request, Collection $products)
    {
        $items = $request->input('items');
        $shorts = $products->where('type', StatusConstant::PRODUCT_TYPE_INDIVIDUAL_SHORT_SET);
        $jerseys = $products->where('type', StatusConstant::PRODUCT_TYPE_INDIVIDUAL_JERSEY_SET);

        if (($this->getTotalCount($items, $jerseys) > 50)) {
            return response()->json([
                'message' => 'The quantity for individual jersey orders should not exceed 50.',
                'type' => 'error'
            ], 503);
        }

        if (!$request->get('set') && (($this->getTotalCount($items, $shorts) + $this->getTotalCount($items, $jerseys)) < 5)) {
            return response()->json([
                'message' => 'The minimum order quantity for Shorts or Jersey is 5.',
                'type' => 'error'
            ], 503);
        }

        if(!$request->get('set') && $this->validateIndividualJersey($items, $jerseys)){
            return response()->json([
                'message' => 'The quantity for individual jersey orders should not exceed 12 for single item.',
                'type' => 'error'
            ], 503);
        }
    }

    /**
     * Validate individual jersey quantities and merge items with the same SKU.
     *
     * @param array $items
     * @param Collection $jerseys
     * @return bool
     */
    public function validateIndividualJersey(array $items, Collection $jerseys): bool
    {
        $mergedItems = $this->filterArrays($items,$jerseys)
            ->groupBy('sku')
            ->map(function ($groupedItems) {
                return [
                    'sku' => $groupedItems->first()['sku'],
                    'quantity' => $groupedItems->sum('quantity'),
                ];
            })
            ->values();

        return (bool) $mergedItems->firstWhere('quantity', '>', 12);
    }


    /**
     * Get the total count of items that have a matching SKU in jerseys.
     *
     * @param array $items
     * @param Collection $jerseys
     * @return int
     */
    public function getTotalCount(array $items, Collection $jerseys): int
    {
        return collect($items)
            ->filter(fn($item) => $jerseys->contains('sku', $item['sku']))
            ->sum('quantity');
    }

    public function filterArrays(array $items, Collection $jerseys): Collection
    {
        return collect($items)
            ->filter(fn($item) => $jerseys->contains('sku', $item['sku']));
    }

    public function placeOrderToWarehouse(Sale $sale): void
    {
        $sale = $sale->load('address', 'salesItems');

        try {
            $shippingAddress    = $sale->address;
            $orderNumber        = $sale->id;
            $items              = [];
            $orderDate          = Carbon::now()->format('Y-m-d');

            foreach ($sale->salesItems as $item) {
                $items[] = [
                    "itemSkuNumber" => $item->sku,
                    "itemName" => $item->title,
                    "itemQty" => $item->quantity,
                ];
            }

            $data = array(
                "customerId"    => 1,
                "orderNumber"   => $orderNumber,
                "orderDate"     => $orderDate,
                "shipMethod"    => "FDXINTGND",
                "productItems"  => $items,
                "orderShippingAddress" => array(
                    "customerOfficeName" => $sale->organization_name,
                    "firstName" => $shippingAddress->first_name,
                    "lastName"  => $shippingAddress->last_name,
                    "email"     => $shippingAddress->email,
                    "phone1"    => $shippingAddress->phone,
                    "address1"  => $shippingAddress->address1,
                    "address2"  => $shippingAddress->address2,
                    "city"      => $shippingAddress->city,
                    "state"     => $shippingAddress->state,
                    "zip"       => $shippingAddress->zip,
                    "country"   => $shippingAddress->country
                )
            );

           $response = Http::withHeaders([
               'apiToken' => config('warehouse.token')
           ])->post(sprintf('%s/orders/createOrder', config('warehouse.api')), $data)->json();

           $sale->warehouse_message	 = $response['message'];

           if($response['returnType'] == "success"){
               $this->orderLogService->saveOrderLog([
                   'comment' => "Order has been placed to warehouse <b>#$orderNumber</b>.",
                   'sale_id' => $sale->id
               ]);

               $sale->warehouse_order_id = $response['result']['orderId'];
           }else{
               $this->orderLogService->saveOrderLog([
                   'comment' => sprintf('Error occurs while placing order to warehouse: <b><i>%s</i></b>.', $response['message']),
                   'sale_id' => $sale->id
               ]);
           }

           $sale->save();
        }catch (\Exception $exception){
            $this->orderLogService->saveOrderLog([
                'comment' => sprintf('Error occurs while placing order to warehouse: <b><i>%s</i></b>.', $exception->getMessage()),
                'sale_id' => $sale->id
            ]);
        }

    }

    public function formatSale(Sale $sale): array
    {
        $sale = $sale->toArray();
        $items = collect($sale['sales_items']);

        $sets = [
            '10' => ['name' => '10 pk Jersey Set', 'items' => collect(), 'total' => 0, "type" => "1"],
            '8'  => ['name' => '8 pk Jersey Set', 'items' => collect(), 'total' => 0, "type" => "2"],
            '1'  => [
                'jersey' => ['name' => 'Single Reversible Jersey', 'items' => collect(), 'total' => 0, "type" => "3"],
                'short'  => ['name' => 'Single Reversible Short', 'items' => collect(), 'total' => 0, "type" => "4"],
            ],
        ];

        $totalJersey = 0;
        $totalShorts = 0;

        $items->each(function ($item) use (&$sets, &$totalJersey, &$totalShorts) {
            $quantity = $item['quantity'] ?? 0;
            $item['total'] = $quantity;

            switch ($item['type']) {
                case '1':
                    $item['total'] = $quantity * 10;
                    $sets['10']['items']->push($item);
                    $sets['10']['total'] += $quantity;
                    $totalJersey += $item['total'];
                    break;

                case '2':
                    $item['total'] = $quantity * 8;
                    $sets['8']['items']->push($item);
                    $sets['8']['total'] += $quantity;
                    $totalJersey += $item['total'];
                    break;

                case '3':
                    $sets['1']['jersey']['items']->push($item);
                    $sets['1']['jersey']['total'] += $quantity;
                    $totalJersey += $quantity;
                    break;

                case '4':
                    $sets['1']['short']['items']->push($item);
                    $sets['1']['short']['total'] += $quantity;
                    $totalShorts += $quantity;
                    break;
            }
        });

        $sale['sales_items'] = collect([
            $sets['10'],
            $sets['8'],
            $sets['1']['jersey'],
            $sets['1']['short'],
        ])->filter(function ($set) {
            return $set['total'] > 0;
        })->values()->toArray();

        $sale['total_jersey'] = $totalJersey;
        $sale['total_shorts'] = $totalShorts;

        return $sale;
    }

}
