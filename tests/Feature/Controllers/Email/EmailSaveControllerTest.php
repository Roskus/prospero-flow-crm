<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\Email;

use App\Models\Email;
use App\Models\Email\Attach;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class EmailSaveControllerTest extends TestCase
{
    /** @test */
    public function it_can_save_email(): void
    {
        $data = [
            'from' => fake()->email(),
            'to' => fake()->email(),
            'subject' => fake()->sentence(3),
            'body' => fake()->text(),
        ];

        $response = $this->post('/email/save', $data);

        $response->assertRedirect('/email');
        $this->assertDatabaseHas('email', $data);
    }

    /** @test */
    public function it_can_save_email_with_attachment(): void
    {
        Storage::fake();

        $file = UploadedFile::fake()->image('attachment.jpg');
        $data = [
            'from' => fake()->email(),
            'to' => fake()->email(),
            'subject' => fake()->sentence(3),
            'body' => fake()->text(),
            'attachment' => [$file],
        ];

        $this->post('/email/save', $data);

        $path = Attach::all()->last()->file;

        Storage::assertExists($path);
        Storage::deleteDirectory('company');
    }

    /** @test */
    public function it_can_update_email(): void
    {
        $email = Email::factory()->create();

        $data = $email->toArray();
        $data['from'] = fake()->email();
        $this->post('/email/save', $data);

        $this->assertDatabaseMissing('email', $data);
        $this->assertEquals($data['from'], Email::all()->last()->from);
        $this->assertNotEquals($email->from, Email::all()->last()->from);
    }
}
