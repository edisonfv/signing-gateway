<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>Prueba de vida (local) · {{ config('app.name') }}</title>
        @viteReactRefresh
        @vite(['resources/css/app.css', 'resources/js/dev/liveness.jsx'])
    </head>
    <body class="min-h-screen bg-zinc-50 text-zinc-900">
        <main class="mx-auto max-w-3xl p-6 space-y-6">
            <header class="space-y-1">
                <p class="text-xs font-semibold uppercase tracking-wide text-amber-700">Solo entorno local · sandbox de FirmaSegura</p>
                <h1 class="text-2xl font-semibold">Prueba de vida del Flujo por API</h1>
                <p class="text-sm text-zinc-600">
                    Simula al SaaS: pide la sesión de prueba de vida, abre la cámara con el SDK de AWS Amplify,
                    envía el resultado y, si coincide, emite el certificado.
                </p>
            </header>

            <section class="rounded-lg border border-zinc-200 bg-white p-4">
                <h2 class="mb-2 font-medium">Solicitudes esperando prueba de vida</h2>
                @forelse ($pending as $request)
                    <a href="{{ route('dev.liveness.show', $request) }}"
                       class="block rounded px-2 py-1 text-sm hover:bg-zinc-100 {{ $selected?->is($request) ? 'bg-zinc-100 font-medium' : '' }}">
                        {{ $request->id }} · {{ $request->status->value }} · {{ $request->created_at->diffForHumans() }}
                    </a>
                @empty
                    <p class="text-sm text-zinc-600">No hay solicitudes en AWAITING_APPLICANT ni FAILED_RETRYABLE. Cree una con POST /api/v1/signature-sessions.</p>
                @endforelse
            </section>

            @if ($selected)
                <section id="liveness-root"
                         data-request-id="{{ $selected->id }}"
                         data-status="{{ $selected->status->value }}"
                         data-session-url="{{ route('dev.liveness.session', $selected) }}"
                         data-result-url="{{ route('dev.liveness.result', $selected) }}"></section>
            @endif
        </main>
    </body>
</html>
