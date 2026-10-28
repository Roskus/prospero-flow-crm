<?php

declare(strict_types=1);

namespace App\Http\Controllers\Invoice;

use App\Http\Controllers\MainController;
use App\Models\Bank\Account;
use App\Models\Invoice;
use Dompdf\Dompdf;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class InvoicePdfController extends MainController
{
    public function download(Request $request, int $id): Response
    {
        $invoice = Invoice::query()
            ->ofCompany($request->user()->company_id)
            ->with(['items', 'order', 'company'])
            ->findOrFail($id);

        $html = view('invoice.pdf', [
            'invoice' => $invoice,
            'bankAccounts' => Account::shownOnInvoicesOf($invoice->company_id),
        ])->render();

        $dompdf = new Dompdf;
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        return response($dompdf->output(), 200)
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'attachment; filename="invoice_'.$invoice->fullNumber().'.pdf"');
    }
}
