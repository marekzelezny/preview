<?php

namespace Adity\Support;

use Adity\Support\InsuranceCategory;

class DbQueryBuilder
{
    public static function prepareQueryForUser() : string
    {
        global $wpdb;
        $query = '';

        $options = [
            'parent_item_id' => 0,
            'is_draft' => 0,
            'user_id' => get_current_user_id(),
        ];

        $query = self::mapArrayToQuery($options);

        if (self::hasFilter()) {
            $query .= self::prepareFilterQuery();
        }

        return $query;
    }

    public static function prepareQueryForOffers(int $application_id) : string
    {
        global $wpdb;
        $query = '';

        $options = [
            'parent_item_id' => $application_id,
        ];

        $query = self::mapArrayToQuery($options);

        if (self::hasFilter()) {
            $query .= self::prepareFilterQuery();
        }

        return $query;
    }

    public static function prepareQueryForInsurance() : string
    {
        global $wpdb;
        $query = '';

        $options = [
            'is_draft' => 0,
        ];

        $query = self::mapArrayToQuery($options);

        $query .= self::prepareCategoryFilter();

        if (self::hasFilter()) {
            $query .= self::prepareFilterQuery();
        }

        return $query;
    }

    public static function mapArrayToQuery($array) : string
    {
        global $wpdb;
        $query = '';

        foreach ($array as $q => $v) {
            if (empty($query)) {

                if (is_string($v)) {
                    $query .= $wpdb->prepare("{$q} = %s", $v);
                    continue;
                }

                $query .= $wpdb->prepare("{$q} = %d", $v);
                continue;
            }

            if (is_string($v)) {
                $query .= $wpdb->prepare(" AND {$q} = %s", $v);
            } else {
                $query .= $wpdb->prepare(" AND {$q} = %d", $v);
            }
        }

        return $query;
    }

    public static function mapArrayToIn($array) : string
    {
        global $wpdb;
        $categories = InsuranceCategory::getUserCategorySlugs();
        $mappedStrings = array_map(function ($item) {
            return "'{$item}'";
        }, $categories);

        return implode(', ', $mappedStrings);
    }

    public static function hasFilter() : bool
    {
        return isset($_GET['filter']);
    }

    public static function prepareFilterQuery() : string
    {
        global $wpdb;

        if (! self::hasFilter()) {
            return '';
        }

        $ids = $wpdb->get_col(
            $wpdb->prepare("SELECT entry_id FROM {$wpdb->prefix}frm_items_adity WHERE user_id = %d AND status = %s", get_current_user_id(), $_GET['filter'])
        );

        if (empty($ids)) {
            $ids = [0];
        }

        return " AND it.id IN(" . implode(', ', $ids) . ")";
    }

    public static function prepareCategoryFilter() : string
    {
        $category = isset($_GET['category']) ? $_GET['category'] : InsuranceCategory::getUserCategorySlugs();
        $query = ' AND form_key IN(' . self::mapArrayToIn(InsuranceCategory::getUserCategorySlugs()) . ')';

        if (is_string($category) && ! empty($category)) {
            $query = " AND form_key = '{$category}'";
        }

        return $query;
    }

    /*public static function prepareFilterQuery() : string
    {
        if (! self::hasFilter()) {
            return '';
        }

        $filter = $_GET['filter'];
        return match ($filter) {
            'today' => self::getTodayFilter(),
            default => self::getStatusFilter($filter),
        };
    }*/

    public static function getStatusFilter(string $filter) : string
    {
        $filter = strtoupper($filter);
        return "adity_status = '{$filter}'";
    }

    public static function getTodayFilter() : string
    {
        return "DATE(it.created_at) = CURDATE()";
    }

    public static function prepareSortQuery() : string
    {
        global $wpdb;

        if (! isset($_GET['sort_by'])) {
            return " ORDER BY it.created_at DESC";
        }

        $order = " ORDER BY it.created_at ";
        $sort = match ($_GET['sort_by']) {
            'latest' => 'DESC',
            'oldest' => 'ASC',
            default => 'DESC',
        };

        return $order . $sort;
    }
}
