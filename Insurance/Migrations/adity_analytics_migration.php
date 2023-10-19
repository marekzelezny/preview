<?php

use Illuminate\Database\Capsule\Manager as Capsule;

global $wpdb;

$tableName = $wpdb->prefix . 'adity_analytics';
$connectedTable = $wpdb->prefix . 'frm_items';

if (Capsule::schema()->hasTable($tableName)) {
    return;
}

Capsule::schema()->create($tableName, function ($table) use ($connectedTable) {
    $table->increments('id');
    $table->string('type');
    $table->foreignId('entry_id')->constrained($connectedTable, 'id');
    $table->foreignId('parent_entry_id')->constrained($connectedTable, 'id');
    $table->foreignId('user_id')->constrained($connectedTable, 'user_id');
    $table->string('status')->nullable();
    $table->bigInteger('price')->nullable();
    $table->string('price_type')->nullable();
    $table->timestamps();
});
