<?php
namespace App\Helpers;


class FormHelper
{
    public static function select($collection, $key, $value) {
        $html = '';
        foreach ( $collection as $item ) {
            $html .= '<option value="'.$item->{$key}.'">'.$item->{$value}.'</option>';
        }

        return $html;
    }
}