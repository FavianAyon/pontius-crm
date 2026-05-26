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
