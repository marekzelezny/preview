<?php

namespace App\Casts;

use App\Concerns\GetField;
use App\Concerns\ModifiesOpeningHours;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;

class OpeningHours implements CastsAttributes
{
    use GetField, ModifiesOpeningHours;

    public function get($model, string $key, $value, array $attributes)
    {
        $hours = ad_has_system_id() ? json_decode($value, true) : $this->modifyData($this->getField('opening_hours'));
        return $this->processOpeningHours($hours);
    }

    public function set($model, string $key, $value, array $attributes)
    {
        return json_encode($value);
    }
}
