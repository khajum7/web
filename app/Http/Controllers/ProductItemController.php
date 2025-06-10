<?php

namespace App\Http\Controllers;

use App\Models\ProductItem;
use App\Models\SaleItem;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Inertia\Inertia;
use Inertia\Response;

class ProductItemController extends Controller
{
    /**
     * @return Response
     */
    public function index(): Response
    {
        $productItems = ProductItem::where('status', '1')->get();

        return Inertia::render('Product/Index', [
            'items' => $productItems
        ]);
    }

    /**
     * Display a listing of the resource.
     */
    public function productDetails(Request $request): JsonResponse
    {
        $sku = $request->input('sku');

        if($sku){
            $products = ProductItem::whereIn('sku', $sku)->get();

            return response()->json([
                'message' => 'Successfully fetch Sku',
                'type' => 'success',
                'data' => $products
            ]);
        }

        return response()->json([
            'message' => 'SKU is required',
            'type' => 'error'
        ], 500);
    }

    public function import(): JsonResponse
    {
        Artisan::call('migrate');
        ProductItem::truncate();

        try {
            $filePath = public_path('items.csv');

            if (($handle = fopen($filePath, 'r')) !== false) {

                fgetcsv($handle);

                while (($row = fgetcsv($handle, 1000, ',')) !== false) {
                    ProductItem::create([
                        'name'     => $row[0],
                        'sku'    => $row[1],
                        'type'    => $row[2],
                        'size'    => $row[3],
                    ]);
                }

                fclose($handle);
            }

          return response()->json([
              'message' => 'SKU imported successfully'
          ]);

        }catch (\Exception $exception){
            return response()->json([
                'message' => $exception->getMessage(),
            ]);
        }
    }

    public function updateSaleItems(): JsonResponse
    {
        $allItems = ProductItem::all();
        $sales = SaleItem::all();

        foreach ($sales as $sale){
            $productItem = $allItems->firstWhere('sku', $sale->sku);
            if($productItem){
                $sale->update([
                    'type' => $productItem->type,
                    'size' => $productItem->size,
                ]);
            }
        }

        return response()->json([
            'message' => 'SKU updated successfully'
        ]);
    }


}
