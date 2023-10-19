<?php

namespace Domain\RecurringPayments\Actions;

use Domain\RecurringPayments\Models\RecurringPayer;
use Domain\RecurringPayments\Models\RecurringPayment;
use Lorisleiva\Actions\Concerns\AsAction;
use Services\Gopay\Enums\StatusEnum;

/**
 * Creates GoPay recurring payment objects for all recurring payers
 * that match the current day of the month.
 */
class CreateNewPaymentsAction
{
    use AsAction;

    public function handle(): void
    {
        RecurringPayer::HavingPaymentDueToday()
            ->each(function (RecurringPayer $payer) {
                $payment = RecurringPayment::create([
                    'parent_gopay_id' => $payer->parent_gopay_id,
                    'amount' => $payer->amount,
                    'status' => StatusEnum::CREATED,
                ]);

                WithdrawRecurringPaymentAction::dispatch($payment);
            });
    }
}
