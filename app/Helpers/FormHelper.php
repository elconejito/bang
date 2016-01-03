<?php
namespace App\Helpers;


class FormHelper
{
    public static function select($collection, $key, $value, $default = null) {
        $html = '';
        foreach ( $collection as $item ) {
            $html .= '<option value="'.$item->{$key}.'" '.($default == $item->{$key} ? 'selected':'' ).'>'.$item->{$value}.'</option>';
        }

        return $html;
    }
}