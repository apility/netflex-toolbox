@push($push ?? 'head')
    @once
        <script src="https://www.google.com/recaptcha/api.js{{ ($scriptOnly ?? false) ? '?render=explicit' : '' }}" async
                defer></script>
    @endonce
@endpush

@unless($scriptOnly ?? false)
    <div class="mb-4">
        <div class="g-recaptcha" data-sitekey="{{ config('recaptcha-v2.site_key') }}"></div>
    </div>
@endunless