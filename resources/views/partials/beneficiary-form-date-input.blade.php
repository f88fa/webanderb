{{-- حقل تاريخ ديناميكي: ميلادي (type=date) أو هجري — $field = BeneficiaryFormField، $initial = قيمة محفوظة (نص Y-m-d) عند التعديل --}}
@php
    $initial = $initial ?? null;
    if ($initial instanceof \Carbon\Carbon || $initial instanceof \DateTimeInterface) {
        $initial = $initial->format('Y-m-d');
    }
    $initial = is_string($initial) ? $initial : '';
    $isHijri = $field->dateCalendar() === 'hijri';
    $hijriYearMax = hijri_calendar_current_year();
    $uid = preg_replace('/[^a-zA-Z0-9_-]/', '_', $field->field_key);
    $k = $field->field_key;
    $selY = (string) old($k.'_hijri_y', '');
    $selM = (string) old($k.'_hijri_m', '');
    $selD = (string) old($k.'_hijri_d', '');
    if ($selY === '' && $selM === '' && $selD === '') {
        $oldVal = old($k);
        if ($oldVal === null || $oldVal === '') {
            $oldVal = $initial;
        }
        if (is_string($oldVal) && preg_match('/^([12]\d{3})-(0?[1-9]|1[0-2])-(0?[1-9]|[12]\d|3[01])$/', $oldVal, $hm)) {
            $selY = $hm[1];
            $selM = (string) (int) $hm[2];
            $selD = (string) (int) $hm[3];
        }
    }
@endphp
@if($isHijri)
    <div class="bf-hijri-picker" style="display: flex; flex-wrap: wrap; align-items: center; gap: 0.65rem 1rem;">
        <div style="display: flex; flex-wrap: wrap; align-items: center; gap: 0.4rem;">
            <label for="bf-hijri-d-{{ $uid }}" class="form-label" style="margin: 0; font-weight: 600; font-size: 0.9rem;">اليوم</label>
            <select id="bf-hijri-d-{{ $uid }}" name="{{ $k }}_hijri_d" class="form-control bf-hijri-d" style="width: auto; min-width: 4.25rem; padding: 0.45rem 0.5rem;"
                @if($field->is_required) required @endif>
                <option value="" @selected($selD === '')>{{ $field->is_required ? '— اختر —' : '—' }}</option>
                @for($d = 1; $d <= 30; $d++)
                    <option value="{{ $d }}" @selected((string) $selD === (string) $d)>{{ $d }}</option>
                @endfor
            </select>
        </div>
        <div style="display: flex; flex-wrap: wrap; align-items: center; gap: 0.4rem;">
            <label for="bf-hijri-m-{{ $uid }}" class="form-label" style="margin: 0; font-weight: 600; font-size: 0.9rem;">الشهر</label>
            <select id="bf-hijri-m-{{ $uid }}" name="{{ $k }}_hijri_m" class="form-control bf-hijri-m" style="width: auto; min-width: 4.25rem; padding: 0.45rem 0.5rem;"
                @if($field->is_required) required @endif>
                <option value="" @selected($selM === '')>{{ $field->is_required ? '— اختر —' : '—' }}</option>
                @for($m = 1; $m <= 12; $m++)
                    <option value="{{ $m }}" @selected((string) $selM === (string) $m)>{{ $m }}</option>
                @endfor
            </select>
        </div>
        <div style="display: flex; flex-wrap: wrap; align-items: center; gap: 0.4rem;">
            <label for="bf-hijri-y-{{ $uid }}" class="form-label" style="margin: 0; font-weight: 600; font-size: 0.9rem;">السنة الهجرية</label>
            <select id="bf-hijri-y-{{ $uid }}" name="{{ $k }}_hijri_y" class="form-control bf-hijri-y" style="width: auto; min-width: 5.5rem; padding: 0.45rem 0.5rem;"
                @if($field->is_required) required @endif>
                <option value="" @selected($selY === '')>{{ $field->is_required ? '— اختر —' : '—' }}</option>
                @for($y = $hijriYearMax; $y >= 1350; $y--)
                    <option value="{{ $y }}" @selected((string) $selY === (string) $y)>{{ $y }}</option>
                @endfor
            </select>
        </div>
    </div>
@else
    @php
        $gregVal = old($field->field_key);
        if ($gregVal === null || $gregVal === '') {
            $gregVal = $initial;
        }
    @endphp
    <input type="date" name="{{ $field->field_key }}" class="form-control" value="{{ $gregVal }}" @if($field->is_required) required @endif>
    <small style="color: #666; font-size: 0.85rem; display: block; margin-top: 0.35rem;">التقويم <strong>الميلادي</strong></small>
@endif
