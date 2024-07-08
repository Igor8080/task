<?php 

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Row;

class RowControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_view_rows()
    {
        Row::factory()->count(3)->create();

        $response = $this->get('/api/rows');

        $response->assertStatus(200);
    }

    public function test_rows_grouped_by_date()
    {
        Row::factory()->create(['date' => '2023-01-01']);
        Row::factory()->create(['date' => '2023-01-01']);
        Row::factory()->create(['date' => '2023-01-02']);

        $response = $this->get('/api/rows');

        $response->assertStatus(200);
       
    }
}