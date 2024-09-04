<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ReturnOperationRequest extends FormRequest
{
    public function authorize()
    {
        return true; // В Laravel 9 можна вказати логіку авторизації тут
    }

    public function rules()
    {
        return [
            'resellerId'        => 'required|integer|exists:sellers,id',
            'clientId'          => 'required|integer|exists:contractors,id',
            'creatorId'         => 'required|integer|exists:employees,id',
            'expertId'          => 'required|integer|exists:employees,id',
            'notificationType'  => 'required|integer|in:1,2',
            'complaintId'       => 'required|integer',
            'complaintNumber'   => 'required|string',
            'consumptionId'     => 'required|integer',
            'consumptionNumber' => 'required|string',
            'agreementNumber'   => 'required|string',
            'date'              => 'required|date',
            'differences'       => 'nullable|array',
            'differences.from'  => 'nullable|integer',
            'differences.to'    => 'nullable|integer',
        ];
    }

    public function messages()
    {
        return [
            'resellerId.required' => 'The reseller ID is required.',
            'clientId.required' => 'The client ID is required.',
        ];
    }
}
