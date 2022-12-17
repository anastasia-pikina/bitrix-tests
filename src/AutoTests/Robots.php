<?php

declare(strict_types=1);

namespace pwd\Tests\AutoTests;

use pwd\Tests\AbstractAutoTests;
use pwd\Tests\Helper;

/**
 * @property string $fileRobotsName
 * @property string $fileGitName
 */
class Robots extends AbstractAutoTests
{
    private string $fileRobotsName = 'robots.txt';

    private string $fileGitName = '.gitignore';

    public function collectData(): void
    {
        $this->getGitignoreData();

        $robotsURL = $_SERVER['DOCUMENT_ROOT'] . '/' . $this->fileRobotsName;

        if ($this->modeType === 'dev') {
            if (filesize($robotsURL) > 0) {
                $this->result['errors'][] = $this->fileRobotsName . ' в режиме разработки должен быть пустой.';
            }
            return;
        }

        $file = Helper::getFile($_SERVER['DOCUMENT_ROOT'] . '/', $this->fileRobotsName);

        if ($file['errors']) {
            $this->result['errors'] = array_merge($this->result['errors'], $file['errors']);
            return;
        }

        $hasHost = $this->getDataRobots($file['handle']);
        fclose($file['handle']);

        if ($hasHost === false) {
            $this->result['errors'][] = 'В файле ' . $this->fileRobotsName . ' директива HOST не найдена.';
        }
    }

    public function compare(): void
    {
        if (!empty($this->data)) {
            // protocol
            if (isset($this->data['protocol'])) {
                if ($this->data['protocol'] !== '') {
                    if ($this->data['protocol'] !== $this->protocol) {
                        $this->result['errors'][] = 'В файле ' . $this->fileRobotsName . ' в директиве HOST протокол указан неверно.
                    Указано: ' . $this->data['protocol'] . '. Верное значение: ' . $this->protocol;
                    }
                } else {
                    $this->result['message'][] = 'В файле ' . $this->fileRobotsName . ' в директиве HOST протокол не указан.';
                }
            }

            // domain
            if (isset($this->data['domain'])) {
                if ($this->data['domain'] !== '') {
                    if ($this->data['domain'] !== $this->domain) {
                        $this->result['errors'][] = 'В файле ' . $this->fileRobotsName . ' в директиве HOST домен указан неверно.
                    Указано: ' . $this->data['domain'] . '. Верное значение: ' . $this->domain;
                    }
                } else {
                    $this->result['errors'][] = 'В файле ' . $this->fileRobotsName . ' в директиве HOST домен не указан.';
                }
            }

            // gitignote
            if ($this->data['gitignore'] !== true) {
                $this->result['errors'][] = 'Файл ' . $this->fileRobotsName . ' не находится в ' . $this->fileGitName . ' .';
            }
        }

        parent::compare();
    }

    /**
     * данные о домене из robots.txt
     * @param $handle
     * @return bool
     */
    private function getDataRobots($handle): bool
    {
        while (($string = fgets($handle, 4096)) !== false) {
            $result = preg_split("/[\s]+/", $string);

            if ($result[0] !== 'Host:') {
                continue;
            }

            if (empty($result[1])) {
                $this->result['errors'][] = 'В файле ' . $this->fileRobotsName . ' в директиве HOST домен не указан.';
                fclose($handle);
                return true;
            }

            if (preg_match('@^([(http(s*))://]*)?([^/]+)@i', $result[1], $matches)) {
                $this->data['protocol'] = $matches[1] ? str_replace('://', '', $matches[1]) : '';
                $this->data['domain'] = $matches[2] ?? '';
            }

            return true;
        }

        return false;
    }

    /**
     * данные .gitignore
     * @return void
     */
    private function getGitignoreData(): void
    {
        $this->data['gitignore'] = false;
        $file = Helper::getFile($_SERVER['DOCUMENT_ROOT'] . '/', $this->fileGitName);

        if ($file['errors']) {
            $this->result['errors'] = array_merge($this->result['errors'], $file['errors']);
            return;
        }

        while (($string = fgets($file['handle'], 4096)) !== false) {
            if (strpos($string, '/robots') === 0) {
                $this->data['gitignore'] = true;
                fclose($file['handle']);
                return;
            }
        }

        fclose($file['handle']);
    }
}
