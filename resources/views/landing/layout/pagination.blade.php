@if ($paginator->hasPages())
    <ul class="pagination justify-content-center text-center mt-space50">
        {{-- Tombol Sebelumnya --}}
        @if ($paginator->onFirstPage())
            <li class="disabled">
                <a href="javascript:void(0)">
                    <i class="bi bi-chevron-left"></i>
                </a>
            </li>
        @else
            <li>
                <a href="{{ $paginator->previousPageUrl() }}">
                    <i class="bi bi-chevron-left"></i>
                </a>
            </li>
        @endif

        {{-- Nomor Halaman --}}
        @foreach ($elements as $element)
            @if (is_string($element))
                <li class="disabled"><a href="javascript:void(0)">{{ $element }}</a></li>
            @endif

            @if (is_array($element))
                @foreach ($element as $page => $url)
                    @if ($page == $paginator->currentPage())
                        <li class="active">
                            <a href="javascript:void(0)">{{ $page }}</a>
                        </li>
                    @else
                        <li>
                            <a href="{{ $url }}">{{ $page }}</a>
                        </li>
                    @endif
                @endforeach
            @endif
        @endforeach

        {{-- Tombol Berikutnya --}}
        @if ($paginator->hasMorePages())
            <li>
                <a href="{{ $paginator->nextPageUrl() }}">
                    <i class="bi bi-chevron-right"></i>
                </a>
            </li>
        @else
            <li class="disabled">
                <a href="javascript:void(0)">
                    <i class="bi bi-chevron-right"></i>
                </a>
            </li>
        @endif
    </ul>
@endif
