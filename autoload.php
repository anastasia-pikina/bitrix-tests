<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    return;
}

use pwd\Tests;

try {
    Tests\EventHandler::boot();
} catch (Throwable $e) {
    Bitrix\Main\Application::getInstance()->getExceptionHandler()->writeToLog($e);
}
