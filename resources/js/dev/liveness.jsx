import { StrictMode, useState } from 'react';
import { createRoot } from 'react-dom/client';
import { FaceLivenessDetectorCore } from '@aws-amplify/ui-react-liveness';
import '@aws-amplify/ui-react/styles.css';

const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

async function postJson(url, body = {}) {
    const response = await fetch(url, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', Accept: 'application/json', 'X-CSRF-TOKEN': csrfToken },
        body: JSON.stringify(body),
    });
    const json = await response.json().catch(() => ({}));

    if (!response.ok) {
        throw new Error(json.message || `HTTP ${response.status}`);
    }

    return json;
}

const displayText = {
    hintCenterFaceText: 'Centre su rostro',
    hintMoveFaceFrontOfCameraText: 'Ubique su rostro frente a la cámara',
    hintFaceDetectedText: 'Rostro detectado',
    hintTooCloseText: 'Aléjese un poco',
    hintTooFarText: 'Acérquese un poco',
    hintHoldFaceForFreshnessText: 'Mantenga la posición',
    hintConnectingText: 'Conectando…',
    hintVerifyingText: 'Verificando…',
    hintCheckCompleteText: 'Verificación completa',
    startScreenBeginCheckText: 'Iniciar prueba de vida',
    photosensitivityWarningHeadingText: 'Advertencia de fotosensibilidad',
    photosensitivityWarningBodyText: 'Esta verificación muestra luces de colores. Tenga cuidado si es fotosensible.',
    cameraNotFoundHeadingText: 'No se encontró la cámara',
    retryCameraPermissionsText: 'Reintentar',
    cancelLivenessCheckText: 'Cancelar prueba de vida',
};

function LivenessTest({ requestId, initialStatus, sessionUrl, resultUrl }) {
    const [phase, setPhase] = useState('idle');
    const [liveness, setLiveness] = useState(null);
    const [result, setResult] = useState(null);
    const [error, setError] = useState(null);

    async function start() {
        setError(null);
        setResult(null);
        setPhase('requesting');

        try {
            setLiveness(await postJson(sessionUrl));
            setPhase('capturing');
        } catch (exception) {
            setError(exception.message);
            setPhase('idle');
        }
    }

    async function complete() {
        setPhase('submitting');

        try {
            const response = await postJson(resultUrl, { liveness_session_id: liveness.liveness_session_id });
            setResult(response.data);
            setPhase('done');
        } catch (exception) {
            setError(exception.message);
            setPhase('idle');
        }
    }

    const credentialProvider = async () => ({
        accessKeyId: liveness.credentials.access_key_id,
        secretAccessKey: liveness.credentials.secret_access_key,
        sessionToken: liveness.credentials.session_token,
        expiration: new Date(liveness.expires_at),
    });

    return (
        <div className="space-y-4 rounded-lg border border-zinc-200 bg-white p-4">
            <div className="text-sm text-zinc-600">
                Solicitud <code className="font-mono">{requestId}</code> · estado inicial <strong>{initialStatus}</strong>
            </div>

            {phase === 'idle' && (
                <button type="button" onClick={start} className="rounded-md bg-zinc-900 px-4 py-2 text-sm font-medium text-white hover:bg-zinc-700">
                    Iniciar prueba de vida
                </button>
            )}

            {phase === 'requesting' && <p className="text-sm">Pidiendo sesión de prueba de vida al proveedor…</p>}

            {phase === 'capturing' && liveness && (
                <FaceLivenessDetectorCore
                    sessionId={liveness.liveness_session_id}
                    region={liveness.region}
                    config={{ credentialProvider }}
                    displayText={displayText}
                    onAnalysisComplete={complete}
                    onUserCancel={() => setPhase('idle')}
                    onError={(livenessError) => {
                        setError(`${livenessError.state}: ${livenessError.error?.message ?? 'error en la prueba de vida'}. La sesión es de un solo uso: inicie una nueva.`);
                        setPhase('idle');
                    }}
                />
            )}

            {phase === 'submitting' && <p className="text-sm">Enviando resultado y emitiendo el certificado…</p>}

            {result && (
                <div className={`rounded-md p-3 text-sm ${result.status === 'CERTIFICATE_STORED' ? 'bg-emerald-50 text-emerald-900' : 'bg-amber-50 text-amber-900'}`}>
                    <p>
                        Estado: <strong>{result.status}</strong> · paso {result.step}
                    </p>
                    {result.status === 'CERTIFICATE_STORED' && <p>Certificado emitido y guardado en la bóveda. El SaaS lo retira sellado con GET …/certificate.</p>}
                    {result.remaining_liveness_attempts !== null && <p>Intentos restantes: {result.remaining_liveness_attempts}</p>}
                    {result.error && <p>Error: {result.error.message}</p>}
                    {result.status === 'FAILED_RETRYABLE' && (
                        <button type="button" onClick={start} className="mt-2 rounded-md bg-zinc-900 px-3 py-1.5 text-white">
                            Reintentar
                        </button>
                    )}
                </div>
            )}

            {error && <p className="rounded-md bg-red-50 p-3 text-sm text-red-800">{error}</p>}
        </div>
    );
}

const root = document.getElementById('liveness-root');

if (root) {
    createRoot(root).render(
        <StrictMode>
            <LivenessTest
                requestId={root.dataset.requestId}
                initialStatus={root.dataset.status}
                sessionUrl={root.dataset.sessionUrl}
                resultUrl={root.dataset.resultUrl}
            />
        </StrictMode>,
    );
}
