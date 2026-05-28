<?php

namespace App\Http\Controllers;

use App\Models\Lead;
use Illuminate\Http\Request;

class PublicLeadController extends Controller
{
    public function create()
    {
        return view('public.leads.create');
    }

    public function store(Request $request)
    {
        if ($request->filled('website_url')) {
            return redirect()->route('public.leads.create');
        }
        $data = $request->validate([
            'campaign' => ['nullable', 'string', 'max:255'],
            'medium' => ['nullable', 'string', 'max:255'],
            'metadata' => ['nullable', 'array'],
            'first_name' => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:255'],
            'whatsapp' => ['nullable', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'intent' => ['required', 'in:buy,sell,both'],
            'source' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string'],
            'interest_target_type' => ['nullable', 'string', 'in:general,development,development_unit,listing'],
            'listing_id' => ['nullable', 'integer', 'exists:listings,id'],
            'development_id' => ['nullable', 'integer', 'exists:developments,id'],
            'development_unit_id' => ['nullable', 'integer', 'exists:development_units,id'],
            'page_url' => ['nullable', 'string', 'max:1000'],
            'referrer' => ['nullable', 'string', 'max:1000'],
            'language' => ['nullable', 'string', 'max:20'],
            'utm_source' => ['nullable', 'string', 'max:255'],
            'utm_campaign' => ['nullable', 'string', 'max:255'],
            'utm_medium' => ['nullable', 'string', 'max:255'],
            'utm_content' => ['nullable', 'string', 'max:255'],
            'utm_term' => ['nullable', 'string', 'max:255'],
        ]);
        if (blank($data['phone'] ?? null) && blank($data['whatsapp'] ?? null) && blank($data['email'] ?? null)) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json([
                    'message' => __('leads.contact_required'),
                    'errors' => [
                        'contact' => [__('leads.contact_required')],
                    ],
                ], 422);
            }

            return back()
                ->withErrors(['contact' => __('leads.contact_required')])
                ->withInput();
        }

        $data['source'] = $request->input('metadata.utm_source') ?: ($data['source'] ?? 'website');
        $data['status'] = 'new';
        $data['priority'] = 'normal';
        $data['preferred_language'] = app()->getLocale();
        $data['interest_target_type'] ??= 'general';
        $data['source'] = $data['utm_source'] ?? $data['source'] ?? 'website';
        $data['campaign'] = $data['utm_campaign'] ?? $data['campaign'] ?? null;
        $data['medium'] = $data['utm_medium'] ?? $data['medium'] ?? null;

        $data['metadata'] = array_merge($data['metadata'] ?? [], [
            'page_url' => $data['page_url'] ?? null,
            'referrer' => $data['referrer'] ?? null,
            'language' => $data['language'] ?? null,
            'utm_source' => $data['utm_source'] ?? null,
            'utm_campaign' => $data['utm_campaign'] ?? null,
            'utm_medium' => $data['utm_medium'] ?? null,
            'utm_content' => $data['utm_content'] ?? null,
            'utm_term' => $data['utm_term'] ?? null,
        ]);
        unset(
            $data['page_url'],
            $data['referrer'],
            $data['language'],
            $data['utm_source'],
            $data['utm_campaign'],
            $data['utm_medium'],
            $data['utm_content'],
            $data['utm_term']
        );
        $lead = Lead::create($data);

        if ($request->expectsJson() || $request->is('api/*')) {
            return response()->json([
                'message' => __('leads.public_lead_created'),
                'lead_id' => $lead->id,
            ], 201);
        }

        return redirect()
            ->route('public.leads.create')
            ->with('success', __('leads.public_lead_created'));
    }
}
