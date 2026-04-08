@if($item->type === 'dropdown')
    <li class="nav-item-dropdown">
        <a href="#" class="nav-link dropdown-toggle">
            {{ $item->title }}
            <i class="fas fa-chevron-down"></i>
        </a>
        @if($item->activeChildren->count() > 0)
            <ul class="dropdown-menu">
                @foreach($item->activeChildren as $child)
                    @if($child->type === 'link')
                        <li>
                            @php
                                $childUrl = $child->url ?? '#';
                                if (!empty($childUrl) && $childUrl !== '#') {
                                    if (strpos($childUrl, 'http://') === 0 || strpos($childUrl, 'https://') === 0) {
                                        $finalUrl = $childUrl;
                                        $isExternal = true;
                                    } elseif (strpos($childUrl, '#') === 0 || strpos($childUrl, '/') === 0) {
                                        $finalUrl = $childUrl;
                                        $isExternal = false;
                                    } else {
                                        $finalUrl = '#' . $childUrl;
                                        $isExternal = false;
                                    }
                                } else {
                                    $finalUrl = '#';
                                    $isExternal = false;
                                }
                            @endphp
                            <a href="{{ $finalUrl }}" class="dropdown-item" 
                               @if($isExternal) target="_blank" rel="noopener noreferrer" @endif>
                                {{ $child->title }}
                            </a>
                        </li>
                    @elseif($child->type === 'page')
                        <li>
                            @php
                                $childPageUrl = $child->url ?? '';
                                $childPageHref = (strpos($childPageUrl, '#') === 0)
                                    ? (url('/') . $childPageUrl)
                                    : url('/' . $childPageUrl);
                            @endphp
                            <a href="{{ $childPageHref }}" class="dropdown-item">
                                {{ $child->title }}
                            </a>
                        </li>
                    @endif
                @endforeach
            </ul>
        @endif
    </li>
@elseif($item->type === 'link')
    @php
        $itemUrl = $item->url ?? '#';
        if (!empty($itemUrl) && $itemUrl !== '#') {
            if (strpos($itemUrl, 'http://') === 0 || strpos($itemUrl, 'https://') === 0) {
                $finalUrl = $itemUrl;
                $isExternal = true;
            } elseif (strpos($itemUrl, '#') === 0 || strpos($itemUrl, '/') === 0) {
                $finalUrl = $itemUrl;
                $isExternal = false;
            } else {
                $finalUrl = '#' . $itemUrl;
                $isExternal = false;
            }
        } else {
            $finalUrl = '#';
            $isExternal = false;
        }
    @endphp
    <li>
        <a href="{{ $finalUrl }}" class="nav-link"
           @if($isExternal) target="_blank" rel="noopener noreferrer" @endif>
            {{ $item->title }}
        </a>
    </li>
@elseif($item->type === 'page')
    @php
        $pageUrl = $item->url ?? '';
        $pageHref = (strpos($pageUrl, '#') === 0)
            ? (url('/') . $pageUrl)
            : url('/' . $pageUrl);
    @endphp
    <li>
        <a href="{{ $pageHref }}" class="nav-link">{{ $item->title }}</a>
    </li>
@endif

