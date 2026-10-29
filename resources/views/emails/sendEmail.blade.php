@component('mail::message')
# Hi, {!! $superAdmin->name !!}

## Please find below route details

@foreach ($routeDetails as $route)
- **Route ID:** {!! $route['route_id'] !!}
- **Shipments:** {!! $route['shipments'] !!}
- **Loaded Miles:** {!! $route['loaded_miles'] !!}
- **Payment:** {!! $route['payment'] !!}
- **DriverName:** {!! $user->name !!}
@endforeach

Thanks,<br>
{{ config('app.name') }}
@endcomponent
