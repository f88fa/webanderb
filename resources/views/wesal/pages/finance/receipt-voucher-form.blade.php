@php
    $isNonProfitReceipt = $isNonProfitReceipt ?? false;
@endphp
<div class="content-card">
    <div class="page-header" style="margin-bottom: 2rem;">
        <h1 class="page-title">
            <i class="fas fa-hand-holding-usd" style="color: var(--primary-color); margin-left: 0.5rem;"></i>
            سند قبض
        </h1>
        <p class="page-subtitle">
            @if($isNonProfitReceipt)
                نموذج الجمعيات — رقم التسلسلي والتاريخ لا يُغيَّران. بعد الإصدار تُعدَّل الملاحظات فقط.
            @else
                إضافة سند قبض جديد - اختر الحسابات من القائمة المنسدلة (المستوى الخامس فما فوق)
            @endif
        </p>
    </div>

    @if($errors->any())
        <div class="alert alert-error" style="margin-bottom: 1.5rem;">
            <i class="fas fa-exclamation-circle"></i>
            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('wesal.finance.journal-entries.store') }}" id="receiptVoucherForm" data-non-profit="{{ $isNonProfitReceipt ? '1' : '0' }}">
        @csrf
        <input type="hidden" name="entry_type" value="receipt">

        @if($isNonProfitReceipt)
            {{-- نموذج سند القبض للجمعيات (القطاع غير الربحي) --}}
            @php
                $defDate = old('entry_date', $selectedPeriod ? $selectedPeriod->start_date->format('Y-m-d') : date('Y-m-d'));
            @endphp
            <input type="hidden" name="entry_date" value="{{ $defDate }}">

            <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 1.5rem; margin-bottom: 2rem; padding: 1rem; background: rgba(255,255,255,0.05); border-radius: 12px;">
                <div class="form-group">
                    <label class="form-label"><i class="fas fa-hashtag" style="color: var(--primary-color); margin-left: 0.5rem;"></i> رقم التسلسلي</label>
                    <input type="text" readonly class="form-control" style="background: rgba(255, 255, 255, 0.1); cursor: not-allowed;" value="سيتم توليده تلقائياً">
                </div>
                <div class="form-group">
                    <label class="form-label"><i class="fas fa-calendar" style="color: var(--primary-color); margin-left: 0.5rem;"></i> التاريخ</label>
                    <input type="text" readonly class="form-control" style="background: rgba(255, 255, 255, 0.1); cursor: not-allowed;" value="{{ $defDate }}">
                </div>
                <div class="form-group">
                    <label class="form-label"><i class="fas fa-heading" style="color: var(--primary-color); margin-left: 0.5rem;"></i> عنوان سند القبض <span style="color: #f44336;">*</span></label>
                    <input type="text" name="description" id="description" value="{{ old('description') }}" required class="form-control" placeholder="عنوان سند القبض">
                </div>
            </div>

            <div style="background: rgba(255, 255, 255, 0.05); padding: 1.5rem; border-radius: 12px; margin-bottom: 2rem; border: 2px solid var(--border-color);">
                <h3 style="color: var(--text-primary); margin-bottom: 1rem;"><i class="fas fa-user" style="color: var(--primary-color);"></i> بيانات المتبرع / المستلم</h3>
                <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 1.5rem;">
                    <div class="form-group">
                        <label class="form-label">اسم المتبرع <span style="color: #f44336;">*</span></label>
                        <input type="text" name="recipient_name" id="recipient_name" value="{{ old('recipient_name') }}" required class="form-control" placeholder="اسم المتبرع">
                    </div>
                    <div class="form-group">
                        <label class="form-label">رقم الجوال <span style="color: #f44336;">*</span></label>
                        <input type="text" name="recipient_phone" id="recipient_phone" value="{{ old('recipient_phone') }}" required class="form-control" placeholder="رقم الجوال">
                    </div>
                    <div class="form-group" style="grid-column: span 2;">
                        <label class="form-label">العنوان <span style="color: #f44336;">*</span></label>
                        <input type="text" name="recipient_address" id="recipient_address" value="{{ old('recipient_address') }}" required class="form-control" placeholder="العنوان">
                    </div>
                </div>
            </div>

            <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 1.5rem; margin-bottom: 2rem;">
                <div class="form-group">
                    <label class="form-label">المبلغ <span style="color: #f44336;">*</span></label>
                    <input type="number" id="non_profit_amount" step="0.01" min="0" value="{{ old('non_profit_amount') }}" class="form-control" placeholder="0.00" dir="ltr" style="text-align:left;">
                </div>
                <div class="form-group">
                    <label class="form-label">الحساب المستقبل (البنك) <span style="color: #f44336;">*</span></label>
                    <div class="searchable-account-wrap">
                        <select name="cash_account_id" id="cash_account_id" class="form-control account-select-native" required>
                            <option value="">-- اختر الحساب المستقبل (البنك) --</option>
                            @foreach($accountsFromLevel3 ?? [] as $acc)
                                <option value="{{ $acc->id }}" {{ old('cash_account_id') == $acc->id ? 'selected' : '' }}>{{ str_repeat(' ', max(0, $acc->level - 1)) }}{{ $acc->code }} - {{ $acc->name_ar }} [م{{ $acc->level }}]</option>
                            @endforeach
                        </select>
                    </div>
                    <small style="color: rgba(255,255,255,0.7); display: block; margin-top: 0.25rem;">من دليل الحسابات — من المستوى الثالث</small>
                </div>
                <div class="form-group">
                    <label class="form-label">حساب الإيراد/التبرع (من دليل الحسابات) <span style="color: #f44336;">*</span></label>
                    <div class="searchable-account-wrap">
                        <select id="receiving_account_id" class="form-control account-select-native">
                            <option value="">-- اختر حساب الإيراد أو التبرع --</option>
                            @foreach($accounts ?? [] as $acc)
                                <option value="{{ $acc->id }}">{{ str_repeat(' ', max(0, $acc->level - 1)) }}{{ $acc->code }} - {{ $acc->name_ar }} [م{{ $acc->level }}]</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            <div class="form-group" style="margin-bottom: 2rem;">
                <label class="form-label"><i class="fas fa-sticky-note" style="color: var(--primary-color);"></i> الملاحظات</label>
                <textarea name="notes" id="notes" class="form-control" rows="3" placeholder="ملاحظات (بعد إصدار السند هي الوحيدة القابلة للتعديل)">{{ old('notes') }}</textarea>
            </div>

            <div style="margin-bottom: 2rem;">
                <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer;">
                    <input type="checkbox" name="post_now" value="1" checked style="width: 20px; height: 20px; cursor: pointer;">
                    <span style="color: var(--text-primary); font-weight: 500;">ترحيل السند مباشرة</span>
                </label>
            </div>
        @else
            {{-- النموذج العادي (قطاع تجاري/حكومي) --}}
            <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 1.5rem; margin-bottom: 2rem;">
                <div class="form-group">
                    <label class="form-label"><i class="fas fa-calendar" style="color: var(--primary-color); margin-left: 0.5rem;"></i> تاريخ السند: <span style="color: #f44336;">*</span></label>
                    <input type="date" name="entry_date" id="entry_date" value="{{ old('entry_date', $selectedPeriod ? $selectedPeriod->start_date->format('Y-m-d') : date('Y-m-d')) }}" required class="form-control">
                </div>
                <div class="form-group">
                    <label class="form-label"><i class="fas fa-calendar-alt" style="color: var(--primary-color); margin-left: 0.5rem;"></i> السنة المالية: <span style="color: #f44336;">*</span></label>
                    <select name="period_id" id="period_id" class="form-control" required>
                        @foreach($yearOptions ?? [] as $opt)
                            <option value="{{ $opt->period_id }}" {{ ($selectedPeriod && $selectedPeriod->fiscal_year_id == $opt->fiscal_year_id) ? 'selected' : '' }}>{{ $opt->year_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label"><i class="fas fa-hashtag" style="color: var(--primary-color); margin-left: 0.5rem;"></i> رقم السند:</label>
                    <input type="text" id="voucher_no" readonly class="form-control" style="background: rgba(255, 255, 255, 0.1); cursor: not-allowed;" value="سيتم توليده تلقائياً">
                </div>
            </div>

            <div style="background: rgba(255, 255, 255, 0.05); padding: 1.5rem; border-radius: 12px; margin-bottom: 2rem; border: 2px solid var(--border-color);">
                <h3 style="color: var(--text-primary); margin-bottom: 1rem;"><i class="fas fa-user" style="color: var(--primary-color);"></i> معلومات المستلم</h3>
                <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 1.5rem;">
                    <div class="form-group">
                        <label class="form-label">اسم المستلم: <span style="color: #f44336;">*</span></label>
                        <input type="text" name="recipient_name" id="recipient_name" value="{{ old('recipient_name') }}" required class="form-control" placeholder="اسم المستلم">
                    </div>
                    <div class="form-group">
                        <label class="form-label">رقم الهوية / السجل التجاري:</label>
                        <input type="text" name="recipient_id" id="recipient_id" value="{{ old('recipient_id') }}" class="form-control" placeholder="رقم الهوية أو السجل التجاري">
                    </div>
                    <div class="form-group">
                        <label class="form-label">رقم الهاتف:</label>
                        <input type="text" name="recipient_phone" id="recipient_phone" value="{{ old('recipient_phone') }}" class="form-control" placeholder="رقم الهاتف">
                    </div>
                    <div class="form-group">
                        <label class="form-label">العنوان:</label>
                        <input type="text" name="recipient_address" id="recipient_address" value="{{ old('recipient_address') }}" class="form-control" placeholder="العنوان">
                    </div>
                </div>
            </div>

            {{-- طريقة الاستلام (إجباري) — قبل اختيار الحساب --}}
            <div style="background: rgba(255, 255, 255, 0.05); padding: 1.5rem; border-radius: 12px; margin-bottom: 2rem; border: 2px solid var(--border-color);">
                <h3 style="color: var(--text-primary); margin-bottom: 1rem;"><i class="fas fa-exchange-alt" style="color: var(--primary-color);"></i> طريقة الاستلام <span style="color: #f44336;">*</span></h3>
                <div class="form-group" style="margin-bottom: 1.5rem;">
                    <select name="payment_method" id="payment_method" class="form-control" required style="max-width: 280px;">
                        <option value="">-- اختر طريقة الاستلام --</option>
                        <option value="transfer" {{ old('payment_method') == 'transfer' ? 'selected' : '' }}>تحويل</option>
                        <option value="cheque" {{ old('payment_method') == 'cheque' ? 'selected' : '' }}>شيك</option>
                        <option value="cash" {{ old('payment_method') == 'cash' ? 'selected' : '' }}>نقدي</option>
                    </select>
                </div>
                {{-- بيانات التحويل أو النقدي --}}
                {{-- بيانات الشيك (رقم الشيك والبنك المصدر فقط للشيك) --}}
                <div id="voucher-data-cheque" class="voucher-method-data" style="display: none;">
                    <h4 style="color: var(--text-primary); margin-bottom: 0.75rem; font-size: 0.95rem;">بيانات الشيك</h4>
                    <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 1rem;">
                        <div class="form-group">
                            <label class="form-label">رقم الشيك: <span style="color: #f44336;">*</span></label>
                            <input type="text" name="cheque_no" id="cheque_no" value="{{ old('cheque_no') }}" class="form-control" placeholder="رقم الشيك">
                        </div>
                        <div class="form-group">
                            <label class="form-label">البنك المصدر: <span style="color: #f44336;">*</span></label>
                            <input type="text" name="cheque_bank_name" id="cheque_bank_name" value="{{ old('cheque_bank_name') }}" class="form-control" placeholder="البنك المصدر للشيك">
                        </div>
                    </div>
                </div>
            </div>

            <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 1.5rem; margin-bottom: 2rem;">
                <div class="form-group">
                    <label class="form-label">حساب الصندوق/البنك: <span style="color: #f44336;">*</span></label>
                    <div class="searchable-account-wrap">
                        <select name="cash_account_id" id="cash_account_id" class="form-control account-select-native" required>
                            <option value="">-- اختر حساب الصندوق أو البنك --</option>
                            @foreach($accounts ?? [] as $acc)
                                <option value="{{ $acc->id }}" {{ old('cash_account_id') == $acc->id ? 'selected' : '' }}>{{ str_repeat(' ', max(0, $acc->level - 1)) }}{{ $acc->code }} - {{ $acc->name_ar }} [م{{ $acc->level }}]</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label class="form-label">سبب القبض: <span style="color: #f44336;">*</span></label>
                    <input type="text" name="description" id="description" value="{{ old('description') }}" required class="form-control" placeholder="سبب القبض">
                </div>
            </div>

            <div style="background: rgba(255, 255, 255, 0.05); padding: 1.5rem; border-radius: 12px; margin-bottom: 2rem; border: 2px solid var(--border-color);">
                <h3 style="color: var(--text-primary); margin-bottom: 1rem;"><i class="fas fa-list" style="color: var(--primary-color);"></i> سطور السند</h3>
                <div style="display: grid; grid-template-columns: 2fr 1fr 1.5fr auto; gap: 1rem; padding: 0.75rem 1rem; background: rgba(0,0,0,0.2); border-radius: 8px; margin-bottom: 0.75rem; font-weight: 700; font-size: 0.9rem; color: var(--text-primary);">
                    <div>الحساب</div><div>المبلغ</div><div>البيان</div><div></div>
                </div>
                <div id="journal-lines-container"></div>
                <div style="margin-top: 0.75rem;">
                    <button type="button" onclick="addReceiptLine()" style="padding: 0.6rem 1.2rem; background: var(--primary-color); color: white; border: none; border-radius: 6px; cursor: pointer; font-weight: 500;"><i class="fas fa-plus"></i> إضافة سطر</button>
                </div>
                <div style="margin-top: 1rem; padding: 1rem; background: rgba(255, 255, 255, 0.1); border-radius: 8px;">
                    <strong style="color: var(--text-primary);">إجمالي المبلغ:</strong>
                    <span id="total-amount" style="color: var(--primary-color); font-weight: 700; font-size: 1.1rem; margin-right: 1rem;">0.00</span>
                </div>
            </div>

            <div class="form-group" style="margin-bottom: 2rem;">
                <label class="form-label"><i class="fas fa-sticky-note" style="color: var(--primary-color); margin-left: 0.5rem;"></i> ملاحظات:</label>
                <textarea name="notes" id="notes" class="form-control" rows="3" placeholder="ملاحظات إضافية">{{ old('notes') }}</textarea>
            </div>

            <div style="margin-bottom: 2rem;">
                <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer;">
                    <input type="checkbox" name="post_now" value="1" checked style="width: 20px; height: 20px; cursor: pointer;">
                    <span style="color: var(--text-primary); font-weight: 500;">ترحيل السند مباشرة</span>
                </label>
            </div>
        @endif

        <div style="display: flex; gap: 1rem; justify-content: flex-start; margin-top: 2rem; padding-top: 1.5rem; border-top: 2px solid var(--border-color);">
            <button type="submit" style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.875rem 2rem; background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%); color: white; border: none; border-radius: 8px; cursor: pointer; font-weight: 600; font-size: 1rem;">
                <i class="fas fa-save"></i>
                <span>حفظ سند القبض</span>
            </button>
            <a href="{{ route('wesal.finance.journal-entries.index') }}" style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.875rem 2rem; background: rgba(255, 255, 255, 0.1); color: var(--text-primary); text-decoration: none; border-radius: 8px; font-weight: 600; font-size: 1rem; border: 2px solid var(--border-color);">
                <i class="fas fa-times"></i>
                <span>إلغاء</span>
            </a>
        </div>
    </form>
</div>

<script>
let journalLineCounter = 0;
const accounts = @json($accounts ?? []);

const isNonProfitReceipt = document.getElementById('receiptVoucherForm').dataset.nonProfit === '1';

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
        trigger.textContent = selOpt ? selOpt.textContent : (select.querySelector('option[value=""]') ? select.querySelector('option[value=""]').textContent : '-- اختر --');
        var dropdown = document.createElement('div');
        dropdown.className = 'account-select-dropdown';
        dropdown.innerHTML = '<input type="text" class="account-select-search" placeholder="بحث برقم الحساب أو الاسم" autocomplete="off"><div class="account-select-options"></div>';
        var searchInput = dropdown.querySelector('.account-select-search');
        var optionsDiv = dropdown.querySelector('.account-select-options');
        wrap.insertBefore(trigger, select);
        wrap.appendChild(dropdown);
        function syncTrigger() {
            var o = select.options[select.selectedIndex];
            trigger.textContent = o ? o.textContent : '';
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
                    dropdown.classList.remove('open');
                    searchInput.value = '';
                    filterOptions('');
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
                var match = !q || text.toLowerCase().indexOf(qLower) !== -1;
                el.style.display = match ? '' : 'none';
            });
        }
        trigger.addEventListener('click', function(e) {
            e.preventDefault();
            document.querySelectorAll('.account-select-dropdown.open').forEach(function(d) { d.classList.remove('open'); });
            if (optionsDiv.children.length === 0) buildOptions();
            dropdown.classList.add('open');
            searchInput.value = '';
            filterOptions('');
            setTimeout(function() { searchInput.focus(); }, 50);
        });
        searchInput.addEventListener('input', function() { filterOptions(this.value); });
        searchInput.addEventListener('click', function(e) { e.stopPropagation(); });
        select.addEventListener('change', syncTrigger);
        document.addEventListener('click', function closeDropdown(e) {
            if (!wrap.contains(e.target)) dropdown.classList.remove('open');
        });
    });
}
document.addEventListener('DOMContentLoaded', function() { initSearchableAccounts(); });

