<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreRedirectRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        return [
            'slug' => ['required', 'string', 'max:255', 'unique:tenant.redirects,slug'],
            'target_url' => ['required', 'url', 'max:500'],
            'status_code' => ['sometimes', 'integer', 'in:301,302,307,308'],
            'description' => ['nullable', 'string', 'max:500'],
            'is_active' => ['sometimes', 'boolean'],
        ];
    }
}
