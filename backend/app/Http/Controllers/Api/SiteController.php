<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\FooterLink;
use App\Models\Site;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SiteController extends Controller
{
    /**
     * Return the current site's branding and configuration info.
     */
    public function config(Request $request): JsonResponse
    {
        /** @var Site $site */
        $site = $request->attributes->get('site');

        return response()->json([
            'data' => [
                'name' => $site->name,
                'domain' => $site->domain,
                'logo_url' => $site->logo_url,
                'favicon_url' => $site->favicon_url,
                'primary_color' => $site->primary_color,
                'secondary_color' => $site->secondary_color,
                'meta_title' => $site->meta_title,
                'meta_description' => $site->meta_description,
                'entry_url' => $site->entry_url,
                'login_url' => $site->login_url,
                'show_sponsors' => $site->show_sponsors,
                'social_links' => $site->social_links,
                'ga_measurement_id' => $site->ga_measurement_id,
                'noindex_mode' => (bool) $site->noindex_mode,
                'custom_css' => $site->custom_css,
                'sponsor_page_visible' => (bool) $site->sponsor_page_visible,
                'sponsor_contact_url' => $site->sponsor_contact_url,
                'sponsor_contact_text' => $site->sponsor_contact_text,
                'footer_links' => FooterLink::active()->ordered()->get(['id', 'link_text', 'link_url']),
            ],
        ]);
    }
}
