<?php

namespace App\Http\Livewire;

use App\Enums\PaymentMethod;
use App\Enums\SubscriptionType;
use App\Models\Issue;
use App\Models\Subscription;
use App\Services\OrderService;
use App\Services\PaymentService;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Log;
use Livewire\Component;

class CheckoutForm extends Component
{
    public $subscription;

    public $subscriptions;

    public $subscription_type;

    public $subscription_start;

    public $issue;

    public $issues;

    public $customer;

    public $payment_method;

    public $notes;

    public $invoice;

    public $shipping_visible;

    public $notes_visible;

    public $newsletter;

    public $recurrent_payment;

    public $coupon;

    public $custom;

    public $special;

    protected $rules = [
        'subscription' => 'required',
        'subscription_start' => 'required|date|after_or_equal:today',
        'issue' => 'nullable|exists:issues,id',
        'customer.type' => 'required',
        'customer.first_name' => 'required|string|max:255',
        'customer.last_name' => 'required|string|max:255',
        'customer.email' => 'required|string|email|max:255|confirmed',
        'customer.email_confirmation' => 'required|string|email|max:255|same:customer.email',
        'customer.phone' => 'required',
        'customer.billing' => 'required_if:invoice,true',
        'customer.billing.*' => 'required_if:invoice,true',
        'customer.billing.street' => 'required_if:invoice,true',
        'customer.billing.street_number' => 'required_if:invoice,true',
        'customer.billing.city' => 'required_if:invoice,true',
        'customer.billing.zip' => 'required_if:invoice,true',
        'customer.shipping' => 'required_if:shipping_visible,true',
        'customer.shipping.*' => 'required_if:shipping_visible,true',
        'customer.shipping.first_name' => 'required_if:shipping_visible,true',
        'customer.shipping.last_name' => 'required_if:shipping_visible,true',
        'customer.shipping.email' => 'required_if:shipping_visible,true',
        'customer.company' => 'required_if:customer_type,company',
        'customer.company.name' => 'required_if:customer_type,company',
        'customer.company.ico' => 'required_if:customer_type,company',
        'customer.company.dic' => 'required_if:customer_type,company',
        'payment_method' => 'required',
        'recurrent_payment' => 'required_if:payment_method,RECURRENT_PAYMENT',
    ];

    protected $validationAttributes = [
        'customer.first_name' => 'Jméno',
        'customer.last_name' => 'Příjmení',
        'customer.email' => 'E-mail',
        'customer.phone' => 'Telefon',
        'customer.billing' => 'Adresu',
        'customer.billing.street' => 'Ulice',
        'customer.billing.street_number' => 'Číslo popisné',
        'customer.billing.city' => 'Město',
        'customer.billing.zip' => 'PSČ',
        'customer.shipping.street' => 'Ulice',
        'customer.shipping.street_number' => 'Číslo popisné',
        'customer.shipping.city' => 'Město',
        'customer.shipping.zip' => 'PSČ',
    ];

    protected $messages = [
        'subscription_start.after_or_equal' => 'Začátek předplatného musí být alespoň od dnešního dne.',
        'customer.*.required' => ':attribute je povinné vyplnit.',
        'customer.*.required_if' => ':attribute je povinné vyplnit.',
        'customer.billing.required_if' => 'Doplňte prosím vaši adresu.',
        'customer.shipping.required_if' => 'Doplňte prosím adresu pro doručení.',
        'customer.company.required_if' => 'Doplňte údaje o vaši firmě.',
        'recurrent_payment.required_if' => 'Zaškrtněte políčko pro udělení souhlasu k založení opakované platby.',
    ];

    public function setDefaultSubscription(): int
    {
        return match ($this->subscription_type) {
            SubscriptionType::DIGITAL => $this->subscriptions->firstWhere('slug', '=', 'digitalni-rocni-predplatne')->id,
            SubscriptionType::PRINT => $this->subscriptions->firstWhere('slug', '=', 'rocni-tisteni-predplatne')->id,
            SubscriptionType::COMBINED => $this->subscriptions->firstWhere('slug', '=', 'kombinovane-predplatne')->id,
        };
    }

    public function mount(SubscriptionType $subscriptionType, bool $custom = false, string $special = ''): void
    {
        $this->subscription_type = $subscriptionType;
        $this->subscription_start = now()->format('Y-m-d');
        $this->subscriptions = Subscription::where('type', $subscriptionType)->get();
        $this->subscription = $this->setDefaultSubscription();
        $this->customer['type'] = 'person';
        $this->payment_method = $subscriptionType == SubscriptionType::DIGITAL ? PaymentMethod::RECURRENT_PAYMENT->value : PaymentMethod::PAYMENT_CARD->value;
        $this->invoice = ! ($subscriptionType == SubscriptionType::DIGITAL);
        $this->shipping_visible = false;
        $this->notes_visible = false;
        $this->newsletter = false;
        $this->custom = $custom ?? false;
        $this->special = $special ?? '';

        if ($subscriptionType !== SubscriptionType::DIGITAL) {
            $this->issues = Issue::available()->get();
            $this->issue = $this->issues->first()->id;
        }
    }

    public function render(): Factory|View|Application
    {
        return view('livewire.checkout-form');
    }

    public function preventOneTimePayment(): void
    {
        if ($this->subscription_type == SubscriptionType::DIGITAL && $this->subscription == 3 && $this->payment_method == PaymentMethod::PAYMENT_CARD->value) {
            $this->payment_method = PaymentMethod::RECURRENT_PAYMENT->value;
        }
    }

    public function setSubscriptionDateFromIssue(): void
    {
        if ($this->subscription_type !== SubscriptionType::DIGITAL) {
            $this->subscription_start = $this->issues->firstWhere('id', $this->issue)->date->format('Y-m-d');
        }
    }

    public function submit(OrderService $orderService, PaymentService $paymentService): RedirectResponse|Application|Redirector
    {
        $this->validate();
        $this->preventOneTimePayment();
        $this->setSubscriptionDateFromIssue();

        $order = $orderService->createOrderFromCheckout($this->all());
        $payment = $paymentService->createPayment($order);

        if (! $payment->hasSucceed()) {
            Log::error('CheckoutForm::submit() failed', [
                'order' => $order->attributesToArray(),
                'payment' => $payment->json,
            ]);

            $order->delete();

            session()->flash('error', 'Nastala chyba při vytváření platby. Zkuste to prosím znovu.');
            exit;
        }

        $order->gopay_id = $payment->json['id'];
        $order->save();

        return redirect($payment->json['gw_url']);
    }
}
