<div class="content-card">
    <div class="page-header" style="display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 1rem;">
        <div>
            <h1 class="page-title">
                <i class="fas fa-list" style="color: var(--primary-color); margin-left: 0.5rem;"></i>
                قائمة الموظفين
            </h1>
            <p class="page-subtitle">عرض وإدارة بيانات الموظفين</p>
        </div>
        @can('hr.employees.create')
        <a href="{{ route('wesal.hr.show', ['section' => 'employees', 'sub' => 'create']) }}" class="btn btn-primary">
            <i class="fas fa-user-plus"></i>
            <span>إضافة موظف</span>
        </a>
        @endcan
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
        <h3 style="color: var(--text-primary); margin-bottom: 1rem;">قائمة الموظفين</h3>
        @if(isset($employees) && $employees->count() > 0)
            <div class="table-container">
                <table style="direction: rtl;">
                    <thead>
                        <tr>
                            <th>الرقم</th>
                            <th>الاسم</th>
                            <th>القسم</th>
                            <th>المسمى</th>
                            <th>الحالة</th>
                            <th style="text-align: center;">الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($employees as $e)
                        <tr>
                            <td><strong>{{ $e->employee_no }}</strong></td>
                            <td>{{ $e->name_ar }} @if($e->user)<span style="font-size: 0.75rem; color: var(--primary-color);" title="مرتبط بحساب">🔗</span>@endif</td>
                            <td>{{ $e->department?->name_ar ?? '-' }}</td>
                            <td>{{ $e->job_title ?? '-' }}</td>
                            <td><span style="color: {{ $e->status === 'active' ? '#4caf50' : '#999' }};">{{ $e->status === 'active' ? 'نشط' : ($e->status === 'left' ? 'منتهي' : 'موقوف') }}</span></td>
                            <td style="text-align: center;">
                                @can('hr.employees.edit')
                                <a href="{{ route('wesal.hr.employees.edit', $e) }}" class="btn btn-secondary" style="padding: 0.4rem 0.8rem; font-size: 0.85rem; margin-left: 0.35rem;"><i class="fas fa-edit"></i></a>
                                @endcan
                                @can('hr.employees.delete')
                                <form method="POST" action="{{ route('wesal.hr.employees.destroy', $e) }}" style="display: inline;" onsubmit="return confirm('حذف الموظف؟');">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-danger" style="padding: 0.4rem 0.8rem; font-size: 0.85rem;"><i class="fas fa-trash"></i></button>
                                </form>
                                @endcan
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <p style="text-align: center; color: var(--text-secondary); padding: 2rem;">لا يوجد موظفون. @can('hr.employees.create')<a href="{{ route('wesal.hr.show', ['section' => 'employees', 'sub' => 'create']) }}" style="color: var(--primary-color);">إضافة موظف</a>@endcan</p>
        @endif
    </div>
</div>
