<?php

namespace Adity\Models;

use Adity\Enums\StatusEnum;
use Adity\Enums\TagEnum;
use Illuminate\Database\Eloquent\Model;

class AdityMeta extends Model
{
    protected $table = 'wp_frm_items_adity';

    protected $fillable = [
        'user_type',
        'user_id',
        'entry_id',
        'status',
        'tag',
    ];

    protected $casts = [
        'status' => StatusEnum::class,
        'tag' => TagEnum::class,
    ];

    public $timestamps = false;
}
