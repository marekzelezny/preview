<?php

namespace App\Extensions\CareCloudApi;

use App\Extensions\CareCloudApi\Concerns\HasFormatting;
use App\Extensions\CareCloudApi\Concerns\MapsProperties;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Collection;

class StoreItem implements Arrayable
{
    use HasFormatting, MapsProperties;

    public string $store_id;
    public string $system_id;
    public string $partner_id;
    public string $name;
    public object $address;
    public ?string $email;
    public ?string $phone;
    public ?string $manager_name;
    public ?array $opening;
    public ?array $opening_exceptions;
    public ?array $partner;
    protected Collection $properties;

    public function __construct(object $store)
    {
        $this->mapStore($store);
        $this->properties = carecloud()->properties($this->store_id);
        $this->opening = $this->mapOpeningHours($store->opening);
        $this->opening_exceptions = $this->mapOpeningExceptions();
        $this->partner = $this->mapPartner();
    }

    private function mapStore(object $store): void
    {
        $this->store_id = $store->store_id;
        $this->system_id = $store->system_id;
        $this->partner_id = $store->partner_id;
        $this->name = $store->name;
        $this->manager_name = $store->manager_name;
        $this->email = $store->contact_email;
        $this->phone = $this->formatPhoneNumber($store->phone_number);
        $this->address = $this->formatAddress($store->store_address);
    }

    private function mapOpeningExceptions(): array
    {
        $rules = [
            'nadpis_notifikace_1',
            'popis_notifikace_1',
            'nadpis_notifikace_2',
            'popis_notifikace_2',
            'nadpis_notifikace_3',
            'popis_notifikace_3',
        ];

        return $this->mapProperties($rules);
    }

    private function mapPartner(): array
    {
        $partner = carecloud()->partner($this->partner_id);

        return [
            'name' => $partner->name,
            'address' => $this->formatAddress($partner->address),
            'ico' => $partner->registration_id,
            'dic' => $partner->vat_id,
            'email' => $partner->email,
            'phone' => $partner->phone,
        ];
    }

    public function mapOpeningHours(array $hours): array
    {
        return [
            'mon' => $this->setOpeningPerDay($hours[0]),
            'tue' => $this->setOpeningPerDay($hours[1]),
            'wed' => $this->setOpeningPerDay($hours[2]),
            'thu' => $this->setOpeningPerDay($hours[3]),
            'fri' => $this->setOpeningPerDay($hours[4]),
            'sat' => $this->setOpeningPerDay($hours[5]),
            'sun' => $this->setOpeningPerDay($hours[6]),
        ];
    }

    protected function setOpeningPerDay(object $day): array
    {
        $a = [
            'morning' => null,
            'afternoon' => null
        ];

        if (!is_null($day->time_intervals[0]->from) && !is_null($day->time_intervals[0]->to)) {
            $a['morning'] = [
                'from' => $this->formatHour($day->time_intervals[0]->from),
                'to'   => $this->formatHour($day->time_intervals[0]->to),
            ];
        }

        if (!is_null($day->time_intervals[1]->from) && !is_null($day->time_intervals[1]->to)) {
            $a['afternoon'] = [
                'from' => $this->formatHour($day->time_intervals[1]->from),
                'to'   => $this->formatHour($day->time_intervals[1]->to),
            ];
        }
        return $a;
    }

    public function toArray()
    {
        if(is_array($this->partner) && !empty($this->partner['address'])) {
            $this->partner['address'] = (array) $this->partner['address'];
        }

        return [
            'store_id' => $this->store_id,
            'pharmacy_id' => $this->store_id,
            'system_id' => $this->system_id,
            'name' => $this->name,
            'address' => (array) $this->address,
            'email' => $this->email,
            'phone' => $this->phone,
            'manager_name' => $this->manager_name,
            'opening' => $this->opening,
            'opening_exceptions' => $this->opening_exceptions,
            'partner' => $this->partner,
        ];
    }
}
