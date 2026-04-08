<div class="content-card" style="padding: 1.5rem;">
    <div class="page-header" style="margin-bottom: 1.5rem;">
        <h1 class="page-title">
            <i class="fas fa-file-invoice" style="color: var(--primary-color); margin-left: 0.5rem;"></i>
            إنشاء قيد يومي
        </h1>
        <p class="page-subtitle">إضافة قيد يومية جديد — اختر الحسابات من القائمة (المستوى الخامس فما فوق)</p>
    </div>

    <!-- أزرار الإجراءات العلوية -->
    <div style="display: flex; gap: 0.5rem; margin-bottom: 1rem; justify-content: flex-start;">
        <a href="{{ route('wesal.finance.journal-entries.index') }}" 
           style="display: inline-flex; align-items: center; gap: 0.25rem; padding: 0.5rem 1rem; background: rgba(255,255,255,0.1); color: var(--text-primary); border: 1px solid var(--border-color); border-radius: 8px; text-decoration: none; font-weight: 500; font-size: 0.85rem; transition: all 0.2s;">
            <i class="fas fa-arrow-right" style="font-size: 0.75rem;"></i>
            <span>العودة للقيود</span>
        </a>
        <button type="button" onclick="alert('ميزة التعديل قيد التطوير')" 
                style="display: inline-flex; align-items: center; gap: 0.25rem; padding: 0.5rem 1rem; background: rgba(255,255,255,0.08); color: var(--text-secondary); border: 1px solid var(--border-color); border-radius: 8px; cursor: pointer; font-weight: 500; font-size: 0.85rem;">
            <i class="fas fa-edit" style="font-size: 0.75rem;"></i>
            <span>تعديل</span>
        </button>
    </div>

    @if($errors->any())
        <div class="alert alert-error" style="margin-bottom: 1rem; background: rgba(244,67,54,0.15); color: #ff8a80; padding: 0.75rem 1rem; border-radius: 8px; border: 1px solid rgba(244,67,54,0.4); font-size: 0.85rem;">
            <i class="fas fa-exclamation-circle"></i>
            <ul style="margin: 0.25rem 0 0 0; padding-right: 1.25rem; font-size: 0.8rem;">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('wesal.finance.journal-entries.store') }}" id="journalEntryForm" enctype="multipart/form-data">
        @csrf
        
        <!-- معلومات القيد -->
        <div style="background: rgba(255, 255, 255, 0.05); padding: 1.5rem; border-radius: 12px; margin-bottom: 1.5rem; border: 2px solid var(--border-color);">
            <h3 style="color: var(--text-primary); margin: 0 0 1rem 0; font-size: 1rem; font-weight: 700;"><i class="fas fa-info-circle" style="color: var(--primary-color); margin-left: 0.5rem;"></i> بيانات القيد</h3>
            <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 1.25rem; direction: rtl;">
                <!-- العمود الأيمن -->
                <div>
                    <div class="form-group" style="margin-bottom: 0.75rem;">
                        <label class="form-label" style="display: block; margin-bottom: 0.25rem; color: var(--text-primary); font-weight: 600; font-size: 0.85rem;">
                            <i class="fas fa-calendar" style="color: var(--primary-color); margin-left: 0.35rem;"></i> تاريخ القيد: <span style="color: #ff8a80;">*</span>
                        </label>
                        <input type="date" name="entry_date" id="entry_date" 
                               value="{{ old('entry_date', date('Y-m-d')) }}" 
                               required
                               class="form-control journal-form-input"
                               onchange="updateHijriDate()"
                               min="{{ $fiscalYear ? $fiscalYear->start_date->format('Y-m-d') : '' }}"
                               max="{{ $fiscalYear ? $fiscalYear->end_date->format('Y-m-d') : '' }}">
                        <div id="hijri-date" style="margin-top: 0.25rem; color: var(--text-secondary); font-size: 0.75rem; font-style: italic;"></div>
                    </div>

                    <div class="form-group" style="margin-bottom: 0.75rem;">
                        <label class="form-label" style="display: block; margin-bottom: 0.25rem; color: var(--text-primary); font-weight: 600; font-size: 0.85rem;">
                            <i class="fas fa-align-right" style="color: var(--primary-color); margin-left: 0.35rem;"></i> بيان القيد: <span style="color: #ff8a80;">*</span>
                        </label>
                        <input type="text" name="description" id="description" 
                               value="{{ old('description') }}" 
                               required
                               class="form-control journal-form-input" 
                               placeholder="بيان القيد">
                    </div>

                    <div class="form-group">
                        <label class="form-label" style="display: block; margin-bottom: 0.25rem; color: var(--text-primary); font-weight: 600; font-size: 0.85rem;">
                            <i class="fas fa-paperclip" style="color: var(--primary-color); margin-left: 0.35rem;"></i> المرفقات
                        </label>
                        <button type="button" onclick="document.getElementById('attachments').click()" 
                                class="journal-btn-secondary" style="width: 100%; margin-bottom: 0.5rem;">
                            <i class="fas fa-upload"></i> إضافة مرفقات
                        </button>
                        <input type="file" id="attachments" name="attachments[]" multiple style="display: none;" onchange="displayAttachments(this)">
                        <div id="attachments-list" style="min-height: 56px; border: 2px dashed var(--border-color); border-radius: 8px; padding: 0.5rem; background: rgba(0,0,0,0.15);">
                            <p style="text-align: center; color: var(--text-secondary); margin: 0; font-size: 0.8rem;">لا توجد مرفقات</p>
                        </div>
                    </div>
                </div>

                <!-- العمود الأوسط -->
                <div>
                    <div class="form-group" style="margin-bottom: 0.75rem;">
                        <label class="form-label" style="display: block; margin-bottom: 0.25rem; color: var(--text-primary); font-weight: 600; font-size: 0.85rem;">
                            <i class="fas fa-hashtag" style="color: var(--primary-color); margin-left: 0.35rem;"></i> رقم القيد
                        </label>
                        <input type="text" value="{{ $nextEntryNo ?? 'سيتم توليده تلقائياً' }}" 
                               readonly class="form-control journal-form-input journal-form-readonly">
                    </div>

                    <div class="form-group" style="margin-bottom: 0.75rem;">
                        <label class="form-label" style="display: block; margin-bottom: 0.25rem; color: var(--text-primary); font-weight: 600; font-size: 0.85rem;">
                            <i class="fas fa-list" style="color: var(--primary-color); margin-left: 0.35rem;"></i> نوع القيد: <span style="color: #ff8a80;">*</span>
                        </label>
                        <select name="entry_type" id="entry_type" class="form-control journal-form-input" required>
                            <option value="manual" {{ old('entry_type', $entryType) == 'manual' ? 'selected' : '' }}>قيد يومية</option>
                            <option value="adjusting" {{ old('entry_type', $entryType) == 'adjusting' ? 'selected' : '' }}>قيد تسوية</option>
                            <option value="opening" {{ old('entry_type', $entryType) == 'opening' ? 'selected' : '' }}>قيد افتتاحي</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="form-label" style="display: block; margin-bottom: 0.25rem; color: var(--text-primary); font-weight: 600; font-size: 0.85rem;">
                            <i class="fas fa-calendar-alt" style="color: var(--primary-color); margin-left: 0.35rem;"></i> السنة المالية
                        </label>
                        <input type="text" value="{{ $fiscalYear ? $fiscalYear->year_name : '' }}" 
                               readonly class="form-control journal-form-input journal-form-readonly">
                        <input type="hidden" name="fiscal_year_id" value="{{ $fiscalYear->id ?? '' }}">
                        <small style="color: var(--text-secondary); font-size: 0.75rem; margin-top: 0.25rem; display: block;">السنة المالية المختارة أعلاه تُحدد تلقائياً من تاريخ القيد</small>
                    </div>
                </div>

                <div></div>
            </div>
        </div>

        <!-- قسم عمليات القيد -->
        <div style="background: rgba(255, 255, 255, 0.05); padding: 1.5rem; border-radius: 12px; margin-bottom: 1.5rem; border: 2px solid var(--border-color);">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.75rem; flex-wrap: wrap; gap: 0.5rem;">
                <h3 style="color: var(--text-primary); margin: 0; font-size: 1rem; font-weight: 700;">
                    <i class="fas fa-table" style="color: var(--primary-color); margin-left: 0.5rem;"></i> عمليات القيد
                </h3>
                <button type="button" onclick="addJournalLine()" class="journal-btn-primary">
                    <i class="fas fa-plus" style="font-size: 0.75rem;"></i> إضافة عملية
                </button>
            </div>
            <p style="color: var(--text-secondary); margin-bottom: 0.75rem; font-size: 0.8rem;">اضغط على «إضافة عملية» لإضافة سطر جديد ثم اختر الحساب والمبلغ.</p>

            <div style="overflow-x: auto; border-radius: 8px; border: 1px solid var(--border-color);">
                <table class="journal-lines-table">
                    <thead>
                        <tr>
                            <th>م</th>
                            <th>مدين</th>
                            <th>دائن</th>
                            <th>الحساب</th>
                            <th>البيان</th>
                            <th>مركز التكلفة</th>
                            <th>رقم المستند</th>
                        </tr>
                    </thead>
                    <tbody id="journal-lines-container"></tbody>
                </table>
            </div>

            <!-- الإجماليات -->
            <div class="journal-totals">
                <div class="journal-total-item">
                    <span class="journal-total-label">إجمالي المدين</span>
                    <span id="total-debit" class="journal-total-value">0.00</span>
                </div>
                <div class="journal-total-item">
                    <span class="journal-total-label">إجمالي الدائن</span>
                    <span id="total-credit" class="journal-total-value">0.00</span>
                </div>
                <div class="journal-total-item">
                    <span class="journal-total-label">تسوية</span>
                    <span id="balance-diff" class="journal-total-value">0.00</span>
                </div>
            </div>
        </div>

        <!-- أزرار الحفظ -->
        <div style="display: flex; gap: 1rem; justify-content: flex-start; margin-top: 1.5rem; padding-top: 1rem; border-top: 2px solid var(--border-color);">
            <button type="submit" class="journal-btn-submit">
                <i class="fas fa-save"></i> إضافة السجل
            </button>
            <a href="{{ route('wesal.finance.journal-entries.index') }}" class="journal-btn-cancel">
                <i class="fas fa-arrow-right"></i> إلغاء
            </a>
        </div>

        <input type="hidden" name="post_now" value="0">
    </form>
