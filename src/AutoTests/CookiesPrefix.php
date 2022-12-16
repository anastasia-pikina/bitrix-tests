<?php

namespace pwd\Tests\AutoTests;

use Bitrix\Main\Config\Option;
use pwd\Tests\AbstractAutoTests;

/**
 * @property string $cookiePrefix
 */

class CookiesPrefix extends AbstractAutoTests
{
    private string $cookiePrefix = '#PROJECT#_PROD';

    function collectData(): void
    {
        $this->data['cookie'] = Option::get("main", "cookie_name");
    }

    function compare(): void
    {
        if ($this->data['cookie']) {
            if ($this->cookiePrefix !== $this->data->cookie) {
                $this->result['errors'][] = 'У cookie установлен неверный префикс (' . $this->data['cookie'] . ')';
            }
        } else {
            $this->result['errors'][] = 'У cookie префикс в настройках главного модуля не установлен.';
        }

        parent::compare();
    }
}
