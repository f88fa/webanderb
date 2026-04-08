<div class="content-card" style="padding: 0;">
    <!-- قسم علوي: بطاقة تفاصيل الحساب المحدد -->
    <div style="padding: 2rem; background: var(--sidebar-bg); border-bottom: 2px solid rgba(255, 255, 255, 0.1); box-shadow: 0 2px 4px rgba(0,0,0,0.2);">
        <div id="account-details-card" style="display: none;">
            <!-- بطاقة العنوان -->
            <div style="display: flex; align-items: center; gap: 1rem; margin-bottom: 1.5rem; padding: 1.25rem; background: rgba(95, 179, 142, 0.3); border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.3); border: 1px solid rgba(95, 179, 142, 0.5);">
                <div style="width: 60px; height: 60px; background: rgba(255, 255, 255, 0.2); border-radius: 50%; display: flex; align-items: center; justify-content: center; backdrop-filter: blur(10px);">
                    <i class="fas fa-coins" style="font-size: 1.75rem; color: var(--text-primary);"></i>
                </div>
                <div>
                    <h2 id="account-name" style="color: var(--text-primary); margin: 0; font-size: 1.75rem; font-weight: 700;"></h2>
                    <p style="color: var(--text-secondary); margin: 0.5rem 0 0 0; font-size: 0.95rem; font-weight: 400;">قم باختيار الحساب من الشجرة بالاسفل</p>
                </div>
            </div>

            <!-- تفاصيل الحساب -->
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 1.25rem; margin-bottom: 1.5rem;">
                <div style="padding: 1rem; background: rgba(255, 255, 255, 0.1); border-radius: 8px; border-left: 4px solid var(--primary-color);">
                    <label style="display: block; color: var(--text-secondary); font-size: 0.85rem; margin-bottom: 0.5rem; font-weight: 500;">المستوى</label>
                    <div id="account-level" style="color: var(--text-primary); font-weight: 600; font-size: 1rem;">-</div>
                </div>
                <div style="padding: 1rem; background: rgba(255, 255, 255, 0.1); border-radius: 8px; border-left: 4px solid var(--primary-color);">
                    <label style="display: block; color: var(--text-secondary); font-size: 0.85rem; margin-bottom: 0.5rem; font-weight: 500;">كود الحساب</label>
                    <div id="account-code" style="color: var(--primary-color); font-weight: 700; font-size: 1.1rem;">-</div>
                </div>
                <div style="padding: 1rem; background: rgba(255, 255, 255, 0.1); border-radius: 8px; border-left: 4px solid var(--primary-color);">
                    <label style="display: block; color: var(--text-secondary); font-size: 0.85rem; margin-bottom: 0.5rem; font-weight: 500;">طبيعة الحساب</label>
                    <div id="account-nature" style="color: var(--text-primary); font-weight: 600; font-size: 1rem;">-</div>
                </div>
                <div style="padding: 1rem; background: rgba(255, 255, 255, 0.1); border-radius: 8px; border-left: 4px solid var(--primary-color);">
                    <label style="display: block; color: var(--text-secondary); font-size: 0.85rem; margin-bottom: 0.5rem; font-weight: 500;">نوع الحساب</label>
                    <div id="account-type" style="color: var(--text-primary); font-weight: 600; font-size: 1rem;">-</div>
                </div>
                <div style="padding: 1rem; background: rgba(95, 179, 142, 0.2); border-radius: 8px; border-left: 4px solid var(--primary-color); grid-column: span 2;">
                    <label style="display: block; color: var(--text-primary); font-size: 0.85rem; margin-bottom: 0.5rem; font-weight: 600;">رصيد حالي</label>
                    <div style="display: flex; align-items: center; gap: 0.75rem;">
                        <span id="account-balance" style="color: var(--text-primary); font-weight: 700; font-size: 1.5rem;">0.00</span>
                        <span id="account-balance-type" style="color: var(--primary-color); font-size: 0.9rem; font-weight: 500; padding: 0.25rem 0.75rem; background: rgba(255,255,255,0.2); border-radius: 12px;">-</span>
                    </div>
                </div>
                <div style="padding: 1rem; background: rgba(255, 255, 255, 0.1); border-radius: 8px; border-left: 4px solid var(--primary-color);">
                    <label style="display: block; color: var(--text-secondary); font-size: 0.85rem; margin-bottom: 0.5rem; font-weight: 500;">تاريخ الإنشاء</label>
                    <div id="account-created-at" style="color: var(--text-primary); font-weight: 600; font-size: 1rem;">-</div>
                </div>
                <div style="padding: 1rem; background: rgba(255, 255, 255, 0.1); border-radius: 8px; border-left: 4px solid var(--primary-color);">
                    <label style="display: block; color: var(--text-secondary); font-size: 0.85rem; margin-bottom: 0.5rem; font-weight: 500;">الحالة</label>
                    <div id="account-status" style="color: var(--text-primary); font-weight: 600; font-size: 1rem;">-</div>
                </div>
            </div>

            <!-- أزرار الإجراءات -->
            <div style="display: flex; gap: 1rem; flex-wrap: wrap;">
                <button id="btn-edit-account" onclick="editAccount()" style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.875rem 1.75rem; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border: none; border-radius: 8px; cursor: pointer; font-weight: 600; transition: all 0.3s ease; box-shadow: 0 2px 8px rgba(102, 126, 234, 0.3);" onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 4px 12px rgba(102, 126, 234, 0.4)'" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 2px 8px rgba(102, 126, 234, 0.3)'">
                    <i class="fas fa-edit"></i>
                    <span>تحديث بيانات الحساب</span>
                </button>
                <button id="btn-add-child" onclick="addChildAccount()" style="display: none; align-items: center; gap: 0.5rem; padding: 0.875rem 1.75rem; background: linear-gradient(135deg, #4caf50 0%, #45a049 100%); color: white; border: none; border-radius: 8px; cursor: pointer; font-weight: 600; transition: all 0.3s ease; box-shadow: 0 2px 8px rgba(76, 175, 80, 0.3);" onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 4px 12px rgba(76, 175, 80, 0.4)'" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 2px 8px rgba(76, 175, 80, 0.3)'">
                    <i class="fas fa-plus"></i>
                    <span>إضافة حساب فرعي</span>
                </button>
                <a id="btn-ledger" href="#" target="_blank" style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.875rem 1.75rem; background: linear-gradient(135deg, #ff9800 0%, #f57c00 100%); color: white; text-decoration: none; border-radius: 8px; font-weight: 600; transition: all 0.3s ease; box-shadow: 0 2px 8px rgba(255, 152, 0, 0.3);" onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 4px 12px rgba(255, 152, 0, 0.4)'" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 2px 8px rgba(255, 152, 0, 0.3)'">
                    <i class="fas fa-book"></i>
                    <span>كشف حساب</span>
                </a>
            </div>
        </div>

        <!-- رسالة عند عدم وجود حساب محدد -->
        <div id="no-account-selected" style="text-align: center; padding: 3rem; color: var(--text-secondary);">
            <i class="fas fa-info-circle" style="font-size: 3rem; opacity: 0.7; margin-bottom: 1rem; color: var(--text-secondary);"></i>
            <p style="color: var(--text-secondary);">اختر حساباً من الشجرة لعرض التفاصيل</p>
        </div>
    </div>

    <!-- قسم الفلترة في الأعلى بشكل أفقي -->
    <div style="padding: 1.5rem 2rem; background: var(--sidebar-bg); border-bottom: 2px solid rgba(255, 255, 255, 0.1); box-shadow: 0 2px 4px rgba(0,0,0,0.2);">
        <div style="display: flex; align-items: center; gap: 2rem; flex-wrap: wrap;">
            <div style="display: flex; align-items: center; gap: 0.5rem;">
                <i class="fas fa-filter" style="color: #667eea; font-size: 1.1rem;"></i>
                <span style="color: #212121; font-weight: 600; font-size: 1rem;">الفلترة:</span>
            </div>
            <div style="display: flex; align-items: center; gap: 1rem; flex-wrap: wrap;">
                <div style="display: flex; align-items: center; gap: 0.5rem;">
                    <label style="color: var(--text-primary); font-size: 0.9rem; font-weight: 500; white-space: nowrap;">السنة المالية:</label>
                    <select id="filter-period" onchange="loadTree()" style="padding: 0.6rem 1rem; border-radius: 8px; border: 2px solid rgba(255, 255, 255, 0.2); background: rgba(255, 255, 255, 0.1); color: var(--text-primary); font-size: 0.95rem; transition: border-color 0.2s; min-width: 200px;" onfocus="this.style.borderColor='var(--primary-color)'" onblur="this.style.borderColor='rgba(255, 255, 255, 0.2)'">
                        <option value="">الكل</option>
                        @foreach(($periods ?? collect())->groupBy('fiscal_year_id') as $fid => $periodsInYear)
                            @php $lastPeriod = $periodsInYear->sortByDesc('start_date')->first(); @endphp
                            @if($lastPeriod)
                                <option value="{{ $lastPeriod->id }}" {{ ($periodId ?? null) == $lastPeriod->id ? 'selected' : '' }}>{{ $lastPeriod->fiscalYear->year_name ?? $fid }}</option>
                            @endif
                        @endforeach
                    </select>
                </div>
                <div style="display: flex; align-items: center; gap: 0.5rem;">
                    <label style="color: var(--text-primary); font-size: 0.9rem; font-weight: 500; white-space: nowrap;">حتى تاريخ:</label>
                    <input type="date" id="filter-as-of" onchange="loadTree()" value="{{ $asOf ? $asOf->format('Y-m-d') : '' }}" style="padding: 0.6rem 1rem; border-radius: 8px; border: 2px solid rgba(255, 255, 255, 0.2); background: rgba(255, 255, 255, 0.1); color: var(--text-primary); font-size: 0.95rem; transition: border-color 0.2s;" onfocus="this.style.borderColor='var(--primary-color)'" onblur="this.style.borderColor='rgba(255, 255, 255, 0.2)'">
                </div>
            </div>
        </div>
    </div>

    <!-- قسم الشجرة - مساحة كاملة -->
    <div style="padding: 2rem;">
        <div style="margin-bottom: 1rem; padding: 0.75rem 1rem; background: rgba(255, 255, 255, 0.1); border-radius: 8px; border: 1px solid rgba(255, 255, 255, 0.1);">
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <h3 style="color: var(--text-primary); margin: 0; display: flex; align-items: center; gap: 0.5rem; font-size: 1.2rem; font-weight: 600;">
                    <i class="fas fa-sitemap" style="color: var(--primary-color); font-size: 1.3rem;"></i>
                    دليل الحسابات
                </h3>
                <div id="tree-loading" style="display: none; color: var(--primary-color); font-weight: 500;">
                    <i class="fas fa-spinner fa-spin"></i> جاري التحميل...
                </div>
            </div>
        </div>
        
        <!-- عنوان الشجرة -->
        <div style="background: rgba(95, 179, 142, 0.3); padding: 1rem 1.25rem; border-radius: 8px 8px 0 0; border-bottom: 2px solid rgba(95, 179, 142, 0.5); direction: rtl; text-align: right; box-shadow: 0 2px 4px rgba(0,0,0,0.2);">
            <span style="color: var(--text-primary); font-size: 0.95rem; font-weight: 500;">
                <i class="fas fa-mouse-pointer" style="margin-left: 0.5rem;"></i>
                قم باختيار الحساب من الشجرة بالاسفل
            </span>
        </div>
        
        <div id="account-tree" style="background: rgba(255, 255, 255, 0.05); border-radius: 0 0 8px 8px; padding: 1.25rem; max-height: calc(100vh - 400px); overflow-y: auto; direction: rtl; min-height: 400px; border: 1px solid rgba(255, 255, 255, 0.1); border-top: none; box-shadow: 0 2px 8px rgba(0,0,0,0.2);">
            <!-- سيتم تحميل Tree هنا عبر JavaScript -->
            <div id="tree-initial-loading" style="text-align: center; padding: 2rem; color: #999;">
                <i class="fas fa-spinner fa-spin"></i> جاري تحميل الشجرة...
            </div>
        </div>
    </div>
</div>

<script>
let selectedAccountId = null;
let currentPeriodId = null;
let currentAsOf = null;

    // تحميل Tree عند تحميل الصفحة
document.addEventListener('DOMContentLoaded', function() {
    // التأكد من أن العناصر موجودة قبل التحميل
    if (document.getElementById('account-tree')) {
        loadTree();
    } else {
        console.error('Tree container not found!');
        setTimeout(loadTree, 500);
    }
    
    @if(isset($selectedAccount) && $selectedAccount)
        setTimeout(() => {
            selectAccount({{ $selectedAccount->id }});
        }, 1000);
    @endif
});

// تحميل Tree
function loadTree() {
    currentPeriodId = document.getElementById('filter-period')?.value || null;
    currentAsOf = document.getElementById('filter-as-of')?.value || null;
    
    const treeContainer = document.getElementById('account-tree');
    const loadingIndicator = document.getElementById('tree-loading');
    const initialLoading = document.getElementById('tree-initial-loading');
    
    if (loadingIndicator) loadingIndicator.style.display = 'block';
    if (initialLoading) initialLoading.style.display = 'block';
    if (treeContainer && !initialLoading) {
        treeContainer.innerHTML = '<div style="text-align: center; padding: 2rem; color: #999;"><i class="fas fa-spinner fa-spin"></i> جاري تحميل الشجرة...</div>';
    }
    
    // بناء URL بشكل صحيح
    const routeUrl = '{{ route("wesal.finance.chart-accounts.tree") }}';
    let url = routeUrl;
    const params = [];
    if (currentPeriodId) params.push('period_id=' + encodeURIComponent(currentPeriodId));
    if (currentAsOf) params.push('as_of=' + encodeURIComponent(currentAsOf));
    if (params.length > 0) {
        url += '?' + params.join('&');
    }
    
    console.log('Loading tree from:', url);
    
    fetch(url, {
        method: 'GET',
        headers: {
            'Accept': 'application/json',
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
        },
        credentials: 'same-origin'
    })
        .then(response => {
            console.log('Response status:', response.status);
            if (!response.ok) {
                return response.text().then(text => {
                    console.error('Response error:', text);
                    throw new Error(`HTTP error! status: ${response.status} - ${text.substring(0, 100)}`);
                });
            }
            return response.json();
        })
        .then(data => {
            console.log('Tree data received:', data);
            if (loadingIndicator) loadingIndicator.style.display = 'none';
            if (initialLoading) initialLoading.style.display = 'none';
            if (treeContainer) {
                if (data.success && data.data && Array.isArray(data.data) && data.data.length > 0) {
                    console.log('Rendering tree with', data.data.length, 'root accounts');
                    renderTree(data.data);
                } else {
                    console.warn('No accounts found or empty data', data);
                    treeContainer.innerHTML = '<div style="text-align: center; padding: 2rem; color: #999;">لا توجد حسابات متاحة</div>';
                }
            }
        })
        .catch(error => {
            console.error('Error loading tree:', error);
            if (loadingIndicator) loadingIndicator.style.display = 'none';
            if (initialLoading) initialLoading.style.display = 'none';
            if (treeContainer) {
                treeContainer.innerHTML = `
                    <div style="text-align: center; padding: 2rem; color: #d32f2f;">
                        <i class="fas fa-exclamation-triangle" style="font-size: 2rem; margin-bottom: 1rem;"></i>
                        <p style="margin-bottom: 0.5rem;">حدث خطأ أثناء تحميل الشجرة</p>
                        <p style="font-size: 0.9rem; color: #666; margin-bottom: 0.5rem;">${error.message || 'خطأ غير معروف'}</p>
                        <p style="font-size: 0.8rem; color: #999; margin-bottom: 1rem;">تحقق من Console للمزيد من التفاصيل (F12)</p>
                        <button onclick="loadTree()" style="padding: 0.5rem 1rem; background: var(--primary-color); color: white; border: none; border-radius: 4px; cursor: pointer;">
                            إعادة المحاولة
                        </button>
                    </div>
                `;
            }
        });
}

// رسم Tree
function renderTree(nodes, parentElement = null, level = 0) {
    const container = parentElement || document.getElementById('account-tree');
    if (!container) {
        console.error('Tree container not found!');
        return;
    }
    
    if (!parentElement) {
        // إزالة رسالة التحميل الأولية
        const initialLoading = document.getElementById('tree-initial-loading');
        if (initialLoading) initialLoading.remove();
        
        container.innerHTML = '';
        if (!nodes || nodes.length === 0) {
            container.innerHTML = '<div style="text-align: center; padding: 2rem; color: #999;">لا توجد حسابات متاحة</div>';
            return;
        }
        console.log('Starting to render tree with', nodes.length, 'root nodes');
        
        // فتح جميع المستويات الجذرية تلقائياً
        // سيتم فتحها تلقائياً في renderTree لأن childrenContainer.display = 'block'
    }
    
    nodes.forEach(node => {
        const item = document.createElement('div');
        item.className = 'tree-item';
        item.dataset.accountId = node.id;
        item.style.cssText = `
            padding: 0.75rem 1rem;
            margin: 0.15rem 0;
            border-radius: 6px;
            cursor: pointer;
            transition: all 0.2s ease;
            padding-right: ${level * 2.5 + 1}rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            direction: rtl;
            text-align: right;
            background: ${level % 2 === 0 ? 'rgba(255, 255, 255, 0.05)' : 'rgba(255, 255, 255, 0.08)'};
            border: 1px solid rgba(255, 255, 255, 0.1);
        `;
        
        // خط منقط يربط الأب بالأبناء
        if (level > 0) {
            item.style.borderRight = '2px dotted rgba(255, 255, 255, 0.2)';
            item.style.marginRight = '0.75rem';
        }
        
        // Highlight إذا كان محدد
        if (selectedAccountId == node.id) {
            item.style.background = 'rgba(95, 179, 142, 0.3)';
            item.style.borderRight = '4px solid var(--primary-color)';
            item.style.borderColor = 'var(--primary-color)';
            item.style.boxShadow = '0 2px 4px rgba(95, 179, 142, 0.4)';
        }
        
        // حتى المستوى الخامس = مجلدات، بعد ذلك = ملفات
        const isFolder = (node.level != null && node.level <= 5) || node.has_children;
        const icon = document.createElement('i');
        if (isFolder) {
            icon.className = 'fas fa-folder';
            icon.style.cssText = 'color: #f9a825; font-size: 1rem; margin-left: 0.5rem;';
        } else {
            icon.className = 'fas fa-file-invoice';
            icon.style.cssText = 'color: var(--text-secondary); font-size: 0.9rem; margin-left: 0.5rem;';
        }
        
        // نص الحساب
        const text = document.createElement('span');
        text.style.flex = '1';
        text.style.color = 'var(--text-primary)';
        text.style.fontSize = '0.95rem';
        text.style.lineHeight = '1.6';
        const balanceValue = node.raw_balance || 0;
        const balanceColor = balanceValue < 0 ? '#d32f2f' : '#388e3c';
        // تنسيق الرصيد: سالب بين قوسين، موجب بدون قوسين
        const balanceDisplay = balanceValue < 0 
            ? `(${formatNumber(Math.abs(balanceValue))})` 
            : formatNumber(balanceValue);
        text.innerHTML = `
            <strong style="color: var(--primary-color); font-size: 1rem;">${node.code}</strong> - 
            <span style="color: var(--text-primary); font-weight: 500;">${node.name_ar}</span> - 
            <span style="color: ${balanceValue < 0 ? '#ff6b6b' : 'var(--primary-color)'}; font-weight: 600; font-size: 0.95rem;">
                رصيد الحساب ${balanceDisplay}
            </span>
        `;
        
        // زر فتح/إغلاق للأبناء (مغلق في البداية - مثل الصورة: + في مربع)
        let toggleBtn = null;
        if (node.has_children) {
            toggleBtn = document.createElement('button');
            toggleBtn.className = 'tree-toggle';
            toggleBtn.innerHTML = '<i class="fas fa-plus"></i>'; // مغلق - علامة +
            toggleBtn.style.cssText = 'background: rgba(255, 255, 255, 0.1); border: 1px solid rgba(255, 255, 255, 0.2); color: var(--text-primary); cursor: pointer; padding: 0.2rem 0.4rem; font-size: 0.7rem; margin-right: 0.5rem; border-radius: 3px; transition: all 0.2s; width: 20px; height: 20px; display: flex; align-items: center; justify-content: center;';
            toggleBtn.onmouseover = function() { this.style.background = 'rgba(255, 255, 255, 0.2)'; this.style.borderColor = 'var(--primary-color)'; };
            toggleBtn.onmouseout = function() { this.style.background = 'rgba(255, 255, 255, 0.1)'; this.style.borderColor = 'rgba(255, 255, 255, 0.2)'; };
            toggleBtn.onclick = (e) => {
                e.stopPropagation();
                toggleChildren(item, node.id);
            };
        }
        
        // ترتيب العناصر: زر التبديل (إن وجد) -> أيقونة -> نص
        if (toggleBtn) item.appendChild(toggleBtn);
        item.appendChild(icon);
        item.appendChild(text);
        
        // حدث النقر - فتح/إغلاق عند الضغط على العنصر أو اختيار الحساب
        item.onclick = (e) => {
            // إذا كان هناك زر تبديل وتم الضغط عليه، لا نفعل شيء (تم التعامل معه في toggleBtn.onclick)
            if (e.target === toggleBtn || toggleBtn?.contains(e.target)) {
                return;
            }
            
            // إذا كان هناك أبناء، فتح/إغلاق عند الضغط على العنصر
            if (node.has_children && toggleBtn) {
                toggleChildren(item, node.id);
            }
            
            // اختيار الحساب
            selectAccount(node.id);
        };
        
        container.appendChild(item);
        
        // حاوية الأبناء (مغلقة في البداية - تفتح عند الضغط)
        if (node.has_children && node.children && node.children.length > 0) {
            const childrenContainer = document.createElement('div');
            childrenContainer.className = `children-container-${node.id}`;
            childrenContainer.style.display = 'none'; // مغلقة في البداية
            childrenContainer.style.paddingRight = '1.25rem';
            childrenContainer.style.marginRight = '0.75rem';
            childrenContainer.style.borderRight = level > 0 ? '2px solid #e3f2fd' : 'none';
            container.appendChild(childrenContainer);
            // رسم الأبناء (لكنهم مخفيين)
            renderTree(node.children, childrenContainer, level + 1);
            
            // تحديث زر التبديل ليكون مغلقاً (مثلث لليمين)
            if (toggleBtn) {
                const icon = toggleBtn.querySelector('i');
                if (icon) {
                    icon.className = 'fas fa-chevron-left'; // مغلق - مثلث لليمين
                }
            }
        }
    });
    
    if (!parentElement) {
        console.log('Tree rendering completed');
    }
}

// فتح/إغلاق الأبناء
function toggleChildren(parentItem, accountId) {
    const childrenContainer = document.querySelector(`.children-container-${accountId}`);
    if (childrenContainer) {
        const isHidden = childrenContainer.style.display === 'none' || childrenContainer.style.display === '';
        childrenContainer.style.display = isHidden ? 'block' : 'none';
        
        const toggleBtn = parentItem.querySelector('.tree-toggle i');
        if (toggleBtn) {
            toggleBtn.className = isHidden ? 'fas fa-minus' : 'fas fa-plus';
        }
        const folderIcon = parentItem.querySelector('i.fa-folder, i.fa-folder-open');
        if (folderIcon) {
            folderIcon.className = isHidden ? 'fas fa-folder-open' : 'fas fa-folder';
        }
        
        // إضافة تأثير سلس للفتح/الإغلاق
        if (isHidden) {
            childrenContainer.style.opacity = '0';
            childrenContainer.style.transform = 'translateX(-10px)';
            setTimeout(() => {
                childrenContainer.style.transition = 'opacity 0.3s ease, transform 0.3s ease';
                childrenContainer.style.opacity = '1';
                childrenContainer.style.transform = 'translateX(0)';
            }, 10);
        }
    }
}

// اختيار حساب
function selectAccount(accountId) {
    selectedAccountId = accountId;
    
    // تحديث Highlight في Tree
    document.querySelectorAll('.tree-item').forEach(item => {
        item.style.background = '';
        item.style.borderRight = '';
    });
    
    // البحث عن العنصر المحدد
    const allItems = Array.from(document.querySelectorAll('.tree-item'));
    const selectedItem = allItems.find(item => {
        return item.dataset.accountId == accountId;
    });
    
    if (selectedItem) {
        selectedItem.style.background = 'rgba(95, 179, 142, 0.3)';
        selectedItem.style.borderRight = '3px solid var(--primary-color)';
    }
    
    // جلب تفاصيل الحساب
    const baseUrl = '{{ route("wesal.finance.chart-accounts.index") }}';
    const url = new URL(`${baseUrl}/${accountId}/details`);
    if (currentPeriodId) url.searchParams.set('period_id', currentPeriodId);
    if (currentAsOf) url.searchParams.set('as_of', currentAsOf);
    
    fetch(url)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                displayAccountDetails(data.account, data.balance);
            }
        })
        .catch(error => {
            console.error('Error loading account details:', error);
        });
}

