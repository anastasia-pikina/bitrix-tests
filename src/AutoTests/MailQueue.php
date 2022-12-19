<?php

declare(strict_types=1);

namespace pwd\Tests\AutoTests;

use Bitrix\Main\Mail\Internal\EventTable;
use pwd\Tests\AbstractAutoTests;

class MailQueue extends AbstractAutoTests
{
    public function collectData(): void
    {
        $this->data['letters'] = EventTable::getList([
            'select' => ['SUCCESS_EXEC', 'COUNT'],
            'filter' => ['!SUCCESS_EXEC' => 'Y'],
            'runtime' => [
                new \Bitrix\Main\Entity\ExpressionField('COUNT', 'COUNT(%s)', ['SUCCESS_EXEC']),
            ],
        ])->fetchAll();
    }

    public function compare(): void
    {
        if (!$this->data['letters']) {
            parent::compare();
            return;
        }

        foreach ($this->data['letters'] as $letterGroup) {
            $this->result['errors'][] = 'Количество писем в очереди на отправку cо статусом ' .
                $letterGroup['SUCCESS_EXEC'] . ': ' .
                $letterGroup['COUNT'] . '.';
        }

        parent::compare();
    }
}
