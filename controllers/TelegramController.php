<?php

declare(strict_types=1);

namespace app\controllers;

use app\components\MessageService;
use app\components\TelegramBotService;
use app\models\Currencies;
use app\models\CurrenciesToUsers;
use app\models\Users;
use Longman\TelegramBot\Telegram;
use Yii;
use yii\filters\VerbFilter;
use yii\web\Controller;

class TelegramController extends Controller {
	/**
	 * {@inheritdoc}
	 */
	public function behaviors() {
		return [
			'verbs' => [
				'class'   => VerbFilter::class,
				'actions' => [
					'set' => ['post'],
				],
			],
		];
	}

	/**
	 * @param \yii\base\Action $action
	 *
	 * @return bool
	 * @throws \yii\web\BadRequestHttpException
	 */
	public function beforeAction($action) {
		if ('set' == $action->id) {
			$this->enableCsrfValidation = false;
		}

		return parent::beforeAction($action);
	}

	/**
	 * Установка курса валют для пользователя
	 *
	 * @return string
	 *
	 * @throws
	 */
	public function actionSet() {
		$telegram = new Telegram((new TelegramBotService())->getToken(), 'exchageRatesBot');

		$telegram->handle();

		$input = json_decode($telegram->getCustomInput());

		//@todo валидация
		$userId = $input->message->from->id;

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

		$text = $input->message->text;

		$curr = Currencies::find()
			->all();/** @var Currencies[] $curr */

		$message = Yii::createObject(MessageService::class);

		foreach ($curr as $cur) {
			if ($cur->short_name !== strtoupper($text)) {
				continue;
			}

			$existLink = CurrenciesToUsers::find()
				->where([
					CurrenciesToUsers::ATTR_USER_ID => $userId,
				])
				->one();/** @var CurrenciesToUsers $existLink */

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

			$message->sendTestMessage($userId, 'Вы подписались на рассылку курса валюты: ' . strtoupper($text));

			return true;
		}

		/** @var MessageService $message */
		$message->sendTestMessage($userId, 'Укажите нужную валюту');

		return true;
	}
}
