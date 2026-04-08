<div class="content-card">
    <div class="page-header" style="margin-bottom: 2rem;">
        <h1 class="page-title">
            <i class="fas fa-building" style="color: var(--primary-color); margin-left: 0.5rem;"></i>
            مراكز التكلفة
        </h1>
        <p class="page-subtitle">إدارة مراكز التكلفة (برنامج، إداري، جمع تبرعات)</p>
    </div>

    @if(session('success'))
        <div class="alert alert-success" style="margin-bottom: 1.5rem;">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
        </div>
    @endif
    @if($errors->any())
        <div class="alert alert-error" style="margin-bottom: 1.5rem;">
            <i class="fas fa-exclamation-circle"></i>
            @foreach($errors->all() as $err) <p>{{ $err }}</p> @endforeach
        </div>
    @endif

    <!-- إضافة مركز تكلفة -->
    <div style="background: rgba(255,255,255,0.05); padding: 1.5rem; border-radius: 12px; margin-bottom: 2rem; border: 2px solid var(--border-color);">
        <h3 style="color: var(--text-primary); margin-bottom: 1rem;">إضافة مركز تكلفة</h3>
        <form method="POST" action="{{ route('wesal.finance.cost-centers.store') }}" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 1rem; align-items: end;">
            @csrf
            <div><label class="form-label">الكود *</label><input type="text" name="code" class="form-control" required value="{{ old('code') }}"></div>
            <div><label class="form-label">الاسم بالعربي *</label><input type="text" name="name_ar" class="form-control" required value="{{ old('name_ar') }}"></div>
            <div><label class="form-label">نوع المركز *</label>
                <select name="center_type" class="form-control" required>
                    <option value="program" {{ old('center_type') == 'program' ? 'selected' : '' }}>برنامج</option>
                    <option value="administrative" {{ old('center_type') == 'administrative' ? 'selected' : '' }}>إداري</option>
                    <option value="fundraising" {{ old('center_type') == 'fundraising' ? 'selected' : '' }}>جمع تبرعات</option>
                </select>
            </div>
            <div><label class="form-label">الحالة</label>
                <select name="status" class="form-control">
                    <option value="active" {{ old('status', 'active') == 'active' ? 'selected' : '' }}>نشط</option>
                    <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>غير نشط</option>
                </select>
            </div>
            <div><button type="submit" style="padding: 0.6rem 1.5rem; background: var(--primary-color); color: white; border: none; border-radius: 8px; cursor: pointer; font-weight: 600;"><i class="fas fa-plus"></i> إضافة</button></div>
        </form>
    </div>

    <!-- جدول مراكز التكلفة -->
    <div style="background: rgba(255,255,255,0.05); padding: 1.5rem; border-radius: 12px; border: 2px solid var(--border-color);">
        <h3 style="color: var(--text-primary); margin-bottom: 1rem;">قائمة مراكز التكلفة</h3>
        @if($costCenters->count() > 0)
            <div style="overflow-x: auto;">
                <table style="width: 100%; border-collapse: collapse; direction: rtl;">
                    <thead>
                        <tr style="background: rgba(0,0,0,0.2);">
                            <th style="padding: 0.75rem; text-align: center; color: var(--text-primary);">الكود</th>
                            <th style="padding: 0.75rem; text-align: center; color: var(--text-primary);">الاسم</th>
                            <th style="padding: 0.75rem; text-align: center; color: var(--text-primary);">النوع</th>
                            <th style="padding: 0.75rem; text-align: center; color: var(--text-primary);">الحالة</th>
                            <th style="padding: 0.75rem; text-align: center; color: var(--text-primary);">الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($costCenters as $cc)
                        <tr style="border-bottom: 1px solid var(--border-color);">
                            <td style="padding: 0.75rem; text-align: center; color: var(--text-primary);"><strong>{{ $cc->code }}</strong></td>
                            <td style="padding: 0.75rem; text-align: center; color: var(--text-primary);">{{ $cc->name_ar }}</td>
                            <td style="padding: 0.75rem; text-align: center; color: var(--text-primary);">{{ $cc->center_type_name_ar }}</td>
                            <td style="padding: 0.75rem; text-align: center;"><span style="color: {{ $cc->status === 'active' ? '#4caf50' : '#999' }};">{{ $cc->status === 'active' ? 'نشط' : 'غير نشط' }}</span></td>
                            <td style="padding: 0.75rem; text-align: center;">
                                <form method="POST" action="{{ route('wesal.finance.cost-centers.destroy', $cc) }}" style="display: inline;" onsubmit="return confirm('حذف هذا المركز؟');">
                                    @csrf @method('DELETE')
                                    <button type="submit" style="padding: 0.4rem 0.8rem; background: #f44336; color: white; border: none; border-radius: 4px; cursor: pointer; font-size: 0.85rem;"><i class="fas fa-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <p style="text-align: center; color: var(--text-secondary); padding: 2rem;">لا توجد مراكز تكلفة.</p>
        @endif
    </div>
</div>
