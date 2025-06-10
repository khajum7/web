<?php

namespace Tests\Feature\Sales;

use App\Models\Address;
use App\Models\OrderLog;
use App\Models\Sale;
use App\Models\User;
use Tests\TestCase;

class OrderLogTest extends TestCase
{

    public function test_order_logs()
    {
        $user = User::factory()->unverified()->create();
        $sale = Sale::factory()->create(['created_by' => $user->id, 'updated_by' => $user->id]);
        Address::factory()->create([
            'sale_id' => $sale->id
        ]);

        OrderLog::factory(5)->create(['sale_id' => $sale->id]);

        $response = $this->actingAs($user)->get(route('sales.order.logs', $sale->id));
        $response->assertStatus(200);
        $response->assertSeeText('Successfully fetch response log.');
        $data = $response->json();
        $logs = $data['data'];
        $this->assertCount(5, $logs);
    }

}
