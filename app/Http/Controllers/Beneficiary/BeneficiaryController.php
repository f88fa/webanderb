<?php

namespace App\Http\Controllers\Beneficiary;

use App\Http\Controllers\Controller;
use App\Models\Beneficiary\Beneficiary;
use App\Models\Beneficiary\BeneficiaryForm;
use Illuminate\Http\Request;

class BeneficiaryController extends Controller
{
    public function store(Request $request)
    {
        $formId = $request->input('beneficiary_form_id');
        $form = $formId ? BeneficiaryForm::with('fields')->find($formId) : null;
        if ($form) {
            $form->mergeHijriDatePartsIntoRequest($request);
        }

        $rules = [
            'beneficiary_no' => 'nullable|string|max:50|unique:ben_beneficiaries,beneficiary_no',
            'beneficiary_form_id' => 'nullable|exists:ben_beneficiary_forms,id',
        ];

        $standardKeys = BeneficiaryForm::STANDARD_KEYS;
        $formData = [];
        $validationMessages = [];

        if ($form && $form->fields->isNotEmpty()) {
            foreach ($form->fields as $field) {
                if ($field->field_type === 'file') {
                    $maxKb = ($field->file_max_mb ?? 5) * 1024;
                    $rules[$field->field_key] = ($field->is_required ? 'required' : 'nullable').'|file|max:'.$maxKb;
                } elseif ($field->field_type === 'multiselect') {
                    $rules[$field->field_key] = ($field->is_required ? 'required' : 'nullable').'|array';
                    $rules[$field->field_key.'.*'] = 'string|max:255';
                } else {
                    $rule = $field->is_required ? 'required' : 'nullable';
                    if ($field->field_type === 'email') {
                        $rules[$field->field_key] = $rule.'|email|max:255';
                    } elseif ($field->field_type === 'number') {
                        $rules[$field->field_key] = $rule.'|numeric';
                    } elseif ($field->field_type === 'date') {
                        if ($field->dateCalendar() === 'hijri') {
                            $rules[$field->field_key] = [
                                $rule,
                                'regex:/^[12]\d{3}-(0?[1-9]|1[0-2])-(0?[1-9]|[12]\d|3[01])$/',
                            ];
                            $validationMessages[$field->field_key.'.regex'] = 'صيغة التاريخ الهجري: سنة-شهر-يوم (مثال 1446-07-15).';
                        } else {
                            $rules[$field->field_key] = $rule.'|date';
                        }
                    } elseif ($field->field_type === 'textarea') {
                        $rules[$field->field_key] = $rule.'|string';
                    } else {
                        $rules[$field->field_key] = $rule.'|string|max:255';
                    }
                }
            }
        } else {
            $rules['name_ar'] = 'required|string|max:255';
            $rules['name_en'] = 'nullable|string|max:255';
            $rules['national_id'] = 'nullable|string|max:50';
            $rules['phone'] = 'nullable|string|max:30';
            $rules['email'] = 'nullable|email';
            $rules['address'] = 'nullable|string';
            $rules['birth_date'] = 'nullable|date';
            $rules['gender'] = 'nullable|in:male,female';
            $rules['notes'] = 'nullable|string';
        }

        $validated = $request->validate($rules, $validationMessages);

        $data = ['status' => 'active'];
        if ($form) {
            $data['beneficiary_form_id'] = $form->id;
        }

        foreach ($standardKeys as $key) {
            if (array_key_exists($key, $validated)) {
                $data[$key] = $validated[$key];
            }
        }

        $fileFieldKeys = $form ? $form->fields->where('field_type', 'file')->pluck('field_key')->all() : [];
        foreach ($validated as $key => $value) {
            if (in_array($key, $standardKeys, true) || in_array($key, ['beneficiary_no', 'beneficiary_form_id'], true) || in_array($key, $fileFieldKeys, true)) {
                continue;
            }
            if ($value !== null && $value !== '' && ! $value instanceof \Illuminate\Http\UploadedFile) {
                $formData[$key] = $value;
            }
        }
        if ($form && $form->fields->isNotEmpty()) {
            foreach ($form->fields as $field) {
                if ($field->field_type === 'file' && $request->hasFile($field->field_key)) {
                    $file = $request->file($field->field_key);
                    $path = $file->store('beneficiary_uploads/'.date('Y/m'), 'public');
                    $formData[$field->field_key] = 'storage/'.$path;
                }
            }
        }

        $data['form_data'] = $formData ?: null;
        $data['beneficiary_no'] = $request->filled('beneficiary_no')
            ? $request->beneficiary_no
            : self::generateBeneficiaryNo();

        // ضمان وجود name_ar حتى مع النماذج المخصصة (حقول ديناميكية)
        if (empty($data['name_ar'])) {
            // محاولة استخدام أول حقل نصي في النموذج كاسم للمستفيد
            if ($form && $form->fields->isNotEmpty()) {
                $nameField = $form->fields
                    ->whereIn('field_type', ['text'])
                    ->first(function ($field) {
                        // تفضيل الحقول القياسية إن وُجدت (name_ar مثلاً)
                        return $field->isStandardKey() || true;
                    });

                if ($nameField && ! empty($validated[$nameField->field_key] ?? null)) {
                    $data['name_ar'] = $validated[$nameField->field_key];
                }
            }

            // في حال لم نجد أي حقل مناسب، نضع اسماً افتراضياً يعتمد على رقم المستفيد
            if (empty($data['name_ar'])) {
                $data['name_ar'] = 'مستفيد '.($data['beneficiary_no'] ?? '');
            }
        }

        Beneficiary::create($data);

        return redirect()->route('wesal.beneficiaries.show', ['section' => 'list'])->with('success', 'تم إضافة المستفيد بنجاح.');
    }

