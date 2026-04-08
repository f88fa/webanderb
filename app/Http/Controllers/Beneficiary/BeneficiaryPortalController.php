<?php

namespace App\Http\Controllers\Beneficiary;

use App\Http\Controllers\Controller;
use App\Models\Beneficiary\BeneficiaryForm;
use App\Models\Beneficiary\RegistrationRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class BeneficiaryPortalController extends Controller
{
    public function index()
    {
        if (Auth::check() && Auth::user()->isBeneficiary()) {
            return redirect()->route('beneficiary-portal.dashboard');
        }

        return view('beneficiary-portal.index');
    }

    public function showLogin()
    {
        if (Auth::check() && Auth::user()->isBeneficiary()) {
            return redirect()->route('beneficiary-portal.dashboard');
        }

        return view('beneficiary-portal.login');
    }

    public function showRegister()
    {
        if (Auth::check() && Auth::user()->isBeneficiary()) {
            return redirect()->route('beneficiary-portal.dashboard');
        }
        $portalFormId = BeneficiaryForm::getPortalFormId();
        $portalForm = $portalFormId ? BeneficiaryForm::with(['fields' => fn ($q) => $q->orderBy('sort_order')->with('dependsOnField')])->find($portalFormId) : null;

        return view('beneficiary-portal.register', compact('portalForm'));
    }

    public function register(Request $request)
    {
        $formId = $request->input('beneficiary_form_id');
        $form = $formId ? BeneficiaryForm::with('fields')->find($formId) : null;
        if ($form) {
            $form->mergeHijriDatePartsIntoRequest($request);
        }

        $rules = [
            'email' => 'required|email|unique:users,email',
            'password' => ['required', 'confirmed', Password::min(8)],
            'beneficiary_form_id' => 'nullable|exists:ben_beneficiary_forms,id',
        ];
        $standardKeys = BeneficiaryForm::STANDARD_KEYS;

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
                        // max:255 مع numeric يعني حد أقصى للقيمة 255 وليس طول النص — يمنع الهوية/الأرقام الطويلة
                        $rules[$field->field_key] = $rule.'|numeric';
                    } elseif ($field->field_type === 'date') {
                        if ($field->dateCalendar() === 'hijri') {
                            // مصفوفة وليس سلسلة بـ | لأن نمط regex يحتوي على | وإلا يُفسَّر كفاصل قواعد Laravel
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
            $rules['address'] = 'nullable|string';
            $rules['birth_date'] = 'nullable|date';
            $rules['gender'] = 'nullable|in:male,female';
            $rules['notes'] = 'nullable|string';
        }

        $validated = $request->validate($rules, array_merge([
            'email.unique' => 'البريد الإلكتروني مسجل مسبقاً. يمكنك تسجيل الدخول أو استعادة كلمة المرور.',
        ], $validationMessages));

        $nameForUser = isset($validated['name_ar']) ? trim((string) $validated['name_ar']) : '';
        if ($nameForUser === '' && $form && $form->fields->isNotEmpty()) {
            foreach ($form->fields as $field) {
                if (! in_array($field->field_type, ['text', 'textarea'], true)) {
                    continue;
                }
                $raw = $validated[$field->field_key] ?? null;
                if (! is_string($raw)) {
                    continue;
                }
                $candidate = trim($raw);
                if ($candidate === '' || filter_var($candidate, FILTER_VALIDATE_EMAIL)) {
                    continue;
                }
                if (preg_match('/^\d{9,}$/', $candidate)) {
                    continue;
                }
                if (preg_match('/\p{L}/u', $candidate)) {
                    $nameForUser = $candidate;
                    break;
                }
            }
        }
        if ($nameForUser === '') {
            $nameForUser = 'مستفيد';
        }

        $user = User::create([
            'name' => $nameForUser,
            'email' => $request->email,
            'password' => $request->password,
        ]);

        $reqData = [
            'user_id' => $user->id,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'status' => 'pending',
        ];
        if ($form) {
            $reqData['beneficiary_form_id'] = $form->id;
        }
        foreach ($standardKeys as $key) {
            if (array_key_exists($key, $validated)) {
                $reqData[$key] = $validated[$key];
            }
        }
        $formData = [];
        $fileFieldKeys = $form ? $form->fields->where('field_type', 'file')->pluck('field_key')->all() : [];
        foreach ($validated as $key => $value) {
            if (in_array($key, $standardKeys, true) || in_array($key, ['email', 'password', 'password_confirmation', 'beneficiary_form_id'], true) || in_array($key, $fileFieldKeys, true)) {
                continue;
            }
            if ($value !== null && $value !== '' && ! $value instanceof \Illuminate\Http\UploadedFile) {
                $formData[$key] = $value;
            }
        }
        // رفع الملفات وتخزين المسارات في form_data
        if ($form && $form->fields->isNotEmpty()) {
            foreach ($form->fields as $field) {
                if ($field->field_type === 'file' && $request->hasFile($field->field_key)) {
                    $file = $request->file($field->field_key);
                    $path = $file->store('beneficiary_uploads/'.date('Y/m'), 'public');
                    $formData[$field->field_key] = 'storage/'.$path;
                }
            }
        }
        $reqData['form_data'] = $formData ?: null;
        if (empty($reqData['name_ar'])) {
            $reqData['name_ar'] = $nameForUser;
        }

        RegistrationRequest::create($reqData);

        return redirect()->route('beneficiary-portal.login')
            ->with('success', 'تم تقديم طلبك بنجاح. يمكنك تسجيل الدخول لاحقاً لمتابعة حالة الطلب بعد مراجعة الإدارة.');
    }

    public function dashboard(Request $request)
    {
        $user = Auth::user();
        if (! $user->isBeneficiary()) {
            return redirect()->route('wesal');
        }

        $beneficiary = $user->beneficiary;
        $registrationRequest = $user->registrationRequests()->where('status', 'pending')->latest()->first();

        if ($registrationRequest) {
            return view('beneficiary-portal.pending', compact('registrationRequest'));
        }

        if ($beneficiary && $beneficiary->status === 'active') {
            $beneficiary->load([
                'serviceRecords' => fn ($q) => $q->with('serviceType')->orderByDesc('service_date')->orderByDesc('id'),
                'assessments' => fn ($q) => $q->orderByDesc('assessment_date')->orderByDesc('id'),
                'requests' => fn ($q) => $q->orderByDesc('submitted_at')->orderByDesc('created_at'),
                'paymentRequests' => fn ($q) => $q->orderByDesc('request_date')->orderByDesc('id'),
            ]);

            return view('beneficiary-portal.dashboard', compact('beneficiary'));
        }

        $rejectedRequest = $user->registrationRequests()->where('status', 'rejected')->latest()->first();
        if ($rejectedRequest) {
            return view('beneficiary-portal.rejected', compact('rejectedRequest'));
        }

        return view('beneficiary-portal.pending', ['registrationRequest' => null]);
    }
}
