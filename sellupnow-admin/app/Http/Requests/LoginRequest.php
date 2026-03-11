<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Re-merge raw JSON body data before validation.
     */
    protected function prepareForValidation(): void
    {
        if ($this->isJson()) {
            $rawBody = $this->getContent();
            if (!empty($rawBody)) {
                $data = json_decode($rawBody, true) ?? [];
                if (!empty($data)) {
                    $this->merge($data);
                }
            }
        }
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $loginType = (int) $this->input('loginType', 0);

        // loginType 1 = phone (Firebase phone OTP — no password required)
        if ($loginType === 1) {
            return [
                'loginType'   => 'required|integer',
                'phoneNumber' => 'required|string',
                'fcmToken'    => 'nullable|string',
                'authIdentity' => 'nullable|string',
            ];
        }

        // loginType 2 = Google (no password required)
        if ($loginType === 2) {
            return [
                'loginType' => 'required|integer',
                'email'     => 'required|string',
                'fcmToken'  => 'nullable|string',
                'name'      => 'nullable|string',
                'authIdentity' => 'nullable|string',
            ];
        }

        // loginType 4 = Flutter email/password login
        if ($loginType === 4) {
            return [
                'loginType' => 'required|integer',
                'email'     => 'required|string',
                'password'  => 'required|string',
                'fcmToken'  => 'nullable|string',
                'authIdentity' => 'nullable|string',
            ];
        }

        // Legacy (no loginType) — web admin panel path
        return [
            'phone'    => 'required',
            'password' => 'required',
        ];
    }

    public function messages(): array
    {
        $request = request();
        if ($request->is('api/*')) {
            $header = strtolower($request->header('accept-language'));
            $lan = (preg_match('/^[a-z]+$/', $header)) ? $header : 'en';
            app()->setLocale($lan);
        }

        return [
            'phone.required'       => __('The phone/email field is required.'),
            'password.required'    => __('The password field is required.'),
            'phoneNumber.required' => __('The phone number field is required.'),
            'email.required'       => __('The email field is required.'),
        ];
    }
}
