<div class="content-card">
    <div class="page-header">
        <h1 class="page-title">
            <i class="fas fa-sort"></i> ترتيب الأقسام
        </h1>
        <p class="page-subtitle">ترتيب وإظهار/إخفاء أقسام الموقع</p>
    </div>

    <div class="content-card" style="background: rgba(255, 255, 255, 0.08); margin-bottom: 2rem; padding: 2rem;">
        <p style="color: var(--text-secondary); margin-bottom: 1.5rem; line-height: 1.8;">
            <i class="fas fa-info-circle" style="color: var(--primary-color); margin-left: 0.5rem;"></i>
            يمكنك ترتيب الأقسام عن طريق السحب والإفلات، أو استخدام الأزرار للتحريك. يمكنك أيضاً إظهار أو إخفاء أي قسم.
        </p>
        <p style="color: var(--text-secondary); font-size: 0.9rem;">
            <strong>ملاحظة:</strong> الهيدر والهيرو والفوتر ثابتة ولا يمكن ترتيبها.
        </p>
    </div>

    <div id="section-order-container" class="content-card" style="background: rgba(255, 255, 255, 0.08);">
        <h2 style="margin-bottom: 1.5rem; color: var(--text-primary);">
            <i class="fas fa-list"></i> الأقسام
        </h2>

        <div id="sections-list" style="display: flex; flex-direction: column; gap: 1rem;">
            @if(isset($sections) && $sections->count() > 0)
                @foreach($sections as $section)
                <div class="section-item" 
                     data-key="{{ $section->section_key }}" 
                     data-order="{{ $section->order }}"
                     style="background: rgba(255, 255, 255, 0.05); border: 2px solid rgba(255, 255, 255, 0.1); border-radius: 15px; padding: 1.5rem; display: flex; align-items: center; justify-content: space-between; gap: 1.5rem; cursor: move; transition: all 0.3s ease;">
                    <div style="display: flex; align-items: center; gap: 1.5rem; flex: 1;">
                        <div class="drag-handle" style="color: var(--text-secondary); font-size: 1.5rem; cursor: grab;">
                            <i class="fas fa-grip-vertical"></i>
                        </div>
                        <div style="flex: 1;">
                            <h3 style="color: var(--text-primary); margin: 0 0 0.5rem; font-size: 1.2rem; font-weight: 600;">
                                {{ $section->section_name }}
                            </h3>
                            <p style="color: var(--text-secondary); margin: 0; font-size: 0.9rem;">
                                الترتيب الحالي: <strong>{{ $section->order }}</strong>
                            </p>
                        </div>
                    </div>
                    <div style="display: flex; align-items: center; gap: 1rem;">
                        <button type="button" class="btn-move-up" 
                                style="width: 40px; height: 40px; border-radius: 50%; background: rgba(95, 179, 142, 0.2); border: 1px solid rgba(95, 179, 142, 0.3); color: var(--primary-color); cursor: pointer; display: flex; align-items: center; justify-content: center; transition: all 0.3s ease;"
                                title="نقل للأعلى">
                            <i class="fas fa-arrow-up"></i>
                        </button>
                        <button type="button" class="btn-move-down"
                                style="width: 40px; height: 40px; border-radius: 50%; background: rgba(95, 179, 142, 0.2); border: 1px solid rgba(95, 179, 142, 0.3); color: var(--primary-color); cursor: pointer; display: flex; align-items: center; justify-content: center; transition: all 0.3s ease;"
                                title="نقل للأسفل">
                            <i class="fas fa-arrow-down"></i>
                        </button>
                        <label style="display: flex; align-items: center; gap: 0.75rem; cursor: pointer; padding: 0.75rem 1.5rem; background: rgba(255, 255, 255, 0.05); border-radius: 25px; border: 1px solid rgba(255, 255, 255, 0.1); transition: all 0.3s ease;">
                            <input type="checkbox" class="section-visibility" 
                                   {{ $section->is_visible ? 'checked' : '' }}
                                   style="width: 20px; height: 20px; cursor: pointer;">
                            <span style="color: var(--text-primary); font-weight: 600;">
                                {{ $section->is_visible ? 'ظاهر' : 'مخفي' }}
                            </span>
                        </label>
                    </div>
                </div>
                @endforeach
            @else
                <div style="text-align: center; padding: 3rem; color: var(--text-secondary);">
                    <i class="fas fa-info-circle" style="font-size: 3rem; margin-bottom: 1rem; opacity: 0.5;"></i>
                    <p style="font-size: 1.1rem; margin-bottom: 1rem;">لا توجد أقسام متاحة</p>
                    <p style="font-size: 0.9rem; opacity: 0.7;">يرجى التأكد من وجود بيانات في جدول section_order</p>
                </div>
            @endif
        </div>

        <div style="margin-top: 2rem; padding-top: 2rem; border-top: 1px solid rgba(255, 255, 255, 0.1); display: flex; gap: 1rem; justify-content: flex-end; align-items: center;">
            <button type="button" id="reset-default-btn" class="btn" style="font-size: 1.1rem; padding: 1rem 2rem; background: rgba(95, 179, 142, 0.15); border: 2px solid rgba(95, 179, 142, 0.4); color: var(--primary-color); cursor: pointer; border-radius: 10px; transition: all 0.3s ease; font-weight: 600;">
                <i class="fas fa-undo"></i> إعادة الإعدادات الافتراضية
            </button>
            <button type="button" id="save-order-btn" class="btn btn-primary" style="font-size: 1.1rem; padding: 1rem 2rem;">
                <i class="fas fa-save"></i> حفظ الترتيب
            </button>
        </div>
    </div>
</div>

