<?php

namespace App\Extensions\Importer\Models;

use App\Enums\BookingAvailability;
use App\Enums\BookingVisibility;
use App\Traits\HasMultisiteDynamicTableName;
use App\Traits\HasWordpressModel;
use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    use HasWordpressModel, HasMultisiteDynamicTableName;

    protected $table = 'importer_bookings';

    protected $fillable = [
        'wp_id',
        'name',
        'visible',
        'available',
        'capacity',
        'stars',
        'address',
        'contact',
        'description',
        'order',
    ];

    protected $casts = [
        'visible' => BookingVisibility::class,
        'available' => BookingAvailability::class,
        'address' => 'array',
        'contact' => 'array',
    ];

    protected string $acfField = 'booking';

    protected string $postType = 'booking';

    public function createWordpressModel(): void
    {
        $wpId = wp_insert_post([
            'post_title' => $this->name,
            'post_name' => sanitize_title($this->name),
            'post_type' => $this->postType,
            'post_status' => 'publish',
            'menu_order' => $this->id,
        ]);

        $this->wp_id = $wpId;
        $this->save();

        $this->updateWordpressModel();
    }
}
