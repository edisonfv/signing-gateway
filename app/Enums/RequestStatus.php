<?php

namespace App\Enums;

/**
 * Estado interno de una solicitud de firma, independiente del proveedor.
 *
 * @method static self draft()
 * @method static self submitting()
 * @method static self submitted()
 * @method static self awaitingApplicant()
 * @method static self validating()
 * @method static self failedRetryable()
 * @method static self approved()
 * @method static self issued()
 * @method static self certificateStored()
 * @method static self delivered()
 * @method static self rejected()
 * @method static self cancelled()
 * @method static self expired()
 * @method static self failed()
 * @method static self revoked()
 */
final class RequestStatus extends ScreamingSnakeEnum
{
    /**
     * @var array<string, array<int, string>>
     */
    private const TRANSITIONS = [
        'DRAFT' => ['SUBMITTING', 'CANCELLED'],
        'SUBMITTING' => ['SUBMITTED', 'AWAITING_APPLICANT', 'REJECTED', 'FAILED'],
        'SUBMITTED' => ['AWAITING_APPLICANT', 'VALIDATING', 'FAILED_RETRYABLE', 'REJECTED', 'CANCELLED', 'EXPIRED', 'FAILED'],
        'AWAITING_APPLICANT' => ['VALIDATING', 'FAILED_RETRYABLE', 'REJECTED', 'CANCELLED', 'EXPIRED', 'FAILED'],
        'VALIDATING' => ['APPROVED', 'ISSUED', 'FAILED_RETRYABLE', 'REJECTED', 'CANCELLED', 'EXPIRED', 'FAILED'],
        'FAILED_RETRYABLE' => ['AWAITING_APPLICANT', 'VALIDATING', 'REJECTED', 'CANCELLED', 'EXPIRED', 'FAILED'],
        'APPROVED' => ['ISSUED', 'REJECTED', 'FAILED'],
        'ISSUED' => ['CERTIFICATE_STORED', 'DELIVERED', 'REVOKED', 'FAILED'],
        'CERTIFICATE_STORED' => ['DELIVERED', 'REVOKED'],
        'DELIVERED' => ['REVOKED'],
    ];

    public function canTransitionTo(self $next): bool
    {
        return in_array($next->value, self::TRANSITIONS[$this->value] ?? [], true);
    }

    public function isTerminal(): bool
    {
        return ! array_key_exists($this->value, self::TRANSITIONS) || $this->equals(self::delivered());
    }
}
