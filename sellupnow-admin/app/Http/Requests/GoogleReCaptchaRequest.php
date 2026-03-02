<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GoogleReCaptchaRequest extends FormRequest
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
        $provider = $this->input('provider', 'google');
        $isActive = (bool) $this->boolean('is_active');

        $rules = [
            'provider' => 'required|in:google,cloudflare',
            // Allow storing keys even when disabled (nullable).
            'site_key' => 'nullable|string|max:5000',
            'secret_key' => 'nullable|string|max:5000',
            'turnstile_site_key' => 'nullable|string|max:5000',
            'turnstile_secret_key' => 'nullable|string|max:5000',
        ];

        if ($isActive) {
            if ($provider === 'cloudflare') {
                $rules['turnstile_site_key'] = 'required|string|max:5000';
                $rules['turnstile_secret_key'] = 'required|string|max:5000';
            } else {
                $rules['site_key'] = 'required|string|max:5000';
                $rules['secret_key'] = 'required|string|max:5000';
            }
        }

        return $rules;
    }
}
