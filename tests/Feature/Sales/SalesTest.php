<?php

namespace Tests\Feature\Sales;

use App\Models\Address;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\User;
use Tests\TestCase;

class SalesTest extends TestCase
{
    public function test_sales_index()
    {
        $user = User::factory()->unverified()->create();

        Sale::factory(10)->create(['created_by' => $user->id, 'updated_by' => $user->id]);

        $response = $this->actingAs($user)->get('/admin/sales');
        $response->assertStatus(200);
        $response->assertInertia();
        $response->assertOk();
    }

    public function test_sales_unauthenticated()
    {
        $user = User::factory()->unverified()->create();

        Sale::factory(10)->create(['created_by' => $user->id, 'updated_by' => $user->id]);

        $response = $this->get('/admin/sales');
        $response->assertStatus(302);
        $response->assertRedirect('/login');
    }

    public function test_sales_crate_page()
    {

        $user = User::factory()->unverified()->create();
        $response = $this->actingAs($user)->get('/admin/sales');

        $response->assertOk();
        $response->assertInertia();

    }

//    public function test_sales_create()
//    {
//        $user = User::factory()->unverified()->create();
//
//        Sale::factory(10)->create(['created_by' => $user->id, 'updated_by' => $user->id]);
//
//        $response = $this->actingAs($user)->post('/admin/sales', [
//            'delivery_date' => $this->faker->date(),
//            'program_start_date' => $this->faker->date(),
//            'organization_name' => $this->faker->company(),
//            'first_name' => $this->faker->firstName(),
//            'last_name' => $this->faker->lastName(),
//            'email' => $this->faker->unique()->safeEmail(),
//            'phone' => $this->faker->phoneNumber(),
//            'address1' => $this->faker->address(),
//            'address2' => $this->faker->address(),
//            'city' => $this->faker->city(),
//            'state' => $this->faker->streetName(),
//            'zip' => $this->faker->postcode(),
//            'country' => $this->faker->country(),
//        ]);
//
//        $response->assertStatus(302);
//        $response->assertSessionHas('success', 'Sales Created Successfully.');
//        $response->assertRedirect('/admin/sales');
//    }

    public function test_sales_edit_page_validation()
    {
        $user = User::factory()->unverified()->create();
        $sale = Sale::factory()->create(['created_by' => $user->id, 'updated_by' => $user->id]);
        Address::factory()->create([
            'sale_id' => $sale->id
        ]);
        $response = $this->actingAs($user)->get(route('sales.edit', $sale->id));

        $response->assertOk();
        $response->assertInertia();
    }

//    public function test_sales_edit(){
//
//        $user = User::factory()->unverified()->create();
//        $sale = Sale::factory()->create(['created_by' => $user->id, 'updated_by' => $user->id]);
//        $address = Address::factory()->create([
//            'sale_id' => $sale->id
//        ]);
//
//        $response = $this->actingAs($user)->put(route('sales.update', $sale->id), [
//            'address_id' => $address->id,
//            'delivery_date' => $this->faker->date(),
//            'program_start_date' => $this->faker->date(),
//            'organization_name' => $this->faker->company(),
//            'first_name' => $this->faker->firstName(),
//            'last_name' => $this->faker->lastName(),
//            'email' => $this->faker->unique()->safeEmail(),
//            'phone' => $this->faker->phoneNumber(),
//            'address1' => $this->faker->address(),
//            'address2' => $this->faker->address(),
//            'city' => $this->faker->city(),
//            'state' => $this->faker->streetName(),
//            'zip' => $this->faker->postcode(),
//            'country' => $this->faker->country(),
//        ]);
//
//        $response->assertStatus(302);
//        $response->assertSessionHas('success', 'Sales Updated Successfully.');
//        $response->assertRedirect('/admin/sales');
//    }


//    public function test_add_sale_item()
//    {
//        $user = User::factory()->unverified()->create();
//        $sale = Sale::factory()->create(['created_by' => $user->id, 'updated_by' => $user->id]);
//        Address::factory()->create([
//            'sale_id' => $sale->id
//        ]);
//
//        $response = $this->actingAs($user)->post(route('sales.items.store'), [
//            'sale_id' => $sale->id,
//            'title' => $this->faker->word(),
//            'quantity' => $this->faker->numberBetween(1,20),
//        ]);
//
//        $response->assertStatus(302);
//        $response->assertSessionHas('success', 'Successfully Added Sales Item.');
//    }



//    public function test_create_sale_edit()
//    {
//        $user = User::factory()->unverified()->create();
//        $sale = Sale::factory()->create(['created_by' => $user->id, 'updated_by' => $user->id]);
//        Address::factory()->create([
//            'sale_id' => $sale->id
//        ]);
//        $saleItem = SaleItem::factory()->create();
//
//        $response = $this->actingAs($user)->get(route('sales.items.edit', $saleItem->id));
//        $response->assertStatus(200);
//        $response->assertInertia();
//    }

