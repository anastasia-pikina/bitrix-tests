<?php

namespace pwd\Tests\AutoTests;

use pwd\Tests\AbstractAutoTests;

class Robots extends AbstractAutoTests
{
	public function collectData(): void
	{
		$robotsURL = $_SERVER['DOCUMENT_ROOT'] . '/robots.txt';

		if (file_exists($robotsURL)) {
			$handle = @fopen($robotsURL, "r");

			if ($handle) {
				while (($string = fgets($handle, 4096)) !== false) {

					$result = preg_split("/[\s]+/", $string);

					if ($result[0] === 'Host:') {
						if (preg_match('@^(https|http)://?([^/]+)@i', $result[1], $matches)) {
							$this->data['protocol'] = $matches[1];
							$this->data['domain'] = $matches[2];
						}
					}
				}

				fclose($handle);
			}
		}
	}

	function compare(): void
	{
		if (!empty($this->data)) {
			// protocol
			if (!empty($this->data['protocol'])) {
				if ($this->data['protocol'] !== $this->protocol) {
					$this->result['errors'][] = 'В файле robots.txt в директиве HOST протокол указан неверно.
					Указано: ' . $this->data['protocol'] . '. Верное значение: ' . $this->protocol;
				}
			} else {
				$this->result['errors'][] = 'В файле robots.txt в директиве HOST протокол не указан.';
			}

			// domain
			if (!empty($this->data['domain'])) {
				if ($this->data['domain'] !== $this->domain) {
					$this->result['errors'][] = 'В файле robots.txt в директиве HOST домен указан неверно.
					Указано: ' . $this->data['domain'] . '. Верное значение: ' . $this->domain;
				}
			} else {
				$this->result['errors'][] = 'В файле robots.txt в директиве HOST домен не указан.';
			}

		} else {
			$this->result['errors'][] = 'Файл robots.txt не найден.';
		}

		parent::compare();
	}
}
