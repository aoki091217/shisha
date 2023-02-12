<?php

namespace App\Http\Requests;

use App\Models\User;
use Hash;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

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
     * @return array<string, mixed>
     */
    public function rules()
    {
        $rules = [
            'user.name' => 'required|max:20',
            'user.email' => 'required|email:rfc,dns'
        ];

        if(request()->method() === 'PATCH') {
            return array_merge($rules, [
                'user.code' => 'required|regex:/^[a-zA-Z0-9]+$/|max:20',
                'user.password' => 'sometimes|required|regex:/^(?=.*[0-9])(?=.*[a-z])(?=.*[A-Z])[0-9a-zA-Z\-]{8,24}$/',
                'user.password_confirm' => 'sometimes|required|same:user.password',
            ]);
        } else {
            return array_merge($rules, [
                'user.code' => 'required|unique:users,code|regex:/^[a-zA-Z0-9]+$/|max:20',
                'user.password' => 'required|regex:/^(?=.*[0-9])(?=.*[a-z])(?=.*[A-Z])[0-9a-zA-Z\-]{8,24}$/',
                'user.password_confirm' => 'required|same:user.password',
            ]);
        }
    }

    public function passedValidation()
    {
        $user = $this->user;
        unset($user['password_confirm']);

        if (!isset($this->user['password'])) {
            $password = User::where('code', $this->user['code'])->first()->password;
            $user = array_merge($user, ['password' => $password]);
            $this->merge(['user' => $user]);
        } else {
            $user = array_merge($user, ['password' => Hash::make($this->password)]);
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
