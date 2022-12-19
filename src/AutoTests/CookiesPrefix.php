<?php

declare(strict_types=1);

namespace pwd\Tests\AutoTests;

use Bitrix\Main\Config\Option;
use pwd\Tests\AbstractAutoTests;

/**
 * @property string $templatePrefix
 */

class CookiesPrefix extends AbstractAutoTests
{
    private string $templatePrefix = '#PROJECT#_PROD';

    public function collectData(): void
    {
        $this->data['cookie'] = Option::get('main', 'cookie_name');
    }

    public function compare(): void
    {
        if (empty($this->data['cookie'])) {
            $this->result['errors'][] = 'У cookie префикс в настройках главного модуля не установлен.';
            parent::compare();
            return;
        }

        if (!preg_match('@^([A-Z0-9]+)_PROD@i', $this->data['cookie'])) {
            $this->result['errors'][] = 'У cookie установлен неверный префикс (' . $this->data['cookie'] . ').
            Префикс должен соответствовать шаблону ' . $this->templatePrefix;
        }

        parent::compare();
    }
}
