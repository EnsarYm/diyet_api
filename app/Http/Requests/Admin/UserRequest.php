<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

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
        return [
            'first_name' => 'required|max:50',
            'last_name' => 'required|max:50',
            'email' => 'required|unique:users,email',
            'phone' => 'sometimes|required|unique:users,phone|digits_between:10,20',
            'password' =>  'required|min:6',
            'gender' => 'sometimes|max:20',
            'weight' => 'required',
            'height' => 'required',
            'birth_date' => 'required'
        ];
    }
}