<!-- SortableJS Library -->
<script src="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const container = document.getElementById('sections-list');
    const saveBtn = document.getElementById('save-order-btn');
    let isDirty = false;

    // Initialize Sortable
    const sortable = Sortable.create(container, {
        handle: '.drag-handle',
        animation: 150,
        ghostClass: 'sortable-ghost',
        chosenClass: 'sortable-chosen',
        onEnd: function() {
            updateOrderNumbers();
            isDirty = true;
        }
    });

    // Update order numbers
    function updateOrderNumbers() {
        const items = container.querySelectorAll('.section-item');
        items.forEach((item, index) => {
            item.setAttribute('data-order', index + 1);
            const orderText = item.querySelector('p strong');
            if (orderText) {
                orderText.textContent = index + 1;
            }
        });
    }

    // Move up button - use event delegation
    container.addEventListener('click', function(e) {
        if (e.target.closest('.btn-move-up')) {
            const btn = e.target.closest('.btn-move-up');
            const item = btn.closest('.section-item');
            const prevItem = item.previousElementSibling;
            if (prevItem && prevItem.classList.contains('section-item')) {
                container.insertBefore(item, prevItem);
                updateOrderNumbers();
                isDirty = true;
            }
        }
        
        if (e.target.closest('.btn-move-down')) {
            const btn = e.target.closest('.btn-move-down');
            const item = btn.closest('.section-item');
            const nextItem = item.nextElementSibling;
            if (nextItem && nextItem.classList.contains('section-item')) {
                container.insertBefore(nextItem, item);
                updateOrderNumbers();
                isDirty = true;
            }
        }
    });

    // Visibility toggle
    container.querySelectorAll('.section-visibility').forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            const label = this.closest('label');
            const span = label.querySelector('span');
            span.textContent = this.checked ? 'ظاهر' : 'مخفي';
            isDirty = true;
        });
    });

    // Save order
    saveBtn.addEventListener('click', function() {
        if (!isDirty) {
            alert('لم يتم إجراء أي تغييرات');
            return;
        }

        const items = container.querySelectorAll('.section-item');
        const sections = Array.from(items).map((item, index) => {
            const checkbox = item.querySelector('.section-visibility');
            const sectionKey = item.getAttribute('data-key');
            
            if (!sectionKey) {
                console.error('Section key not found for item:', item);
                return null;
            }
            
            return {
                key: sectionKey,
                order: index + 1,
                visible: checkbox ? checkbox.checked : true
            };
        }).filter(section => section !== null);
        
        if (sections.length === 0) {
            alert('لا توجد أقسام لحفظها');
            saveBtn.disabled = false;
            saveBtn.innerHTML = '<i class="fas fa-save"></i> حفظ الترتيب';
            return;
        }

        saveBtn.disabled = true;
        saveBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> جاري الحفظ...';

        fetch('{{ route("dashboard.section-order.update") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify({ sections: sections })
        })
        .then(response => {
            if (!response.ok) {
                return response.json().then(data => {
                    throw new Error(data.message || 'خطأ في الطلب');
                });
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                alert('تم حفظ الترتيب بنجاح! سيتم تحديث الواجهة الأمامية تلقائياً.');
                isDirty = false;
                // إعادة تحميل الصفحة بعد ثانية واحدة
                setTimeout(() => {
                    location.reload();
                }, 1000);
            } else {
                alert('حدث خطأ: ' + (data.message || 'خطأ غير معروف'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('حدث خطأ أثناء الحفظ: ' + error.message);
        })
        .finally(() => {
            saveBtn.disabled = false;
            saveBtn.innerHTML = '<i class="fas fa-save"></i> حفظ الترتيب';
        });
    });

    // Reset to default order
    const resetBtn = document.getElementById('reset-default-btn');
    resetBtn.addEventListener('click', function() {
        if (!confirm('هل أنت متأكد من إعادة الإعدادات الافتراضية لترتيب الأقسام؟ سيتم إعادة تعيين الترتيب والإظهار إلى القيم الافتراضية.')) {
            return;
        }

        resetBtn.disabled = true;
        resetBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> جاري إعادة التعيين...';

        fetch('{{ route("dashboard.section-order.reset") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}',
                'Accept': 'application/json'
            }
        })
        .then(response => {
            if (!response.ok) {
                return response.json().then(data => {
                    throw new Error(data.message || 'خطأ في الطلب');
                });
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                alert('تم إعادة الإعدادات الافتراضية بنجاح! سيتم تحديث الصفحة الآن.');
                location.reload();
            } else {
                alert('حدث خطأ: ' + (data.message || 'خطأ غير معروف'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('حدث خطأ أثناء إعادة التعيين: ' + error.message);
        })
        .finally(() => {
            resetBtn.disabled = false;
            resetBtn.innerHTML = '<i class="fas fa-undo"></i> إعادة الإعدادات الافتراضية';
        });
    });
});
</script>

<style>
.sortable-ghost {
    opacity: 0.4;
    background: rgba(95, 179, 142, 0.2) !important;
}

.sortable-chosen {
    background: rgba(95, 179, 142, 0.1) !important;
}

.section-item:hover {
    border-color: var(--primary-color) !important;
    transform: translateX(-5px);
}

.btn-move-up:hover,
.btn-move-down:hover {
    background: var(--primary-color) !important;
    border-color: var(--primary-color) !important;
    color: white !important;
    transform: scale(1.1);
}

#reset-default-btn:hover {
    background: rgba(95, 179, 142, 0.25) !important;
    border-color: var(--primary-color) !important;
    color: var(--primary-dark) !important;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(95, 179, 142, 0.3);
}

#reset-default-btn:disabled {
    opacity: 0.6;
    cursor: not-allowed;
}

.drag-handle:active {
    cursor: grabbing;
}
</style>
