<?php

declare(strict_types=1);

namespace pwd\Tests\AutoTests;

use Bitrix\Main\Config\Option;
use pwd\Tests\AbstractAutoTests;

class CopyDevelopment extends AbstractAutoTests
{

    /**
     * @inheritDoc
     *
     */
    public function collectData(): void
    {
        $this->data['development'] = Option::get('main', 'update_devsrv');
    }

    public function compare(): void
    {
        if (empty($this->modeType)) {
            $this->result['message'][] = 'Переменная окружения для определения среды разработки не задана.';
            parent::compare();
            return;
        }

        if ($this->data['development'] !== 'Y' && $this->isModeDev()) {
            $this->result['errors'][] = 'На тесторой площадке должна быть указана настрока "Установка для разработки" в настройках главного модуля.';
        }

        if ($this->data['development'] === 'Y' && $this->isModeProd()) {
            $this->result['errors'][] = 'На прод площадке  не должна быть указана настрока "Установка для разработки" в настройках главного модуля.';
        }

        parent::compare();
    }
}
