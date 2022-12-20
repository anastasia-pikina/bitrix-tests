<?php

declare(strict_types=1);

namespace pwd\Tests;

/**
 * @property string $testsFile
 * @property string $testsParentCode
 */
class GenerateTestList
{
    public static string $testsFile = __DIR__ . '/../tests.json';

    public static string $testsParentCode = 'PWD';

    /**
     * результат выполнения теста
     * @param array $arCheckList
     * @return array Массив со списком тестов
     */
    static public function getTestList(
        array $arCheckList
    ): array
    {
        $checkList = [
            'CATEGORIES' => [],
            'POINTS' => [],
        ];

        $checkList['CATEGORIES'][self::$testsParentCode] = [
            'NAME' => 'Тесты PWD',
            'LINKS' => '',
        ];

        if (file_exists(self::$testsFile)) {
            $tests = json_decode(file_get_contents(self::$testsFile));

            foreach ($tests as $test) {

                $testData = [
                    'PARENT' => self::$testsParentCode,
                    'REQUIRE' => 'Y',
                    'AUTO' => $test->type === 'Auto' ? 'Y' : 'N',
                    'NAME' => $test->name,
                    'DESC' => 'Ответственный: ' . $test->responsible,
                    'HOWTO' => $test->description,
                    //'FAILED' => 'Y',
                ];

                if ($test->type === 'Auto' && $test->code) {
                    $className = ucwords(strtolower(str_replace('_', ' ', $test->code)));

                    $className = __NAMESPACE__ . '\AutoTests\\' . str_replace(' ', '', $className);

                    $testData['CLASS_NAME'] = RunAutoTests::class;
                    $testData['METHOD_NAME'] = 'run';
                    $testData['PARAMS'] = [
                        'class' => $className,
                    ];
                }

                $checkList['POINTS'][$test->code] = $testData;
            }
        }
        return $checkList;
    }

}
