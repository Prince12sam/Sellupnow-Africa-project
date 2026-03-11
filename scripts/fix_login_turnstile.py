#!/usr/bin/env python3
"""
Add Cloudflare Turnstile (and reCAPTCHA fallback) to the login page blade
and add captcha validation to LoginController.
"""

import re

# ─── 1. user-login.blade.php ────────────────────────────────────────────────

LOGIN_BLADE = '/home/sellupnow/htdocs/www.sellupnow.com/main-file/listocean/core/resources/views/frontend/user/user-login.blade.php'

with open(LOGIN_BLADE, 'r') as f:
    blade = f.read()

# 1a. Add Turnstile script loader inside the login-Wrapper div (same pattern as register)
OLD_WRAPPER = '<div class="col-xl-7 col-lg-7 order-lg-1 order-0 login-Wrapper">'
NEW_WRAPPER = '''<div class="col-xl-7 col-lg-7 order-lg-1 order-0 login-Wrapper">
                    @if(!empty(get_static_option('site_google_captcha_enable')))
                        @if(get_static_option('captcha_provider') == 'cloudflare')
                            <script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer></script>
                        @else
                            <script src='https://www.google.com/recaptcha/api.js'></script>
                        @endif
                    @endif'''

if OLD_WRAPPER in blade:
    blade = blade.replace(OLD_WRAPPER, NEW_WRAPPER, 1)
    print('1a OK: script loader added')
else:
    print('1a FAIL: login-Wrapper div not found')

# 1b. Add captcha widget before the submit button
OLD_BTN = '<div class="col-sm-12">\n                            <div class="passRemember mt-20">'
NEW_BTN = '''<div class="col-sm-12">
                            @if(!empty(get_static_option('site_google_captcha_enable')))
                                <div class="my-3">
                                    @if(get_static_option('captcha_provider') == 'cloudflare')
                                        <div class="cf-turnstile" data-sitekey="{{ get_static_option('cloudflare_turnstile_site_key') }}" data-appearance="always" data-theme="light" data-size="normal"></div>
                                        @if ($errors->has('cf-turnstile-response'))
                                            <span class="text-danger">{{ $errors->first('cf-turnstile-response') }}</span>
                                        @endif
                                    @else
                                        <div class="g-recaptcha" data-sitekey="{{ get_static_option('recaptcha_2_site_key') }}"></div>
                                        @if ($errors->has('g-recaptcha-response'))
                                            <span class="text-danger">{{ $errors->first('g-recaptcha-response') }}</span>
                                        @endif
                                    @endif
                                </div>
                            @endif
                            </div>
                            <div class="col-sm-12">
                            <div class="passRemember mt-20">'''

if OLD_BTN in blade:
    blade = blade.replace(OLD_BTN, NEW_BTN, 1)
    print('1b OK: captcha widget added before remember-me')
else:
    print('1b FAIL: passRemember div not found, trying alternate pattern')
    # Try with different whitespace
    alt_old = '<div class="passRemember mt-20">'
    if alt_old in blade:
        # Find the col-sm-12 before passRemember
        idx = blade.find(alt_old)
        # Find the preceding <div class="col-sm-12"> before this
        pre_idx = blade.rfind('<div class="col-sm-12">', 0, idx)
        snippet = blade[pre_idx:idx+len(alt_old)]
        print(f'1b DEBUG snippet: {repr(snippet[:200])}')
    else:
        print('1b FAIL: passRemember also not found')

# 1c. Update the AJAX call to include the cf-turnstile-response token
OLD_AJAX_DATA = '''                        data: {
                            username : $('#username').val(),
                            password : $('#password').val(),
                            remember : $('#remember').val(),
                        },'''
NEW_AJAX_DATA = '''                        data: {
                            username : $('#username').val(),
                            password : $('#password').val(),
                            remember : $('#check15').is(':checked') ? 1 : 0,
                            'cf-turnstile-response': $('[name="cf-turnstile-response"]').val() || '',
                            'g-recaptcha-response': $('[name="g-recaptcha-response"]').val() || '',
                        },'''

if OLD_AJAX_DATA in blade:
    blade = blade.replace(OLD_AJAX_DATA, NEW_AJAX_DATA, 1)
    print('1c OK: AJAX data updated with captcha tokens')
else:
    print('1c FAIL: AJAX data block not found')

with open(LOGIN_BLADE, 'w') as f:
    f.write(blade)

lines = blade.count('\n') + 1
print(f'Blade saved. Lines: {lines}')

# ─── 2. LoginController.php ─────────────────────────────────────────────────

LOGIN_CTRL = '/home/sellupnow/htdocs/www.sellupnow.com/main-file/listocean/core/app/Http/Controllers/Auth/LoginController.php'

with open(LOGIN_CTRL, 'r') as f:
    ctrl = f.read()

# Add captcha validation after the existing $request->validate() block in userLogin()
OLD_VALIDATE = '''            $request->validate([
                'username' => 'required|string',
                'password' => 'required|min:6'
            ],
                [
                    'username.required' => sprintf(__('%s required'),$email_or_username),
                    'password.required' => __('password required')
                ]);

            if (Auth::guard('web')->attempt'''

NEW_VALIDATE = '''            $request->validate([
                'username' => 'required|string',
                'password' => 'required|min:6'
            ],
                [
                    'username.required' => sprintf(__('%s required'),$email_or_username),
                    'password.required' => __('password required')
                ]);

            // Captcha validation
            if (!empty(get_static_option('site_google_captcha_enable'))) {
                $captcha_provider = get_static_option('captcha_provider');
                if ($captcha_provider == 'cloudflare') {
                    $result = cloudflare_turnstile_check($request->input('cf-turnstile-response', ''));
                    if (empty($result['success'])) {
                        return response()->json([
                            'msg'    => __('Captcha verification failed. Please try again.'),
                            'type'   => 'danger',
                            'status' => 'not_ok',
                        ]);
                    }
                } else {
                    $captcha_validate = $request->validate(['g-recaptcha-response' => 'required']);
                    $recaptcha_url    = 'https://www.google.com/recaptcha/api/siteverify';
                    $recaptcha_secret = get_static_option('recaptcha_2_secret_key');
                    $recaptcha_data   = ['secret' => $recaptcha_secret, 'response' => $request->input('g-recaptcha-response')];
                    $verify_response  = file_get_contents($recaptcha_url.'?'.http_build_query($recaptcha_data));
                    $response_data    = json_decode($verify_response);
                    if (!$response_data->success) {
                        return response()->json([
                            'msg'    => __('Captcha verification failed. Please try again.'),
                            'type'   => 'danger',
                            'status' => 'not_ok',
                        ]);
                    }
                }
            }

            if (Auth::guard('web')->attempt'''

if OLD_VALIDATE in ctrl:
    ctrl = ctrl.replace(OLD_VALIDATE, NEW_VALIDATE, 1)
    print('2 OK: captcha validation added to LoginController')
else:
    print('2 FAIL: validate block not found in LoginController')
    # Debug: show the area around the validate call
    idx = ctrl.find("'username' => 'required|string'")
    if idx >= 0:
        print(f'DEBUG: found validate at {idx}: {repr(ctrl[idx-100:idx+200])}')

with open(LOGIN_CTRL, 'w') as f:
    f.write(ctrl)

print('LoginController saved.')
print('Done!')
