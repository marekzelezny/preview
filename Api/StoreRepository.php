<?php

namespace App\Extensions\CareCloudApi;

use Illuminate\Support\Collection;

class StoreRepository extends Collection
{
    public function __construct(mixed $data)
    {
        if(is_object($data)) {
            $stores = [];

            foreach($data->stores as $store) {
                $stores[] = new StoreItem($store);
            }

            parent::__construct($stores);
        } else {
            parent::__construct($data);
        }
    }
}
