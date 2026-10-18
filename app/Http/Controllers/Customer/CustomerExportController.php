<?php

declare(strict_types=1);

namespace App\Http\Controllers\Customer;

use App\Models\Customer;
use App\Services\CsvExportService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\StreamedResponse;

class CustomerExportController
{
    public function __construct(private CsvExportService $csvExportService) {}

    public function export(Request $request): StreamedResponse
    {
        $customer = new Customer;
        $fileName = 'customers_'.Auth::user()->company->name.'_'.date('Ymd_His').'.csv';
        $customers = Customer::where('company_id', (int) Auth::user()->company_id)->get();

        $columns = array_merge(['id'], $customer->getFillable(), ['created_at', 'updated_at']);

        $rows = array_map(function (array $row): array {
            $row['tags'] = is_array($row['tags']) ? implode(separator: ',', array: $row['tags']) : '';
            $row['notes'] = is_null($row['notes']) ? null : str_replace(["\r", "\n"], '-', $row['notes']);
            $row['country_id'] = $row['country']['id'] ?? null;
            $row['seller_id'] = $row['seller']['id'] ?? null;
            $row['industry_id'] = $row['industry']['id'] ?? null;

            return $row;
        }, $customers->toArray());

        return $this->csvExportService->download($fileName, $columns, $rows);
    }
}
