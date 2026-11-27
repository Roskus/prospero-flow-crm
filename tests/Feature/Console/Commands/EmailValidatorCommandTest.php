<?php

declare(strict_types=1);

namespace Tests\Feature\Console\Commands;

use App\Enums\EmailVerificationStatus;
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
            'email_verified' => EmailVerificationStatus::Pending,
        ]);

        $this->artisan('crm:email:validate')->assertSuccessful();

        $this->assertSame(EmailVerificationStatus::Invalid, $supplierContact->fresh()->email_verified);
    }

    #[Test]
    public function it_skips_already_verified_supplier_contact_emails(): void
    {
        $supplierContact = SupplierContact::factory()->create([
            'email' => 'ld.@example.com',
            'email_verified' => EmailVerificationStatus::Valid,
        ]);

        $this->artisan('crm:email:validate')->assertSuccessful();

        $this->assertSame(EmailVerificationStatus::Valid, $supplierContact->fresh()->email_verified);
    }
}
