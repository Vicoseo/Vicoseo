<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreSiteRequest extends FormRequest
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
            'domain' => ['required', 'string', 'max:255', 'unique:landlord.sites,domain'],
            'name' => ['required', 'string', 'max:255'],
            'logo_url' => ['nullable', 'string', 'max:500'],
            'favicon_url' => ['nullable', 'string', 'max:500'],
            'primary_color' => ['nullable', 'string', 'max:20'],
            'secondary_color' => ['nullable', 'string', 'max:20'],
            'db_name' => ['nullable', 'string', 'max:255', 'unique:landlord.sites,db_name'],
            'is_active' => ['sometimes', 'boolean'],
            'meta_title' => ['nullable', 'string', 'max:255'],
            'meta_description' => ['nullable', 'string', 'max:1000'],
            'entry_url' => ['nullable', 'string', 'max:500'],
            'login_url' => ['nullable', 'string', 'max:500'],
            'show_sponsors' => ['sometimes', 'boolean'],
            'social_links' => ['nullable', 'array'],
            'social_links.telegram' => ['nullable', 'string', 'max:500'],
            'social_links.instagram' => ['nullable', 'string', 'max:500'],
            'social_links.x' => ['nullable', 'string', 'max:500'],
            'social_links.youtube' => ['nullable', 'string', 'max:500'],
            'social_links.tiktok' => ['nullable', 'string', 'max:500'],
            'social_links.whatsapp' => ['nullable', 'string', 'max:500'],
            'social_links.support_email' => ['nullable', 'string', 'max:500'],
            'ga_measurement_id' => ['nullable', 'string', 'max:50'],
        ];
    }
}
