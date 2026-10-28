<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\Invoice;

use App\Models\Bank\Account;
use App\Models\Company;
use App\Models\Invoice;
use App\Models\Invoice\Item;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class InvoicePdfControllerTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function the_pdf_lists_the_accounts_marked_to_be_shown_on_invoices(): void
    {
        $invoice = $this->createInvoice();
        Account::factory()->create(['company_id' => $this->user->company_id, 'account_name' => 'Main account', 'iban' => 'ES9121000418450200051332', 'swift' => 'CAIXESBBXXX', 'show_on_invoice' => true]);
        Account::factory()->create(['company_id' => $this->user->company_id, 'iban' => 'ES7921000813610123456789', 'show_on_invoice' => false]);
        Account::factory()->create(['company_id' => Company::factory()->create()->id, 'iban' => 'ES1000492352082414205416', 'show_on_invoice' => true]);

        $html = $this->renderPdf($invoice);

        $this->assertStringContainsString(__('Bank transfer details'), $html);
        $this->assertStringContainsString('ES9121000418450200051332', $html);
        $this->assertStringContainsString('CAIXESBBXXX', $html);
        $this->assertStringContainsString('Issuer S.L.', $html);
        $this->assertStringNotContainsString('ES7921000813610123456789', $html);
        $this->assertStringNotContainsString('ES1000492352082414205416', $html);
    }

    #[Test]
    public function the_pdf_has_no_bank_transfer_block_without_marked_accounts(): void
    {
        $html = $this->renderPdf($this->createInvoice());

        $this->assertStringNotContainsString(__('Bank transfer details'), $html);
    }

    #[Test]
    public function the_pdf_shows_the_invoice_data(): void
    {
        $invoice = $this->createInvoice();
        Item::factory()->create(['invoice_id' => $invoice->id, 'description' => 'Coffee beans', 'sku' => 'CF-01', 'barcode' => '4008496982943']);

        $html = $this->renderPdf($invoice->fresh());

        $this->assertStringContainsString($invoice->fullNumber(), $html);
        $this->assertStringContainsString('Coffee beans', $html);
        $this->assertStringContainsString('CF-01<br>4008496982943', $html);
        $this->assertStringContainsString('Ana García', $html);
    }

    #[Test]
    public function the_pdf_shows_the_registration_number_apart_from_the_vat(): void
    {
        $invoice = $this->createInvoice();
        $invoice->update(['issuer_data' => [...$invoice->issuer_data, 'company' => [...$invoice->issuer_data['company'], 'registration_number' => '12345678']]]);

        $html = $this->renderPdf($invoice->fresh());

        $this->assertStringContainsString(__('Company registration number').': 12345678', $html);
        $this->assertStringNotContainsString(__('VAT').':', $html);
    }

    #[Test]
    public function the_pdf_is_downloaded(): void
    {
        $invoice = $this->createInvoice();

        $response = $this->get(route('invoice.download', $invoice->id));

        $response->assertOk();
        $response->assertHeader('Content-Type', 'application/pdf');
        $response->assertHeader('Content-Disposition', 'attachment; filename="invoice_'.$invoice->fullNumber().'.pdf"');
        $this->assertStringStartsWith('%PDF', $response->getContent());
    }

    #[Test]
    public function the_pdf_of_another_company_returns_not_found(): void
    {
        $this->get(route('invoice.download', Invoice::factory()->create()->id))->assertNotFound();
    }

    private function createInvoice(): Invoice
    {
        return Invoice::factory()->create([
            'company_id' => $this->user->company_id,
            'customer_data' => ['name' => 'Ana', 'business_name' => 'García', 'vat' => null, 'address' => []],
            'issuer_data' => ['company' => ['business_name' => 'Issuer S.L.', 'currency' => 'EUR'], 'branch' => ['code' => '0001', 'name' => 'Main', 'address' => []]],
        ]);
    }

    private function renderPdf(Invoice $invoice): string
    {
        return view('invoice.pdf', [
            'invoice' => $invoice->load(['items', 'order', 'company']),
            'bankAccounts' => Account::shownOnInvoicesOf($this->user->company_id),
        ])->render();
    }
}
