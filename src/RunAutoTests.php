<?php

declare(strict_types=1);

namespace pwd\Tests;

class RunAutoTests
{
    /**
     * прослойка для запуска автотеста, так как Bitrix не создает объект при запуске автотеста
     * @param array $params параметры, передаваемые в метод запуска теста
     */
    public static function run(array $params)
    {
        if ($class = $params['class']) {
            try {
                if (class_exists($class) && (method_exists($class, 'run'))) {
                    $test = new $class;
                    return $test->run();
                }
            } catch (Throwable $e) {
                Bitrix\Main\Application::getInstance()->getExceptionHandler()->writeToLog($e);
            }
        }
    }
}