    public function test_update_sale_item()
    {
        $user = User::factory()->unverified()->create();
        $sale = Sale::factory()->create(['created_by' => $user->id, 'updated_by' => $user->id]);
        Address::factory()->create([
            'sale_id' => $sale->id
        ]);

        $saleItem = SaleItem::factory()->create();

        $response = $this->actingAs($user)->put(route('sales.items.update', $saleItem->id), [
            'title' => $this->faker->word(),
            'quantity' => $this->faker->numberBetween(1,20),
        ]);

        $response->assertStatus(302);
        $response->assertSessionHas('success', 'Item updated successfully.');
    }

    public function test_update_note()
    {
        $user = User::factory()->unverified()->create();
        $sale = Sale::factory()->create(['created_by' => $user->id, 'updated_by' => $user->id]);
        Address::factory()->create([
            'sale_id' => $sale->id
        ]);

        $response = $this->actingAs($user)->put(route('sales.update.note', $sale->id), [
            'note' => $this->faker()->text()
        ]);

        $response->assertStatus(302);
        $response->assertSessionHas('success', 'Successfully update notes.');
    }

    public function test_update_invalid_note()
    {
        $user = User::factory()->unverified()->create();
        $sale = Sale::factory()->create(['created_by' => $user->id, 'updated_by' => $user->id]);
        Address::factory()->create([
            'sale_id' => $sale->id
        ]);

        $response = $this->actingAs($user)->put(route('sales.update.note', $sale->id), [
            'note' => null
        ]);

        $response->assertStatus(302);
        $response->assertSessionHasErrors(['note']);
        $response->assertSessionHasErrors([
            'note' => 'The note field is required.',
        ]);
    }

    public function test_update_status_sale_for_reject()
    {
        $user = User::factory()->unverified()->create();
        $sale = Sale::factory()->create(['created_by' => $user->id, 'updated_by' => $user->id]);
        Address::factory()->create([
            'sale_id' => $sale->id
        ]);

        $response = $this->actingAs($user)->put(route('sales.status', $sale->id), [
            'note' => $this->faker()->text,
            'status' => 'REJECTED'
        ]);

        $response->assertStatus(302);
        $response->assertSessionHas('success', 'Successfully update status.');
    }

    public function test_update_status_sale_for_approved()
    {
        $user = User::factory()->unverified()->create();
        $sale = Sale::factory()->create(['created_by' => $user->id, 'updated_by' => $user->id]);
        Address::factory()->create([
            'sale_id' => $sale->id
        ]);

        $response = $this->actingAs($user)->put(route('sales.status', $sale->id), [
            'note' => $this->faker()->text,
            'status' => 'APPROVED'
        ]);

        $response->assertStatus(302);
        $response->assertSessionHas('success', 'Successfully update status.');
    }


    public function test_update_status_sale_for_invalid_status()
    {
        $user = User::factory()->unverified()->create();
        $sale = Sale::factory()->create(['created_by' => $user->id, 'updated_by' => $user->id]);
        Address::factory()->create([
            'sale_id' => $sale->id
        ]);

        $response = $this->actingAs($user)->put(route('sales.status', $sale->id), [
            'note' => null,
            'status' => 'APPROVED1'
        ]);

        $response->assertSessionHasErrors(['status', 'note']);
        $response->assertStatus(302);
        $response->assertSessionHasErrors([
            'note' => 'The note field is required.',
        ]);
    }
}
