<?php

use App\Models\Pharmacy;

try {
    $counter = 0;
    $stores = [];

    carecloud()
        ->stores(ff_site_language())
        ->each(function ($store) use (&$counter, &$stores) {
            $result = Pharmacy::updateOrCreate(
                ['pharmacy_id' => $store->store_id],
                $store->toArray(),
            );

            if($result->wasRecentlyCreated || $result->wasChanged()) {
                $counter++;
                $stores[] = $store->store_id;
            }
        });

    $ids = count($stores) ? '[IDs: '. implode(' ', $stores) .']' : '';
    carecloud_logger("Stores updated or created: $counter $ids");
} catch (Throwable $e) {
    carecloud_logger($e->getMessage() . ' - ' . $e->getTraceAsString());
}
