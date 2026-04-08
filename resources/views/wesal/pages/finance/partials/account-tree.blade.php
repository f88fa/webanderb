@foreach($accounts as $account)
    <div style="margin: 0.5rem 0; padding: 1rem; background: rgba(255, 255, 255, 0.05); border-radius: 8px; border-right: 3px solid {{ $account->status == 'active' ? 'var(--primary-color)' : 'rgba(255, 255, 255, 0.3)' }};">
        <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
            <div style="display: flex; align-items: center; gap: 1rem; flex: 1;">
                <div style="padding-right: {{ $level * 1.5 }}rem;">
                    @if(isset($account->children_list) && count($account->children_list) > 0)
                        <i class="fas fa-folder" style="color: var(--primary-color);"></i>
                    @else
                        <i class="fas fa-file-invoice" style="color: var(--text-secondary);"></i>
                    @endif
                </div>
                <div>
                    <div style="display: flex; align-items: center; gap: 0.75rem;">
                        <strong style="color: var(--text-primary);">{{ $account->code }}</strong>
                        <span style="color: var(--text-primary); font-weight: 600;">{{ $account->name_ar }}</span>
                        @if($account->is_fixed)
                            <span style="padding: 0.25rem 0.5rem; background: rgba(95, 179, 142, 0.2); color: var(--primary-color); border-radius: 4px; font-size: 0.75rem;">ثابت</span>
                        @endif
                        @if($account->is_postable)
                            <span style="padding: 0.25rem 0.5rem; background: rgba(95, 179, 142, 0.2); color: var(--primary-color); border-radius: 4px; font-size: 0.75rem;">قابل للترحيل</span>
                        @endif
                        @if($account->status == 'inactive')
                            <span style="padding: 0.25rem 0.5rem; background: rgba(255, 0, 0, 0.2); color: #ff6b6b; border-radius: 4px; font-size: 0.75rem;">غير نشط</span>
                        @endif
                    </div>
                    <div style="margin-top: 0.25rem; font-size: 0.85rem; color: var(--text-secondary);">
                        <span>{{ ucfirst($account->type) }} - {{ $account->nature == 'debit' ? 'مدين' : 'دائن' }}</span>
                    </div>
                </div>
            </div>
            <div style="display: flex; gap: 0.5rem;">
                <a href="{{ route('wesal.finance.chart-accounts.show', $account) }}" style="padding: 0.5rem 1rem; background: rgba(255, 255, 255, 0.1); color: var(--text-primary); text-decoration: none; border-radius: 6px; font-size: 0.9rem;">
                    <i class="fas fa-eye"></i> عرض
                </a>
                @if(!$account->is_fixed)
                    <a href="{{ route('wesal.finance.chart-accounts.edit', $account) }}" style="padding: 0.5rem 1rem; background: rgba(255, 255, 255, 0.1); color: var(--text-primary); text-decoration: none; border-radius: 6px; font-size: 0.9rem;">
                        <i class="fas fa-edit"></i> تعديل
                    </a>
                @endif
            </div>
        </div>
    </div>
    
    @if(isset($account->children_list) && count($account->children_list) > 0)
        <div style="padding-right: 1.5rem;">
            @include('wesal.pages.finance.partials.account-tree', ['accounts' => $account->children_list, 'level' => $level + 1])
        </div>
    @endif
@endforeach
