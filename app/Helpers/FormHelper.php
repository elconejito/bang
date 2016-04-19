<?php
namespace App\Helpers;


class FormHelper
{
    public static function select($collection, $key, $value, $default = null) {
        $html = '';
        foreach ( $collection as $item ) {
            if ( is_array($value) ) {
                $label = '';
                foreach ( $value as $v ) {
                    $label .= $item->{$v} . ' ';
                }
                rtrim($label);
            } else {
                $label = $item->{$value}();
            }
            $html .= '<option value="'.$item->{$key}.'" '.($default == $item->{$key} ? 'selected':'' ).'>'.$label.'</option>';
        }
        
        return $html;
    }
}