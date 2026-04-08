<div class="content-card">
    <div class="page-header" style="margin-bottom: 2rem;">
        <h1 class="page-title">
            <i class="fas fa-plus-circle" style="color: var(--primary-color); margin-left: 0.5rem;"></i>
            {{ isset($chartAccount) ? 'تعديل حساب' : 'إضافة حساب فرعي' }}
        </h1>
        <p class="page-subtitle">
            @if(isset($parent) && $parent)
                إضافة حساب فرعي تحت: <strong>{{ $parent->code }} - {{ $parent->name_ar }}</strong>
            @elseif(isset($chartAccount))
                تعديل بيانات الحساب
            @else
                إضافة حساب جديد
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

    <form method="POST" action="{{ isset($chartAccount) ? route('wesal.finance.chart-accounts.update', $chartAccount) : route('wesal.finance.chart-accounts.store') }}" id="accountForm">
        @csrf
        @if(isset($chartAccount))
            @method('PUT')
        @endif

        @if(isset($parent) && $parent)
            <!-- معلومات الحساب الأب (للعرض فقط) -->
            <div style="background: rgba(255, 255, 255, 0.05); padding: 1.5rem; border-radius: 12px; margin-bottom: 2rem; border: 2px solid var(--border-color);">
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem;">
                    <div>
                        <label style="display: block; color: var(--text-secondary); font-size: 0.85rem; margin-bottom: 0.5rem;">الحساب الرئيسي</label>
                        <div style="color: var(--text-primary); font-weight: 600; font-size: 1.1rem;">{{ $parent->code }} - {{ $parent->name_ar }}</div>
                    </div>
                    <div>
                        <label style="display: block; color: var(--text-secondary); font-size: 0.85rem; margin-bottom: 0.5rem;">المستوى</label>
                        <div style="color: var(--text-primary); font-weight: 600;">{{ $parent->level + 1 }}</div>
                    </div>
                    <div>
                        <label style="display: block; color: var(--text-secondary); font-size: 0.85rem; margin-bottom: 0.5rem;">كود الحساب الجديد</label>
                        <div style="color: var(--primary-color); font-weight: 700; font-size: 1.1rem;">{{ $parent->code }}{{ $nextSequence ?? '01' }}</div>
                    </div>
                </div>
            </div>
        @endif

        <div style="max-width: 600px; margin: 0 auto;">
            <!-- عنوان الحساب -->
            <div class="form-group">
                <label class="form-label">
                    <i class="fas fa-heading" style="color: var(--primary-color); margin-left: 0.5rem;"></i>
                    اسم الحساب: <span style="color: #f44336;">*</span>
                </label>
                <input type="text" name="name_ar" id="name_ar" 
                       value="{{ old('name_ar', $chartAccount->name_ar ?? '') }}" 
                       required
                       class="form-control"
                       placeholder="أدخل اسم الحساب"
                       autofocus>
            </div>

            <!-- الحقول المخفية -->
            @if(isset($parent) && $parent)
                <input type="hidden" name="parent_id" value="{{ $parent->id }}">
                <input type="hidden" name="type" value="{{ $parent->type }}">
                <input type="hidden" name="nature" value="{{ $parent->nature }}">
                <input type="hidden" name="code" value="{{ $parent->code }}{{ $nextSequence ?? '01' }}">
                <input type="hidden" name="status" value="active">
            @endif
        </div>

        <!-- أزرار الإجراءات -->
        <div style="display: flex; gap: 1rem; justify-content: flex-start; margin-top: 2rem; padding-top: 1.5rem; border-top: 2px solid var(--border-color);">
            <button type="submit" style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.875rem 2rem; background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%); color: white; border: none; border-radius: 8px; cursor: pointer; font-weight: 600; font-size: 1rem; transition: all 0.3s ease; box-shadow: 0 2px 8px rgba(95, 179, 142, 0.3);" 
                    onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 4px 12px rgba(95, 179, 142, 0.4)'" 
                    onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 2px 8px rgba(95, 179, 142, 0.3)'">
                <i class="fas fa-save"></i>
                <span>{{ isset($chartAccount) ? 'تحديث السجل' : 'إضافة السجل' }}</span>
            </button>
            <a href="{{ route('wesal.finance.chart-accounts.index') }}" style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.875rem 2rem; background: rgba(255, 255, 255, 0.1); color: var(--text-primary); text-decoration: none; border-radius: 8px; font-weight: 600; font-size: 1rem; transition: all 0.3s ease; border: 2px solid var(--border-color);"
               onmouseover="this.style.background='rgba(255, 255, 255, 0.15)'; this.style.borderColor='var(--primary-color)'" 
               onmouseout="this.style.background='rgba(255, 255, 255, 0.1)'; this.style.borderColor='var(--border-color)'">
                <i class="fas fa-times"></i>
                <span>إلغاء</span>
            </a>
        </div>
    </form>
</div>

<script>
function updateAccountType() {
    const parentSelect = document.getElementById('parent_id');
    const accountTypeDisplay = document.getElementById('account_type_display');
    const levelDisplay = document.getElementById('level_display');
    
    if (parentSelect.value) {
        accountTypeDisplay.value = 'sub';
        const selectedOption = parentSelect.options[parentSelect.selectedIndex];
        const parentLevel = parseInt(selectedOption.dataset.level) || 1;
        levelDisplay.value = parentLevel + 1;
    } else {
        accountTypeDisplay.value = 'main';
        levelDisplay.value = '1';
    }
    
    updateAccountCode();
}

