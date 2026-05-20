<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\Email;

use App\Models\Email;
use App\Models\Email\Attach;
use App\Models\User;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class EmailDownloadAttachmentControllerTest extends TestCase
{
    #[Test]
    public function it_blocks_unauthenticated_attachment_download(): void
    {
        $email = Email::factory()->create(['company_id' => $this->user->company_id]);
        $attach = Attach::create([
            'email_id' => $email->id,
            'original_name' => 'test.txt',
            'file' => 'company/test/email/1/test.txt',
            'mime' => 'text/plain',
        ]);

        auth()->guard('web')->logout();

        $response = $this->get("/email/download-attachment/{$attach->id}");

        $response->assertRedirect('/login');
    }

    #[Test]
    public function it_blocks_cross_tenant_attachment_download(): void
    {
        $otherUser = User::factory()->create();
        $email = Email::factory()->create(['company_id' => $otherUser->company_id]);
        $attach = Attach::create([
            'email_id' => $email->id,
            'original_name' => 'secret.txt',
            'file' => 'company/other/email/1/secret.txt',
            'mime' => 'text/plain',
        ]);

        $response = $this->get("/email/download-attachment/{$attach->id}");

        $response->assertForbidden();
    }

    #[Test]
    public function it_allows_own_tenant_attachment_download(): void
    {
        $filePath = storage_path('app/company/test/email/1/file.txt');
        @mkdir(dirname($filePath), 0755, true);
        file_put_contents($filePath, 'content');

        $email = Email::factory()->create(['company_id' => $this->user->company_id]);
        $attach = Attach::create([
            'email_id' => $email->id,
            'original_name' => 'file.txt',
            'file' => 'company/test/email/1/file.txt',
            'mime' => 'text/plain',
        ]);

        $response = $this->get("/email/download-attachment/{$attach->id}");

        @unlink($filePath);

        $response->assertOk();
        $response->assertDownload('file.txt');
    }
}
