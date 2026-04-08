@php
    $letter = $letter ?? null;
    $isOutgoing = $letter && $letter->direction === 'outgoing';
    $listUrl = $isOutgoing ? route('wesal.communications.outgoing') : route('wesal.communications.incoming');
@endphp
@if($letter)
<div class="content-card" style="padding: 1.5rem;">
    <div class="page-header" style="margin-bottom: 1.5rem; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
        <div>
            <h1 class="page-title">
                <i class="fas {{ $isOutgoing ? 'fa-paper-plane' : 'fa-inbox' }}" style="color: var(--primary-color); margin-left: 0.5rem;"></i>
                {{ $isOutgoing ? 'خطاب صادر' : 'خطاب وارد' }}
            </h1>
            <p class="page-subtitle">{{ $letter->subject }}</p>
        </div>
        <div style="display: flex; gap: 0.5rem; flex-wrap: wrap;">
            <a href="{{ route('wesal.communications.print', $letter) }}" target="_blank" rel="noopener" style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.6rem 1.25rem; background: var(--primary-color); color: white; border: none; border-radius: 8px; text-decoration: none; font-weight: 600;">
                <i class="fas fa-print"></i> طباعة
            </a>
            <a href="{{ $listUrl }}" style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.6rem 1.25rem; background: rgba(255,255,255,0.1); color: var(--text-primary); border: 1px solid var(--border-color); border-radius: 8px; text-decoration: none; font-weight: 600;">
                <i class="fas fa-arrow-right"></i> رجوع
            </a>
        </div>
    </div>

    <div style="background: rgba(255,255,255,0.05); padding: 1.25rem; border-radius: 12px; border: 1px solid var(--border-color);" dir="rtl">
        <table style="width: 100%; border-collapse: collapse;">
            <tr style="border-bottom: 1px solid var(--border-color);">
                <td style="padding: 0.75rem 0; color: var(--text-secondary); width: 140px;">رقم الخطاب</td>
                <td style="padding: 0.75rem 0; color: var(--text-primary);">{{ $letter->letter_no ?: '—' }}</td>
            </tr>
            <tr style="border-bottom: 1px solid var(--border-color);">
                <td style="padding: 0.75rem 0; color: var(--text-secondary);">الموضوع</td>
                <td style="padding: 0.75rem 0; color: var(--text-primary); font-weight: 600;">{{ $letter->subject }}</td>
            </tr>
            <tr style="border-bottom: 1px solid var(--border-color);">
                <td style="padding: 0.75rem 0; color: var(--text-secondary);">التاريخ</td>
                <td style="padding: 0.75rem 0; color: var(--text-primary);">{{ $letter->letter_date?->format('Y-m-d') ?: $letter->created_at->format('Y-m-d') }}</td>
            </tr>
            <tr style="border-bottom: 1px solid var(--border-color);">
                <td style="padding: 0.75rem 0; color: var(--text-secondary);">من</td>
                <td style="padding: 0.75rem 0; color: var(--text-primary);">{{ $letter->from_party ?: '—' }}</td>
            </tr>
            <tr style="border-bottom: 1px solid var(--border-color);">
                <td style="padding: 0.75rem 0; color: var(--text-secondary);">إلى</td>
                <td style="padding: 0.75rem 0; color: var(--text-primary);">{{ $letter->to_party ?: '—' }}</td>
            </tr>
            @if($letter->reference_no)
            <tr style="border-bottom: 1px solid var(--border-color);">
                <td style="padding: 0.75rem 0; color: var(--text-secondary);">الرقم المرجعي</td>
                <td style="padding: 0.75rem 0; color: var(--text-primary);">{{ $letter->reference_no }}</td>
            </tr>
            @endif
        </table>
        @if($letter->body)
            <div style="margin-top: 1.25rem; padding-top: 1rem; border-top: 1px solid var(--border-color);">
                <div style="color: var(--text-secondary); margin-bottom: 0.5rem; font-size: 0.9rem;">محتوى الخطاب</div>
                <div style="color: var(--text-primary); white-space: pre-wrap;">{{ $letter->body }}</div>
            </div>
        @endif
        @if($letter->notes)
            <div style="margin-top: 1rem; padding-top: 0.75rem; color: var(--text-secondary); font-size: 0.9rem;">
                <strong>ملاحظات:</strong> {{ $letter->notes }}
            </div>
        @endif
        <div style="margin-top: 1rem; padding-top: 0.75rem; color: var(--text-secondary); font-size: 0.85rem;">
            سجّل بواسطة: {{ $letter->creator->name ?? '—' }} في {{ $letter->created_at->format('Y-m-d H:i') }}
        </div>
    </div>
</div>
@endif
