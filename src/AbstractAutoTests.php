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
	protected string $protocol;

    public function __construct()
    {
		$this->domain = $_SERVER['SERVER_NAME'];
		$this->protocol = $_SERVER['HTTP_X_FORWARDED_PROTO'];
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
		$message .= $this->prepareMessage('message');
		$message .= $this->prepareMessage('errors');

		return [
			'STATUS' => $this->result['status'],
			'MESSAGE' => [
				'PREVIEW' => ($this->result['status'] === true) ? 'Тест пройден успешно!' : 'Тест  не пройден!',
				'DETAIL' => $message,
			],
		];
	}

	/**
	 * обраблотка сообщений результата выполнения
	 * @param string $type
	 * @return string
	 */

	private function prepareMessage(string $type): string
	{
		$message = '';

		if (!empty($this->result[$type])) {
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
}
