<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class SecureFormRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Auth::check();
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            // Base security rules
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Sanitize input data
        $this->merge([
            // Strip dangerous tags and scripts
            ...$this->sanitizeInput($this->all())
        ]);
    }

    /**
     * Sanitize input to prevent XSS attacks
     */
    private function sanitizeInput(array $data): array
    {
        array_walk_recursive($data, function (&$value) {
            if (is_string($value)) {
                // Remove dangerous HTML tags
                $value = strip_tags($value, '<p><br><strong><em><ul><ol><li>');
                
                // Remove dangerous attributes
                $value = preg_replace('/\bon\w+="[^"]*"/i', '', $value);
                $value = preg_replace('/javascript:/i', '', $value);
                
                // Trim whitespace
                $value = trim($value);
            }
        });

        return $data;
    }
}