<?php

use Illuminate\Database\Capsule\Manager as Capsule;

global $wpdb;

$tableName = $wpdb->prefix . 'frm_items_adity';
$connectedTable = $wpdb->prefix . 'frm_items';

if (Capsule::schema()->hasTable($tableName)) {
    return;
}

Capsule::schema()->create($tableName, function ($table) use ($connectedTable) {
    $table->increments('id');
    $table->string('user_type')->nullable();
    $table->foreignId('user_id')->constrained($connectedTable, 'user_id');
    $table->foreignId('entry_id')->constrained($connectedTable, 'id');
    $table->string('status')->nullable();
    $table->string('tag')->nullable();
});
