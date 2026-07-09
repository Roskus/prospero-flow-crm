<?php

declare(strict_types=1);

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\MainController;
use App\Http\Requests\ImportRequest;
use App\Models\Customer;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class CustomerImportSaveController extends MainController
{
    private const string CUSTOMER_REDIRECT_URL = '/customer';

    private const array SKIP_COLUMNS = [
        'id', 'company_id', 'seller_id', 'source_id',
        'created_at', 'updated_at',
        'created_by', 'updated_by', 'deleted_by', 'deleted_at',
        'latitude', 'longitude', 'opt_in',
        'phone_verified', 'phone2_verified', 'mobile_verified', 'email_verified', 'website_verified',
    ];

    private const array DATE_COLUMNS = [
        'dob', 'schedule_contact',
    ];

    /**
     * @return Application|RedirectResponse|Redirector
     */
    public function save(ImportRequest $request)
    {
        $errors = [];
        $file = $request->file('upload');

        $filePath = $file->path();

        try {
            $handle = fopen($filePath, 'r');
        } catch (\Throwable $t) {
            return redirect(self::CUSTOMER_REDIRECT_URL)->withErrors(__("Can't read uploaded file"));
        }

        $rowCount = 0;
        $successfulImports = 0;
        $separator = $request->input('separator') ?: ';';

        $header = fgetcsv($handle, 0, $separator);

        if ($header === false) {
            fclose($handle);

            return redirect(self::CUSTOMER_REDIRECT_URL)->withErrors(__('Empty file'));
        }

        $columnMap = $this->buildColumnMap($header);

        while (($data = fgetcsv($handle, 0, $separator)) !== false) {
            if (empty($data[0])) {
                continue;
            }

            $customer = $this->mapRowToCustomer($data, $columnMap);

            try {
                $customer->save();
                $successfulImports++;
            } catch (\Throwable $t) {
                Log::error($t->getMessage().' | row number:'.($rowCount + 1));
                $errors[] = __('Error in row :row. Message: :message', ['row' => $rowCount + 1, 'message' => $t->getMessage()]);
            }
            $rowCount++;
        }
        fclose($handle);

        $status = $successfulImports > 0 ? 'success' : 'error';
        $message = $status === 'success' ? __('Customers imported successfully, with :count errors.', ['count' => count($errors)]) : __('An error occurred while importing customers.');

        $response = [
            'status' => $status,
            'message' => $message,
            'count' => $rowCount,
        ];

        return redirect(self::CUSTOMER_REDIRECT_URL)->with($response);
    }

    private function buildColumnMap(array $header): array
    {
        $map = [];

        foreach ($header as $index => $column) {
            $column = trim($column);

            $map[$column] = $index;
        }

        return $map;
    }

    private function mapRowToCustomer(array $data, array $columnMap): Customer
    {
        $customer = new Customer;

        $customer->company_id = Auth::user()->company_id;

        foreach ($columnMap as $column => $index) {
            if (in_array($column, self::SKIP_COLUMNS, true)) {
                continue;
            }

            if (! isset($data[$index])) {
                continue;
            }

            $value = $data[$index];

            if (in_array($column, ['phone', 'phone2', 'mobile'], true)) {
                $value = str_replace([' ', '(', ')', '.', '-', '/', '|'], '', $value);
            }

            if ($column === 'email2' && empty($value)) {
                continue;
            }

            if ($column === 'country_id') {
                $value = strlen(trim($value)) == 2 ? strtolower(trim($value)) : '';
            }

            if ($column === 'website') {
                $value = rtrim($value, '/');
            }

            if ($column === 'tags') {
                if (is_string($value)) {
                    $value = array_map('trim', explode(',', $value));
                }
                if (empty($value) || $value === ['']) {
                    continue;
                }
            }

            if (in_array($column, self::DATE_COLUMNS, true) && empty($value)) {
                continue;
            }

            if ($column === 'industry_id' && $value === '') {
                continue;
            }

            if ($column === 'notes') {
                $value = str_replace(["\r", "\n"], '-', $value);
            }

            $customer->$column = $value;
        }

        $customer->seller_id = Auth::user()->id;

        return $customer;
    }
}
