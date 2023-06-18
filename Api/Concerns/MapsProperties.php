<?php

namespace App\Extensions\CareCloudApi\Concerns;

trait MapsProperties
{
    public function mapProperties(array $rules): array
    {
        return $this->properties
            ->filter(function ($property) use ($rules) {
                return in_array($property->property_id, $rules);
            })
            ->map(function ($property) {
                return [$property->property_id => $property->property_value];
            })
            ->toArray();
    }
}