function handleAccountTypeChange() {
    const accountTypeDisplay = document.getElementById('account_type_display');
    const parentSelect = document.getElementById('parent_id');
    
    if (accountTypeDisplay.value === 'main') {
        // إذا كان رئيسي، يجب إزالة الحساب الرئيسي
        parentSelect.value = '';
        parentSelect.required = false;
    } else {
        // إذا كان فرعي، يجب اختيار حساب رئيسي
        parentSelect.required = true;
    }
    
    updateAccountType();
}

function updateAccountCode() {
    const parentSelect = document.getElementById('parent_id');
    const secondarySequence = document.getElementById('secondary_sequence');
    const codeInput = document.getElementById('code');
    
    if (parentSelect.value && secondarySequence.value) {
        const selectedOption = parentSelect.options[parentSelect.selectedIndex];
        const parentCode = selectedOption.dataset.code || '';
        const sequence = secondarySequence.value;
        
        // بناء الكود: كود الأب + التسلسل الثانوي
        codeInput.value = parentCode + sequence;
    } else if (!parentSelect.value) {
        codeInput.value = '';
    }
}

// تحديث نوع الحساب (type) بناءً على الحساب الرئيسي
document.getElementById('parent_id').addEventListener('change', function() {
    const parentSelect = document.getElementById('parent_id');
    const typeInput = document.getElementById('type');
    const closingDisplay = document.getElementById('closing_display');
    
    if (parentSelect.value) {
        const selectedOption = parentSelect.options[parentSelect.selectedIndex];
        const parentType = selectedOption.dataset.type;
        
        if (parentType) {
            typeInput.value = parentType;
            
            // تحديث عرض الإقفال
            if (['asset', 'liability', 'equity'].includes(parentType)) {
                closingDisplay.value = 'balance_sheet';
            } else {
                closingDisplay.value = 'income_statement';
            }
        } else {
            // جلب نوع الحساب الأب من الخادم إذا لم يكن في data-type
            fetch(`{{ url('/wesal/finance/chart-accounts') }}/${parentSelect.value}/details`)
                .then(response => response.json())
                .then(data => {
                    if (data.success && data.account) {
                        typeInput.value = data.account.type;
                        
                        // تحديث عرض الإقفال
                        if (['asset', 'liability', 'equity'].includes(data.account.type)) {
                            closingDisplay.value = 'balance_sheet';
                        } else {
                            closingDisplay.value = 'income_statement';
                        }
                    }
                })
                .catch(error => {
                    console.error('Error fetching parent account:', error);
                    // افتراضي
                    typeInput.value = 'asset';
                    closingDisplay.value = 'balance_sheet';
                });
        }
    } else {
        typeInput.value = 'asset';
        closingDisplay.value = 'balance_sheet';
    }
    
    updateAccountType();
});

// تحديث الكود عند تغيير التسلسل الثانوي
document.getElementById('secondary_sequence').addEventListener('change', updateAccountCode);

// تهيئة القيم عند تحميل الصفحة
document.addEventListener('DOMContentLoaded', function() {
    @if(isset($parent) && $parent)
        document.getElementById('parent_id').value = '{{ $parent->id }}';
        document.getElementById('account_type_display').value = 'sub';
        updateAccountType();
        // تحديث نوع الحساب من الأب
        const parentSelect = document.getElementById('parent_id');
        const selectedOption = parentSelect.options[parentSelect.selectedIndex];
        if (selectedOption.dataset.type) {
            document.getElementById('type').value = selectedOption.dataset.type;
            const closingDisplay = document.getElementById('closing_display');
            if (['asset', 'liability', 'equity'].includes(selectedOption.dataset.type)) {
                closingDisplay.value = 'balance_sheet';
            } else {
                closingDisplay.value = 'income_statement';
            }
        }
    @endif
    
    @if(isset($chartAccount))
        // إذا كان تعديل، لا نغير الكود تلقائياً
        document.getElementById('secondary_sequence').removeAttribute('required');
        // تحديث نوع الحساب بناءً على وجود parent_id
        if ({{ $chartAccount->parent_id ? 'true' : 'false' }}) {
            document.getElementById('account_type_display').value = 'sub';
        } else {
            document.getElementById('account_type_display').value = 'main';
        }
    @endif
});
</script>

<style>
.form-group {
    margin-bottom: 1rem;
}

.form-label {
    display: block;
    margin-bottom: 0.5rem;
    color: var(--text-primary);
    font-size: 0.95rem;
    font-weight: 600;
}

.form-control {
    width: 100%;
    padding: 0.875rem 1rem;
    border-radius: 8px;
    border: 2px solid var(--border-color);
    background: rgba(255, 255, 255, 0.1);
    color: var(--text-primary);
    font-size: 1rem;
    transition: all 0.2s ease;
}

.form-control:focus {
    outline: none;
    border-color: var(--primary-color);
    background: rgba(255, 255, 255, 0.15);
}

.form-control::placeholder {
    color: var(--text-secondary);
    opacity: 0.7;
}

input[readonly],
input[disabled] {
    cursor: not-allowed;
    background: rgba(255, 255, 255, 0.1) !important;
}
</style>
