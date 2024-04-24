<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class Order_request extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'first_name'=> 'required|max:100',
            'last_name'=>'required|max:100',
            'father_name'=>'required|max:100',
            'birthday'=>'required|date',
            'gender'=>'required|in:0,1',
            'phone'=>'required|max:15',
            'address'=>'required',
            'email'=>'required|email',
            'classification'=>'required|in:0,1',
            'class'=>'required',
            'year'=>'required|max:4'

        ];
    }
}
