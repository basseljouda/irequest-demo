<?php

namespace App\Http\Requests;

class StoreTicket extends CoreRequest
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
            'from_hospital' => 'required',
            'subject' => 'required',
            'due_date' => 'required',
            "items.*"  => "required",
            "requested_by"  => "required",
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
