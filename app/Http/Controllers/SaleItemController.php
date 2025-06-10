<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\SaleItem;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SaleItemController extends Controller
{

    /**
     * Show the form for creating a new resource.
     */
    public function create(Sale $sale)
    {
        return  Inertia::render('Sales/Items/Create', [
            'sale' => $sale
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'sale_id' => 'required',
            'title' => 'required',
            'quantity' => 'required|integer|min:1',
        ]);

        $saleItem = new SaleItem();
        $saleItem->sale_id  = $request->input('sale_id');
        $saleItem->title    = $request->input('title');
        $saleItem->quantity = $request->input('quantity');
        $saleItem->save();

        return redirect(route('sales.show', $saleItem->sale_id))->with('success', 'Successfully Added Sales Item.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(SaleItem $saleItem)
    {
        return Inertia::render('Sales/Items/Edit', ['saleItem' => $saleItem]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, SaleItem $saleItem)
    {

        $request->validate([
            'title' => 'required',
            'quantity' => 'required|integer|min:1',
        ]);

        try {
            $saleItem->title = $request->input('title');
            $saleItem->quantity = $request->input('quantity');
            $saleItem->save();

        }catch (\Exception $exception){
        }

        return redirect(route('sales.show', $saleItem->sale_id))->with('success', 'Item updated successfully.');

    }
}
