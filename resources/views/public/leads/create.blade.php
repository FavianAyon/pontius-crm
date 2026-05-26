<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <title>Lead Capture</title>
</head>
<body>
@if (session('success'))
    <p>{{ session('success') }}</p>
@endif

<form method="POST" action="{{ route('public.leads.store') }}">
    @csrf
    <input type="text" name="website_url" style="display:none" tabindex="-1" autocomplete="off">
    <input type="hidden" name="campaign" value="{{ request('utm_campaign') }}">
    <input type="hidden" name="medium" value="{{ request('utm_medium') }}">
    <input type="hidden" name="metadata[utm_source]" value="{{ request('utm_source') }}">
    <input type="hidden" name="metadata[utm_content]" value="{{ request('utm_content') }}">
    <input type="hidden" name="metadata[utm_term]" value="{{ request('utm_term') }}">
    <input name="first_name" placeholder="{{ __('leads.first_name') }}" required>
    <input name="phone" placeholder="{{ __('leads.phone') }}">
    <input name="whatsapp" placeholder="{{ __('leads.whatsapp') }}">
    <input name="email" placeholder="{{ __('leads.email') }}">

    <select name="intent" required>
        <option value="buy">{{ __('leads.buy') }}</option>
        <option value="sell">{{ __('leads.sell') }}</option>
        <option value="both">{{ __('leads.both') }}</option>
    </select>

    <textarea name="notes" placeholder="{{ __('leads.notes') }}"></textarea>

    <input type="hidden" name="source" value="website">

    <button type="submit">
        {{ __('leads.send') }}
    </button>
</form>
</body>
</html>
