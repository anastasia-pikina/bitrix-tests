<?php

namespace pwd\Tests;

use Bitrix\Main\EventManager;
use pwd\Tests\GenerateTestList;

class EventHandler
{
    /**
     * Регистрирует обработчик для модификации тестов
     * Вызывается автоматически при подключении autoloader.
     */
    public static function boot(): void
    {
        $eventManager = EventManager::getInstance();
        $eventManager->addEventHandler(
            'main',
            'OnCheckListGet',
            ['pwd\Tests\GenerateTestList', 'getTestList']
        );
    }
}
