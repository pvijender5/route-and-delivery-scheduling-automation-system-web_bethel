@props(['deliveryWindow'])

<td>
    @if (isset($deliveryWindow[0]['start']) && isset($deliveryWindow[0]['end']))
        @php
            $startTime = \Carbon\Carbon::parse($deliveryWindow[0]['start'])->format('h:i A');
            $endTime = \Carbon\Carbon::parse($deliveryWindow[0]['end'])->format('h:i A');
        @endphp
        {{ $startTime }} - {{ $endTime }}
    @else
        N/A
    @endif
</td> 