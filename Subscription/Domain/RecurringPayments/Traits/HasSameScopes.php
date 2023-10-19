<?php

namespace Domain\RecurringPayments\Traits;

use Illuminate\Database\Eloquent\Builder;
use Services\Gopay\Enums\StatusEnum;

trait HasSameScopes
{
    public function scopeWhereParentGopayId(Builder $query, string $parent_gopay_id): Builder
    {
        return $query->where('parent_gopay_id', $parent_gopay_id);
    }

    public function scopeWhereStatus(Builder $query, StatusEnum $status): Builder
    {
        return $query->where('status', $status);
    }

    public function updateStatus(StatusEnum $status): void
    {
        $this->update(['status' => $status]);
    }
}