document.addEventListener('DOMContentLoaded', function() {
    var sel = document.getElementById('payment_method');
    var transferBlock = document.getElementById('voucher-data-transfer');
    var chequeBlock = document.getElementById('voucher-data-cheque');
    var chequeNo = document.getElementById('cheque_no');
    var chequeBank = document.getElementById('cheque_bank_name');
    if (!sel) return;
    function toggle() {
        var v = (sel.value || '').toLowerCase();
        if (transferBlock) transferBlock.style.display = (v === 'transfer' || v === 'cash') ? 'block' : 'none';
        if (chequeBlock) chequeBlock.style.display = v === 'cheque' ? 'block' : 'none';
        var required = v === 'cheque';
        if (chequeNo) { chequeNo.required = required; if (required) chequeNo.setAttribute('required', 'required'); else chequeNo.removeAttribute('required'); }
        if (chequeBank) { chequeBank.required = required; if (required) chequeBank.setAttribute('required', 'required'); else chequeBank.removeAttribute('required'); }
    }
    sel.addEventListener('change', toggle);
    toggle();
});

if (!isNonProfitReceipt) {
    (function initReceiptLines() {
        const container = document.getElementById('journal-lines-container');
        if (container && container.children.length === 0) addReceiptLine();
    })();
    document.addEventListener('DOMContentLoaded', function() {
        const container = document.getElementById('journal-lines-container');
        if (container && container.children.length === 0) addReceiptLine();
    });
}

