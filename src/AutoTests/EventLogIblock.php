<?php

declare(strict_types=1);

namespace pwd\Tests\AutoTests;

use pwd\Tests\AbstractAutoTests;

/**
 * @property array $iblockLogSettings
 */
class EventLogIblock extends AbstractAutoTests
{
	private array $iblockLogSettings = [
        'LOG_SECTION_ADD' => 'Записывать добавление раздела',
        'LOG_SECTION_EDIT' => 'Записывать изменение раздела',
        'LOG_SECTION_DELETE' => 'Записывать удаление раздела',
        'LOG_ELEMENT_ADD' => 'Записывать добавление элемента',
        'LOG_ELEMENT_EDIT' => 'Записывать изменение элемента',
        'LOG_ELEMENT_DELETE' => 'Записывать удаление элемента',
    ];

    /**
     * @inheritDoc
     */
    public function collectData(): void
    {
		$iblocks = \Bitrix\Iblock\IblockTable::getList([
            'select' => [
                'ID',
                'NAME',
                'FIELDS_' => 'FIELDS',
            ],
            'filter' => ['FIELDS.FIELD_ID' => array_keys($this->iblockLogSettings)],
            'runtime' => [
                'FIELDS' => [
                    'data_type' => '\Bitrix\Iblock\IblockFieldTable',
                    'reference' => [
                        '=this.ID' => 'ref.IBLOCK_ID',
                    ],
                    'join_type' => 'left',
                ],
            ],
        ]);

		while ($iblockData = $iblocks->fetch()) {
			$this->data[$iblockData['ID']]['NAME'] = $iblockData['NAME'];
			$this->data[$iblockData['ID']]['FIELDS'][$iblockData['FIELDS_FIELD_ID']] = $iblockData['FIELDS_IS_REQUIRED'];
		}
    }

    public function compare(): void
    {
        if (!empty($this->data)) {
            foreach ($this->data as $iblockID => $iblock) {
                foreach ($iblock['FIELDS'] as $fieldID => $fieldValue) {
                    if ($fieldValue !== 'Y') {
                        if (empty($this->result['errors'][$iblockID]['name'])) {
                            $this->result['errors'][$iblockID]['name'] = 'У инфоблока "' . $iblock['NAME'] . '" (ID: ' . $iblockID . ') не установлено:';
                        }
                        $this->result['errors'][$iblockID]['text'][] = '"' . $this->iblockLogSettings[$fieldID] . '"';
                    }
				}
			}
		} else {
			$this->result['message'][] = 'Не найдено ни одного инфоблока.';
		}

		parent::compare();
	}
}
