<?php
namespace App\Helpers;


use Illuminate\Support\Collection;

class FormHelper
{
    /**
     * @param Collection|array $collection Collection or array of elements to put into the select options
     * @param string $key Which field is used as the key/ID that will be set by the option
     * @param array|string $value Which field(s) is used to display the label for the option
     * @param null $default
     *
     * @return string
     */
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
