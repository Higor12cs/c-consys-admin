<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SyncMonthlyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'records' => 'required|array|min:1',
            'records.*.date' => 'required|date_format:Y-m-d H:i:s',
            'records.*.company' => 'required|integer',
            'records.*.year' => 'required|integer|min:2000|max:2100',
            'records.*.month' => 'required|integer|min:1|max:12',
            'records.*.indicator' => 'required|string|max:255',
            'records.*.target' => 'required|numeric',
            'records.*.actual' => 'required|numeric',
        ];
    }
}
