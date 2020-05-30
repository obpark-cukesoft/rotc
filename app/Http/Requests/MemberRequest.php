<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class MemberRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        if (Auth::user()->isAdmin()) return true;
        if (Auth::user()->isMember()) return true;
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'required',
            //'email' => 'required|email|unique:users,email',
            'email' => ['required', 'email', Rule::unique('users')->ignore($this->id)],
            //'username' => 'nullable|unique:users,username',
            //'username' => ['nullable', Rule::unique('users')->ignore($this->id)],
            'password' => 'nullable|confirmed|min:6',
        ];
    }

    public function messages()
    {
        return [
            /*'username.required' => '아이디를 입력하여 주십시오.',
            'username.unique' => '이미 등록된 아이디입니다.',*/
            'email.required' => '이메일을 입력하여 주십시오.',
            'email.unique' => '이미 등록된 이메일입니다.',
            'name.required' => '이름을 입력하여 주십시오.',
            'password.required' => '비밀번호를 입력하여 주십시오.',
            'password.confirmed' => '비밀번호가 일치하도록 입력하여 주십시오.',
            'password.min' => '비밀번호를 :min자 이상으로 입력하여 주십시오.'
        ];
    }

    public function prepareForValidation()
    {
        /*$replaces = $this->input();
        $this->replace($replaces);*/
    }
}
