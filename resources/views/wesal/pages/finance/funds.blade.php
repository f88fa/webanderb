<div class="content-card">
    <div class="page-header" style="margin-bottom: 2rem;">
        <h1 class="page-title">
            <i class="fas fa-mosque" style="color: var(--primary-color); margin-left: 0.5rem;"></i>
            أصناف الأموال (الأوقاف)
        </h1>
        <p class="page-subtitle">إدارة أصناف الأموال: غير مقيد، مقيد، وقف</p>
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

    @if(isset($editingFund) && $editingFund)
    <!-- تعديل صنف مال -->
    <div style="background: rgba(33,150,243,0.15); padding: 1.5rem; border-radius: 12px; margin-bottom: 2rem; border: 2px solid #2196f3;">
        <h3 style="color: var(--text-primary); margin-bottom: 1rem;"><i class="fas fa-edit" style="margin-left: 0.5rem;"></i> تعديل صنف مال: {{ $editingFund->name_ar }}</h3>
        <form method="POST" action="{{ route('wesal.finance.funds.update', $editingFund) }}" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 1rem; align-items: end;">
            @csrf
            @method('PUT')
            <div><label class="form-label">الكود *</label><input type="text" name="code" class="form-control" required value="{{ old('code', $editingFund->code) }}" placeholder="مثال: WAQF"></div>
            <div><label class="form-label">الاسم بالعربي *</label><input type="text" name="name_ar" class="form-control" required value="{{ old('name_ar', $editingFund->name_ar) }}" placeholder="مثال: أوقاف"></div>
            <div><label class="form-label">نوع القيد *</label>
                <select name="restriction_type" class="form-control" required>
                    <option value="unrestricted" {{ old('restriction_type', $editingFund->restriction_type) == 'unrestricted' ? 'selected' : '' }}>غير مقيد</option>
                    <option value="restricted" {{ old('restriction_type', $editingFund->restriction_type) == 'restricted' ? 'selected' : '' }}>مقيد</option>
                    <option value="endowment" {{ old('restriction_type', $editingFund->restriction_type) == 'endowment' ? 'selected' : '' }}>وقف</option>
                </select>
            </div>
            <div><label class="form-label">الحالة</label>
                <select name="status" class="form-control">
                    <option value="active" {{ old('status', $editingFund->status) == 'active' ? 'selected' : '' }}>نشط</option>
                    <option value="inactive" {{ old('status', $editingFund->status) == 'inactive' ? 'selected' : '' }}>غير نشط</option>
                </select>
            </div>
            <div style="display: flex; gap: 0.5rem;">
                <button type="submit" style="padding: 0.6rem 1.5rem; background: #2196f3; color: white; border: none; border-radius: 8px; cursor: pointer; font-weight: 600;"><i class="fas fa-save"></i> حفظ</button>
                <a href="{{ route('wesal.finance.funds.index') }}" style="padding: 0.6rem 1.5rem; background: rgba(255,255,255,0.2); color: var(--text-primary); border: 1px solid var(--border-color); border-radius: 8px; text-decoration: none; font-weight: 600;">إلغاء</a>
            </div>
        </form>
    </div>
    @endif

    <!-- إضافة صنف مال -->
    <div style="background: rgba(255,255,255,0.05); padding: 1.5rem; border-radius: 12px; margin-bottom: 2rem; border: 2px solid var(--border-color);">
        <h3 style="color: var(--text-primary); margin-bottom: 1rem;">{{ isset($editingFund) && $editingFund ? 'إضافة صنف آخر' : 'إضافة صنف مال (وقف / مقيد / غير مقيد)' }}</h3>
        <form method="POST" action="{{ route('wesal.finance.funds.store') }}" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 1rem; align-items: end;">
            @csrf
            <div><label class="form-label">الكود *</label><input type="text" name="code" class="form-control" required value="{{ old('code') }}" placeholder="مثال: WAQF"></div>
            <div><label class="form-label">الاسم بالعربي *</label><input type="text" name="name_ar" class="form-control" required value="{{ old('name_ar') }}" placeholder="مثال: أوقاف"></div>
            <div><label class="form-label">نوع القيد *</label>
                <select name="restriction_type" class="form-control" required>
                    <option value="unrestricted" {{ old('restriction_type') == 'unrestricted' ? 'selected' : '' }}>غير مقيد</option>
                    <option value="restricted" {{ old('restriction_type') == 'restricted' ? 'selected' : '' }}>مقيد</option>
                    <option value="endowment" {{ old('restriction_type') == 'endowment' ? 'selected' : '' }}>وقف</option>
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

    <!-- جدول أصناف الأموال -->
    <div style="background: rgba(255,255,255,0.05); padding: 1.5rem; border-radius: 12px; border: 2px solid var(--border-color);">
        <h3 style="color: var(--text-primary); margin-bottom: 1rem;">قائمة أصناف الأموال</h3>
        @if($funds->count() > 0)
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
                        @foreach($funds as $f)
                        <tr style="border-bottom: 1px solid var(--border-color);">
                            <td style="padding: 0.75rem; text-align: center; color: var(--text-primary);"><strong>{{ $f->code }}</strong></td>
                            <td style="padding: 0.75rem; text-align: center; color: var(--text-primary);">{{ $f->name_ar }}</td>
                            <td style="padding: 0.75rem; text-align: center; color: var(--text-primary);">{{ $f->restriction_type_name_ar }}</td>
                            <td style="padding: 0.75rem; text-align: center;"><span style="color: {{ $f->status === 'active' ? '#4caf50' : '#999' }};">{{ $f->status === 'active' ? 'نشط' : 'غير نشط' }}</span></td>
                            <td style="padding: 0.75rem; text-align: center;">
                                <a href="{{ route('wesal.finance.funds.edit', $f) }}" style="display: inline-block; padding: 0.4rem 0.8rem; background: #2196f3; color: white; border-radius: 4px; text-decoration: none; font-size: 0.85rem; margin-left: 0.25rem;" title="تعديل"><i class="fas fa-edit"></i></a>
                                <form method="POST" action="{{ route('wesal.finance.funds.destroy', $f) }}" style="display: inline;" onsubmit="return confirm('حذف هذا الصنف؟');">
                                    @csrf @method('DELETE')
                                    <button type="submit" style="padding: 0.4rem 0.8rem; background: #f44336; color: white; border: none; border-radius: 4px; cursor: pointer; font-size: 0.85rem;" title="حذف"><i class="fas fa-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <p style="text-align: center; color: var(--text-secondary); padding: 2rem;">لا توجد أصناف أموال. يمكنك تشغيل Seeder: php artisan db:seed --class=FundsSeeder</p>
        @endif
    </div>
</div>
