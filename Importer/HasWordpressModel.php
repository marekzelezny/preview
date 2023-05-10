<?php

namespace App\Traits;

use App\Extensions\Importer\Models\Image;
use Timber\Timber;

trait HasWordpressModel
{
    public function wordpress(): ?object
    {
        if ($this->wp_id) {
            return Timber::get_post($this->wp_id);
        }

        return null;
    }

    public function saveWordpressModel(): void
    {
        if ($this->wordpress()) {
            $this->updateWordpressModel();
        } else {
            $this->createWordpressModel();
        }
    }

    public function createWordpressModel(): void
    {
        $wpId = wp_insert_post([
            'post_title' => $this->name,
            'post_name' => $this->slug ?? sanitize_title($this->name),
            'post_type' => $this->postType,
            'post_status' => 'publish',
        ]);

        if ($wpId) {
            $this->wp_id = $wpId;
            $this->save();
            $this->updateWordpressModel();
        }
    }

    public function updateWordpressModel(): void
    {
        if (! $this->wp_id) {
            return;
        }

        if ($this->acfField) {
            update_field(
                selector: $this->acfField,
                value: $this->toArray(),
                post_id: $this->wp_id
            );
        }

        if (property_exists($this, 'wpCasts')) {
            $this->updateFieldsByWpCasts();
        }
    }

    public function updateFieldsByWpCasts(): void
    {
        if (! $this->wp_id) {
            return;
        }

        foreach ($this->wpCasts as $field => $data) {
            $value = match ($data['type']) {
                'text' => $this->updateTextField($field),
                'date' => $this->updateDateField($field),
                'term' => $this->updateTermField($field),
                'image' => $this->updateImageField($field),
                'custom' => $this->updateCustomField($field, $data['callback']),
                'default' => null,
            };

            if (! $value) {
                continue;
            }

            update_field(
                selector: $data['meta_key'],
                value: $value,
                post_id: $this->wp_id
            );
        }
    }

    public function updateTextField($field): mixed
    {
        return $this->getAttribute($field) ?? null;
    }

    public function updateDateField($field): mixed
    {
        $value = $this->getAttribute($field);

        return $value ? $value->format('Y-m-d H:i:s') : null;
    }

    public function updateTermField($field): mixed
    {
        $value = $this->getAttribute($field);

        if (! $value) {
            return null;
        }

        if ($term = get_term_by('name', $value, $field)) {
            return $term->term_id;
        }

        $newTerm = wp_insert_term($value, $field);

        if (is_array($newTerm)) {
            return $newTerm['term_id'];
        }

        return null;
    }

    public function updateImageField($field): mixed
    {
        $value = $this->getAttribute($field);

        if (! $value) {
            return null;
        }

        if ($image = Image::where('name', '=', $value)->first()) {
            return $image->wp_id;
        }

        return null;
    }

    public function updateCustomField($field, $callback): mixed
    {
        $value = $this->getAttribute($field);

        if (method_exists($this, $callback)) {
            $this->{$callback}();
        }

        return null;
    }
}
