<?php

declare(strict_types=1);

namespace pwd\Tests\AutoTests;

use Bitrix\Main\Config\Option;
use pwd\Tests\AbstractAutoTests;

/**
 * @property string $cookiePrefix
 */

class CookiesPrefix extends AbstractAutoTests
{
    private string $cookiePrefix = '#PROJECT#_PROD';

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

        if ($this->cookiePrefix !== $this->data['cookie']) {
            $this->result['errors'][] = 'У cookie установлен неверный префикс (' . $this->data['cookie'] . ')';
        }

        parent::compare();
    }
}
