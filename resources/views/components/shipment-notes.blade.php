@props(['notes', 'shipmentId'])

<td class="shipment-notes" data-shipment-id="{{ $shipmentId }}" data-notes="{{ $notes[0]['text'] ?? '' }}">
    @if(!empty($notes) && !empty($notes[0]['text']))
        <a class="btn btn-rounded btn-link notes-btn" data-toggle="modal" data-target="#notesModal">
            <i class="fa fa-file-text"></i>
        </a>
    @else
        <a class="btn btn-rounded btn-link notes-btn" data-toggle="modal" data-target="#notesModal">
            <i class="fa fa-plus"></i>
        </a>
    @endif
</td> 