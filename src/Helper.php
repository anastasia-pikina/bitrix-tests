<?php

declare(strict_types=1);

namespace pwd\Tests;

class Helper
{
    /**
     * сравнение вхождения в массив или совпадение со строкой
     * @param string $value
     * @param array|string $comparison
     * @return bool результат сравнения
     */
    public static function checkIdentically(
        string $value,
               $comparison
    ): bool
    {
        if (is_array($comparison)) {
            if (in_array($value, $comparison)) {
                return true;
            }
        } else {
            if ($value === $comparison) {
                return true;
            }
        }

        return false;
    }

    public static function getFile($filePath, $fileName): array
    {
        $result = [];
        $fileUrl = $filePath . $fileName;

        if (!file_exists($fileUrl)) {
            $result['errors'][] = 'Файл ' . $fileName . ' не найден.';
            return $result;
        }

        $handle = @fopen($fileUrl, 'r');

        if (!$handle) {
            $result['errors'][] = 'Ошибка при чтении файла ' . $fileName . '.';
            return $result;
        }

        $result['handle'] = $handle;

        return $result;
    }

    public static function getDataUrl(string $url)
    {
        if (preg_match('@^([(http(s*))://]*)?([^/]+)@i', $reader->value, $matches)) {

        }

    }
}
