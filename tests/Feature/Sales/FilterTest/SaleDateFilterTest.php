<?php

namespace Tests\Feature\Sales\FilterTest;

use App\Constant\StatusConstant;
use App\Models\Sale;
use App\Models\User;
use GuzzleHttp\Psr7\Request;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class SaleDateFilterTest extends TestCase
{
    /**
     * Test case to verify the filtering of sales based on date range.
     *
     * Steps:
     * 1. Create a user and authenticate.
     * 2. Create sales records with different timestamps.
     * 3. Construct a URL with query parameters for filtering by date range.
     * 4. Send a GET request to the endpoint.
     * 5. Assert that the response status is 200, indicating successful filtering.
    */
    public function test_sale_filter_with_date_range(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        Sale::factory()->create([
            'status' => 'ORDER PLACED',
            'approval_status' => 'REJECTED',
            'created_at' => now()->subDays(5)->endOfDay(),
        ]);

        Sale::factory()->create([
            'status' => 'ORDER PLACED',
            'approval_status' => 'APPROVED',
            'created_at' => now()->subDays(5)->endOfDay(),
        ]);

        $start_from = now()->subDays(5)->startOfDay();
        $end_date = now()->endOfDay();
        $url = "/admin/sales?approval_status=&end_to={$end_date}&start_from={$start_from}";
        $response = $this->get($url);
        $response->assertStatus(200);
        $response->assertSee('APPROVED');
        $response->assertSee('REJECTED');
        $response->assertSee('ORDER PLACED');
    }

    /**
     * Test case to verify the filtering of sales based on date range and approval status.
     *
     * Steps:
     * 1. Create a user and authenticate.
     * 2. Create sales records with different approval statuses and timestamps.
     * 3. Construct a URL with query parameters for filtering by approval status and date range.
     * 4. Send a GET request to the endpoint.
     * 5. Assert that the response status is 200, indicating successful filtering.
     */
    public function test_sale_filter_with_date_range_and_approval_status_filter(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        Sale::factory()->create([
            'status' => 'ORDER PLACED',
            'approval_status' => 'APPROVED',
            'created_at' => now()->subMonths(3)->endOfDay(),
        ]);

        Sale::factory()->create([
            'status' => 'ORDER PLACED',
            'approval_status' => 'APPROVED',
            'created_at' => now()->subDays(5)->endOfDay(),
        ]);


        Sale::factory()->create([
            'status' => 'ORDER PLACED',
            'approval_status' => 'REJECTED',
            'created_at' => now()->subDays(5)->endOfDay(),
        ]);

        $start_from = now()->subDays(5)->startOfDay();
        $end_date = now()->endOfDay();
        $url = "/admin/sales?approval_status=1&end_to={$end_date}&start_from={$start_from}";
        $response = $this->get($url);
        $response->assertStatus(200);
        $response->assertSee('APPROVED');
        $response->assertDontSee('UNAPPROVED');
    }

     /*
     * This test case validates the date filtering on the sales endpoint.
     * - A new user is created and authenticated.
     * - The 'start_from' date is set to an invalid string ("Merry john") to trigger a validation error.
     * - The 'end_date' is set to the current date and time at the end of the day.
     * - A GET request is made to the sales endpoint with these date filters.
     * - The test asserts that the session contains an error message indicating that the 'start_from' field must be a valid date.
     */
    public function test_sales_date_filter_start_date_validation () {
        $user = User::factory()->create();
        $this->actingAs($user);
        $start_from = "Merry john";
        $end_date = now()->endOfDay();
        $url = "/admin/sales?approval_status=3&end_to={$end_date}&start_from={$start_from}";
        $response = $this->get($url);
        $response->assertSessionHas('error', 'The start from field must be a valid date.');
    }

      /*
     * This test case validates the date filtering on the sales endpoint.
     * - A new user is created and authenticated.
     * - The 'end_date' date is set to an invalid string ("Merry john") to trigger a validation error.
     * - The 'start_from' is set to the current date and time at the end of the day.
     * - A GET request is made to the sales endpoint with these date filters.
     * - The test asserts that the session contains an error message indicating that the 'end_date' field must be a valid date.
     */

    public function test_sales_date_filter_end_date_validation () {
        $user = User::factory()->create();
        $this->actingAs($user);
        $start_from = now()->subDays(5)->startOfDay();
        $end_date = "keshab";
        $url = "/admin/sales?approval_status=3&end_to={$end_date}&start_from={$start_from}";
        $response = $this->get($url);
        $response->assertSessionHas('error', 'The end to field must be a valid date.');
    }

      /*
     * This test case validates the date filtering on the sales endpoint.
     * - A new user is created and authenticated.
     * - The 'approval_type' date is set to an invalid data
     * - The 'end_date' is set to the current date and time at the end of the day.
     * - The 'start_date' is set to the before 2 month of the current date and time at the end of the day.
     * - A GET request is made to the sales endpoint with these date filters.
     * - The test asserts that the session contains an error message indicating that the 'approval_type' field must be a valid.
     */
    public function test_sales_date_filter_approval_type_validation () {
        $user = User::factory()->create();
        $this->actingAs($user);
        $start_from = now()->subDays(5)->startOfDay();
        $end_date = now()->endOfDay();
        $url = "/admin/sales?approval_status=3&end_to={$end_date}&start_from={$start_from}";
        $response = $this->get($url);
        $response->assertSessionHas('error', 'The selected approval status is invalid.');
    }
}
