<x-mail::message>
<p>Hi {{ $customerName }},</p>

<p>Your package is ready for pickup. Here are the tracking numbers:</p>
<ul>
@foreach ($trackingNumbers as $tracking)
    <li>{{ $tracking }}</li>
@endforeach
</ul>

@if (!empty($imageUrls))
    <p><strong>Attached Image{{ count($imageUrls) > 1 ? 's' : '' }}:</strong></p>
    @foreach ($imageUrls as $url)
        <p style="margin-bottom: 10px;">
            <img src="{{ asset($url) }}" alt="Package Image" style="max-width: 300px; height: auto; border: 1px solid #ccc; border-radius: 6px;">
        </p>
    @endforeach
@endif

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
