@props(['status', 'deletedAt' => null, 'deletedByUser' => null])

@if ($deletedAt)
    <span class="text-danger">
        Deleted at {{ date('m/d h:i:s A', strtotime($deletedAt)) }}
        @if ($deletedByUser)
            by {{ $deletedByUser->name }}
        @endif
    </span>
@else
    @php
        $statusClasses = [
            'new' => 'btn-new',
            'loaded' => 'btn-loaded',
            'planned' => 'btn-planned',
            'delivered' => 'btn-delivered',
            'in-progress' => 'btn-in-progress',
            'driver-assigned' => 'btn-driver-assigned',
            'in-transit-delay' => 'btn-in-transit-delay',
            'in-transit-on-time' => 'btn-in-transit-on-time',
            'posted-to-loadboard' => 'btn-posted-to-loadboard',
            'default' => 'btn-default'
        ];
        
        $statusClass = $statusClasses[strtolower($status)] ?? $statusClasses['default'];
    @endphp
    
    <span class="shipment-status {{ $statusClass }}">
        {{ $status ?? 'N/A' }}
    </span>
@endif 