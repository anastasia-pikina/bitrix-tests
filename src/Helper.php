<?php

namespace pwd\Tests;

class Helper
{
    /**
     * сравнение вхождения в массив или совпадение со строкой
     * @param string $value
     * @param array|string $comparison
     * @return bool результат сравнения
     */
    public static function checkIdentically(string $value, $comparison): bool
    {
        if (is_array($comparison)) {
            if(in_array($value, $comparison)) {
                return true;
            }
        } else {
            if ($value === $comparison) {
                return true;
            }
        }

        return false;
    }

}