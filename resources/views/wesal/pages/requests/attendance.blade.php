@php
    $attendanceMode = $settings['attendance_mode'] ?? 'anywhere';
    $attendanceInfo = \App\Services\AttendanceRestrictionService::getInfoMessage();
@endphp
<div class="content-card">
    <div class="page-header">
        <h1 class="page-title">
            <i class="fas fa-fingerprint" style="color: var(--primary-color); margin-left: 0.5rem;"></i>
            تسجيل حضور وانصراف
        </h1>
        <p class="page-subtitle">تسجيل حضورك وانصرافك — خاص بك فقط</p>
    </div>

    {{-- رسائل success و error تظهر من wesal/index.blade.php في main-content لتجنب التكرار (خصوصاً على الموبايل) --}}
    @if(session('info'))
        <div class="alert" style="background: rgba(255,152,0,0.2); border: 1px solid rgba(255,152,0,0.5); color: #fff;">{{ session('info') }}</div>
    @endif

    @if($attendanceInfo)
        <div class="alert" style="background: rgba(95, 179, 142, 0.15); border: 1px solid rgba(95, 179, 142, 0.4); color: var(--text-primary);">
            <i class="fas fa-info-circle"></i> {{ $attendanceInfo }}
        </div>
    @endif
    @if(!$employee)
        <div class="alert" style="background: rgba(255,152,0,0.2); border: 1px solid rgba(255,152,0,0.5); color: #fff;">
            <i class="fas fa-info-circle"></i> يجب ربط حسابك بموظف في قسم الموارد البشرية لتمكين تسجيل الحضور. تواصل مع المدير.
        </div>
    @else
        <div style="background: rgba(255,255,255,0.05); padding: 1.5rem; border-radius: 12px; margin-bottom: 2rem; border: 2px solid var(--border-color);">
            <h3 style="color: var(--text-primary); margin-bottom: 1rem;">تسجيل سريع — {{ $employee->name_ar }}</h3>
            <form method="POST" action="{{ route('wesal.requests.attendance.check-in') }}" class="attendance-form" data-need-location="{{ $attendanceMode === 'location_restricted' ? '1' : '0' }}" style="display: inline-flex; gap: 1rem; align-items: center; flex-wrap: wrap;">
                @csrf
                <input type="hidden" name="date" value="{{ now()->toDateString() }}">
                @if($attendanceMode === 'location_restricted')
                    <input type="hidden" name="latitude" value="">
                    <input type="hidden" name="longitude" value="">
                @endif
                <button type="submit" class="btn btn-primary"><i class="fas fa-sign-in-alt"></i> حضور</button>
            </form>
            <form method="POST" action="{{ route('wesal.requests.attendance.check-out') }}" class="attendance-form" data-need-location="{{ $attendanceMode === 'location_restricted' ? '1' : '0' }}" style="display: inline-flex; gap: 1rem; align-items: center; margin-right: 1rem;">
                @csrf
                <input type="hidden" name="date" value="{{ now()->toDateString() }}">
                @if($attendanceMode === 'location_restricted')
                    <input type="hidden" name="latitude" value="">
                    <input type="hidden" name="longitude" value="">
                @endif
                <button type="submit" class="btn" style="background: linear-gradient(135deg, #ff9800 0%, #f57c00 100%); color: white;"><i class="fas fa-sign-out-alt"></i> انصراف</button>
            </form>
        </div>

        @php
            $today = $todayRecord ?? null;
        @endphp
        @if($today && $today->check_in)
            <div class="attendance-status-card" style="background: linear-gradient(135deg, rgba(95, 179, 142, 0.25) 0%, rgba(76, 175, 80, 0.15) 100%); border: 2px solid rgba(95, 179, 142, 0.5); border-radius: 12px; padding: 1.5rem; margin-bottom: 2rem;">
                <h3 style="color: var(--text-primary); margin: 0 0 1rem 0; font-size: 1.1rem;"><i class="fas fa-clock" style="color: var(--primary-color); margin-left: 0.5rem;"></i> حالة اليوم — {{ now()->translatedFormat('Y-m-d (l)') }}</h3>
                <div style="display: flex; flex-wrap: wrap; gap: 2rem; align-items: flex-start;">
                    <div>
                        <span style="color: rgba(255,255,255,0.7); font-size: 0.9rem;">وقت الحضور</span>
                        <div style="font-size: 1.4rem; font-weight: 700; color: var(--primary-color);">{{ \Carbon\Carbon::parse($today->date->format('Y-m-d') . ' ' . $today->check_in)->format('H:i') }}</div>
                    </div>
                    @if($today->check_out)
                        <div>
                            <span style="color: rgba(255,255,255,0.7); font-size: 0.9rem;">وقت الانصراف</span>
                            <div style="font-size: 1.4rem; font-weight: 700; color: #ff9800;">{{ \Carbon\Carbon::parse($today->date->format('Y-m-d') . ' ' . $today->check_out)->format('H:i') }}</div>
                        </div>
                        @php
                            $start = \Carbon\Carbon::parse($today->date->format('Y-m-d') . ' ' . $today->check_in);
                            $end = \Carbon\Carbon::parse($today->date->format('Y-m-d') . ' ' . $today->check_out);
                            $mins = $start->diffInMinutes($end);
                            $durH = floor($mins / 60);
                            $durM = $mins % 60;
                            $durStr = ($durH > 0 ? $durH . ' ساعة ' : '') . ($durM > 0 ? $durM . ' دقيقة' : '');
                            $durStr = trim($durStr) ?: '0 دقيقة';
                        @endphp
                        <div>
                            <span style="color: rgba(255,255,255,0.7); font-size: 0.9rem;">مدة الحضور</span>
                            <div style="font-size: 1.4rem; font-weight: 700; color: #fff;">{{ $durStr }}</div>
                        </div>
                    @else
                        <div>
                            <span style="color: rgba(255,255,255,0.7); font-size: 0.9rem;">الوقت المنقضي</span>
                            <div id="attendance-elapsed" style="font-size: 1.4rem; font-weight: 700; color: #fff;" data-check-in="{{ $today->date->format('Y-m-d') }}T{{ $today->check_in }}+03:00">--</div>
                        </div>
                    @endif
                </div>
            </div>
        @endif

        <h3 style="color: var(--text-primary); margin-bottom: 1rem;">سجل الحضور والانصراف ليوم {{ isset($date) ? \Carbon\Carbon::parse($date)->translatedFormat('Y-m-d (l)') : now()->translatedFormat('Y-m-d (l)') }}</h3>
        <form method="GET" action="{{ route('wesal.requests.show', ['section' => 'attendance']) }}" style="display: flex; gap: 1rem; align-items: center; flex-wrap: wrap; margin-bottom: 1rem;">
            <label class="form-label" style="margin: 0;">التاريخ</label>
            <input type="date" name="date" value="{{ $date ?? now()->toDateString() }}" class="form-control" style="max-width: 180px;">
            <button type="submit" class="btn" style="background: rgba(255,255,255,0.1); color: var(--text-primary); border: 1px solid var(--border-color);">عرض</button>
        </form>
        @if(isset($myRecords) && $myRecords->count() > 0)
            <div class="table-container">
                <table style="direction: rtl;">
                    <thead><tr><th>التاريخ</th><th style="text-align: center;">وقت الحضور</th><th style="text-align: center;">وقت الانصراف</th><th style="text-align: center;">مدة الحضور</th></tr></thead>
                    <tbody>
                        @foreach($myRecords as $r)
                        @php
                            $ci = $r->check_in ? \Carbon\Carbon::parse($r->date->format('Y-m-d') . ' ' . $r->check_in) : null;
                            $co = $r->check_out ? \Carbon\Carbon::parse($r->date->format('Y-m-d') . ' ' . $r->check_out) : null;
                            $dur = ($ci && $co) ? $ci->diffInMinutes($co) : null;
                            $durFormatted = $dur !== null ? (floor($dur/60) > 0 ? floor($dur/60) . ' س ' : '') . ($dur%60) . ' د' : '-';
                        @endphp
                        <tr>
                            <td>{{ $r->date->format('Y-m-d') }}</td>
                            <td style="text-align: center;">{{ $r->check_in ? $ci->format('H:i') : '-' }}</td>
                            <td style="text-align: center;">{{ $r->check_out ? $co->format('H:i') : '-' }}</td>
                            <td style="text-align: center;">{{ $durFormatted }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <p style="text-align: center; color: var(--text-secondary); padding: 2rem;">لا توجد تسجيلات لهذا اليوم.</p>
        @endif
    @endif
</div>

<script>
(function() {
    var el = document.getElementById('attendance-elapsed');
    if (!el) return;
    var checkIn = el.getAttribute('data-check-in');
    if (!checkIn) return;
    function update() {
        var start = new Date(checkIn);
        var now = new Date();
        var diff = Math.floor((now - start) / 1000);
        if (diff < 0) { el.textContent = '--'; return; }
        var h = Math.floor(diff / 3600);
        var m = Math.floor((diff % 3600) / 60);
        var s = diff % 60;
        el.textContent = (h > 0 ? h + ' ساعة ' : '') + (m > 0 ? m + ' دقيقة ' : '') + s + ' ثانية';
    }
    update();
    setInterval(update, 1000);
})();
</script>
@if(($attendanceMode ?? '') === 'location_restricted')
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
                    alert('تعذّر الحصول على الموقع. تأكد من تفعيل الموقع للموقع أو السماح للمتصفح بالوصول إليه.');
                },
                { enableHighAccuracy: true, timeout: 10000, maximumAge: 0 }
            );
        });
    });
})();
</script>
@endif
