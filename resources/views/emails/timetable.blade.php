@component('mail::message')
# Timetable

Period: **{{ $startDate }} - {{ $endDate }}**

@component('mail::table')
| Event | Date | Time |
|:------|:-----|:-----|
@foreach($timetableEvents as $event)
| {{ $event['title'] ?? 'Event' }} | {{ $event['date'] ?? '-' }} | {{ $event['time'] ?? '-' }} |
@endforeach
@endcomponent

@endcomponent