// عرض تفاصيل الحساب
function displayAccountDetails(account, balance) {
    document.getElementById('no-account-selected').style.display = 'none';
    document.getElementById('account-details-card').style.display = 'block';
    
    document.getElementById('account-name').textContent = account.name_ar;
    document.getElementById('account-code').textContent = account.code;
    document.getElementById('account-level').textContent = account.level;
    document.getElementById('account-nature').textContent = account.nature === 'debit' ? 'مدين' : 'دائن';
    document.getElementById('account-type').textContent = account.is_postable ? 'حساب فرعي' : 'حساب رئيسي';
    document.getElementById('account-balance').textContent = formatNumber(balance.raw_balance);
    document.getElementById('account-balance').style.color = balance.raw_balance >= 0 ? 'var(--primary-color)' : '#ff6b6b';
    document.getElementById('account-balance-type').textContent = balance.balance_type === 'debit' ? 'مدين' : 'دائن';
    document.getElementById('account-created-at').textContent = account.created_at ? new Date(account.created_at).toLocaleDateString('ar-SA') : '-';
    document.getElementById('account-status').textContent = account.status === 'active' ? 'مفعل' : 'غير مفعل';
    
    // تحديث الأزرار
    const baseUrl = '{{ route("wesal.finance.chart-accounts.index") }}';
    document.getElementById('btn-edit-account').onclick = () => editAccount(account.id);
    document.getElementById('btn-add-child').onclick = () => addChildAccount(account.id);
    const ledgerUrl = `${baseUrl}/${account.id}/ledger`;
    const ledgerParams = new URLSearchParams();
    if (currentPeriodId) ledgerParams.set('period_id', currentPeriodId);
    if (currentAsOf) ledgerParams.set('as_of', currentAsOf);
    document.getElementById('btn-ledger').href = ledgerUrl + (ledgerParams.toString() ? '?' + ledgerParams.toString() : '');
    
    // إخفاء أزرار التعديل للحسابات الثابتة
    if (account.is_fixed) {
        document.getElementById('btn-edit-account').style.display = 'none';
    } else {
        document.getElementById('btn-edit-account').style.display = 'inline-flex';
    }
    
    // إظهار زر "إضافة حساب فرعي" لجميع الحسابات
    document.getElementById('btn-add-child').style.display = 'inline-flex';
}

