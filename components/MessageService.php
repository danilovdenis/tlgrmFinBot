<?php

declare(strict_types=1);

namespace app\components;

use app\models\Currencies;
use app\models\CurrenciesToUsers;
use app\models\Users;
use yii\base\BaseObject;
use SoapFault;

/**
 * Сервис отправки сообщений.
 */
class MessageService extends BaseObject {

	/** @var TelegramBotService Сервис для работы с ботом телеграмма */
	protected $bot;

	/** @var ApiService API получения данных из сервиса */
	protected $api;

	/**
	 * MessageService constructor.
	 *
	 * @param TelegramBotService $bot Сервис для работы с ботом телеграмма
	 * @param ApiService         $api API получения данных из сервиса
	 */
	public function __construct(TelegramBotService $bot, ApiService $api) {
		$this->bot = $bot;
		$this->api = $api;

		parent::__construct();
	}

	/**
	 * Отправка тестового сообщения.
	 *
	 * @param int    $userId  Идентификатор пользователя
	 * @param string $message Текст сообщения
	 */
	public function sendTestMessage($userId, $message) {
		$this->bot->sendUserMessage($userId, $message);
	}

	/**
	 * Отправка курсов пользователям.
	 *
	 * @throws SoapFault
	 */
	public function sendRatesToUsers() {
		$this->api->updateRates();

		$rates = Currencies::find()
			->select([Currencies::ATTR_SHORT_NAME])
			->indexBy([Currencies::ATTR_SHORT_NAME])
			->column();

		$usersIds = Users::find()
			->select([Users::ATTR_ID])
			->column();

		foreach ($usersIds as $userId) {
			$this->bot->sendUserMessage($userId, urlencode(implode("\n", $rates)));
		}
	}

	/**
	 * Отправка курса пользователям.
	 *
	 * @throws SoapFault
	 */
	public function sendRateToUser() {
		$this->api->updateRates();

		$subscribes = CurrenciesToUsers::find()
			->all()
		;/** @var CurrenciesToUsers[] $subscribes */

		foreach ($subscribes as $subscribe) {
			$rate = Currencies::find()
				->where([Currencies::ATTR_ID => $subscribe->cur_id])
				->one();/** @var Currencies $rate */

			$this->bot->sendUserMessage($subscribe->user_id, $rate->short_name . ': ' . $rate->value);
		}

		return true;
	}
}
