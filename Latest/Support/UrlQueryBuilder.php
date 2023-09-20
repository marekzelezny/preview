<?php

namespace Adity\Support;

class UrlQueryBuilder
{
    public static function currentUrl() : string
    {
        global $wp;
        return home_url($wp->request);
    }

    public static function urlWithParams(array $params = []) : string
    {
        global $wp;
        $params = array_merge($_GET, $params);

        return home_url(add_query_arg($params, $wp->request));
    }

    public static function urlWithoutParams(array $unsetParams = []) : string
    {
        global $wp;
        $params = $_GET;

        foreach ($unsetParams as $param) {
            unset($params[$param]);
        }

        return home_url(add_query_arg($params, $wp->request));
    }

    public static function hasParamValueInUrl(string $value, string $param) : bool
    {
        return isset($_GET[$param]) && $_GET[$param] == $value;
    }

    public static function buildHiddenInputsWithout(array $avoid = []) : void
    {
        $inputs = '';
        foreach ($_GET as $key => $value) {
            if (! in_array($key, $avoid)) {
                $inputs .= "<input type='hidden' name='$key' value='$value'>";
            }
        }

        echo $inputs;
    }

    public static function hasSelected(string $value, string $key) : void
    {
        if (isset($_GET[$key]) && $_GET[$key] == $value) {
            echo 'selected';
        }
    }
}