function addReceiptLine() {
    journalLineCounter++;
    const container = document.getElementById('journal-lines-container');
    if (!container) return;
    const lineDiv = document.createElement('div');
    lineDiv.className = 'journal-line';
    lineDiv.dataset.lineIndex = journalLineCounter;
    lineDiv.style.cssText = 'display: grid; grid-template-columns: 2fr 1fr 1.5fr auto; gap: 1rem; padding: 1rem; margin-bottom: 1rem; background: rgba(248,249,250,0.98); border-radius: 8px; border: 1px solid rgba(0,0,0,0.15); align-items: center;';
    let accountOptions = '<option value="">-- اختر حساب --</option>';
    accounts.forEach(account => {
        const indent = '\u2003'.repeat(Math.max(0, (account.level || 1) - 1));
        const levelLabel = account.level ? ` [م${account.level}]` : '';
        accountOptions += `<option value="${account.id}">${indent}${account.code} - ${account.name_ar}${levelLabel}</option>`;
    });
    const inputStyle = 'width:100%;padding:0.5rem;border:1px solid rgba(0,0,0,0.2);border-radius:4px;background:#fff !important;color:#222 !important;font-size:0.9rem;';
    const descVal = document.getElementById('description') ? document.getElementById('description').value : '';
    lineDiv.innerHTML = `
        <div class="searchable-account-wrap"><select class="form-control account-select-native line-account-select" required style="${inputStyle}">${accountOptions}</select></div>
        <div><input type="number" class="form-control line-amount" step="0.01" min="0" placeholder="0.00" onchange="calculateReceiptTotal()" dir="ltr" style="${inputStyle} text-align:left;"></div>
        <div><input type="text" class="form-control line-description" placeholder="وصف السطر" value="${descVal}" style="${inputStyle}"></div>
        <div><button type="button" onclick="removeJournalLine(this)" style="padding: 0.5rem; background: #d32f2f; color: white; border: none; border-radius: 4px; cursor: pointer;"><i class="fas fa-trash"></i></button></div>
    `;
    container.appendChild(lineDiv);
    initSearchableAccounts();
}

