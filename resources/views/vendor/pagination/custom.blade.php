@if ($paginator->hasPages())
    <nav>
        <ul class="pagination">
            @php
                $queryParams = request()->except($paginator->getPageName());
                $pageName = $paginator->getPageName();
                $current = $paginator->currentPage();
                $last = $paginator->lastPage();
            @endphp

            {{-- Go to First Page --}}
            <li class="page-item {{ $current == 1 ? 'disabled' : '' }}">
                <a class="page-link" 
                   href="{{ $current == 1 ? '#' : url()->current() . '?' . http_build_query(array_merge($queryParams, [$pageName => 1])) }}"
                   aria-label="First">
                    &laquo;
                </a>
            </li>

            {{-- Previous Page --}}
            <li class="page-item {{ !$paginator->onFirstPage() ? '' : 'disabled' }}">
                <a class="page-link"
                   href="{{ $paginator->onFirstPage() ? '#' : url()->current() . '?' . http_build_query(array_merge($queryParams, [$pageName => $current - 1])) }}"
                   rel="prev" aria-label="@lang('pagination.previous')">&lsaquo;</a>
            </li>

            {{-- Page Numbers --}}
            @foreach ($elements as $element)
                @if (is_string($element))
                    <li class="page-item disabled"><span class="page-link">{{ $element }}</span></li>
                @endif

                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        <li class="page-item {{ $page == $current ? 'active' : '' }}">
                            <a class="page-link"
                               href="{{ url()->current() . '?' . http_build_query(array_merge($queryParams, [$pageName => $page])) }}">
                                {{ $page }}
                            </a>
                        </li>
                    @endforeach
                @endif
            @endforeach

            {{-- Next Page --}}
            <li class="page-item {{ $paginator->hasMorePages() ? '' : 'disabled' }}">
                <a class="page-link"
                   href="{{ $paginator->hasMorePages() ? url()->current() . '?' . http_build_query(array_merge($queryParams, [$pageName => $current + 1])) : '#' }}"
                   rel="next" aria-label="@lang('pagination.next')">&rsaquo;</a>
            </li>

            {{-- Go to Last Page --}}
            <li class="page-item {{ $current == $last ? 'disabled' : '' }}">
                <a class="page-link"
                   href="{{ $current == $last ? '#' : url()->current() . '?' . http_build_query(array_merge($queryParams, [$pageName => $last])) }}"
                   aria-label="Last">
                    &raquo;
                </a>
            </li>
        </ul>
    </nav>
@endif
