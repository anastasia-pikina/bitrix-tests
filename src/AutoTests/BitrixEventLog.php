<?php

namespace pwd\Tests\AutoTests;

use Bitrix\Main\Config\Option;
use pwd\Tests\AbstractAutoTests;
use Bitrix\Main\Localization\Loc;

/**
 * @property array $eventLogSettings
 */

class BitrixEventLog extends AbstractAutoTests
{
	private array $eventLogSettings = [
		'event_log_logout'           => 'Записывать выход из системы',
		'event_log_login_success'    => 'Записывать успешный вход',
		'event_log_login_fail'       => 'Записывать ошибки входа',
		'event_log_permissions_fail' => 'Записывать ошибки доступа к файлам',
		'event_log_block_user'       => 'Записывать блокировку пользователя',
		'event_log_register'         => 'Записывать регистрацию нового пользователя',
		'event_log_register_fail'    => 'Записывать ошибки регистрации',
		'event_log_password_request' => 'Записывать запросы на смену пароля',
		'event_log_password_change'  => 'Записывать смену пароля',
		'event_log_user_edit'        => 'Записывать редактирование пользователя',
		'event_log_user_delete'      => 'Записывать удаление пользователя',
		'event_log_user_groups'      => 'Записывать изменение групп пользователя',
		'event_log_group_policy'     => 'Записывать изменение политики безопасности группы',
		'event_log_module_access'    => 'Записывать изменение доступа к модулю',
		'event_log_file_access'      => 'Записывать изменение доступа к файлу',
		'event_log_task'             => 'Записывать изменение уровня доступа',
		'event_log_marketplace'      => 'Записывать установку и удаление решений из Marketplace',
	];

    /**
     * @inheritDoc
     */
    public function collectData(): void
    {
		foreach ($this->eventLogSettings as $settingCode => $setting) {
			$this->data['setting'][$settingCode] = Option::get("main", $settingCode);
		}
    }

	function compare(): void
	{
		foreach ($this->data['setting'] as $settingCode => $setting) {
			if ($setting !== 'Y') {
				$this->result['errors'][] = 'Не собирается статистика по "' . $this->eventLogSettings[$settingCode] . '".';
			}
		}

		parent::compare();
	}
}
