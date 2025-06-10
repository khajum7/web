<?php

namespace Tests\Feature\Sales\FilterTest;

use App\Models\Address;
use App\Models\Sale;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class SearchSaleTest extends TestCase
{
     /**
     * A basic feature test example.
     */
    public function test_search_sales(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);
        $sale = Sale::factory()->create([
            'organization_name' => 'Mishra Home',
        ]);

        $response = $this->get('/search?search='.$sale['id']);
        $response->assertStatus(200);
        $response->assertSee($sale['id']);
        $response->assertSee('Mishra Home');
    }

    public function test_search_sale_validation() {
        
        $user = User::factory()->create();
        $this->actingAs($user);
        Sale::factory()->create([
            'organization_name' => 'Mishra Home',
        ]);

        $response = $this->get('/search?search=');

        $response->assertSessionHasErrors('search');
        $this->assertArrayHasKey('search', session('errors')->getMessages());
        $response->assertStatus(302);
    }

    public function test_search_sale_from_sale_address() {
        
        $user = User::factory()->create();
        $this->actingAs($user);
        $sale =  Sale::factory()->create([
            'organization_name' => 'Mishra Home',
        ]);
        Address::factory()->create([
            'sale_id' => $sale['id'],
            'email' => 'acharyakeshab20@gmail.com',
        ]);
        $response = $this->get('/search?search=acharyakeshab20');
        $response->assertSee($sale['id']);
        $response->assertStatus(200);
        $response->assertSee('Mishra Home');
    }
}
