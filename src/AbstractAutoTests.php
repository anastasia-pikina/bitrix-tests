<?php

namespace pwd\Tests;

/**
 * @property array $data
 * @property array $result
 * @property string $domain
 */
abstract class AbstractAutoTests
{
    protected array $data;
    protected array $result;
    protected string $domain;

    public function __construct()
    {
        $this->domain = $_SERVER['SERVER_NAME'];
    }

    /**
     * сбор данных
     */

    abstract public function collectData();

    /**
     * анализ данных
     */

    public function compare(): void
    {
        $this->result['status'] = false;

        if (empty($this->result['errors'])) {
            $this->result['status'] = true;
        }
    }

    /**
     * запускает выполнения теста
     * @return array Массив с результатом выполнения теста
     */

    public function run(): array
    {
        $this->collectData();
        $this->compare();
        return $this->prepareResult();
    }

    /**
     * результат выполнения теста
     * @return array Массив с результатом выполнения теста
     */
    public function prepareResult(): array
    {
        $message = '';

        if (is_array($this->result['errors'])) {
            $message = implode('<br>', $this->result['errors']);
        }

        return [
            'STATUS' => $this->result['status'],
            'MESSAGE' => [
                'PREVIEW' => ($this->result['status'] === true) ? 'Тест пройден успешно!' : 'Тест  не пройден!',
                'DETAIL' => $message,
            ],
        ];
    }
}
