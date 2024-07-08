<?php 

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UploadControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_upload_invalid_file()
    {
        Storage::fake('uploads');

        $file = UploadedFile::fake()->create('test.txt', 100);

        $response = $this->post('/api/upload', [
            'file' => $file,
        ]);

        $response->assertStatus(302);
        
    }
}