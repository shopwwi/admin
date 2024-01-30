<li class="cxd-AsideNav-item @if(!empty($item->children)) is-top is-open @elseif($menuActive == $item->id) is-active @endif" data-key="{{ $item->id }}">
    <a href="{{ !empty($item->path)? shopwwiUserUrl($item->path):'javascript:void(0);' }}">
        @if(!empty($item->children))
            <span class="cxd-AsideNav-itemArrow fa fa-chevron-right"></span>
        @endif
        <span class="cxd-AsideNav-itemLabel" style="pointer-events:none">{{ $item->name }}</span>
    </a>
    @if(!empty($item->children))
        <ul class="cxd-AsideNav-subList">
            @foreach($item->children as $item2)
                @include('user.layouts.menu',['item'=>$item2])
            @endforeach
        </ul>
@endif