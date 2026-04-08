<?php

namespace App\Http\Requests\Finance;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreChartAccountRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $accountId = $this->route('chartAccount')?->id;
        
        // إذا كان هناك parent_id، الكود سيتم توليده تلقائياً
        $codeRules = $this->parent_id 
            ? ['nullable', 'string', 'max:50']
            : [
                'required', 
                'string', 
                'max:50', 
                Rule::unique('chart_accounts', 'code')->ignore($accountId)
            ];
        
        return [
            'code' => $codeRules,
            'name_ar' => ['required', 'string', 'max:255'],
            'name_en' => ['nullable', 'string', 'max:255'],
            'parent_id' => ['required', 'exists:chart_accounts,id'],
            'type' => ['nullable', 'in:asset,liability,equity,revenue,expense'], // سيتم وراثته من الأب
            'nature' => ['nullable', 'in:debit,credit'], // سيتم وراثته من الأب
            'description' => ['nullable', 'string'],
            'status' => ['nullable', 'in:active,inactive'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'code.unique' => 'كود الحساب موجود مسبقاً',
            'parent_id.exists' => 'الحساب الأب غير موجود',
            'type.in' => 'نوع الحساب غير صحيح',
            'nature.in' => 'طبيعة الحساب غير صحيحة',
        ];
    }
}
