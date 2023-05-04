<?php

namespace App\Http\Requests;

use App\Models\User;
use Hash;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class ShopRequest extends FormRequest
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
     * @return array<string, mixed>
     */
    public function rules()
    {
        $rules = [
            'shop.name' => 'required|max:50',
            'user.name' => 'required|max:20',
            'user.email' => 'required|email:rfc,dns'
        ];

        if(request()->method() === 'PATCH') {
            return array_merge($rules, [
                'user.code' => 'required|regex:/^[a-zA-Z0-9]+$/|max:20',
                'user.password' => 'sometimes|required|regex:/^(?=.*[0-9])(?=.*[a-z])(?=.*[A-Z])[0-9a-zA-Z\-]{8,24}$/',
                'user.password_confirmation' => 'sometimes|required|same:user.password',
            ]);
        } else {
            return array_merge($rules, [
                'user.code' => 'required|unique:users,code|regex:/^[a-zA-Z0-9]+$/|max:20',
                'user.password' => 'required|regex:/^(?=.*[0-9])(?=.*[a-z])(?=.*[A-Z])[0-9a-zA-Z\-]{8,24}$/',
                'user.password_confirmation' => 'required|same:user.password',
            ]);
        }
    }

    public function passedValidation()
    {
        $user = $this->user;
        unset($user['password_confirmation'], $user['is_change']);

        if (!isset($this->user['password'])) {
            $password = User::where('code', $this->user['code'])->first()->password;
            $user = array_merge($user, ['password' => $password, 'role_id' => 2]);
            $this->merge(['user' => $user]);
        } else {
            $user = array_merge($user, ['password' => Hash::make($this->user['password']), 'role_id' => 2]);
            $this->merge(['user' => $user]);
        }
    }

    protected function failedValidation(Validator $validator)
    {
        $this->merge(['validated', true]);
        throw new HttpResponseException(
            redirect($this->getRedirectUrl())
            ->withErrors($validator)
            ->withInput($this->input())
        );
    }
}
