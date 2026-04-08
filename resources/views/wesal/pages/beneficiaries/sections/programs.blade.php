<div class="content-card">
    <div class="page-header">
        <h1 class="page-title">
            <i class="fas fa-project-diagram" style="color: var(--primary-color); margin-left: 0.5rem;"></i>
            البرامج والحملات
        </h1>
        <p class="page-subtitle">إدارة البرامج والحملات والمستفيدين المسجلين فيها</p>
    </div>

    @if(session('success'))
        <div class="alert alert-success">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
        </div>
    @endif

    <div style="background: rgba(255,255,255,0.05); padding: 1.5rem; border-radius: 12px; margin-bottom: 2rem; border: 2px solid var(--border-color);">
        <h3 style="color: var(--text-primary); margin-bottom: 1rem;">إضافة برنامج جديد</h3>
        <form method="POST" action="{{ route('wesal.beneficiaries.programs.store') }}" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 1rem; align-items: end; margin-bottom: 2rem;">
            @csrf
            <div>
                <label class="form-label">اسم البرنامج <span style="color: #ff8a80;">*</span></label>
                <input type="text" name="name_ar" class="form-control" required>
            </div>
            <div>
                <label class="form-label">تاريخ البداية</label>
                <input type="date" name="start_date" class="form-control">
            </div>
            <div>
                <label class="form-label">تاريخ النهاية</label>
                <input type="date" name="end_date" class="form-control">
            </div>
            <div style="grid-column: 1 / -1;">
                <label class="form-label">الوصف</label>
                <textarea name="description" class="form-control" rows="2"></textarea>
            </div>
            <div>
                <button type="submit" class="btn btn-primary"><i class="fas fa-plus"></i> إضافة</button>
            </div>
        </form>

        <h3 style="color: var(--text-primary); margin-bottom: 1rem;">البرامج</h3>
        @if(isset($programs) && $programs->count() > 0)
            <div class="table-container">
                <table style="direction: rtl;">
                    <thead>
                        <tr>
                            <th>اسم البرنامج</th>
                            <th>تاريخ البداية</th>
                            <th>تاريخ النهاية</th>
                            <th style="text-align: center;">الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($programs as $p)
                        <tr>
                            <td>{{ $p->name_ar }}</td>
                            <td>{{ $p->start_date?->format('Y-m-d') ?? '-' }}</td>
                            <td>{{ $p->end_date?->format('Y-m-d') ?? '-' }}</td>
                            <td style="text-align: center;">
                                <form method="POST" action="{{ route('wesal.beneficiaries.programs.destroy', $p) }}" style="display: inline;" onsubmit="return confirm('حذف البرنامج؟');">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-danger" style="padding: 0.4rem 0.8rem; font-size: 0.85rem;"><i class="fas fa-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <p style="text-align: center; color: var(--text-secondary); padding: 2rem;">لا توجد برامج. أضف برنامجاً من النموذج أعلاه.</p>
        @endif
    </div>
</div>
