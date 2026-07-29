@php
    $flashMessages = collect([
        'success' => session('success'),
        'error' => session('error'),
        'warning' => session('warning'),
        'info' => session('status'),
    ])->filter();
@endphp

@foreach ($flashMessages as $type => $message)
    <div class="admin-flash admin-flash-{{ $type }}" role="status" data-flash-alert>
        <span class="admin-flash-icon" aria-hidden="true">
            <i @class([
                'fa-solid',
                'fa-circle-check' => $type === 'success',
                'fa-circle-exclamation' => $type === 'error',
                'fa-triangle-exclamation' => $type === 'warning',
                'fa-circle-info' => $type === 'info',
            ])></i>
        </span>
        <p>{{ $message }}</p>
        <button type="button" data-alert-dismiss aria-label="Tutup pemberitahuan">
            <i class="fa-solid fa-xmark" aria-hidden="true"></i>
        </button>
    </div>
@endforeach

@if ($errors->any())
    <div class="admin-flash admin-flash-error admin-flash-validation" role="alert" data-flash-alert>
        <span class="admin-flash-icon" aria-hidden="true">
            <i class="fa-solid fa-circle-exclamation" aria-hidden="true"></i>
        </span>
        <div>
            <p><strong>Periksa kembali data yang dikirim.</strong></p>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        <button type="button" data-alert-dismiss aria-label="Tutup pemberitahuan">
            <i class="fa-solid fa-xmark" aria-hidden="true"></i>
        </button>
    </div>
@endif
