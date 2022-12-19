<?php

declare(strict_types=1);

namespace pwd\Tests\AutoTests;

use pwd\Tests\{AbstractAutoTests};

/**
 * @property string $fileSitemapName
 */
class Sitemap extends AbstractAutoTests
{
    private string $fileSitemapName = 'sitemap*.xml';

    public function collectData(): void
    {
        $dir = $_SERVER['DOCUMENT_ROOT'] . '/';

        foreach (glob($dir . '/' . $this->fileSitemapName) as $file) {
            $filesSitemap = basename($file);
            $reader = new \XMLReader;
            $reader->open($dir . $filesSitemap);

            while ($reader->read()) {
                if ($reader->nodeType === \XMLReader::ELEMENT && $reader->localName === 'loc') {
                    $reader->read();
                    if ($reader->nodeType === \XMLReader::TEXT
                        && preg_match('@^([(http(s*))://]*)?([^/]+)@i', $reader->value, $matches)
                    ) {
                        $this->data['sitemap'][$filesSitemap][] = [
                            'protocol' => $matches[1] ? str_replace('://', '', $matches[1]) : '',
                            'domain' => $matches[2] ?? '',
                            'value' => $reader->value,
                        ];
                    }
                }
            }

            $reader->close();
        }
    }

    public function compare(): void
    {
        if (empty($this->data)) {
            $this->result['message'][] = 'Не найдено ни одного файла ' . $this->fileSitemapName . '.';
            parent::compare();
            return;
        }

        foreach ($this->data['sitemap'] as $sitemapFile => $sitemapValues) {

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
                } elseif ($url['domain'] !== $this->domain) {
                    $fileErrors[] = 'Домен указан неверно (' . $url['value'] . ').';
                }
            }

            if (!empty($fileErrors)) {
                $this->result['errors'][$sitemapFile] = [
                    'name' => 'Файл ' . $sitemapFile . ':',
                    'text' => $fileErrors,
                ];
            }
        }

        parent::compare();
    }
}
