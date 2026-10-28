<?php

declare(strict_types=1);

namespace App\Http\Controllers\Report;

use App\Http\Controllers\MainController;
use App\Http\Requests\ReportHoursRequest;
use App\Services\CsvExportService;
use App\Services\WorkHourService;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportHoursCsvController extends MainController
{
    public function __construct(private CsvExportService $csvExportService) {}

    public function download(ReportHoursRequest $request, WorkHourService $workHourService): StreamedResponse
    {
        $report = $request->reportData($workHourService);
        $columns = [__('Employee'), __('Worked hours'), __('Expected hours'), __('Difference'), __('Days worked'), __('Open entries')];
        $line = fn (string $name, array $values): array => array_combine($columns, [
            $name,
            number_format($values['worked'], 2, '.', ''),
            number_format($values['expected'], 2, '.', ''),
            number_format($values['difference'], 2, '.', ''),
            (string) $values['days_worked'],
            (string) $values['open_entries'],
        ]);

        $rows = array_map(fn (array $row): array => $line(trim($row['employee']->first_name.' '.$row['employee']->last_name), $row), $report['rows']);
        $rows[] = $line(__('Total'), $report['totals']);

        $fileName = 'hours_'.$report['from']->format('Ymd').'_'.$report['to']->format('Ymd').'.csv';

        return $this->csvExportService->download($fileName, $columns, $rows);
    }
}