    public function update(Request $request, Beneficiary $beneficiary)
    {
        $form = $beneficiary->beneficiary_form_id
            ? BeneficiaryForm::with('fields')->find($beneficiary->beneficiary_form_id)
            : null;
        if ($form) {
            $form->mergeHijriDatePartsIntoRequest($request);
        }

        $rules = [
            'beneficiary_no' => 'required|string|max:50|unique:ben_beneficiaries,beneficiary_no,'.$beneficiary->id,
            'status' => 'required|in:active,archived',
        ];
        $standardKeys = BeneficiaryForm::STANDARD_KEYS;
        $validationMessages = [];

        if ($form && $form->fields->isNotEmpty()) {
            foreach ($form->fields as $field) {
                if ($field->field_type === 'file') {
                    $maxKb = ($field->file_max_mb ?? 5) * 1024;
                    $existing = data_get($beneficiary->form_data, $field->field_key);
                    $needsFile = $field->is_required && empty($existing);
                    $rules[$field->field_key] = ($needsFile ? 'required' : 'nullable').'|file|max:'.$maxKb;
                } elseif ($field->field_type === 'multiselect') {
                    $rules[$field->field_key] = ($field->is_required ? 'required' : 'nullable').'|array';
                    $rules[$field->field_key.'.*'] = 'string|max:255';
                } else {
                    $rule = $field->is_required ? 'required' : 'nullable';
                    if ($field->field_type === 'email') {
                        $rules[$field->field_key] = $rule.'|email|max:255';
                    } elseif ($field->field_type === 'number') {
                        $rules[$field->field_key] = $rule.'|numeric';
                    } elseif ($field->field_type === 'date') {
                        if ($field->dateCalendar() === 'hijri') {
                            $rules[$field->field_key] = [
                                $rule,
                                'regex:/^[12]\d{3}-(0?[1-9]|1[0-2])-(0?[1-9]|[12]\d|3[01])$/',
                            ];
                            $validationMessages[$field->field_key.'.regex'] = 'صيغة التاريخ الهجري: سنة-شهر-يوم (مثال 1446-07-15).';
                        } else {
                            $rules[$field->field_key] = $rule.'|date';
                        }
                    } elseif ($field->field_type === 'textarea') {
                        $rules[$field->field_key] = $rule.'|string';
                    } else {
                        $rules[$field->field_key] = $rule.'|string|max:255';
                    }
                }
            }
        } else {
            $rules['name_ar'] = 'required|string|max:255';
            $rules['name_en'] = 'nullable|string|max:255';
            $rules['national_id'] = 'nullable|string|max:50';
            $rules['phone'] = 'nullable|string|max:30';
            $rules['email'] = 'nullable|email';
            $rules['address'] = 'nullable|string';
            $rules['birth_date'] = 'nullable|date';
            $rules['gender'] = 'nullable|in:male,female';
            $rules['notes'] = 'nullable|string';
        }

        $validated = $request->validate($rules, $validationMessages);

        if ($form && $form->fields->isNotEmpty()) {
            foreach ($form->fields as $field) {
                if ($field->field_type === 'multiselect' && ! array_key_exists($field->field_key, $validated)) {
                    $validated[$field->field_key] = [];
                }
            }

            $data = [
                'beneficiary_no' => $validated['beneficiary_no'],
                'status' => $validated['status'],
            ];

            foreach ($standardKeys as $key) {
                if (array_key_exists($key, $validated)) {
                    $data[$key] = $validated[$key];
                } else {
                    $data[$key] = $beneficiary->{$key};
                }
            }

            $fileFieldKeys = $form->fields->where('field_type', 'file')->pluck('field_key')->all();
            $formData = is_array($beneficiary->form_data) ? $beneficiary->form_data : [];

            foreach ($validated as $key => $value) {
                if (in_array($key, $standardKeys, true) || in_array($key, ['beneficiary_no', 'status'], true) || in_array($key, $fileFieldKeys, true)) {
                    continue;
                }
                if ($value instanceof \Illuminate\Http\UploadedFile) {
                    continue;
                }
                if (is_array($value)) {
                    $formData[$key] = $value;
                } elseif ($value === null || $value === '') {
                    unset($formData[$key]);
                } else {
                    $formData[$key] = $value;
                }
            }

            foreach ($form->fields as $field) {
                if ($field->field_type === 'file' && $request->hasFile($field->field_key)) {
                    $file = $request->file($field->field_key);
                    $path = $file->store('beneficiary_uploads/'.date('Y/m'), 'public');
                    $formData[$field->field_key] = 'storage/'.$path;
                }
            }

            if (empty($data['name_ar'])) {
                if ($form->fields->isNotEmpty()) {
                    $nameField = $form->fields->whereIn('field_type', ['text'])->first();
                    if ($nameField && ! empty($validated[$nameField->field_key] ?? null)) {
                        $data['name_ar'] = $validated[$nameField->field_key];
                    }
                }
                if (empty($data['name_ar'])) {
                    $data['name_ar'] = $beneficiary->name_ar ?: ('مستفيد '.($data['beneficiary_no'] ?? ''));
                }
            }

            $data['form_data'] = $formData ?: null;
            $beneficiary->update($data);

            return redirect()->route('wesal.beneficiaries.show', ['section' => 'list'])->with('success', 'تم تحديث بيانات المستفيد.');
        }

        $beneficiary->update($request->only([
            'beneficiary_no', 'name_ar', 'name_en', 'national_id', 'phone', 'email',
            'address', 'birth_date', 'gender', 'status', 'notes',
        ]));

        return redirect()->route('wesal.beneficiaries.show', ['section' => 'list'])->with('success', 'تم تحديث بيانات المستفيد.');
    }

    public function archive(Beneficiary $beneficiary)
    {
        $beneficiary->update(['status' => 'archived']);

        return redirect()->route('wesal.beneficiaries.show', ['section' => 'list'])->with('success', 'تم أرشفة المستفيد.');
    }

    public function unarchive(Beneficiary $beneficiary)
    {
        $beneficiary->update(['status' => 'active']);

        return redirect()->route('wesal.beneficiaries.show', ['section' => 'archive'])->with('success', 'تم إعادة تفعيل المستفيد.');
    }

    public function destroy(Beneficiary $beneficiary)
    {
        $beneficiary->delete();

        return redirect()->route('wesal.beneficiaries.show', ['section' => 'list'])->with('success', 'تم حذف المستفيد.');
    }

    public static function generateBeneficiaryNo(): string
    {
        $year = date('Y');
        $last = Beneficiary::whereRaw('beneficiary_no LIKE ?', ["BEN-{$year}-%"])->orderByDesc('id')->first();
        $num = $last ? (int) substr($last->beneficiary_no, -4) + 1 : 1;

        return sprintf('BEN-%s-%04d', $year, $num);
    }
}
