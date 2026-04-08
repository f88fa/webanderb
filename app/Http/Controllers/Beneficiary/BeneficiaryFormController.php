<?php

namespace App\Http\Controllers\Beneficiary;

use App\Http\Controllers\Controller;
use App\Models\Beneficiary\BeneficiaryForm;
use App\Models\Beneficiary\BeneficiaryFormField;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class BeneficiaryFormController extends Controller
{
    public function index()
    {
        $data = [
            'page' => 'beneficiaries',
            'settings' => \App\Models\SiteSetting::getAllAsArray(),
            'formType' => 'ben-forms',
            'benSection' => 'forms',
            'benSub' => null,
            'beneficiaryForms' => BeneficiaryForm::withCount(['fields', 'beneficiaries'])->orderBy('order')->orderBy('name_ar')->get(),
            'activeFormId' => BeneficiaryForm::getUnifiedFormId(),
        ];

        return view('wesal.index', $data);
    }

    public function create()
    {
        $data = [
            'page' => 'beneficiaries',
            'settings' => \App\Models\SiteSetting::getAllAsArray(),
            'formType' => 'ben-forms-create',
            'benSection' => 'forms',
            'benSub' => 'create',
            'standardKeys' => BeneficiaryForm::STANDARD_KEYS,
            'fieldTypes' => BeneficiaryFormField::TYPES,
        ];

        return view('wesal.index', $data);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name_ar' => 'required|string|max:255',
            'slug' => 'nullable|string|max:80|unique:ben_beneficiary_forms,slug',
        ]);
        $slug = $request->filled('slug') ? $request->slug : Str::slug($validated['name_ar']);
        if (BeneficiaryForm::where('slug', $slug)->exists()) {
            $slug = $slug.'-'.time();
        }
        $form = BeneficiaryForm::create([
            'name_ar' => $validated['name_ar'],
            'slug' => $slug,
            'order' => BeneficiaryForm::max('order') + 1,
        ]);

        return redirect()->route('wesal.beneficiaries.forms.edit', $form)
            ->with('success', 'تم إنشاء النموذج. أضف الحقول الآن.');
    }

    public function edit(BeneficiaryForm $form)
    {
        $form->load(['fields' => fn ($q) => $q->orderBy('sort_order')->with('dependsOnField')]);
        $data = [
            'page' => 'beneficiaries',
            'settings' => \App\Models\SiteSetting::getAllAsArray(),
            'formType' => 'ben-forms-edit',
            'benSection' => 'forms',
            'benSub' => 'edit',
            'editForm' => $form,
            'formBeneficiariesCount' => $form->beneficiaries()->count(),
            'standardKeys' => BeneficiaryForm::STANDARD_KEYS,
            'fieldTypes' => BeneficiaryFormField::TYPES,
        ];

        return view('wesal.index', $data);
    }

    public function update(Request $request, BeneficiaryForm $form)
    {
        $validated = $request->validate([
            'name_ar' => 'required|string|max:255',
            'slug' => 'required|string|max:80|unique:ben_beneficiary_forms,slug,'.$form->id,
        ]);
        $form->update($validated);

        return back()->with('success', 'تم تحديث النموذج.');
    }

    public function destroy(BeneficiaryForm $form)
    {
        if ($form->id === BeneficiaryForm::getUnifiedFormId()) {
            return back()->with('error', 'لا يمكن حذف النموذج المستخدم في الإعدادات. غيّر النموذج المعيّن أولاً.');
        }
        if ($form->beneficiaries()->exists()) {
            return back()->with('error', 'لا يمكن حذف النموذج لوجود مستفيدين مرتبطين به. يمكنك إضافة حقول جديدة من «تعديل الحقول» ليظهر للمستفيدين السابقين عند تحديث بياناتهم وتعبئة الحقول الناقصة.');
        }
        $form->delete();

        return redirect()->route('wesal.beneficiaries.forms.index')->with('success', 'تم حذف النموذج.');
    }

    public function storeField(Request $request, BeneficiaryForm $form)
    {
        $allTypes = array_merge(array_keys(BeneficiaryFormField::TYPES), array_keys(BeneficiaryFormField::TYPES_LEGACY));
        $validated = $request->validate([
            'field_key' => 'required|string|max:80',
            'label_ar' => 'required|string|max:255',
            'help_text' => 'nullable|string|max:500',
            'field_type' => 'required|in:'.implode(',', array_unique($allTypes)),
            'is_required' => 'nullable|boolean',
            'options' => 'nullable',
            'file_accept' => 'nullable|string|max:255',
            'file_max_mb' => 'nullable|integer|min:1|max:50',
            'sort_order' => 'nullable|integer',
            'depends_on_field_id' => 'nullable|exists:ben_beneficiary_form_fields,id',
            'depends_on_value' => 'nullable|string|max:255',
            'date_calendar' => 'nullable|in:gregorian,hijri',
        ]);
        if ($form->fields()->where('field_key', $validated['field_key'])->exists()) {
            return back()->with('error', 'مفتاح الحقل موجود مسبقاً في هذا النموذج.');
        }
        $dependsOnId = $validated['depends_on_field_id'] ?? null;
        if ($dependsOnId) {
            $depField = $form->fields()->find($dependsOnId);
            if (! $depField) {
                return back()->with('error', 'الحقل المرتبط يجب أن يكون من نفس النموذج.');
            }
            if (! in_array($depField->field_type, ['select', 'radio'], true)) {
                return back()->with('error', 'السؤال الشرطي مربوط بحقل «خيار واحد» فقط.');
            }
        }
        $options = $this->resolveFieldOptions($validated['field_type'] ?? '', $validated, $request);
        $form->fields()->create([
            'field_key' => $validated['field_key'],
            'label_ar' => $validated['label_ar'],
            'help_text' => $validated['help_text'] ?? null,
            'field_type' => $validated['field_type'],
            'is_required' => $request->boolean('is_required'),
            'options' => $options,
            'sort_order' => (int) ($validated['sort_order'] ?? $form->fields()->max('sort_order') + 1),
            'depends_on_field_id' => $dependsOnId,
            'depends_on_value' => $validated['depends_on_value'] ?? null,
        ]);

        return back()->with('success', 'تمت إضافة الحقل. سيظهر للمستفيدين السابقين عند تعديل بياناتهم ليمكنهم تعبئة الحقول الجديدة.');
    }

    private function resolveFieldOptions(string $fieldType, array $validated, Request $request)
    {
        if ($fieldType === 'file') {
            return [
                'accept' => $validated['file_accept'] ?? 'image/*,.pdf,.doc,.docx',
                'max_mb' => (int) ($validated['file_max_mb'] ?? 5),
            ];
        }
        if ($fieldType === 'date') {
            $cal = $request->input('date_calendar', 'gregorian');

            return [
                'calendar' => in_array($cal, ['hijri', 'gregorian'], true) ? $cal : 'gregorian',
            ];
        }
        if (in_array($fieldType, ['select', 'radio', 'multiselect'], true)) {
            $raw = $request->input('options');
            if (is_array($raw)) {
                return array_values(array_filter(array_map('trim', $raw)));
            }
            if (is_string($raw) && $raw !== '') {
                $opts = array_map('trim', explode("\n", $raw));

                return array_values(array_filter($opts));
            }
        }

        return null;
    }

    public function updateField(Request $request, BeneficiaryFormField $field)
    {
        $allTypes = array_merge(array_keys(BeneficiaryFormField::TYPES), array_keys(BeneficiaryFormField::TYPES_LEGACY));
        $validated = $request->validate([
            'label_ar' => 'required|string|max:255',
            'help_text' => 'nullable|string|max:500',
            'field_type' => 'required|in:'.implode(',', array_unique($allTypes)),
            'is_required' => 'nullable|boolean',
            'options' => 'nullable',
            'file_accept' => 'nullable|string|max:255',
            'file_max_mb' => 'nullable|integer|min:1|max:50',
            'sort_order' => 'nullable|integer',
            'depends_on_field_id' => 'nullable|exists:ben_beneficiary_form_fields,id',
            'depends_on_value' => 'nullable|string|max:255',
        ]);
        $dependsOnId = $validated['depends_on_field_id'] ?? null;
        if ($dependsOnId && $dependsOnId == $field->id) {
            return back()->with('error', 'لا يمكن ربط الحقل بنفسه.');
        }
        if ($dependsOnId && $field->beneficiaryForm) {
            $depField = $field->beneficiaryForm->fields()->find($dependsOnId);
            if (! $depField) {
                return back()->with('error', 'الحقل المرتبط يجب أن يكون من نفس النموذج.');
            }
            if (! in_array($depField->field_type, ['select', 'radio'], true)) {
                return back()->with('error', 'السؤال الشرطي مربوط بحقل «خيار واحد» فقط.');
            }
        }
        $options = $this->resolveFieldOptions($validated['field_type'] ?? '', $validated, $request);
        $field->update([
            'label_ar' => $validated['label_ar'],
            'help_text' => $validated['help_text'] ?? null,
            'field_type' => $validated['field_type'],
            'is_required' => $request->boolean('is_required'),
            'options' => $options,
            'sort_order' => (int) ($validated['sort_order'] ?? $field->sort_order),
            'depends_on_field_id' => $dependsOnId,
            'depends_on_value' => $validated['depends_on_value'] ?? null,
        ]);

        return back()->with('success', 'تم تحديث الحقل.');
    }

    public function destroyField(BeneficiaryFormField $field)
    {
        $field->delete();

        return back()->with('success', 'تم حذف الحقل.');
    }

    public function updateFormSettings(Request $request)
    {
        $raw = $request->input('active_form_id');
        if ($raw === null || $raw === '') {
            BeneficiaryForm::setUnifiedFormId(null);

            return back()->with('success', 'تم حفظ النموذج النشط (لوحة التحكم وبوابة المستفيدين).');
        }
        $validated = $request->validate([
            'active_form_id' => 'required|exists:ben_beneficiary_forms,id',
        ]);
        BeneficiaryForm::setUnifiedFormId((int) $validated['active_form_id']);

        return back()->with('success', 'تم حفظ النموذج النشط (لوحة التحكم وبوابة المستفيدين).');
    }
}
