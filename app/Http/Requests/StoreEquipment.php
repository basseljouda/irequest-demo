<?php

namespace App\Http\Requests;
use Illuminate\Foundation\Http\FormRequest;



class StoreEquipment extends FormRequest
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
         //$item = $this->route('equipment');
        return [
            //'name' => 'required|unique:equipments,name,'.$this->name,
            //'name' => ['required', 'string', \Illuminate\Validation\Rule::unique('equipments')->ignore($this->id)]

            
        ];
    }

    public function messages()
    {
        return [
          
        ];
    }
}