</div>

<style>
.journal-form-input {
    width: 100%; padding: 0.5rem 0.75rem; border: 1px solid var(--border-color); border-radius: 8px;
    font-size: 0.85rem; background: rgba(255,255,255,0.1); color: var(--text-primary);
}
.journal-form-input::placeholder { color: var(--text-secondary); opacity: 0.8; }
.journal-form-readonly { cursor: not-allowed; opacity: 0.9; }
.journal-btn-primary {
    display: inline-flex; align-items: center; gap: 0.35rem; padding: 0.5rem 1rem;
    background: var(--primary-color); color: white; border: none; border-radius: 8px;
    cursor: pointer; font-weight: 600; font-size: 0.85rem; transition: all 0.2s;
}
.journal-btn-primary:hover { background: var(--primary-dark); transform: translateY(-1px); }
.journal-btn-secondary {
    display: inline-flex; align-items: center; justify-content: center; gap: 0.35rem; padding: 0.5rem;
    background: rgba(255,255,255,0.1); color: var(--text-primary); border: 1px solid var(--border-color);
    border-radius: 8px; cursor: pointer; font-weight: 500; font-size: 0.85rem;
}
.journal-btn-secondary:hover { background: rgba(255,255,255,0.15); }
.journal-btn-submit {
    display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.75rem 2rem;
    background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%);
    color: white; border: none; border-radius: 8px; cursor: pointer; font-weight: 600; font-size: 1rem;
    box-shadow: 0 2px 8px rgba(0,0,0,0.2); transition: all 0.2s;
}
.journal-btn-submit:hover { transform: translateY(-2px); box-shadow: 0 4px 12px rgba(0,0,0,0.3); }
.journal-btn-cancel {
    display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.75rem 2rem;
    background: rgba(255,255,255,0.1); color: var(--text-primary); border: 2px solid var(--border-color);
    border-radius: 8px; text-decoration: none; font-weight: 600; font-size: 1rem; transition: all 0.2s;
}
.journal-btn-cancel:hover { background: rgba(255,255,255,0.15); color: var(--text-primary); }
.journal-lines-table {
    width: 100%; border-collapse: collapse; direction: rtl; font-size: 0.85rem;
}
.journal-lines-table thead tr { background: rgba(0,0,0,0.25); }
.journal-lines-table th {
    padding: 0.6rem 0.5rem; text-align: center; color: var(--text-primary); font-weight: 700;
    border-left: 1px solid var(--border-color); font-size: 0.8rem;
}
/* عمود الحساب: عرض كافٍ لقراءة النص وصندوق البحث */
.journal-lines-table th:nth-child(4),
.journal-lines-table td:nth-child(4) {
    min-width: 280px;
}
.journal-lines-table tbody tr { border-bottom: 1px solid var(--border-color); }
.journal-lines-table tbody tr:hover { background: rgba(255,255,255,0.03); }
.journal-lines-table .line-input {
    width: 100%; padding: 0.35rem 0.5rem; border: 1px solid var(--border-color); border-radius: 6px;
    font-size: 0.8rem; background: rgba(255,255,255,0.1); color: var(--text-primary);
}
.journal-totals {
    margin-top: 1rem; padding: 1rem; background: rgba(0,0,0,0.2); border-radius: 8px;
    display: flex; justify-content: space-around; align-items: center; flex-wrap: wrap; gap: 1rem;
    border-top: 2px solid var(--border-color);
}
.journal-total-item { text-align: center; }
.journal-total-label { display: block; color: var(--text-secondary); font-size: 0.8rem; margin-bottom: 0.2rem; font-weight: 600; }
.journal-total-value { color: var(--primary-color); font-weight: 700; font-size: 1.05rem; }
#balance-diff.balance-ok { color: var(--primary-color); }
#balance-diff.balance-error { color: #ff8a80; }
.journal-line-btn {
    padding: 0.35rem; border: none; border-radius: 6px; cursor: pointer; width: 28px; height: 28px;
    display: flex; align-items: center; justify-content: center; transition: all 0.2s;
}
.journal-line-btn-add { background: var(--primary-color); color: white; }
.journal-line-btn-add:hover { background: var(--primary-dark); }
.journal-line-btn-remove { background: rgba(244,67,54,0.5); color: #ff8a80; }
.journal-line-btn-remove:hover { background: rgba(244,67,54,0.8); color: white; }

/* بحث الحساب في قيد اليومية — بطاقة عائمة كبيرة واحترافية */
.searchable-account-wrap { position: relative; min-width: 260px; width: 100%; }
.account-select-trigger {
    width: 100%;
    padding: 0.5rem 0.75rem;
    background: rgba(255,255,255,0.1);
    border: 1px solid var(--border-color);
    border-radius: 6px;
    color: var(--text-primary);
    cursor: pointer;
    text-align: right;
    direction: rtl;
    font-size: 0.95rem;
    min-height: 38px;
}
.account-select-trigger::after {
    content: '\f0d7';
    font-family: 'Font Awesome 5 Free';
    font-weight: 900;
    margin-right: 0.35rem;
    float: left;
    color: var(--text-secondary);
}
/* البطاقة العائمة — تظهر فوق الصفحة بدون تقيد بالجدول */
.account-select-backdrop {
    position: fixed;
    inset: 0;
    background: rgba(0,0,0,0.4);
    z-index: 9998;
    opacity: 0;
    pointer-events: none;
    transition: opacity 0.2s;
}
.account-select-backdrop.show {
    opacity: 1;
    pointer-events: auto;
}
.account-select-dropdown {
    display: none;
    position: fixed;
    z-index: 9999;
    min-width: 420px;
    width: 480px;
    max-width: 95vw;
    max-height: 75vh;
    background: rgba(18, 48, 38, 0.98);
    border: 2px solid rgba(95, 179, 142, 0.5);
    border-radius: 12px;
    box-shadow: 0 12px 40px rgba(0,0,0,0.4);
    overflow: hidden;
    direction: rtl;
}
.account-select-dropdown.open {
    display: flex;
    flex-direction: column;
}
.account-select-dropdown .account-select-search {
    width: 100%;
    padding: 0.85rem 1.25rem;
    border: none;
    border-bottom: 2px solid rgba(95, 179, 142, 0.3);
    background: rgba(255,255,255,0.08);
    color: #fff;
    font-size: 1.05rem;
    box-sizing: border-box;
}
.account-select-dropdown .account-select-search::placeholder {
    color: rgba(255,255,255,0.6);
    font-size: 1rem;
}
.account-select-dropdown .account-select-options {
    flex: 1;
    max-height: 60vh;
    overflow-y: auto;
    padding: 0.5rem 0;
}
.account-select-dropdown .account-select-option {
    padding: 0.7rem 1.25rem;
    cursor: pointer;
    color: rgba(255,255,255,0.95);
    border-bottom: 1px solid rgba(255,255,255,0.06);
    font-size: 1rem;
    line-height: 1.45;
    transition: background 0.15s;
}
.account-select-dropdown .account-select-option:hover {
    background: rgba(95, 179, 142, 0.3);
}
.account-select-dropdown .account-select-option:active {
    background: rgba(95, 179, 142, 0.4);
}
</style>

<script>
let journalLineCounter = 0;
const accounts = @json($accounts ?? []);
const costCenters = @json($costCenters ?? []);

// عند تحميل الصفحة: لا تظهر أي سطور معبأة — المستخدم يضيف السطور من زر "إضافة عملية"
document.addEventListener('DOMContentLoaded', function() {
    updateHijriDate();
    var balanceEl = document.getElementById('balance-diff');
    if (balanceEl) balanceEl.classList.add('balance-ok');
});

// تحويل التاريخ الميلادي إلى هجري (دالة بسيطة)
function updateHijriDate() {
    const dateInput = document.getElementById('entry_date');
    const hijriDiv = document.getElementById('hijri-date');
    
    if (!dateInput.value) return;
    
    const date = new Date(dateInput.value);
    const hijriDate = gregorianToHijri(date);
    
    const days = ['الأحد', 'الإثنين', 'الثلاثاء', 'الأربعاء', 'الخميس', 'الجمعة', 'السبت'];
    const dayName = days[date.getDay()];
    const months = ['محرم', 'صفر', 'ربيع الأول', 'ربيع الثاني', 'جمادى الأولى', 'جمادى الثانية', 'رجب', 'شعبان', 'رمضان', 'شوال', 'ذو القعدة', 'ذو الحجة'];
    
    hijriDiv.textContent = `${dayName}, ${hijriDate.day} ${months[hijriDate.month - 1]} ${hijriDate.year} هـ`;
}

// دالة بسيطة لتحويل التاريخ الميلادي إلى هجري
function gregorianToHijri(date) {
    // خوارزمية بسيطة للتحويل (ليست دقيقة 100% لكنها تعمل)
    const gYear = date.getFullYear();
    const gMonth = date.getMonth() + 1;
    const gDay = date.getDate();
    
    // حساب الفرق بالأيام
    const epoch = new Date(622, 6, 16); // 16 يوليو 622 (بداية التقويم الهجري)
    const diff = Math.floor((date - epoch) / (1000 * 60 * 60 * 24));
    
    // السنة الهجرية
    const hYear = Math.floor(diff / 354.37) + 1;
    
    // الشهر واليوم (تقريبي)
    const remainingDays = diff % 354.37;
    const hMonth = Math.floor(remainingDays / 29.5) + 1;
    const hDay = Math.floor(remainingDays % 29.5) + 1;
    
    return {
        year: hYear,
        month: hMonth > 12 ? 12 : hMonth < 1 ? 1 : hMonth,
        day: hDay > 30 ? 30 : hDay < 1 ? 1 : hDay
    };
}

// عرض المرفقات
function displayAttachments(input) {
    const container = document.getElementById('attachments-list');
    if (input.files.length === 0) {
        container.innerHTML = '<p style="text-align: center; color: #999; margin: 0; font-size: 0.75rem;">لا توجد مرفقات</p>';
        return;
    }
    
    let html = '<div style="display: flex; flex-direction: column; gap: 0.25rem;">';
    for (let i = 0; i < input.files.length; i++) {
        html += `<div style="display: flex; align-items: center; gap: 0.5rem; padding: 0.4rem; background: rgba(255,255,255,0.08); border-radius: 6px; border: 1px solid var(--border-color);">
            <i class="fas fa-file" style="color: var(--primary-color); font-size: 0.75rem;"></i>
            <span style="flex: 1; color: var(--text-primary); font-size: 0.75rem;">${input.files[i].name}</span>
            <span style="color: var(--text-secondary); font-size: 0.7rem;">${(input.files[i].size / 1024).toFixed(2)} KB</span>
        </div>`;
    }
    html += '</div>';
    container.innerHTML = html;
}

// إضافة سطر جديد في القيد
function addJournalLine() {
    journalLineCounter++;
    const container = document.getElementById('journal-lines-container');
    
    const row = document.createElement('tr');
    row.className = 'journal-line';
    row.dataset.lineIndex = journalLineCounter;
    row.style.borderBottom = '1px solid var(--border-color)';
    
    // بناء قائمة منسدلة للحسابات مع عرض المستويات الهرمية
    let accountOptions = '<option value="">ابحث عن حساب بالكود أو الوصف</option>';
    accounts.forEach(account => {
        const indent = '\u2003'.repeat(Math.max(0, (account.level || 1) - 1)); // مسافات حسب المستوى
        const levelLabel = account.level ? ` [م${account.level}]` : '';
        accountOptions += `<option value="${account.id}">${indent}${account.code} - ${account.name_ar}${levelLabel}</option>`;
    });
    
    // بناء قائمة منسدلة لمراكز التكلفة
    let costCenterOptions = '<option value="">ابحث عن مركز بالكود او الوصف</option>';
    if (costCenters && costCenters.length > 0) {
        costCenters.forEach(center => {
            costCenterOptions += `<option value="${center.id}">${center.code || center.id} - ${center.name_ar || center.name}</option>`;
        });
    }
    
    row.innerHTML = `
        <td style="padding: 0.4rem; text-align: center; border-left: 1px solid var(--border-color); color: var(--text-primary); font-weight: 600; font-size: 0.8rem;">${journalLineCounter}</td>
        <td style="padding: 0.4rem; border-left: 1px solid var(--border-color);">
            <input type="number" name="lines[${journalLineCounter}][debit]" step="0.01" min="0" 
                   class="form-control line-debit line-input" placeholder="0.00" onchange="calculateTotals()" dir="ltr" style="text-align:left;">
        </td>
        <td style="padding: 0.4rem; border-left: 1px solid var(--border-color);">
            <input type="number" name="lines[${journalLineCounter}][credit]" step="0.01" min="0" 
                   class="form-control line-credit line-input" placeholder="0.00" onchange="calculateTotals()" dir="ltr" style="text-align:left;">
        </td>
        <td style="padding: 0.4rem; border-left: 1px solid var(--border-color);">
            <div class="searchable-account-wrap">
                <select name="lines[${journalLineCounter}][account_id]" class="form-control line-account-select account-select-native line-input" required>${accountOptions}</select>
            </div>
        </td>
        <td style="padding: 0.4rem; border-left: 1px solid var(--border-color);">
            <input type="text" name="lines[${journalLineCounter}][description]" class="form-control line-input" placeholder="البيان">
        </td>
        <td style="padding: 0.4rem; border-left: 1px solid var(--border-color);">
            <select name="lines[${journalLineCounter}][cost_center_id]" class="form-control line-cost-center-select line-input">${costCenterOptions}</select>
        </td>
        <td style="padding: 0.4rem;">
            <div style="display: flex; gap: 0.25rem; align-items: center;">
                <input type="text" name="lines[${journalLineCounter}][reference]" class="form-control line-input" placeholder="رقم المستند" style="flex: 1;">
                <button type="button" onclick="addJournalLineAfter(this)" class="journal-line-btn journal-line-btn-add" title="إضافة سطر"><i class="fas fa-plus" style="font-size: 0.65rem;"></i></button>
                <button type="button" onclick="removeJournalLine(this)" class="journal-line-btn journal-line-btn-remove" title="حذف سطر"><i class="fas fa-times" style="font-size: 0.65rem;"></i></button>
            </div>
        </td>
    `;
    
    container.appendChild(row);
    updateLineNumbers();
    initSearchableAccounts();
    return row;
}

// بحث مباشر عند اختيار الحساب — بطاقة عائمة كبيرة (قيد اليومية)
function initSearchableAccounts() {
    document.querySelectorAll('.searchable-account-wrap').forEach(function(wrap) {
        var select = wrap.querySelector('select.account-select-native');
        if (!select || wrap.querySelector('.account-select-trigger')) return;
        select.style.position = 'absolute';
        select.style.left = '-9999px';
        select.style.width = '1px';
        select.style.height = '1px';
        select.style.opacity = '0';
        select.style.pointerEvents = 'none';
        var trigger = document.createElement('div');
        trigger.className = 'account-select-trigger';
        trigger.setAttribute('tabindex', '0');
        var selOpt = select.options[select.selectedIndex];
        trigger.textContent = selOpt ? selOpt.textContent : (select.querySelector('option[value=""]') ? select.querySelector('option[value=""]').textContent : '-- اختر حساب --');
        var dropdown = document.createElement('div');
        dropdown.className = 'account-select-dropdown';
        dropdown.innerHTML = '<input type="text" class="account-select-search" placeholder="بحث برقم الحساب أو الاسم" autocomplete="off"><div class="account-select-options"></div>';
        var searchInput = dropdown.querySelector('.account-select-search');
        var optionsDiv = dropdown.querySelector('.account-select-options');
        wrap.insertBefore(trigger, select);
        wrap.appendChild(dropdown);
        var backdrop = null;
        function syncTrigger() {
            var o = select.options[select.selectedIndex];
            trigger.textContent = o ? o.textContent : '-- اختر حساب --';
        }
        function buildOptions() {
            optionsDiv.innerHTML = '';
            for (var i = 0; i < select.options.length; i++) {
                var opt = select.options[i];
                var d = document.createElement('div');
                d.className = 'account-select-option';
                d.dataset.value = opt.value;
                d.textContent = opt.textContent;
                d.addEventListener('click', function() {
                    select.value = this.dataset.value;
                    trigger.textContent = this.textContent;
                    closePanel();
                });
                optionsDiv.appendChild(d);
            }
        }
        function filterOptions(q) {
            q = (q || '').trim();
            var qLower = q.toLowerCase();
            var items = optionsDiv.querySelectorAll('.account-select-option');
            items.forEach(function(el) {
                var text = (el.textContent || '').trim();
                el.style.display = !q || text.toLowerCase().indexOf(qLower) !== -1 ? '' : 'none';
            });
        }
        function positionPanel() {
            var rect = trigger.getBoundingClientRect();
            var w = 480;
            var maxW = Math.min(w, window.innerWidth * 0.95);
            var left = rect.left;
            if (left + maxW > window.innerWidth) left = window.innerWidth - maxW;
            if (left < 8) left = 8;
            dropdown.style.width = maxW + 'px';
            dropdown.style.minWidth = '420px';
            dropdown.style.top = (rect.bottom + 8) + 'px';
            dropdown.style.left = left + 'px';
            dropdown.style.right = 'auto';
        }
        function closePanel() {
            dropdown.classList.remove('open');
            if (backdrop && backdrop.parentNode) backdrop.remove();
            backdrop = null;
            wrap.appendChild(dropdown);
            searchInput.value = '';
            filterOptions('');
        }
        trigger.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            document.querySelectorAll('.account-select-dropdown.open').forEach(function(d) { d.classList.remove('open'); });
            document.querySelectorAll('.account-select-backdrop.show').forEach(function(b) { b.classList.remove('show'); if (b.parentNode) b.remove(); });
            if (optionsDiv.children.length === 0) buildOptions();
            backdrop = document.createElement('div');
            backdrop.className = 'account-select-backdrop';
            backdrop.addEventListener('click', closePanel);
            document.body.appendChild(backdrop);
            document.body.appendChild(dropdown);
            positionPanel();
            dropdown.classList.add('open');
            requestAnimationFrame(function() { backdrop.classList.add('show'); });
            searchInput.value = '';
            filterOptions('');
            setTimeout(function() { searchInput.focus(); }, 100);
        });
        searchInput.addEventListener('input', function() { filterOptions(this.value); });
        searchInput.addEventListener('click', function(e) { e.stopPropagation(); });
        searchInput.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') { closePanel(); e.preventDefault(); }
        });
        select.addEventListener('change', syncTrigger);
    });
}