// تنسيق الأرقام
function formatNumber(num) {
    return new Intl.NumberFormat('ar-SA', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
    }).format(num);
}

// تعديل حساب
function editAccount(accountId) {
    if (!accountId) accountId = selectedAccountId;
    if (!accountId) {
        alert('يرجى اختيار حساب أولاً');
        return;
    }
    const baseUrl = '{{ route("wesal.finance.chart-accounts.index") }}';
    window.location.href = `${baseUrl}/${accountId}/edit`;
}

// إضافة حساب ابن
function addChildAccount(parentId) {
    if (!parentId) parentId = selectedAccountId;
    if (!parentId) {
        alert('يرجى اختيار حساب أولاً');
        return;
    }
    const createUrl = '{{ route("wesal.finance.chart-accounts.create") }}';
    window.location.href = `${createUrl}?parent_id=${parentId}`;
}
</script>

<style>
.tree-item {
    color: var(--text-primary);
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
}

.tree-item:hover {
    background: rgba(255, 255, 255, 0.15) !important;
    border-color: var(--primary-color) !important;
    transform: translateX(-2px);
    box-shadow: 0 2px 4px rgba(0,0,0,0.3) !important;
}

.tree-item.selected {
    background: rgba(95, 179, 142, 0.3) !important;
    border-right: 4px solid var(--primary-color) !important;
    border-color: var(--primary-color) !important;
    box-shadow: 0 2px 6px rgba(95, 179, 142, 0.4) !important;
}

#account-tree {
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
}

/* Scrollbar محسّن */
#account-tree::-webkit-scrollbar {
    width: 10px;
}

#account-tree::-webkit-scrollbar-track {
    background: #f5f5f5;
    border-radius: 5px;
}

#account-tree::-webkit-scrollbar-thumb {
    background: #bdbdbd;
    border-radius: 5px;
    border: 2px solid #f5f5f5;
}

#account-tree::-webkit-scrollbar-thumb:hover {
    background: #9e9e9e;
}

/* تحسين الأزرار */
.tree-toggle:hover {
    background: #e0e0e0 !important;
    border-color: #bdbdbd !important;
    transform: scale(1.05);
}

/* تحسين الأيقونات */
.tree-item i.fa-folder {
    filter: drop-shadow(0 1px 2px rgba(33, 150, 243, 0.3));
}

/* تحسين النصوص */
.tree-item strong {
    text-shadow: 0 1px 1px rgba(0,0,0,0.05);
}
</style>
