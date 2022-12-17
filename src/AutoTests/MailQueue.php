<?php

declare(strict_types=1);

namespace pwd\Tests\AutoTests;

use Bitrix\Main\Mail\Internal\EventTable;
use pwd\Tests\AbstractAutoTests;

class MailQueue extends AbstractAutoTests
{
    public function collectData(): void
    {
        $this->data['mailCount'] = EventTable::getList([
            'select' => ['COUNT'],
            'filter' => ['!SUCCESS_EXEC' => 'Y'],
            'runtime' => [
                new \Bitrix\Main\Entity\ExpressionField('COUNT', 'COUNT(*)'),
            ],
        ])->fetch()['COUNT'];
    }

    public function compare(): void
    {
        if ($this->data['mailCount'] > 0) {
            $this->result['errors'][] = 'В очереди на отправку ' . $this->data['mailCount'] . ' писем. Очередь должна быть пустая.';
        }

        parent::compare();
    }
}
