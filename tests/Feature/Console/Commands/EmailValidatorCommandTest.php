<?php

declare(strict_types=1);

namespace Tests\Feature\Console\Commands;

use App\Helpers\EmailVerification;
use App\Models\Supplier\SupplierContact;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class EmailValidatorCommandTest extends TestCase
{
    #[Test]
    public function it_marks_invalid_supplier_contact_emails(): void
    {
        $supplierContact = SupplierContact::factory()->create([
            'email' => 'ld.@example.com',
            'email_verified' => EmailVerification::PENDING,
        ]);

        $this->artisan('crm:email:validate')->assertSuccessful();

        $this->assertSame(EmailVerification::INVALID, (int) $supplierContact->fresh()->getRawOriginal('email_verified'));
    }

    #[Test]
    public function it_skips_already_verified_supplier_contact_emails(): void
    {
        $supplierContact = SupplierContact::factory()->create([
            'email' => 'ld.@example.com',
            'email_verified' => EmailVerification::VALID,
        ]);

        $this->artisan('crm:email:validate')->assertSuccessful();

        $this->assertSame(EmailVerification::VALID, (int) $supplierContact->fresh()->getRawOriginal('email_verified'));
    }
}
