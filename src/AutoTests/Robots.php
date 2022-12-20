<?php

declare(strict_types=1);

namespace pwd\Tests\AutoTests;

use pwd\Tests\{AbstractAutoTests, Helper};

/**
 * @property string $fileRobotsName
 * @property string $fileGitName
 * @property string $sites
 */
class Robots extends AbstractAutoTests
{
    private string $fileRobotsName = 'robots.txt';

    private string $fileGitName = '.gitignore';

    private array $sites;

    public function __construct()
    {
        $this->sites = Helper::getSites();
        parent::__construct();
    }

    public function collectData(): void
    {
        foreach ($this->sites as $site) {
            if (empty($site['DOC_ROOT'])) {
                $this->result['errors'][$site['LID']]['text'][] = 'У сайта ' . $site['LID'] . ' не указан путь к корневой папке.';
                continue;
            }

            $this->getGitignoreData($site);
            $this->getRobotsData($site);
        }
    }

    public function compare(): void
    {
        if (empty($this->data)) {
            parent::compare();
            return;
        }

        foreach ($this->data as $siteID => $site) {
            // protocol
            if (isset($site['protocol'])) {
                if ($site['protocol'] !== '') {
                    if ($site['protocol'] !== $this->protocol) {
                        $this->result['errors'][$siteID]['text'][] = 'В файле ' . $this->fileRobotsName . ' в директиве HOST протокол указан неверно.
                    Указано: ' . $site['protocol'] . '. Верное значение: ' . $this->protocol;
                    }
                } else {
                    $this->result['message'][$siteID]['text'][] = 'В файле ' . $this->fileRobotsName . ' в директиве HOST протокол не указан.';
                }
            }

            // domain
            if (isset($site['domain'])) {
                if ($site['domain'] !== '') {
                    if (is_array($this->sites[$siteID]['DOMAINS']) && !in_array($site['domain'], $this->sites[$siteID]['DOMAINS'], false)) {
                        $this->result['errors'][$siteID]['text'][] = 'В файле ' . $this->fileRobotsName . ' в директиве HOST домен указан неверно.
                    Указано: ' . $site['domain'] . '. Верное значение одно из: ' . implode(', ', $this->sites[$siteID]['DOMAINS']);
                    }
                } else {
                    $this->result['errors'][$siteID]['text'][] = 'В файле ' . $this->fileRobotsName . ' в директиве HOST домен не указан.';
                }
            }

            // .gitignore
            if ($site['gitignore'] !== true) {
                $this->result['errors'][$siteID]['text'][] = 'Файл ' . $this->fileRobotsName . ' не находится в ' . $this->fileGitName . ' .';
            }

            if (!empty($this->result['errors'][$siteID]['text'])) {
                $this->result['errors'][$siteID]['name'] = 'Сайт ' . $siteID;
            }
        }
        ksort($this->result['errors']);
        parent::compare();
    }

    /**
     * данные .gitignore
     * @return void
     */
    private function getGitignoreData($site): void
    {
        $this->data[$site['LID']]['gitignore'] = false;
        $file = Helper::getFile($site['DOC_ROOT'] . '/', $this->fileGitName);

        if ($file['errors']) {
            $this->addErrors($file['errors'], $site['LID']);
            $this->result['errors'][$site['LID']]['name'] = 'Сайт ' . $site['LID'];
            return;
        }

        while (($string = fgets($file['handle'], 4096)) !== false) {
            if (strpos($string, '/robots') === 0) {
                $this->data[$site['LID']]['gitignore'] = true;
                fclose($file['handle']);
                return;
            }
        }

        fclose($file['handle']);
    }

    /**
     * данные robots.txt
     * @return void
     */
    private function getRobotsData($site): void
    {
        if (empty($site['DOMAINS'])) {
            $this->result['errors'][$site['LID']]['text'][] = 'У сайта ' . $site['LID'] . ' не указано ни одного доменного имени.';
            return;
        }

        $dir = $site['DOC_ROOT'] . '/';
        $robotsURL = $dir . $this->fileRobotsName;

        if ($this->isModeDev()) {
            if (!file_exists($robotsURL)) {
                $this->result['errors'][$site['LID']]['text'][] = 'Файл ' . $this->fileRobotsName . ' не найден.';
                return;
            }

            if (filesize($robotsURL) > 0) {
                $this->result['errors'][$site['LID']]['text'][] = 'Файл ' .
                    $this->fileRobotsName . ' в режиме разработки должен быть пустой.';
            }
            return;
        }

        $file = Helper::getFile($dir, $this->fileRobotsName);

        if ($file['errors']) {
            $this->addErrors($file['errors'], $site['LID']);
            $this->result['errors'][$site['LID']]['name'] = 'Сайт ' . $site['LID'];
            return;
        }

        $hasHost = $this->prepareRobotsData($file['handle'], $site['LID']);
        fclose($file['handle']);

        if ($hasHost === false) {
            $this->result['errors'][$site['LID']]['text'][] = 'В файле ' . $this->fileRobotsName . ' директива HOST не найдена.';
        }
    }

    /**
     * данные о домене из robots.txt
     * @param $handle
     * @return bool
     */
    private function prepareRobotsData($handle, $siteID): bool
    {
        while (($string = fgets($handle, 4096)) !== false) {
            $result = preg_split("/[\s]+/", $string);

            if ($result[0] !== 'Host:') {
                continue;
            }

            if (empty($result[1])) {
                $this->result['errors'][$siteID]['text'][] = 'В файле ' . $this->fileRobotsName . ' в директиве HOST домен не указан.';
                return true;
            }

            $dataRobots = Helper::getDataUrl($result[1]);
            if ($dataRobots['protocol']) {
                $this->data[$siteID]['protocol'] = $dataRobots['protocol'];
            }

            if ($dataRobots['domain']) {
                $this->data[$siteID]['domain'] = $dataRobots['domain'];
            }

            return true;
        }

        return false;
    }
}
