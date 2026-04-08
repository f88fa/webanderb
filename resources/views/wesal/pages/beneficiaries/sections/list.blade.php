<div class="content-card">
    <div class="page-header" style="display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 1rem;">
        <div>
            <h1 class="page-title">
                <i class="fas fa-list" style="color: var(--primary-color); margin-left: 0.5rem;"></i>
                قائمة المستفيدين
            </h1>
            <p class="page-subtitle">عرض وإدارة المستفيدين المسجلين</p>
        </div>
        <a href="{{ route('wesal.beneficiaries.show', ['section' => 'create']) }}" class="btn btn-primary">
            <i class="fas fa-user-plus"></i>
            <span>إضافة مستفيد</span>
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-error">
            <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
        </div>
    @endif

    <div style="background: rgba(255,255,255,0.05); padding: 1.5rem; border-radius: 12px; margin-bottom: 2rem; border: 2px solid var(--border-color);">
        <form method="GET" action="{{ route('wesal.beneficiaries.show', ['section' => 'list']) }}" role="search" style="display: flex; flex-wrap: wrap; align-items: center; gap: 0.65rem; margin-bottom: 1.25rem;">
            <label for="beneficiaries-search-q" class="form-label" style="margin: 0; flex: 1 1 100%; font-weight: 600; color: var(--text-primary);">بحث عن مستفيد</label>
            <input type="search" id="beneficiaries-search-q" name="q" class="form-control" value="{{ old('q', $beneficiariesListQuery ?? request('q')) }}" placeholder="رقم المستفيد، الاسم، الهوية، الجوال، البريد، العنوان…" autocomplete="off" style="flex: 1 1 220px; min-width: 180px; max-width: 100%;">
            <button type="submit" class="btn btn-primary" style="white-space: nowrap;"><i class="fas fa-search"></i> بحث</button>
            @if(!empty(trim((string) ($beneficiariesListQuery ?? request('q', '')))))
                <a href="{{ route('wesal.beneficiaries.show', ['section' => 'list']) }}" class="btn btn-secondary" style="white-space: nowrap;"><i class="fas fa-times"></i> مسح البحث</a>
            @endif
        </form>

        @if(isset($beneficiariesList) && $beneficiariesList->count() > 0)
            <div class="table-container">
                <table style="direction: rtl;">
                    <thead>
                        <tr>
                            <th>رقم المستفيد</th>
                            <th>الاسم</th>
                            <th>الهوية</th>
                            <th>الجوال</th>
                            <th>عدد الطلبات</th>
                            <th style="text-align: center;">الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($beneficiariesList as $b)
                        <tr>
                            <td><strong>{{ $b->beneficiary_no }}</strong></td>
                            <td>{{ $b->name_ar }}</td>
                            <td>{{ $b->national_id ?? '-' }}</td>
                            <td>{{ $b->phone ?? '-' }}</td>
                            <td>{{ $b->requests_count ?? 0 }}</td>
                            <td style="text-align: center;">
                                <a href="{{ route('wesal.beneficiaries.show', ['section' => 'profile', 'sub' => $b->id]) }}" class="btn btn-primary" style="padding: 0.4rem 0.8rem; font-size: 0.85rem; margin-left: 0.25rem;"><i class="fas fa-user-folder"></i> ملف المستفيد</a>
                                <a href="{{ route('wesal.beneficiaries.show', ['section' => 'edit', 'sub' => $b->id]) }}" class="btn btn-secondary" style="padding: 0.4rem 0.8rem; font-size: 0.85rem; margin-left: 0.25rem;"><i class="fas fa-edit"></i></a>
                                <form method="POST" action="{{ route('wesal.beneficiaries.beneficiaries.archive', $b) }}" style="display: inline;" onsubmit="return confirm('أرشفة المستفيد؟');">
                                    @csrf
                                    <button type="submit" class="btn btn-secondary" style="padding: 0.4rem 0.8rem; font-size: 0.85rem; margin-left: 0.25rem;"><i class="fas fa-archive"></i></button>
                                </form>
                                <form method="POST" action="{{ route('wesal.beneficiaries.beneficiaries.destroy', $b) }}" style="display: inline;" onsubmit="return confirm('حذف المستفيد نهائياً؟');">
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
            <p style="text-align: center; color: var(--text-secondary); padding: 2rem;">
                @if(!empty(trim((string) ($beneficiariesListQuery ?? request('q', '')))))
                    لا توجد نتائج مطابقة للبحث.
                    <a href="{{ route('wesal.beneficiaries.show', ['section' => 'list']) }}" style="color: var(--primary-color);">عرض الكل</a>
                @else
                    لا يوجد مستفيدون. <a href="{{ route('wesal.beneficiaries.show', ['section' => 'create']) }}" style="color: var(--primary-color);">إضافة مستفيد</a>
                @endif
            </p>
        @endif
    </div>
</div>
