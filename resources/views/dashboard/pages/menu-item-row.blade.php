<tr>
    <td>{{ $item->id }}</td>
    <td>
        @if($level > 0)
            <span style="margin-right: {{ $level * 20 }}px; color: rgba(95, 179, 142, 0.6);">└─</span>
        @endif
        {{ $item->title }}
    </td>
    <td>
        @if($item->type === 'link')
            <span class="badge badge-info">رابط</span>
        @elseif($item->type === 'dropdown')
            <span class="badge badge-warning">قائمة منسدلة</span>
        @else
            <span class="badge badge-success">صفحة</span>
        @endif
    </td>
    <td>
        @if($item->type === 'link')
            <a href="{{ $item->url }}" target="_blank" style="color: var(--primary-color);">
                {{ mb_substr($item->url, 0, 30) }}{{ mb_strlen($item->url) > 30 ? '...' : '' }}
            </a>
        @elseif($item->type === 'dropdown')
            <span style="color: rgba(255, 255, 255, 0.5);">قائمة منسدلة ({{ $item->children->count() }} عنصر)</span>
        @else
            <span style="color: rgba(255, 255, 255, 0.5);">
                {{ mb_substr(strip_tags($item->page_content), 0, 30) }}{{ mb_strlen(strip_tags($item->page_content)) > 30 ? '...' : '' }}
            </span>
        @endif
    </td>
    <td>
        @if($item->parent)
            <span style="color: rgba(255, 255, 255, 0.7);">{{ $item->parent->title }}</span>
        @else
            <span style="color: rgba(255, 255, 255, 0.5);">-</span>
        @endif
    </td>
    <td>{{ $item->order }}</td>
    <td>
        @if($item->is_active)
            <span class="badge badge-success">نشط</span>
        @else
            <span class="badge badge-danger">غير نشط</span>
        @endif
    </td>
    <td>
        <a href="{{ website_page_url('menu', ['edit' => $item->id]) }}" 
           class="btn btn-sm btn-primary">
            <i class="fas fa-edit"></i> تعديل
        </a>
        <form method="POST" action="{{ route('dashboard.menu.destroy', $item->id) }}" 
              style="display: inline-block;" 
              onsubmit="return confirm('هل أنت متأكد من حذف هذا العنصر؟ سيتم حذف جميع العناصر الفرعية أيضاً.');">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-sm btn-danger">
                <i class="fas fa-trash"></i> حذف
            </button>
        </form>
    </td>
</tr>
@foreach($menuItems->where('parent_id', $item->id) as $child)
    @include('dashboard.pages.menu-item-row', ['item' => $child, 'level' => $level + 1, 'menuItems' => $menuItems])
@endforeach

