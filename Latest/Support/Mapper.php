<?php

namespace Adity\Support;

use Adity\Models\EntryValue;
use Illuminate\Support\Collection;

class Mapper
{
    public static function mapItemMetaToEntryValue(array $item_meta, string|int $form_id) : Collection
    {
        $output = new Collection();

        foreach ($item_meta as $meta_key => $value) {
            if (! $value) {
                continue;
            }

            $output->push(
                new EntryValue($meta_key, $form_id, $value)
            );
        }

        return $output;
    }
}
