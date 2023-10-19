<?php

namespace Domain\RecurringPayments\Actions;

use Lorisleiva\Actions\Concerns\AsAction;
use Services\Gopay\DataTransferObjects\PaymentResponseData;
use Services\Gopay\Enums\StatusEnum;
use Services\Gopay\GopayService;

/**
 * Checks GoPay's payment state and updates payment
 * and payer status accordingly.
 */
class ProcessRecurringPaymentAction
{
    use AsAction;

    protected PaymentResponseData $payment;
    protected PaymentResponseData $parent_payment;

    public function __construct(
        public GopayService $gopay,
    )
    {}

    public function handle(int $payment_id, int $parent_payment_id): void
    {
        $this->payment = $this->gopay->getStatus($payment_id);
        $this->parent_payment = $this->gopay->getStatus($parent_payment_id);

        match($this->parent_payment->hasRecurrenceActive()) {
            true => $this->processPayment(),
            false => $this->cancelPayerAccount(),
        };
    }

    public function processPayment(): void
    {
        match($this->payment->state) {
            StatusEnum::PAID => $this->processSuccessfulPayment(),
            StatusEnum::CANCELED => $this->processCanceledPayment(),
        };
    }

    public function processSuccessfulPayment(): void
    {
        UpdateStatusAction::updatePaymentByGopayId($this->payment->id, StatusEnum::PAID);
        ExtendTydenikWebReaderAccessAction::run($this->parent_payment->id);
    }

    public function processCanceledPayment(): void
    {
        UpdateStatusAction::updatePaymentByGopayId($this->payment->id, StatusEnum::CANCELED);
    }

    public function cancelPayerAccount(): void
    {
        UpdateStatusAction::updatePayerByGopayId($this->parent_payment->id, StatusEnum::STOPPED);
    }
}
