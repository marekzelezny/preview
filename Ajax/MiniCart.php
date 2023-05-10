<?php

namespace App\Controllers\Components;

use App\Factory\WooProduct;
use WeDevs\ORM\WP\PostMeta;

class MiniCart
{
    public function __construct()
    {
        /** Ajax call to get cars associated to this product */
        add_action('wp_ajax_minicart_update', [$this, 'handleAjaxCall']);
        add_action('wp_ajax_nopriv_minicart_update', [$this, 'handleAjaxCall']);

        // Clears cart on Thank You page
        add_action('woocommerce_thankyou', [$this, 'clearCart']);

        // Clears cart if user requests it
        add_action('init', [$this, 'clearCartOnRequest']);
    }

    /**
     * Ajax call for singleCompetition popup
     */
    public function handleAjaxCall()
    {
        $products = json_decode(stripslashes($_POST['products']), true);

        switch ($_POST['type']) {
            case 'add_to_cart':
                $success = $this->addToCart($products);
                break;

            case 'remove_from_cart':
                $success = $this->removeFromCart($products);
                break;

            default:
                break;

        }

        if ($success) {
            $this->sendResponseToApp();
        } else {
            echo json_encode('error');
        }

        wp_die();
    }

    /**
     * Adds products to cart
     *
     * @return bool
     */
    public function addToCart($products)
    {
        foreach ($products as $product) {
            if (isset($product['location'])) {

                // Checks if there is enough stock for the product
                if ($this->checkStockInProductLocation($product['id'], $product['location'], $product['quantity'])) {
                    WC()->cart->add_to_cart(
                        $product['id'],
                        $product['quantity'],
                        '0',
                        [],
                        $this->generateCartItemData($product)
                    );
                } else {
                    return false;
                }
            } else {
                WC()->cart->add_to_cart(
                    $product['id'],
                    $product['quantity']
                );
            }
        }

        return true;
    }

    /**
     * Removes product from cart
     */
    public function removeFromCart($products)
    {
        foreach ($products as $product) {

            if (isset($product['location']) && $product['location'] != '') {
                $product_cart_id = WC()->cart->generate_cart_id(
                    $product['id'],
                    '0',
                    [],
                    $this->generateCartItemData($product)
                );
            } else {
                $product_cart_id = WC()->cart->generate_cart_id($product['id']);
            }

            if ($cart_item_key = WC()->cart->find_product_in_cart($product_cart_id)) {
                WC()->cart->remove_cart_item($cart_item_key);
            }
        }

        return true;
    }

    /**
     * Generates unique cart item data
     */
    public function generateCartItemData($product)
    {
        $location = get_term_by('id', $product['location'], 'locations');
        $terms = get_terms(['taxonomy' => 'locations', 'hide_empty' => false, 'parent' => 0]);

        foreach ($terms as $t => $v) {
            if ($location->term_id == $v->term_id) {
                $locationData = [];
                $locationData['select_location']['location_name'] = $location->name;
                $locationData['select_location']['location_key'] = $t;
                $locationData['select_location']['location_qty'] = get_post_meta($product['id'], "wcmlim_stock_at_{$location->term_id}", true);
                $locationData['select_location']['location_termId'] = $location->term_id;
                $locationData['select_location']['location_cart_price'] = '';
                $locationData['select_location']['location_org_price'] = '';
            }
        }

        return $locationData;
    }

    /**
     * Gets current cart status and sends it to frontend
     */
    public function sendResponseToApp()
    {
        $cart = [];

        foreach (WC()->cart->get_cart() as $cartItem) {
            $product = new WooProduct($cartItem['product_id']);

            $cart[] = [
                'id' => $cartItem['product_id'],
                'link' => $product->data->link,
                'image' => $product->data->thumbnail->src,
                'name' => $product->data->title,
                'quantity' => $cartItem['quantity'],
                'price' => $product->woo->price,
                'location' => ($cartItem['select_location']['location_termId'] ?: ''),
            ];

        }

        $cartCount = WC()->cart->get_cart_contents_count();

        switch ($cartCount) {
            case 0:
                $cartCount = '';
                break;

            case 1:
                $cartCount = '1 producto';
                break;

            default:
                $cartCount = "{$cartCount} productos";
                break;
        }

        $output = [
            'subtotal' => number_format(WC()->cart->get_subtotal(), 2),
            'count' => $cartCount,
            'products' => $cart,
        ];

        echo json_encode($output);
    }

    /**
     * Creates a Javascript-echoed prefill with current user cart data
     */
    public static function getUserCart()
    {
        $cartCount = WC()->cart->get_cart_contents_count();

        switch ($cartCount) {
            case 0:
                $cartCount = '';
                break;

            case 1:
                $cartCount = '1 producto';
                break;

            default:
                $cartCount = "{$cartCount} productos";
                break;
        }

        $data = [
            'subtotal' => number_format(WC()->cart->get_subtotal(), 2),
            'count' => $cartCount,
        ];

        $outputProducts = '';
        $cartItemCount = 0;
        foreach (WC()->cart->get_cart() as $cartItem) {
            $product = new WooProduct($cartItem['product_id']);

            $location = (isset($cartItem['select_location']['location_termId']) ? $cartItem['select_location']['location_termId'] : 'null');

            $outputProducts .= "{$cartItemCount}: {
                id: {$cartItem['product_id']},
                link: '{$product->data->link}',
                image: '{$product->data->thumbnail->src}',
                name: '{$product->data->title}',
                quantity: {$cartItem['quantity']},
                price: {$product->woo->price},
                location: {$location}
            },";

            $cartItemCount++;
        }

        $output = "{
            subtotal: {$data['subtotal']},
            count: '{$data['count']}',
            products: {{$outputProducts}}
        }";

        return $output;
    }

    /**
     * Checks if the selected product has selected amount of products in stock.
     *
     * @return bool
     */
    public function checkStockInProductLocation($productId, $productLocation, $userQuantity)
    {
        $productLocationQuantity = (int) PostMeta::where('post_id', $productId)->where('meta_key', "wcmlim_stock_at_{$productLocation}")->pluck('meta_value')->first();

        if ($userQuantity > $productLocationQuantity) {
            return false;
        }

        return true;
    }

    /**
     * Clears cart on request
     */
    public function clearCartOnRequest()
    {
        if (isset($_GET['accion']) && $_GET['accion'] == 'vaciar-el-carrito') {
            $this->clearCart();
        }
    }

    /**
     * Clears cart on Thank You page
     */
    public function clearCart()
    {
        WC()->cart->empty_cart();
    }
}
