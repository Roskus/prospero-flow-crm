<?php

declare(strict_types=1);

namespace App\Http\Controllers\Report;

use App\Http\Controllers\MainController;
use App\Http\Requests\ReportAnnualSaleRequest;
use Dompdf\Dompdf;
use Illuminate\Http\Response;

class ReportAnnualSalePdfController extends MainController
{
    public function download(ReportAnnualSaleRequest $request): Response
    {
        $data = $request->annualReportData();

        $dompdf = new Dompdf;
        $dompdf->loadHtml(view('report.annual_sale_pdf', $data)->render());
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        return response($dompdf->output(), 200)
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'attachment; filename="annual_sales_report_'.$data['year'].'.pdf"');
    }
}
