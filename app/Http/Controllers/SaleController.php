<?php

namespace App\Http\Controllers;

use App\Http\Requests\SaleFilterRequest;
use App\Http\Requests\SaleRequest;
use App\Http\Services\AwsService;
use App\Http\Services\OrderLogService;
use App\Http\Services\SaleService;
use App\Http\Trait\ResponseTrait;
use App\Models\Address;
use App\Models\OrderLog;
use App\Models\ProductItem;
use App\Models\Sale;
use App\Models\SaleItem;
use Aws\Exception\AwsException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Inertia\Response;

class SaleController extends Controller
{
    use ResponseTrait;

    /**
     * Display a listing of the resource.
     */
    public function index(SaleFilterRequest $request): Response|RedirectResponse
    {
        $sales      = Sale::query();
        $startFrom  = $request->start_from ? Carbon::parse($request->start_from) : now()->subMonth(1)->startOfDay();
        $endTo      = $request->end_to ? Carbon::parse($request->end_to) : now()->endOfDay();
        $sales      = $sales->select('*')->whereBetween('created_at', [$startFrom, $endTo]);

        if($request->has('approval_status') && (!empty($request->approval_status) || $request->approval_status === "0")) {
            $sales->where('approval_status', (string) $request->approval_status);
        }

        $filterSaleLists = $sales->orderBy('id', 'DESC')->get();

        return Inertia::render('Sales/Index', [
            'sales' => $filterSaleLists,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        return Inertia::render('Sales/Create');
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(SaleRequest $request, OrderLogService $orderLogService, AwsService $awsService, SaleService $saleService): JsonResponse
    {
        $allItems = ProductItem::all();
        DB::beginTransaction();

        try {
            $response = $saleService->validateSale($request, $allItems);
            if ($response) return $response;

            $sales = new Sale();
            $sales->delivery_date       = $request->input('shipping_address.delivery_date');
            $sales->program_start_date  = $request->input('shipping_address.date_first_game');
            $sales->organization_name   = $request->input('shipping_address.organization_name');
            $sales->save();

            if($sales){
                $address = new Address();
                $address->first_name = $request->input('shipping_address.first_name');
                $address->last_name = $request->input('shipping_address.last_name');
                $address->email     = $request->input('shipping_address.email');
                $address->phone      = $request->input('shipping_address.phone');
                $address->address1  = $request->input('shipping_address.address1');
                $address->address2  =  $request->input('shipping_address.address2');
                $address->city      = $request->input('shipping_address.city');
                $address->state     = $request->input('shipping_address.state');
                $address->zip       = $request->input('shipping_address.zip');
                $address->country   = 'US';
                $address->sale_id   = $sales->id;
                $address->save();

                $items = $request->input('items');

                foreach ($items as $item)
                {
                    $productItem = $allItems->firstWhere('sku', $item['sku']);

                    SaleItem::create([
                        'title' => $productItem->name,
                        'quantity' => $item['quantity'],
                        'sku' => $item['sku'],
                        'sale_id' => $sales->id,
                        'type' => $productItem->type,
                        'size' => $productItem->size,
                    ]);
                }
            }

            if(env('APP_ENV') == "production") {
                $jsonData = array(
                    'order_id' => $sales->id
                );

                $topic = 'arn:aws:sns:us-west-1:174687093387:JazzOrdersNotifications';

                try {
                    $awsService->publishMessageToSns($jsonData, $topic);
                } catch (AwsException $e) {
                    Log::error($e->getMessage());
                }
            }

            DB::commit();

            $orderLogService->saveOrderLog([
                'comment' => sprintf('Order has been placed by <b>%s</b>.', $address->first_name. ' '.$address->last_name),
                'sale_id' => $sales->id
            ]);

            $sales->load('address', 'salesItems');

            return response()->json([
                'message' => 'Order has been placed',
                'type' => 'success',
                'data' => $saleService->formatSale($sales),
            ]);

        }catch (\Exception $exception){
            DB::rollBack();

            return response()->json([
                'message' => 'Something went wrong while placing order.',
                'type' => 'error',
                'data' => [],
            ], 403);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Sale $sale)
    {
        $sale->load('address', 'salesItems');

        return Inertia::render('Sales/Show', ['sale' => $sale]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Sale $sale)
    {
        $sale->load('address');
        return Inertia::render('Sales/Edit', ['sale' => $sale]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(SaleRequest $request, Sale $sale)
    {
        $user = auth()->user();

        DB::beginTransaction();
        try {
            $sale->delivery_date       = $request->input('delivery_date');
            $sale->program_start_date  = $request->input('program_start_date');
            $sale->organization_name   = $request->input('organization_name');
            $sale->updated_by          = $user->id;
            $sale->save();

            $address = Address::find($request->input('address_id'));

            if($sale && $address){
                $address->first_name    = $request->input('first_name');
                $address->last_name     = $request->input('last_name');
                $address->email         = $request->input('email');
                $address->phone         = $request->input('phone');
                $address->address1      = $request->input('address1');
                $address->address2      =  $request->input('address2');
                $address->city          = $request->input('city');
                $address->state         = $request->input('state');
                $address->zip           = $request->input('zip');
                $address->country       = $request->input('country');
                $address->save();
            }

            DB::commit();
        }catch (\Exception $exception){
            DB::rollBack();
            return back()->with('error', 'something went wrong.');
        }

        return redirect(route('sales.index'))->with('success', 'Sales Updated Successfully.');
    }

    public function updateStatus(Request $request, Sale $sale, OrderLogService $orderLogService, SaleService $saleService, AwsService $awsService): RedirectResponse
    {
        $user = auth()->user();
        $request->validate([
            'note'      => 'required|min:2',
            'status'    => 'required|in:APPROVED,REJECTED',
        ]);

        try {
            $sale->approval_status  = $request->get('status');
            $sale->approvalNote     = $request->get('note');
            $sale->updated_by       = $user->id;
            $sale->save();

            if($sale->approval_status == 'APPROVED'){
                $saleService->placeOrderToWarehouse($sale);
            }

            if($sale->approval_status == 'REJECTED'){
                $sale->status       = 'ORDER CANCELED';
                $sale->notified     = 0;
                $sale->save();

                if(env('APP_ENV') == "production") {
                    $jsonData = array(
                        'order_id' => $sale->id
                    );

                    $topic = 'arn:aws:sns:us-west-1:174687093387:JazzOrdersNotifications';

                    try {
                        $awsService->publishMessageToSns($jsonData, $topic);
                    } catch (AwsException $e) {
                        Log::error($e->getMessage());
                    }
                }

            }

            $orderLogService->saveOrderLog([
                'comment' => sprintf('<b>%s</b> update Order status to <b>%s</b>.', $user->name, $sale->approval_status),
                'sale_id' => $sale->id
            ]);

            return back()->with('success', 'Successfully update status.');

        }catch (\Exception $exception){
            return back()->with('error', 'Something went wrong while updating status.');
        }
    }

    public function updateNote(Request $request, Sale $sale, OrderLogService $orderLogService)
    {
        $user = auth()->user();
        $request->validate([
            'note' => 'required',
        ]);

        try{
            $sale->notes        = $request->get('note');
            $sale->updated_by   = $user->id;
            $sale->save();

            $orderLogService->saveOrderLog([
                'comment' => sprintf('<b>%s</b> update order Note.', $user->name),
                'sale_id' => $sale->id
            ]);

        }catch (\Exception $exception){
            return back()->with('error', 'Error while updating order notes.');
        }

        return back()->with('success', 'Successfully update notes.');
    }

    public function orderLogs(Sale $sale)
    {
        $orderLogs = OrderLog::where('sale_id', $sale->id)->with('createdBy:id,name')->orderBy('id', 'desc')->get();

        return $this->successResponseWithData('Successfully fetch response log.', $orderLogs);
    }

    /**
     * Handle the search request and return search results.
     *
     * This function validates the search input to ensure it is not empty.
     * It then trims and converts the search string to lowercase.
     * The search string is used to query the Sale model.
     * Finally, it returns the search results to the Inertia.js view.
     *
     * @param Request $request The incoming request object containing the search input.
     * @return Response The response containing the search results.
     */
    public function search(Request $request): Response
    {
        $request->validate([
            'search' => 'required'
        ]);

        $search = strtolower(trim($request->get('search'),''));

        $sales = Sale::search($search)->get();

        return Inertia::render('Search/Index', [
            'sales' => $sales,
            'search' => $search,
        ]);
    }
}
