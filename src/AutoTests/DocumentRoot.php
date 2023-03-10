<?php

declare(strict_types=1);

namespace pwd\Tests\AutoTests;

use pwd\Tests\AbstractAutoTests;

class DocumentRoot extends AbstractAutoTests
{
    public function collectData(): void
    {
        // sites DOCUMENT_ROOT
        $sites = \Bitrix\Main\SiteTable::getList([
            'filter' => ['ACTIVE' => 'Y'],
            'select' => ['LID', 'DOC_ROOT'],
        ]);

        while ($site = $sites->fetch()) {
            $this->data['sites'][$site['LID']] = trim($site['DOC_ROOT']);
        }
    }

    public function compare(): void
    {
        $documentRoot = $_SERVER['DOCUMENT_ROOT'];

        if ($this->data['sites']) {
            $documentRootCheck = false;

            foreach ($this->data['sites'] as $sitedocumentRootID => $sitedocumentRoot) {
                if ($sitedocumentRoot) {
                    if ($documentRoot === $sitedocumentRoot) {
                        $documentRootCheck = true;
                    }
                } else {
                    $this->result['errors'][] = 'DOCUMENT_ROOT в настройках сайта "' . $sitedocumentRootID . '" не установлено.';
                }
            }

            if ($documentRootCheck === false) {
                $this->result['errors'][] = 'Текущий DOCUMENT_ROOT (' . $documentRoot . ') не совпадает в настройках ни одного активного сайта.';
            }
        }

        parent::compare();
    }
}