function removeJournalLine(button) {
    const lineDiv = button.closest('.journal-line');
    if (lineDiv) { lineDiv.remove(); calculateReceiptTotal(); }
}

function calculateReceiptTotal() {
    let total = 0;
    document.querySelectorAll('.journal-line .line-amount').forEach(input => { total += parseFloat(input.value) || 0; });
    const el = document.getElementById('total-amount');
    if (el) el.textContent = total.toFixed(2);
}

document.getElementById('receiptVoucherForm').addEventListener('submit', function(e) {
    const form = this;
    form.querySelectorAll('input[name^="lines["]').forEach(el => el.remove());

    if (isNonProfitReceipt) {
        const cashAccountId = document.getElementById('cash_account_id').value;
        const receivingId = document.getElementById('receiving_account_id').value;
        const amount = parseFloat(document.getElementById('non_profit_amount').value) || 0;
        const description = (document.getElementById('description') && document.getElementById('description').value) || '';

        if (!cashAccountId) { e.preventDefault(); alert('يجب اختيار الحساب المستقبل (البنك)'); return false; }
        if (!receivingId) { e.preventDefault(); alert('يجب اختيار حساب الإيراد/التبرع'); return false; }
        if (amount <= 0) { e.preventDefault(); alert('يجب إدخال مبلغ أكبر من صفر'); return false; }

        const lines = [
            { account_id: cashAccountId, debit: amount, credit: 0, description: description },
            { account_id: receivingId, debit: 0, credit: amount, description: description }
        ];
        lines.forEach((line, i) => {
            ['account_id','debit','credit','description'].forEach(f => {
                const inp = document.createElement('input');
                inp.type = 'hidden';
                inp.name = `lines[${i}][${f}]`;
                inp.value = line[f] ?? '';
                form.appendChild(inp);
            });
        });
        return true;
    }

    const cashAccountId = document.getElementById('cash_account_id').value;
    if (!cashAccountId) { e.preventDefault(); alert('يجب اختيار حساب الصندوق/البنك'); return false; }

    const lines = [];
    let totalAmount = 0;
    document.querySelectorAll('.journal-line').forEach(lineEl => {
        const accountId = lineEl.querySelector('.line-account-select').value;
        const amount = parseFloat(lineEl.querySelector('.line-amount').value) || 0;
        const description = lineEl.querySelector('.line-description').value || '';
        if (!accountId || amount <= 0) return;
        totalAmount += amount;
        lines.push({ account_id: accountId, debit: 0, credit: amount, description });
    });

    if (lines.length === 0) {
        e.preventDefault();
        alert('يجب إضافة سطر واحد على الأقل بمبلغ أكبر من صفر');
        return false;
    }

    const mainDesc = document.getElementById('description').value || '';
    lines.unshift({ account_id: cashAccountId, debit: totalAmount, credit: 0, description: mainDesc });

    lines.forEach((line, i) => {
        ['account_id','debit','credit','description'].forEach(f => {
            const inp = document.createElement('input');
            inp.type = 'hidden';
            inp.name = `lines[${i}][${f}]`;
            inp.value = line[f] ?? '';
            form.appendChild(inp);
        });
    });
});
</script>

