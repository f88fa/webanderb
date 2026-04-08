<div class="content-card">
    <div class="page-header">
        <h1 class="page-title">
            <i class="fas fa-heartbeat" style="color: var(--primary-color); margin-left: 0.5rem;"></i>
            المتابعة الطبية
        </h1>
        <p class="page-subtitle">المتابعة الطبية للمستفيدين</p>
    </div>

    @if(session('success'))
        <div class="alert alert-success">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
        </div>
    @endif

    <div style="background: rgba(255,255,255,0.05); padding: 1.5rem; border-radius: 12px; margin-bottom: 2rem; border: 2px solid var(--border-color);">
        <h3 style="color: var(--text-primary); margin-bottom: 1rem;">إضافة سجل طبي</h3>
        <form method="POST" action="{{ route('wesal.beneficiaries.medical-records.store') }}" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 1rem; align-items: end; margin-bottom: 2rem;">
            @csrf
            <div>
                <label class="form-label">المستفيد <span style="color: #ff8a80;">*</span></label>
                <select name="beneficiary_id" class="form-control" required>
                    <option value="">-- اختر --</option>
                    @foreach($beneficiaries ?? [] as $b)
                        <option value="{{ $b->id }}">{{ $b->beneficiary_no }} - {{ $b->name_ar }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="form-label">تاريخ السجل <span style="color: #ff8a80;">*</span></label>
                <input type="date" name="record_date" class="form-control" value="{{ date('Y-m-d') }}" required>
            </div>
            <div>
                <label class="form-label">التشخيص</label>
                <input type="text" name="diagnosis" class="form-control">
            </div>
            <div style="grid-column: 1 / -1;">
                <label class="form-label">العلاج</label>
                <textarea name="treatment" class="form-control" rows="2"></textarea>
            </div>
            <div style="grid-column: 1 / -1;">
                <label class="form-label">ملاحظات</label>
                <textarea name="notes" class="form-control" rows="1"></textarea>
            </div>
            <div>
                <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> حفظ</button>
            </div>
        </form>

        <h3 style="color: var(--text-primary); margin-bottom: 1rem;">سجل المتابعة الطبية</h3>
        @if(isset($medicalRecords) && $medicalRecords->count() > 0)
            <div class="table-container">
                <table style="direction: rtl;">
                    <thead>
                        <tr>
                            <th>المستفيد</th>
                            <th>التاريخ</th>
                            <th>التشخيص</th>
                            <th>العلاج</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($medicalRecords as $mr)
                        <tr>
                            <td>{{ $mr->beneficiary?->name_ar }}</td>
                            <td>{{ $mr->record_date?->format('Y-m-d') }}</td>
                            <td>{{ $mr->diagnosis ?? '-' }}</td>
                            <td>{{ Str::limit($mr->treatment, 50) ?? '-' }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            {{ $medicalRecords->links() }}
        @else
            <p style="text-align: center; color: var(--text-secondary); padding: 2rem;">لا توجد سجلات طبية.</p>
        @endif
    </div>
</div>
