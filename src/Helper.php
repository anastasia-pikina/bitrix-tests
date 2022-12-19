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

    /**
     * открытие и проверка файла на существование
     * @param string $filePath
     * @param string $fileName
     * @return array
     */
    public static function getFile(string $filePath, string $fileName): array
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

    /**
     * извлечение из URL домена и протокола
     * @param string $url
     * @return array
     */
    public static function getDataUrl(string $url): array
    {
        if (preg_match('@^([(http(s*))://]*)?([^/]+)@i', $url, $matches)) {
            return [
                'protocol' => $matches[1] ? str_replace('://', '', $matches[1]) : '',
                'domain' => $matches[2] ?? '',
            ];
        }

        return [];
    }

    /**
     * иполучение данных об активных сайтах
     * @return array
     */
    public static function getSites(): array
    {
        $result = [];
        $sites = \Bitrix\Main\SiteTable::getList([
            'filter' => ['ACTIVE' => 'Y'],
            'select' => [
                'LID',
                'SERVER_NAME',
                'DOC_ROOT',
                'DOMAINS_' => 'DOMAINS',
            ],
            'order' => ['LID' => 'ASC'],
            'runtime' => [
                'DOMAINS' => [
                    'data_type' => '\Bitrix\Main\SiteDomainTable',
                    'reference' => [
                        '=this.LID' => 'ref.LID',
                    ],
                    'join_type' => 'left',
                ],
            ],
        ]);

        while ($site = $sites->fetch()) {
            if (empty($result[$site['LID']]['SERVER_NAME'])) {
                $result[$site['LID']]['SERVER_NAME'] = trim($site['SERVER_NAME']);
            }

            if (empty($result[$site['LID']]['DOC_ROOT'])) {
                $result[$site['LID']]['DOC_ROOT'] = trim($site['DOC_ROOT']);
            }

            if (empty($result[$site['LID']]['LID'])) {
                $result[$site['LID']]['LID'] = $site['LID'];
            }

            if ($site['DOMAINS_DOMAIN']) {
                $result[$site['LID']]['DOMAINS'][] = trim($site['DOMAINS_DOMAIN']);
            }
        }

        return $result;
    }
}
