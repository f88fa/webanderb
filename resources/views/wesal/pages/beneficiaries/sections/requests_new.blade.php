<div class="content-card">
    <div class="page-header" style="display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 1rem;">
        <div>
            <h1 class="page-title">
                <i class="fas fa-plus-circle" style="color: var(--primary-color); margin-left: 0.5rem;"></i>
                طلبات جديدة
            </h1>
            <p class="page-subtitle">الطلبات المقدمة حديثاً</p>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
        </div>
    @endif

    <div style="background: rgba(255,255,255,0.05); padding: 1.5rem; border-radius: 12px; margin-bottom: 2rem; border: 2px solid var(--border-color);">
        <h3 style="color: var(--text-primary); margin-bottom: 1rem;">تقديم طلب جديد</h3>
        <form method="POST" action="{{ route('wesal.beneficiaries.requests.store') }}" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 1rem; align-items: end; margin-bottom: 2rem;">
            @csrf
            <div>
                <label class="form-label">المستفيد <span style="color: #ff8a80;">*</span></label>
                <select name="beneficiary_id" class="form-control" required>
                    <option value="">-- اختر --</option>
                    @foreach($beneficiaries ?? [] as $b)
                        <option value="{{ $b->id }}" {{ old('beneficiary_id') == $b->id ? 'selected' : '' }}>{{ $b->beneficiary_no }} - {{ $b->name_ar }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="form-label">نوع الطلب</label>
                <input type="text" name="request_type" class="form-control" value="{{ old('request_type') }}" placeholder="مساعدة مالية، عينية، طبية...">
            </div>
            <div style="grid-column: 1 / -1;">
                <label class="form-label">الوصف</label>
                <textarea name="description" class="form-control" rows="2">{{ old('description') }}</textarea>
            </div>
            <div>
                <label class="form-label">ملاحظات</label>
                <textarea name="notes" class="form-control" rows="1">{{ old('notes') }}</textarea>
            </div>
            <div>
                <button type="submit" class="btn btn-primary"><i class="fas fa-paper-plane"></i> تقديم</button>
            </div>
        </form>

        <h3 style="color: var(--text-primary); margin-bottom: 1rem;">قائمة الطلبات الجديدة</h3>
        @if(isset($requests) && $requests->count() > 0)
            <div class="table-container">
                <table style="direction: rtl;">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>المستفيد</th>
                            <th>نوع الطلب</th>
                            <th>تاريخ التقديم</th>
                            <th style="text-align: center;">الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($requests as $r)
                        <tr>
                            <td>{{ $r->id }}</td>
                            <td>{{ $r->beneficiary?->name_ar }} ({{ $r->beneficiary?->beneficiary_no }})</td>
                            <td>{{ $r->request_type ?? '-' }}</td>
                            <td>{{ $r->submitted_at?->format('Y-m-d H:i') ?? '-' }}</td>
                            <td style="text-align: center;">
                                <form method="POST" action="{{ route('wesal.beneficiaries.requests.study', $r) }}" style="display: inline;">
                                    @csrf
                                    <button type="submit" class="btn btn-primary" style="padding: 0.4rem 0.8rem; font-size: 0.85rem;"><i class="fas fa-search"></i> نقل للدراسة</button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <p style="text-align: center; color: var(--text-secondary); padding: 2rem;">لا توجد طلبات جديدة.</p>
        @endif
    </div>
</div>
