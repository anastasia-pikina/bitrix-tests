<?php

namespace pwd\Tests\AutoTests;

use Bitrix\Main\Mail\Internal\EventTable;
use pwd\Tests\AbstractAutoTests;

class MailQueue extends AbstractAutoTests
{
    function collectData(): void
    {
        $this->data['mailCount'] = EventTable::getList([
            'select' => ['COUNT'],
            'filter' => ['!SUCCESS_EXEC' => 'Y'],
            'runtime' => array(
                new \Bitrix\Main\Entity\ExpressionField('COUNT', 'COUNT(*)')
            )
        ])->fetch()['COUNT'];
    }

    function compare(): void
    {
        if ($this->data['mailCount'] > 0) {
            $this->result['errors'][] = 'В очереди на отправку ' . $this->data['mailCount'] . ' писем. Очередь должна быть пустая.';
        }

        parent::compare();
    }
}
