<div class="col-sm-12 col-md-6 col-lg-6 col-xl-6">
    <ol class="breadcrumb justify-content-start">
        @foreach ($breadcrumbs as $key => $breadcrumb)
            @if ($key == 0)
                @if (isset($breadcrumb['url']))
                    <li class="breadcrumb-item"><a href="{{ $breadcrumb['url'] }}">{{ $breadcrumb['name'] }}</a></li>
                @else
                    <li class="breadcrumb-item">{{ $breadcrumb['name'] }}</li>
                @endif
            @else
                @if (isset($breadcrumb['url']))
                    <li class="breadcrumb-item"><a href="{{ $breadcrumb['url'] }}">{{ $breadcrumb['name'] }}</a></li>
                @else
                    @if ($key != count($breadcrumbs) - 1)
                    <li class="breadcrumb-item active">{{ $breadcrumb['name'] }}</li>
                    @else
                    <li class="breadcrumb-item">{{ $breadcrumb['name'] }}</li>
                    @endif
                @endif
            @endif
        @endforeach
    </ol>
</div>