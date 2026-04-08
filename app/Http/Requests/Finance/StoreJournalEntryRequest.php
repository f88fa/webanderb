<?php

namespace App\Http\Requests\Finance;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

class StoreJournalEntryRequest extends FormRequest
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
        $rules = [
            'entry_date' => ['required', 'date'],
            'description' => ['nullable', 'string'],
            'entry_type' => ['required', 'in:manual,receipt,payment,donation,grant,adjusting,opening,closing'],
            'notes' => ['nullable', 'string'],
            'payment_method' => ['nullable', 'string', 'in:transfer,cheque,cash'],
            'transfer_reference' => ['nullable', 'string', 'max:255'],
            'transfer_bank_name' => ['nullable', 'string', 'max:255'],
            'cheque_no' => ['nullable', 'string', 'max:100'],
            'cheque_bank_name' => ['nullable', 'string', 'max:255'],
            'lines' => ['required', 'array', 'min:2'],
            'lines.*.account_id' => ['required', 'exists:chart_accounts,id'],
            'lines.*.cost_center_id' => ['nullable', 'exists:cost_centers,id'],
            'lines.*.debit' => ['required_without:lines.*.credit', 'numeric', 'min:0'],
            'lines.*.credit' => ['required_without:lines.*.debit', 'numeric', 'min:0'],
            'lines.*.description' => ['nullable', 'string'],
            'lines.*.reference' => ['nullable', 'string', 'max:100'],
        ];
        if (in_array($this->input('entry_type'), ['receipt', 'payment'], true)) {
            $rules['payment_method'] = ['required', 'string', 'in:transfer,cheque,cash'];
            $rules['cash_account_id'] = ['nullable', 'exists:chart_accounts,id'];
            if ($this->input('payment_method') === 'cheque') {
                $rules['cheque_no'] = ['required', 'string', 'max:100'];
                $rules['cheque_bank_name'] = ['required', 'string', 'max:255'];
            }
        }
        return $rules;
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $lines = $this->input('lines', []);
            $totalDebit = 0;
            $totalCredit = 0;

            foreach ($lines as $index => $line) {
                $debit = (float) ($line['debit'] ?? 0);
                $credit = (float) ($line['credit'] ?? 0);

                if ($debit > 0 && $credit > 0) {
                    $validator->errors()->add("lines.{$index}", "السطر {$index}: لا يمكن أن يكون لديه مدين ودائن في نفس الوقت");
                }

                if ($debit == 0 && $credit == 0) {
                    $validator->errors()->add("lines.{$index}", "السطر {$index}: يجب أن يكون لديه مدين أو دائن");
                }

                $totalDebit += $debit;
                $totalCredit += $credit;
            }

            $difference = abs($totalDebit - $totalCredit);
            if ($difference >= 0.01) {
                $validator->errors()->add('lines', "القيد غير متوازن. الفرق: " . number_format($difference, 2));
            }
        });
    }

    /**
     * عند فشل التحقق لسند صرف من طلب صرف، إعادة التوجيه لنموذج سند الصرف مع payment_request_id
     */
    protected function failedValidation(Validator $validator): void
    {
        if ($this->entry_type === 'payment' && $this->filled('payment_request_id')) {
            throw new HttpResponseException(
                redirect()->route('wesal.finance.payment-voucher.create', ['payment_request_id' => $this->payment_request_id])
                    ->withErrors($validator)->withInput()
            );
        }
        parent::failedValidation($validator);
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'lines.required' => 'يجب إضافة سطرين على الأقل',
            'lines.min' => 'يجب إضافة سطرين على الأقل',
            'entry_date.required' => 'تاريخ القيد مطلوب',
            'entry_type.required' => 'نوع القيد مطلوب',
            'payment_method.required' => 'طريقة الاستلام/الدفع مطلوبة',
            'payment_method.in' => 'طريقة الاستلام/الدفع غير صالحة',
            'cheque_no.required' => 'رقم الشيك مطلوب عند اختيار الدفع/الاستلام بشيك',
            'cheque_bank_name.required' => 'البنك المصدر للشيك مطلوب',
        ];
    }
}
