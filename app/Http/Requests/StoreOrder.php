<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Request;
use Illuminate\Validation\Rule;

class StoreOrder extends CoreRequest
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
            'hospital' => 'required',
            'patient_name' => 'required',
            'staff' => 'required',
            'cost_center' => 'required',
            "equipments.*"  => "required",
            "room"  => "required",
            "unit_floor"  => "required",
            "date_needed"  => "required",
            "contact_phone"  => "required",
        ];
    }

    public function messages()
    {
        return [
           // 'modality.required' => __('errors.fieldRequired'),
           // 'sub_modality.required' => __('errors.fieldRequired')
        ];
    }
}
