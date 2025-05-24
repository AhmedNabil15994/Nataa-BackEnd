<ul class="submenu dropdown-menu nsted">

    @foreach($children as $k => $category)

        <li class="menu-item menu-item-has-children {{ count($category->children) > 0 ? 'arrowleft' : '' }}">
            <a href="{{ route('frontend.categories.products', $category->slug) }}"
               class="dropdown-toggle"> {{ $category->translate(locale())->title }} </a>
            <span class="toggle-submenu hidden-mobile"></span>

            @if(count($category->children))
                @include('apps::frontend.layouts._nested_categories', ['children' => $category->children])
            @endif

        </li>

    @endforeach

</ul>
