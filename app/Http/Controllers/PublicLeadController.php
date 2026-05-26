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
        ]);
        if (blank($data['phone'] ?? null) && blank($data['whatsapp'] ?? null) && blank($data['email'] ?? null)) {
            return back()
                ->withErrors(['contact' => __('leads.contact_required')])
                ->withInput();
        }

        $data['source'] = $request->input('metadata.utm_source') ?: ($data['source'] ?? 'website');
        $data['status'] = 'new';
        $data['priority'] = 'normal';
        $data['preferred_language'] = app()->getLocale();

        Lead::create($data);

        return redirect()
            ->route('public.leads.create')
            ->with('success', __('leads.public_lead_created'));
    }
}
