<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules;

class UserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $user = request()->input('userId');

        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users', 'name')->ignore($user)],
            'role' => ['required'],
            'password' => [Rule::requiredIf(function () use ($user) {
                return ! $user;
            }), 'confirmed', Rules\Password::default()],
        ];
    }

    public function messages()
    {
        return [
            'email.unique' => 'User with this email already exist',
        ];
    }
}
