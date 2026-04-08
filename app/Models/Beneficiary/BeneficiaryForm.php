<?php

namespace App\Models\Beneficiary;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Http\Request;

class BeneficiaryForm extends Model
{
    protected $table = 'ben_beneficiary_forms';

    protected $fillable = ['name_ar', 'slug', 'order'];

    protected $casts = ['order' => 'integer'];

    public function fields(): HasMany
    {
        return $this->hasMany(BeneficiaryFormField::class, 'beneficiary_form_id')->orderBy('sort_order');
    }

    /** مستفيدون مرتبطون بهذا النموذج (يمنع حذف النموذج طالما وُجد منهم واحد على الأقل) */
    public function beneficiaries(): HasMany
    {
        return $this->hasMany(Beneficiary::class, 'beneficiary_form_id');
    }

    /** مفاتيح الحقول القياسية التي تُخزَّن في أعمدة الجدول */
    public const STANDARD_KEYS = [
        'name_ar', 'name_en', 'national_id', 'phone', 'email',
        'address', 'birth_date', 'gender', 'notes',
    ];

    /** قيمة مخزّنة فقط — للتوافق مع الإصدارات السابقة */
    private static function getStoredAddFormId(): ?int
    {
        $v = \DB::table('ben_beneficiary_form_settings')->where('key', 'add_form_id')->value('value');

        return $v ? (int) $v : null;
    }

    private static function getStoredPortalFormId(): ?int
    {
        $v = \DB::table('ben_beneficiary_form_settings')->where('key', 'portal_form_id')->value('value');

        return $v ? (int) $v : null;
    }

    /**
     * النموذج الواحد النشط: إضافة مستفيد من لوحة التحكم + تسجيل بوابة المستفيدين.
     * يُفضّل المفتاح القديم add_form_id ثم portal_form_id إن وُجد إعداد قديم منفصل.
     */
    public static function getUnifiedFormId(): ?int
    {
        return self::getStoredAddFormId() ?? self::getStoredPortalFormId();
    }

    public static function getAddFormId(): ?int
    {
        return self::getUnifiedFormId();
    }

    public static function getPortalFormId(): ?int
    {
        return self::getUnifiedFormId();
    }

    /** يحدّث نفس المعرف في كلا المفتاحين ليبقى التخزين متوافقاً مع أي كود قديم */
    public static function setUnifiedFormId(?int $id): void
    {
        self::setAddFormId($id);
        self::setPortalFormId($id);
    }

    public static function setAddFormId(?int $id): void
    {
        \DB::table('ben_beneficiary_form_settings')->updateOrInsert(
            ['key' => 'add_form_id'],
            ['value' => $id ? (string) $id : null]
        );
    }

    public static function setPortalFormId(?int $id): void
    {
        \DB::table('ben_beneficiary_form_settings')->updateOrInsert(
            ['key' => 'portal_form_id'],
            ['value' => $id ? (string) $id : null]
        );
    }

    /**
     * يدمج قيم القوائم (يوم/شهر/سنة هجرية) في حقل واحد بصيغة YYYY-MM-DD قبل التحقق من الطلب.
     */
    public function mergeHijriDatePartsIntoRequest(Request $request): void
    {
        foreach ($this->fields as $field) {
            if ($field->field_type !== 'date' || $field->dateCalendar() !== 'hijri') {
                continue;
            }
            $k = $field->field_key;
            $y = $request->input($k.'_hijri_y');
            $m = $request->input($k.'_hijri_m');
            $d = $request->input($k.'_hijri_d');
            if ($y !== null && $y !== '' && $m !== null && $m !== '' && $d !== null && $d !== '') {
                $request->merge([
                    $k => sprintf('%s-%02d-%02d', $y, (int) $m, (int) $d),
                ]);
            }
        }
    }
}
