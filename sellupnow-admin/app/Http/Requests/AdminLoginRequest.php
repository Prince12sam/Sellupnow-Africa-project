<?php

namespace App\Http\Requests;

use App\Models\GoogleReCaptcha;
use App\Rules\CaptchaValidate;
use App\Rules\EmailRule;
use Illuminate\Foundation\Http\FormRequest;

class AdminLoginRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $reCaptcha = GoogleReCaptcha::first();

        $rules = [
            'email' => ['required', 'email', 'exists:users,email'],
            'password' => 'required',
        ];

        if ($reCaptcha && $reCaptcha->is_active) {
            // All roles must pass reCAPTCHA — the root account is the highest-value
            // target and must not be exempted from this protection.
            if ($reCaptcha->provider === 'cloudflare') {
                $rules['cf-turnstile-response'] = ['required', new CaptchaValidate];
            } else {
                $rules['g-recaptcha-response'] = ['required', new CaptchaValidate];
            }
        }

        return $rules;
    }

    /**
     * Get the error messages for the defined validation rules.
     */
    public function messages(): array
    {
        return [
            'g-recaptcha-response.required' => 'The captcha field is required.',
            'cf-turnstile-response.required' => 'The captcha field is required.',
        ];
    }
}
