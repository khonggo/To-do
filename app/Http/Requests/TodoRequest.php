<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TodoRequest extends FormRequest
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
        return [
            'todo'=>'required|between:2,50'
        ];
    }

    public function messages(){
        return [
            'todo.required'=>'todo cannot be empty',
            'todo.between'=>'todo must be 2 - 50 word'
        ];
    }
}
