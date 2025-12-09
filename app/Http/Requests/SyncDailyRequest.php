<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SyncDailyRequest extends FormRequest
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
            'records.*.company' => 'required|string|max:255',
            'records.*.indicator' => 'required|string|max:255',
            'records.*.target' => 'required|numeric',
            'records.*.actual' => 'required|numeric',
            'records.*.direction' => 'required|integer|in:-1,0,1',
        ];
    }
}