// إضافة سطر بعد سطر معين
function addJournalLineAfter(button) {
    const currentRow = button.closest('tr');
    const newRow = addJournalLine();
    currentRow.parentNode.insertBefore(newRow, currentRow.nextSibling);
    updateLineNumbers();
}

// تحديث أرقام السطور
function updateLineNumbers() {
    const rows = document.querySelectorAll('#journal-lines-container tr');
    rows.forEach((row, index) => {
        const seqCell = row.querySelector('td:first-child span');
        if (seqCell) {
            seqCell.textContent = index + 1;
        }
    });
}

// حذف سطر
function removeJournalLine(button) {
    const row = button.closest('tr');
    if (document.querySelectorAll('#journal-lines-container tr').length <= 1) {
        alert('يجب أن يكون هناك سطر واحد على الأقل');
        return;
    }
    row.remove();
    updateLineNumbers();
    calculateTotals();
}

// حساب الإجماليات
function calculateTotals() {
    let totalDebit = 0;
    let totalCredit = 0;
    
    document.querySelectorAll('.journal-line').forEach(line => {
        const debit = parseFloat(line.querySelector('.line-debit').value) || 0;
        const credit = parseFloat(line.querySelector('.line-credit').value) || 0;
        totalDebit += debit;
        totalCredit += credit;
    });
    
    document.getElementById('total-debit').textContent = totalDebit.toFixed(2);
    document.getElementById('total-credit').textContent = totalCredit.toFixed(2);
    
    const diff = totalDebit - totalCredit;
    const diffElement = document.getElementById('balance-diff');
    diffElement.textContent = diff.toFixed(2);
    diffElement.classList.remove('balance-ok', 'balance-error');
    diffElement.classList.add(Math.abs(diff) < 0.01 ? 'balance-ok' : 'balance-error');
}

// التحقق من الاتزان قبل الإرسال
document.getElementById('journalEntryForm').addEventListener('submit', function(e) {
    const totalDebit = parseFloat(document.getElementById('total-debit').textContent) || 0;
    const totalCredit = parseFloat(document.getElementById('total-credit').textContent) || 0;
    
    if (Math.abs(totalDebit - totalCredit) > 0.01) {
        e.preventDefault();
        alert('القيد غير متوازن! يجب أن يكون إجمالي المدين مساوياً لإجمالي الدائن.');
        return false;
    }
    
    // التحقق من وجود حسابات
    const lines = document.querySelectorAll('.journal-line');
    if (lines.length === 0) {
        e.preventDefault();
        alert('يجب إضافة سطر واحد على الأقل');
        return false;
    }
    
    let hasAccount = false;
    lines.forEach(line => {
        const accountSelect = line.querySelector('.line-account-select');
        if (accountSelect && accountSelect.value) {
            hasAccount = true;
        }
    });
    
    if (!hasAccount) {
        e.preventDefault();
        alert('يجب اختيار حساب واحد على الأقل');
        return false;
    }
});
</script>
