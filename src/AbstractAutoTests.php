<?php

declare(strict_types=1);

namespace pwd\Tests;

use function spaceonfire\Common\Env\env;

/**
 * @property array $data
 * @property array $result
 * @property string $domain
 * @property string $protocol
 * @property string|null $modeType
 *
 */
abstract class AbstractAutoTests
{
    protected array $data = [];

    protected array $result = [];

    protected string $domain;

    protected string $protocol;

    protected $modeType;

    public function __construct()
    {
        $this->domain = $_SERVER['SERVER_NAME'];
        $this->protocol = $_SERVER['HTTP_X_FORWARDED_PROTO'];
        $this->result['errors'] = [];
        $this->modeType = env('MODE_TYPE') ?? '';
    }

    /**
     * сбор данных
     */
    abstract public function collectData();

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
     * результат выполнения теста
     * @return array Массив с результатом выполнения теста
     */
    public function prepareResult(): array
    {
        $message = '';
        $message .= $this->prepareMessage('message', 'Предупреждение');
        $message .= $this->prepareMessage('errors', 'Ошибки');

        return [
            'STATUS' => $this->result['status'],
            'MESSAGE' => [
                'PREVIEW' => $this->result['status'] === true ? 'Тест пройден успешно!' : 'Тест  не пройден!',
                'DETAIL' => $message,
            ],
        ];
    }

    /**
     * обработка сообщений результата выполнения
     * @param string $type
     * @param string|null $title
     * @return string
     */
    private function prepareMessage(string $type, ?string $title = null): string
    {
        $message = '';

        if (!empty($this->result[$type])) {
            if ($title) {
                $message .= '<p><b>' . $title . '</b></p>';
            }

            foreach ($this->result[$type] as &$msg) {
                if (!empty($msg['text'])) {
                    $msgName = $msg['name'] ? '<p><b>' . $msg['name'] . '</b></p>' : '';
                    $msg = $msgName . '<ol><li>' . implode('</li><li>', $msg['text']) . '</li></ol>';
                }
            }

            $message .= '<ul><li>' . implode('</li><li>', $this->result[$type]) . '</li></ul>';
        }

        return $message;
    }

    /**
     * обраблотка режим разработки
     * @return bool
     */
    public function isModeDev(): bool
    {
        if ($this->modeType === 'dev') {
            return true;
        }

        return false;
    }

    /**
     * обраблотка режим прода
     * @return bool
     */
    public function isModeProd(): bool
    {
        if ($this->modeType === 'prod') {
            return true;
        }

        return false;
    }

    /**
     * добавляем ошибки в массив
     * @param array $errors
     * @param string $groupID
     * @return void
     */
    public function addErrors(array $errors, string $groupID): void
    {
        $errorsAdd[$groupID]['text'] = $errors;
        $this->result['errors'] = array_merge($this->result['errors'], $errorsAdd);
    }
}
