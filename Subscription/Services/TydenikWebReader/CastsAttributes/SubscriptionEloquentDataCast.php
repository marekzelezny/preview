<?php

namespace Services\TydenikWebReader\CastsAttributes;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;
use Services\TydenikWebReader\TydenikWebReader;

class SubscriptionEloquentDataCast implements CastsAttributes
{
    public function get(
        Model $model,
        string $key,
        mixed $value,
        array $attributes
    )
    {
        return app(TydenikWebReader::class)
            ->getSubscriber($model->email);
    }

    public function set(
        Model $model,
        string $key,
        mixed $value,
        array $attributes
    )
    {
        return $value->toArray();
    }
}
