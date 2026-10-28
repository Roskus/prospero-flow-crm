<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\Customer;
use App\Models\Lead;
use App\Models\Prospect;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Builder;
use libphonenumber\NumberParseException;
use libphonenumber\PhoneNumberUtil;

class PhoneValidatorCommand extends Command
{
    /**
     * Phone fields and the flag that marks each one as verified.
     */
    private const array FIELDS = [
        'phone' => 'phone_verified',
        'mobile' => 'mobile_verified',
        'phone2' => 'phone2_verified',
    ];

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'crm:phone:validate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Validate the phone numbers of leads and customers';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $phoneUtil = PhoneNumberUtil::getInstance();

        foreach (['Leads' => Lead::class, 'Customers' => Customer::class] as $label => $model) {
            foreach (self::FIELDS as $field => $verificationField) {
                $validated = $this->validateField($model, $phoneUtil, $field, $verificationField);
                $this->info("{$label} validated {$field}: {$validated}");
            }
        }

        return self::SUCCESS;
    }

    /**
     * @param  class-string<Prospect>  $model
     */
    private function validateField(string $model, PhoneNumberUtil $phoneUtil, string $field, string $verificationField): int
    {
        $prospects = $model::query()
            ->whereNotNull($field)
            ->where($field, '!=', '')
            ->where(fn (Builder $query) => $query->where($verificationField, false)->orWhereNull($verificationField))
            ->get();
        $validated = 0;

        foreach ($prospects as $prospect) {
            try {
                $number = $phoneUtil->parse((string) $prospect->{$field}, strtoupper((string) $prospect->getAttribute('country_id')));
                if ($phoneUtil->isValidNumber($number)) {
                    $prospect->{$verificationField} = true;
                    $prospect->save();
                    $validated++;
                }
            } catch (NumberParseException) {
                $this->error("Error parsing {$field} of ".class_basename($model)." ID {$prospect->getKey()}");
            }
        }

        return $validated;
    }
}
