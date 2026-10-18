<?php

declare(strict_types=1);

namespace App\Http\Controllers\Report;

use App\Http\Controllers\MainController;
use App\Http\Requests\ReportSaleRequest;
use Dompdf\Dompdf;
use Illuminate\Http\Response;

class ReportSalePdfController extends MainController
{
    public function download(ReportSaleRequest $request): Response
    {
        $data = $request->reportData();
        $html = view('report.sale_pdf', $data)->render();

        $dompdf = new Dompdf;
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        $fileName = 'sales_report_'.$data['from']->format('Ymd').'_'.$data['to']->format('Ymd').'.pdf';

        return response($dompdf->output(), 200)
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'attachment; filename="'.$fileName.'"');
    }
}
