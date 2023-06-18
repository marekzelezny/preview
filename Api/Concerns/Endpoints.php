<?php

namespace App\Extensions\CareCloudApi\Concerns;

use App\Extensions\CareCloudApi\StoreRepository;

trait Endpoints
{
    public function store(string $id)
    {
        return $this->get("stores/{$id}");
    }

    public function stores(string $language = 'cz', int $limit = 99999)
    {
        $id = match($language) {
            'cz' => '86e05affc7a7abefcd513ab400',
            'sk' => '8bed991c68a470e7aaeffbf048',
            default => '86e05affc7a7abefcd513ab400',
        };

        return new StoreRepository(
            $this->get('stores', [
                'query' => [
                    'count' => $limit,
                    'property_id' => 'p1_zeme',
                    'property_value' => $id,
                ]
            ])
        );
    }

    public function properties(string $storeId = null)
    {
        if($storeId === null) {
            return collect($this->get('store-properties'));
        }

        return collect($this->get("stores/{$storeId}/property-records")->property_records);
    }

    public function partner(string $partnerId)
    {
        return $this->get("partners/{$partnerId}");
    }
}
