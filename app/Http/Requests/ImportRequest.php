<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ImportRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $maxFileSize = $this->getMaxFileSize(); // Asegúrate de implementar este método

        return [
            'upload' => [
                'required',
                'file',
                'mimes:csv,txt',
                'max:'.$maxFileSize,
            ],
        ];
    }

    /**
     * Obtener el tamaño máximo de archivo permitido desde la configuración de PHP en kilobytes.
     *
     * @return int Tamaño máximo de archivo en kilobytes.
     */
    private function getMaxFileSize(): int
    {
        // Convertir upload_max_filesize y post_max_size de php.ini a kilobytes
        $uploadMaxFilesize = $this->convertPhpIniValueToKilobytes(ini_get('upload_max_filesize'));
        $postMaxSize = $this->convertPhpIniValueToKilobytes(ini_get('post_max_size'));

        // Devuelve el menor valor entre upload_max_filesize y post_max_size
        return min($uploadMaxFilesize, $postMaxSize);
    }

    /**
     * Convertir los valores de configuración de PHP a kilobytes.
     *
     * @param  string  $value  Valor de configuración de PHP.
     * @return int Valor en kilobytes.
     */
    private function convertPhpIniValueToKilobytes(string $value): int
    {
        $value = trim($value);
        $lastChar = strtolower($value[strlen($value) - 1]);
        $value = (int) $value;

        switch ($lastChar) {
            case 'g':
                $value *= 1024 * 1024;
                break;
            case 'm':
                $value *= 1024;
                break;
            case 'k':
                break;
            default:
                // Si no hay sufijo, asume que el valor ya está en bytes, convierte a KB
                $value = $value / 1024;
        }

        return $value;
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'upload.required' => __('The upload field is required.'),
            'upload.file' => __('The uploaded file is not valid.'),
            'upload.mimes' => __('The file must be a CSV or TXT.'),
            'upload.max' => __('The file may not be greater than :max kilobytes.', ['max' => $this->getMaxFileSize()]),
        ];
    }
}
