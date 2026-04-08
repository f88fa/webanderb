<div class="content-card">
    <div class="page-header">
        <h1 class="page-title">
            <i class="fas fa-file-alt" style="color: var(--primary-color); margin-left: 0.5rem;"></i>
            المستندات
        </h1>
        <p class="page-subtitle">مستندات المستفيدين</p>
    </div>

    @if(session('success'))
        <div class="alert alert-success">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
        </div>
    @endif

    <div style="background: rgba(255,255,255,0.05); padding: 1.5rem; border-radius: 12px; margin-bottom: 2rem; border: 2px solid var(--border-color);">
        <h3 style="color: var(--text-primary); margin-bottom: 1rem;">إضافة مستند</h3>
        <form method="POST" action="{{ route('wesal.beneficiaries.documents.store') }}" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 1rem; align-items: end; margin-bottom: 2rem;">
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
                <label class="form-label">نوع المستند</label>
                <input type="text" name="document_type" class="form-control" placeholder="هوية، شهادة، إلخ">
            </div>
            <div>
                <label class="form-label">تاريخ المستند</label>
                <input type="date" name="document_date" class="form-control" value="{{ date('Y-m-d') }}">
            </div>
            <div style="grid-column: 1 / -1;">
                <label class="form-label">ملاحظات</label>
                <textarea name="notes" class="form-control" rows="1"></textarea>
            </div>
            <div>
                <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> حفظ</button>
            </div>
        </form>

        <h3 style="color: var(--text-primary); margin-bottom: 1rem;">قائمة المستندات</h3>
        @if(isset($documents) && $documents->count() > 0)
            <div class="table-container">
                <table style="direction: rtl;">
                    <thead>
                        <tr>
                            <th>المستفيد</th>
                            <th>نوع المستند</th>
                            <th>التاريخ</th>
                            <th style="text-align: center;">الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($documents as $doc)
                        <tr>
                            <td>{{ $doc->beneficiary?->name_ar }}</td>
                            <td>{{ $doc->document_type ?? '-' }}</td>
                            <td>{{ $doc->document_date?->format('Y-m-d') ?? '-' }}</td>
                            <td style="text-align: center;">
                                <form method="POST" action="{{ route('wesal.beneficiaries.documents.destroy', $doc) }}" style="display: inline;" onsubmit="return confirm('حذف المستند؟');">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-danger" style="padding: 0.4rem 0.8rem; font-size: 0.85rem;"><i class="fas fa-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            {{ $documents->links() }}
        @else
            <p style="text-align: center; color: var(--text-secondary); padding: 2rem;">لا توجد مستندات.</p>
        @endif
    </div>
</div>
