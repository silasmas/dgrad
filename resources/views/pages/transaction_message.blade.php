@extends('models.app')

@section('content')

<div class="card border rounded-0 text-center shadow-0">
    <div class="card-body py-5">

        @if (!empty($status_code))
        @if ($status_code == '0')
        <h1 class="text-success" style="font-size: 5rem;"><span class="fa fa-check-circle"></span></h1>
        @endif

        @if ($status_code == '1')
        <h1 class="text-warning" style="font-size: 5rem;"><span class="fa fa-exclamation-circle"></span></h1>
        @endif

        @if ($status_code == '2')
        <h1 class="text-danger" style="font-size: 5rem;"><span class="fa fa-times-circle"></span></h1>
        @endif
        @endif
        <h3 class="h3 mb-4">{{ $message_content }}</h3>

        @if (!empty($data))
        <div class="card border mb-4 shadow-0">
            <div class="card-body d-flex justify-content-between align-items-center">
                <div class="px-2 py-1 border-start border-3 text-start">
                    <p class="m-0 text-black">{{ $data->reference }}</p>
                    <h4 class="h4 mt-0 mb-1 fw-bold border text-truncate"
                        style="font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif">
                        {{ $data->amount . ' ' . $data->currency }}
                    </h4>
                    <p class="m-0 small">{{ $data->created_at }}</p>
                </div>

                <div class="px-3 py-1 text-center">
                    <p class="m-0 text-black text-uppercase text-truncate">{{ $data->channel }}</p>
                    <div class="badg p-2 rounded-pill fw-normal">
                        {{ $data->etat == '0' ? 'Transaction aboutie' : ($data->etat == '1' ? 'Transaction annulée' : 'Transaction échoué') }}
                    </div>
                </div>
            </div>
        </div>
        @endif

        <a href="{{ route('home') }}" class="btn btn-warning py-3 px-5 rounded-pill shadow-0 detect-webview">
            Retourner à l'accueil</a>

    </div>
</div>

@endsection