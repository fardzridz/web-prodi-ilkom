@php
    $flashMessages = collect([
        'success' => session('success'),
        'error' => session('error'),
        'warning' => session('warning'),
        'info' => session('status'),
    ])->filter();
@endphp

@foreach ($flashMessages as $type => $message)
    <x-admin.alert :variant="$type" :message="$message" class="mb-4" />
@endforeach

@if ($errors->any())
    <x-admin.alert variant="error" title="Periksa kembali data yang dikirim." class="mb-4">
        <ul class="mt-2 list-disc pl-5 text-sm">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </x-admin.alert>
@endif