<style>
.searchable-account-wrap { position: relative; }
.account-select-trigger {
    width: 100%;
    padding: 0.6rem 1rem;
    background: rgba(255,255,255,0.08);
    border: 1px solid rgba(255,255,255,0.2);
    border-radius: 6px;
    color: var(--text-primary, #fff);
    cursor: pointer;
    text-align: right;
    direction: rtl;
}
.account-select-trigger::after {
    content: '\f0d7';
    font-family: 'Font Awesome 5 Free';
    font-weight: 900;
    margin-right: 0.5rem;
    float: left;
}
.account-select-dropdown {
    display: none;
    position: absolute;
    top: 100%;
    right: 0;
    left: 0;
    margin-top: 4px;
    background: rgba(15, 61, 46, 0.98);
    border: 1px solid rgba(95, 179, 142, 0.4);
    border-radius: 8px;
    box-shadow: 0 8px 24px rgba(0,0,0,0.3);
    z-index: 1000;
    max-height: 320px;
    overflow: hidden;
    direction: rtl;
}
.account-select-dropdown.open { display: block; }
.account-select-search {
    width: 100%;
    padding: 0.6rem 1rem;
    border: none;
    border-bottom: 1px solid rgba(255,255,255,0.2);
    background: rgba(255,255,255,0.1);
    color: #fff;
    font-size: 0.95rem;
    box-sizing: border-box;
}
.account-select-search::placeholder { color: rgba(255,255,255,0.5); }
.account-select-options {
    max-height: 260px;
    overflow-y: auto;
}
.account-select-option {
    padding: 0.55rem 1rem;
    cursor: pointer;
    color: rgba(255,255,255,0.95);
    border-bottom: 1px solid rgba(255,255,255,0.06);
    font-size: 0.9rem;
}
.account-select-option:hover { background: rgba(95, 179, 142, 0.25); }
.tree-item-selectable:hover {
    background: rgba(255, 255, 255, 0.15) !important;
    border-color: var(--primary-color) !important;
}
.journal-line {
    transition: all 0.2s ease;
    background: rgba(248,249,250,0.98) !important;
}
.journal-line:hover {
    background: rgba(240,242,245,0.98) !important;
}
.journal-line input,
.journal-line select,
.journal-line .form-control {
    background: #fff !important;
    color: #222 !important;
    border-color: rgba(0,0,0,0.2) !important;
}
.journal-line input::placeholder {
    color: #888 !important;
}
/* حقل اختيار الحساب داخل سطور السند - وضوح النص */
.journal-line .account-select-trigger {
    background: #fff !important;
    color: #222 !important;
    border: 1px solid rgba(0,0,0,0.2) !important;
}
.journal-line .account-select-trigger::after {
    color: #555 !important;
}
</style>
