<?php

declare(strict_types=1);

namespace app\components;

use app\models\Currencies;
use app\models\CurrenciesToUsers;
use app\models\Users;
use yii\base\InvalidConfigException;

/**
 * Обработчик комманд.
 *
 * @package app\components
 */
class CommandProcessor {

	const COMMAND_START = '/start';
	const COMMAND_STOP  = '/stop';

	/**
	 * @param int         $userId
	 * @param string|null $command
	 *
	 * @return string[]
	 */
	public function init(int $userId, ?string $command = ''): array {
		$result = [
			'result'  => false,
			'message' => '',
		];

		switch ($command) {
			case static::COMMAND_START:
				if (false === $this->saveUser($userId)) {
					return $result;
				}

				$result['result']  = true;
				$result['message'] = 'Пользователь добавлен';

				return $result;
			case
			static::COMMAND_STOP:
				if (false === $this->delUser($userId)) {
					return $result;
				}

				$result['result']  = true;
				$result['message'] = 'Пользователь отписан';

				return $result;
			default:
				$answer = $this->process($userId, $command);

				if (false === $answer) {
					return $result;
				}

				$result['result']  = true;
				$result['message'] = $answer;

				return $result;
		}
	}

	/**
	 * Удалить пользователя
	 *
	 * @param int $userId Идентификатор пользователя
	 *
	 * @return bool
	 */
	protected function delUser(int $userId): bool {
		// @todo Транзакция
		Users::deleteAll([Users::ATTR_ID => $userId]);
		CurrenciesToUsers::deleteAll([CurrenciesToUsers::ATTR_ID => $userId]);

		return true;
	}

	/**
	 * Сохранить пользователя в базу
	 *
	 * @param int $userId
	 *
	 * @return true
	 */
	protected function saveUser(int $userId): bool {
		$userExists = Users::find()
			->where([
				Users::ATTR_TELEGRAMM_ID => $userId,
			])
			->exists();

		if (false === $userExists) {
			$users               = new Users;
			$users->telegramm_id = $userId;

			if (false === $users->save()) {
				return false;
			}
		}

		return true;
	}

	/**
	 * Обработка сообщения
	 *
	 * @param int         $userId
	 * @param string|null $text
	 *
	 * @return bool|string|null
	 */
	protected function process(int $userId, ?string $text = '') {
		$this->saveUser($userId);

		$curr = Currencies::find()
			->all();
		/** @var Currencies[] $curr */

		foreach ($curr as $cur) {
			if ($cur->short_name !== strtoupper($text)) {
				continue;
			}

			if (false === $this->saveLink($userId, $cur)) {
				return false;
			}

			return 'Вы подписались на рассылку курса валюты: ' . strtoupper($text);
		}

		return 'Укажите нужную валюту';
	}

	/**
	 * Сохранить связь пользователя с валютой
	 *
	 * @param int        $userId
	 * @param Currencies $cur
	 *
	 * @return bool
	 */
	protected function saveLink(int $userId, Currencies $cur): bool {
		$existLink = CurrenciesToUsers::find()
			->where([
				CurrenciesToUsers::ATTR_USER_ID => $userId,
			])
			->one();
		/** @var CurrenciesToUsers $existLink */

		if (NULL === $existLink) {
			$link          = new CurrenciesToUsers;
			$link->user_id = $userId;
			$link->cur_id  = $cur->id;

			if (false === $link->save()) {
				return false;
			}
		} else {
			$existLink->cur_id = $cur->id;

			if (false === $existLink->save()) {
				return false;
			}
		}

		return true;
	}
}