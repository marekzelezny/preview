<?php

namespace App\Controllers\Components;

use App\Models\Car;

class Popup
{
    public function __construct()
    {
        /** Ajax call to get cars associated to this product */
        add_action('wp_ajax_single_product_get_cars', [$this, 'getCarsByProduct']);
        add_action('wp_ajax_nopriv_single_product_get_cars', [$this, 'getCarsByProduct']);
    }

    /**
     * Ajax call for singleCompetition popup
     */
    public function getCarsByProduct()
    {
        $brand = $_POST['brand'];
        $product = $_POST['product'];

        $cars = Car::brand($brand)->whereHas('product', function ($query) use ($product) {
            $query->where('ID', $product);
        })->get()->toArray();

        echo json_encode($cars);

        wp_die();
    }
}
