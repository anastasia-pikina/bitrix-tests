<?php

declare(strict_types=1);

namespace pwd\Tests\AutoTests;

use pwd\Tests\{AbstractAutoTests, Helper};

/**
 * @property string $fileSitemapName
 */
class Sitemap extends AbstractAutoTests
{
    private string $fileSitemapName = 'sitemap*.xml';

    public function collectData(): void
    {
        $sites = Helper::getSites();

        foreach ($sites as $siteID => $site) {
            if (empty($site['DOC_ROOT'])) {
                $this->result['errors'][] = 'У сайта ' . $siteID . ' не указан путь к корневой папке.';
                continue;
            }

            if (empty($site['DOMAINS'])) {
                $this->result['errors'][] = 'У сайта ' . $siteID . ' не указано ни одного доменного имени.';
                continue;
            }

            $dir = $site['DOC_ROOT'] . '/';
            $this->data['sitemap'][$siteID]['domains'] = $site['DOMAINS'];

            foreach (glob($dir . $this->fileSitemapName) as $file) {
                $filesSitemap = basename($file);
                $reader = new \XMLReader;
                $reader->open($dir . $filesSitemap);

                while ($reader->read()) {
                    if ($reader->nodeType === \XMLReader::ELEMENT && $reader->localName === 'loc') {
                        $reader->read();
                        if ($reader->nodeType === \XMLReader::TEXT && $reader->value) {
                            $dataURL = Helper::getDataUrl($reader->value);

                            if (!empty($dataURL)) {
                                $this->data['sitemap'][$siteID]['files'][$filesSitemap][] = [
                                    'protocol' => $dataURL['protocol'],
                                    'domain' => $dataURL['domain'],
                                    'value' => $reader->value,
                                ];
                            }
                        }
                    }
                }

                $reader->close();
            }

            if (empty($this->data['sitemap'][$siteID]['files'])) {
                $this->result['message'][] = 'У сайта ' . $siteID . ' не найдено ни одного файла ' . $this->fileSitemapName . '.';
            }
        }
    }

    public function compare(): void
    {
        if (empty($this->data)) {
            $this->result['message'][] = 'Не найдено ни одного файла ' . $this->fileSitemapName . '.';
            parent::compare();
            return;
        }

        foreach ($this->data['sitemap'] as $siteID => $siteSitemap) {
            foreach ($siteSitemap['files'] as $sitemapFile => $sitemapValues) {

                $fileErrors = [];
                foreach ($sitemapValues as $url) {

                    // protocol
                    if (empty($url['protocol'])) {
                        $fileErrors[] = 'Протокол не указан (' . $url['value'] . ').';
                    } elseif ($url['protocol'] !== $this->protocol) {
                        $fileErrors[] = 'Протокол указан неверно (' . $url['value'] . ').';
                    }

                    // domain
                    if (empty($url['domain'])) {
                        $fileErrors[] = 'Домен не указан (' . $url['value'] . ').';
                    } elseif (!in_array($url['domain'], $siteSitemap['domains'], false)) {
                        $fileErrors[] = 'Домен указан неверно (' . $url['value'] . ').';
                    }
                }

                if (!empty($fileErrors)) {
                    $this->result['errors'][$sitemapFile] = [
                        'name' => 'Файл ' . $sitemapFile . ' (сайт ' . $siteID . '):',
                        'text' => $fileErrors,
                    ];
                }
            }
        }

        ksort($this->result['errors']);
        parent::compare();
    }
}
