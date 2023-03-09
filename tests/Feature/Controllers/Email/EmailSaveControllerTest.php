<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\Email;

use App\Models\Email;
use App\Models\Email\Attach;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class EmailSaveControllerTest extends TestCase
{
    protected $user;

    public function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->actingAs($this->user);
    }

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
        $this->withoutExceptionHandling();

        Storage::fake();

        $file = UploadedFile::fake()->image('attachment.jpg');
        $data = [
            'from' => fake()->email(),
            'to' => fake()->email(),
            'subject' => fake()->sentence(3),
            'body' => fake()->text(),
            'attachment' => [$file],
        ];

        $response = $this->post('/email/save', $data);

        $hashName = Attach::all()->last()->file;
        $path = 'company'.DIRECTORY_SEPARATOR.'email'.DIRECTORY_SEPARATOR.$this->user->id;

        Storage::disk()->assertExists($path.DIRECTORY_SEPARATOR.$hashName);
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
