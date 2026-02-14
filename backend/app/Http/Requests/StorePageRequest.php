<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Models\Site;
use App\Services\TenantManager;
use Illuminate\Foundation\Http\FormRequest;

class StorePageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $siteId = $this->route('siteId');
        if ($siteId) {
            $site = Site::findOrFail($siteId);
            app(TenantManager::class)->setTenant($site);
        }
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        return [
            'slug' => ['required', 'string', 'max:255', 'unique:tenant.pages,slug'],
            'title' => ['required', 'string', 'max:255'],
            'content' => ['required', 'string'],
            'meta_title' => ['nullable', 'string', 'max:255'],
            'meta_description' => ['nullable', 'string', 'max:1000'],
            'meta_keywords' => ['nullable', 'string', 'max:500'],
            'is_published' => ['sometimes', 'boolean'],
            'sort_order' => ['sometimes', 'integer', 'min:0'],
        ];
    }
}
