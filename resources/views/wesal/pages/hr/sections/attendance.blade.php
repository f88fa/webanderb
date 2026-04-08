@php
    $attendanceMode = $settings['attendance_mode'] ?? 'anywhere';
    $attendanceInfo = \App\Services\AttendanceRestrictionService::getInfoMessage();
@endphp
<div class="content-card">
    <div class="page-header">
        <h1 class="page-title">
            <i class="fas fa-fingerprint" style="color: var(--primary-color); margin-left: 0.5rem;"></i>
            تسجيل الحضور والانصراف
        </h1>
        <p class="page-subtitle">تسجيل الحضور والانصراف ليوم {{ now()->translatedFormat('Y-m-d (l)') }}</p>
    </div>

    @if(session('success'))
        <div class="alert alert-success"><i class="fas fa-check-circle"></i> {{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-error"><i class="fas fa-exclamation-circle"></i> {{ session('error') }}</div>
    @endif
    @if(session('info'))
        <div class="alert" style="background: rgba(255,152,0,0.2); border: 1px solid rgba(255,152,0,0.5); color: #fff;">{{ session('info') }}</div>
    @endif
    @if($attendanceInfo)
        <div class="alert" style="background: rgba(95, 179, 142, 0.15); border: 1px solid rgba(95, 179, 142, 0.4); color: var(--text-primary);">
            <i class="fas fa-info-circle"></i> {{ $attendanceInfo }}
        </div>
    @endif

    <div style="background: rgba(255,255,255,0.05); padding: 1.5rem; border-radius: 12px; margin-bottom: 2rem; border: 2px solid var(--border-color);">
        <h3 style="color: var(--text-primary); margin-bottom: 1rem;">تسجيل سريع</h3>
        <form method="POST" action="{{ route('wesal.hr.attendance.check-in') }}" class="attendance-form" data-need-location="{{ $attendanceMode === 'location_restricted' ? '1' : '0' }}" style="display: flex; flex-wrap: wrap; gap: 1rem; align-items: flex-end;">
            @csrf
            <input type="hidden" name="date" value="{{ now()->toDateString() }}">
            @if($attendanceMode === 'location_restricted')
                <input type="hidden" name="latitude" value="">
                <input type="hidden" name="longitude" value="">
            @endif
            <div style="min-width: 220px;">
                <label class="form-label">الموظف</label>
                <select name="employee_id" class="form-control" required>
                    <option value="">-- اختر الموظف --</option>
                    @foreach($employees ?? [] as $emp)
                        <option value="{{ $emp->id }}">{{ $emp->name_ar }} ({{ $emp->employee_no }})</option>
                    @endforeach
                </select>
            </div>
            <div><button type="submit" class="btn btn-primary"><i class="fas fa-sign-in-alt"></i> حضور</button></div>
        </form>
        <form method="POST" action="{{ route('wesal.hr.attendance.check-out') }}" class="attendance-form" data-need-location="{{ $attendanceMode === 'location_restricted' ? '1' : '0' }}" style="display: flex; flex-wrap: wrap; gap: 1rem; align-items: flex-end; margin-top: 1rem;">
            @csrf
            <input type="hidden" name="date" value="{{ now()->toDateString() }}">
            @if($attendanceMode === 'location_restricted')
                <input type="hidden" name="latitude" value="">
                <input type="hidden" name="longitude" value="">
            @endif
            <div style="min-width: 220px;">
                <label class="form-label">الموظف</label>
                <select name="employee_id" class="form-control" required>
                    <option value="">-- اختر الموظف --</option>
                    @foreach($employees ?? [] as $emp)
                        <option value="{{ $emp->id }}">{{ $emp->name_ar }} ({{ $emp->employee_no }})</option>
                    @endforeach
                </select>
            </div>
            <div><button type="submit" class="btn" style="background: linear-gradient(135deg, #ff9800 0%, #f57c00 100%); color: white;"><i class="fas fa-sign-out-alt"></i> انصراف</button></div>
        </form>
    </div>

    <div style="background: rgba(255,255,255,0.05); padding: 1.5rem; border-radius: 12px; border: 2px solid var(--border-color);">
        <h3 style="color: var(--text-primary); margin-bottom: 1rem;">سجل اليوم</h3>
        @if(isset($todayRecords) && $todayRecords->count() > 0)
            <div class="table-container">
                <table style="direction: rtl;">
                    <thead><tr><th>الموظف</th><th style="text-align: center;">وقت الحضور</th><th style="text-align: center;">وقت الانصراف</th><th style="text-align: center;">مدة الحضور</th></tr></thead>
                    <tbody>
                        @foreach($todayRecords as $r)
                        @php
                            $ci = $r->check_in ? \Carbon\Carbon::parse($r->date->format('Y-m-d') . ' ' . $r->check_in) : null;
                            $co = $r->check_out ? \Carbon\Carbon::parse($r->date->format('Y-m-d') . ' ' . $r->check_out) : null;
                            $dur = ($ci && $co) ? $ci->diffInMinutes($co) : null;
                            $durFormatted = $dur !== null ? (floor($dur/60) > 0 ? floor($dur/60) . ' س ' : '') . ($dur%60) . ' د' : '-';
                        @endphp
                        <tr>
                            <td>{{ $r->employee->name_ar }}</td>
                            <td style="text-align: center;">{{ $r->check_in ? $ci->format('H:i') : '-' }}</td>
                            <td style="text-align: center;">{{ $r->check_out ? $co->format('H:i') : '-' }}</td>
                            <td style="text-align: center;">{{ $durFormatted }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <p style="text-align: center; color: var(--text-secondary); padding: 2rem;">لا توجد تسجيلات ليوم اليوم.</p>
        @endif
    </div>
</div>

@if($attendanceMode === 'location_restricted')
<script>
(function() {
    document.querySelectorAll('.attendance-form[data-need-location="1"]').forEach(function(form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            var f = e.target;
            if (!navigator.geolocation) {
                alert('المتصفح لا يدعم الموقع. يُسمح بالتسجيل فقط من الموقع المعتمد.');
                return;
            }
            navigator.geolocation.getCurrentPosition(
                function(pos) {
                    var lat = f.querySelector('input[name="latitude"]');
                    var lng = f.querySelector('input[name="longitude"]');
                    if (lat) lat.value = pos.coords.latitude;
                    if (lng) lng.value = pos.coords.longitude;
                    f.submit();
                },
                function() {
                    alert('تعذّر الحصول على الموقع. تأكد من تفعيل الموقع أو السماح للمتصفح بالوصول إليه.');
                },
                { enableHighAccuracy: true, timeout: 10000, maximumAge: 0 }
            );
        });
    });
})();
</script>
@endif
