<?php

namespace App\Services;

use App\Models\Order;
use App\Models\PaboExport;
use App\Models\Subscription;
use App\Models\User;
use App\Notifications\UserNewAccountNotification;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Fluent;

class PaboService
{
    public function createPaboExportModelItem(string $data): PaboExport
    {
        $data = json_decode($data);

        if ($data === null) {
            throw new \Exception('Invalid JSON');
        }

        $fluent = new Fluent($data);

        if (! isset($fluent['dexId'])) {
            throw new \Exception('Missing dexId');
        }

        $paboExport = PaboExport::find($fluent->dexId) ?? new PaboExport();

        if (! $paboExport->id) {
            $paboExport->id = $fluent->dexId;
        }

        $paboExport->status = 'CREATED';
        $paboExport->pabo_id = $fluent->orderId;
        $paboExport->data = $fluent;
        $paboExport->issue_from = $fluent->issueFrom;
        $paboExport->issue_to = $fluent->issueTo;

        if ($order = Order::find($fluent->orderExtId)) {
            $paboExport->order()->associate($order);
        }

        if ($user = User::whereEmail($fluent->email)->first()) {
            $paboExport->subscriber()->associate($user);
        } else {
            $user = $this->createUserFromPaboExport($fluent);
            $paboExport->subscriber()->associate($user);
        }

        if ($subscription = Subscription::wherePaboId($fluent->priceId)->first()) {
            $paboExport->subscription()->associate($subscription);
        }

        $paboExport->save();

        return $paboExport;
    }

    public function createUserFromPaboExport(Fluent $paboExport): User
    {
        $subscription = Subscription::wherePaboId($paboExport->priceId)->first();
        $subscriptionStart = Carbon::createFromFormat('Ymd', $paboExport->issueFrom);

        $user = (new UserService)->firstOrCreate(
            email: $paboExport->email,
            firstName: $paboExport->firstName,
            lastName: $paboExport->lastName,
            subscriptionStart: $subscriptionStart,
            subscriptionEnd: Carbon::createFromFormat('Ymd', $paboExport->issueTo),
            paymentRecurrence: 0,
            gopayId: 0,
        );

        $resetPasswordLink = Http::getPasswordResetLink($user->ID)->url;

        $vars = [
            'ACCOUNT_TYPE' => 'new',
            'EMAIL' => $paboExport->email,
            'RESET_PASSWORD_LINK' => $resetPasswordLink,
            'SUBSCRIPTION_DURATION' => $subscription->period,
            'SUBSCRIPTION_START' => $subscriptionStart->translatedFormat('j. n. Y'),
        ];

        $user->notify(new UserNewAccountNotification($vars));

        return $user;
    }
}
