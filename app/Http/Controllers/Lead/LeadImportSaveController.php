<?php

declare(strict_types=1);

namespace App\Http\Controllers\Lead;

use App\Http\Controllers\MainController;
use App\Http\Requests\ImportRequest;
use App\Models\Lead;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class LeadImportSaveController extends MainController
{
    private const string LEAD_REDIRECT_URL = '/lead';

    private const array SKIP_COLUMNS = [
        'id', 'company_id', 'seller_id',
        'created_at', 'updated_at',
        'created_by', 'updated_by', 'deleted_by', 'deleted_at',
    ];

    private const array DATE_COLUMNS = [
        'dob', 'schedule_contact',
    ];

    /**
     * @return Application|RedirectResponse|Redirector
     */
    public function save(ImportRequest $request)
    {
        $file = $request->file('upload');

        $allowedExtensions = ['csv'];
        if (! in_array($file->getClientOriginalExtension(), $allowedExtensions)) {
            return redirect(self::LEAD_REDIRECT_URL)->withErrors(__('Invalid file type. Only CSV files allowed.'));
        }

        $filePath = $file->path();

        try {
            $handle = fopen($filePath, 'r');
        } catch (\Throwable $t) {
            return redirect(self::LEAD_REDIRECT_URL)->withErrors(__("Can't read uploaded file"));
        }

        $rowCount = 0;
        $separator = (! empty($request->separator)) ? $request->separator : ';';

        $header = fgetcsv($handle, 0, $separator);

        if ($header === false) {
            fclose($handle);

            return redirect(self::LEAD_REDIRECT_URL)->withErrors(__('Empty file'));
        }

        $columnMap = $this->buildColumnMap($header);

        while (($data = fgetcsv($handle, 0, $separator)) !== false) {
            if (empty($data[0])) {
                continue;
            }

            $lead = $this->mapRowToLead($data, $columnMap);

            try {
                $lead->save();
                $rowCount++;
            } catch (\Throwable $t) {
                Log::error($t->getMessage().' | row number:'.($rowCount + 1));
            }
        }
        fclose($handle);

        return redirect(self::LEAD_REDIRECT_URL)->with([
            'message' => ($rowCount > 0) ? 'Leads imported :count successfully' : 'An error occurred while importing leads',
            'count' => $rowCount,
        ]);
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

    private function mapRowToLead(array $data, array $columnMap): Lead
    {
        $lead = new Lead;

        $lead->company_id = Auth::user()->company_id;

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

            if ($column === 'tags' && is_string($value)) {
                $value = array_map('trim', explode(',', $value));
            }

            if (in_array($column, self::DATE_COLUMNS, true) && empty($value)) {
                continue;
            }

            $lead->$column = $value;
        }

        $lead->seller_id = Auth::user()->id;

        return $lead;
    }
}
