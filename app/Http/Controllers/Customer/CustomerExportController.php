<?php

declare(strict_types=1);

namespace App\Http\Controllers\Customer;

use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class CustomerExportController
{
    public function export(Request $request): BinaryFileResponse
    {
        $separator = ';';
        $customer = new Customer;
        $fileName = 'customers_'.Auth::user()->company->name.'_'.date('Ymd_His').'.csv';
        $customers = Customer::where('company_id', (int) Auth::user()->company_id)->get();
        $headers = [
            'Content-type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=$fileName",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        $columns = array_merge(['id'], $customer->getFillable(), ['created_at', 'updated_at']);
        $rowHeaders = implode($separator, $columns);

        $data = $customers->toArray();

        $content = $rowHeaders."\n";
        foreach ($data as $row) {

            $line = [];

            $row['tags'] = is_array($row['tags']) ? implode(separator: ',', array: $row['tags']) : '';
            $row['notes'] = is_null($row['notes']) ? null : str_replace(["\r", "\n"], '-', $row['notes']);
            $row['country_id'] = $row['country']['id'];
            $row['seller_id'] = $row['seller']['id'];
            $row['industry_id'] = $row['industry']['id'];

            // This ensures that each value is in the same order as the fields in the header.
            foreach ($columns as $column) {
                if (isset($row[$column])) {
                    $line[] = $row[$column];
                }
            }

            $line = implode($separator, $line);
            $content = $content.$line."\n";
        }

        $tempFilePath = $this->createTempFile($fileName, $content);

        // Retorna el archivo para descarga
        return Response::download($tempFilePath, basename($tempFilePath), $headers)->deleteFileAfterSend(true);
    }

    protected function createTempFile($fileName, $content): string
    {
        $tempFilePath = tempnam(sys_get_temp_dir(), $fileName).'.csv';
        file_put_contents($tempFilePath, $content);

        return $tempFilePath;
    }
}
