<?php

declare(strict_types=1);

namespace pwd\Tests\AutoTests;

use Bitrix\Main\Config\Option;
use pwd\Tests\{AbstractAutoTests, Helper};

class Domain extends AbstractAutoTests
{
    public function collectData(): void
    {
        $this->data['main'] = Option::get('main', 'server_name');
        $this->data['sites'] = Helper::getSites();
    }

    public function compare(): void
    {
        if ($this->data['main']) {
            if ($this->domain !== $this->data['main']) {
                $this->result['errors'][] = 'Доменное имя (' . $this->domain . ') в настройках главного модуля указано неверно.';
            }
        } else {
            $this->result['errors'][] = 'Доменное имя (' . $this->domain . ') в настройках главного модуля не установлено.';
        }

        if ($this->data['sites']) {
            $serverNameCheck = false;
            $domainCheck = false;

            foreach ($this->data['sites'] as $siteDomainID => $site) {
                if ($this->checkDomain(
                        $this->domain,
                        $site['SERVER_NAME'],
                        $siteDomainID,
                        'URL сервера'
                    ) === true) {
                    $serverNameCheck = true;
                }

                if ($this->checkDomain(
                        $this->domain,
                        $site['DOMAINS'],
                        $siteDomainID,
                        'Доменное имя'
                    ) === true) {
                    $domainCheck = true;
                }
            }

            if ($serverNameCheck === false) {
                $this->result['errors'][] = 'URL сервера (' . $this->domain . ') не совпадает в настройках ни одного активного сайта.';
            }

            if ($domainCheck === false) {
                $this->result['errors'][] = 'Доменное имя (' . $this->domain . ') не совпадает в настройках ни одного активного сайта.';
            }
        }

        parent::compare();
    }

    /**
     * результат выполнения теста
     * @param string $value
     * @param array|string $comparison
     * @param string $siteID
     * @param string $message
     * @return bool результат сравнения
     */
    private function checkDomain(string $value, $comparison, string $siteID, string $message): bool
    {
        if ($comparison) {
            return Helper::checkIdentically($value, $comparison);
        }
        $this->result['errors'][] = $message . ' в настройках сайта "' . $siteID . '" не установлено.';

        return false;
    }
}
