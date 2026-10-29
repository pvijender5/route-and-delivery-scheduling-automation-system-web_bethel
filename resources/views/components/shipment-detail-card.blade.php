@props(['title', 'value', 'icon'])

<div class="col-sm-12 col-md-6 col-lg-4 mb-3">
    <div class="ttl-info balance-card text-start light-card p-3 rounded">
        <div class="svg-box">
            @if(Str::startsWith($icon, '<svg'))
                {!! $icon !!}
            @else
                <i data-feather="{{ $icon }}"></i>
            @endif
        </div>
        <div>
            <span class="f-light">{{ $title }}</span>
            <h6 class="mt-1 mb-0">{{ $value }}</h6>
            @if($slot->isNotEmpty())
                {{ $slot }}
            @endif
        </div>
    </div>
</div> 