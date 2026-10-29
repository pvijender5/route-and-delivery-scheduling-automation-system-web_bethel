<div class="table-responsive">
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Sr. No.</th>
                <th>Date/Time</th>
                <th>User</th>
                <th>Event Type</th>
                <th>Event Description</th>
                <!-- ... Add more table headers if needed ... -->
            </tr>
        </thead>
        <tbody>
            <!-- Loop through shipments data and populate table rows -->
            @forelse ($shipmentsActivitylogData as $index => $activity)
            <tr>
                <td>{{ $index + 1 }}</td> <!-- Serial number (Sl No) -->
                <td>{{ date('m/d/Y H:i:s', strtotime($activity->created_at)) }}</td>
                <td>{{ isset($activity->user) ? $activity->user->name : "N/A" }}</td>
                <td>{{ $activity->event_type }}</td>
                <td>{!! $activity->event_description !!}</td>
                <!-- ... Populate additional table cells ... -->
            </tr>
            @empty
            <!-- This block will be executed if no records are found -->
            <tr>
                <td colspan="5">No records found.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>