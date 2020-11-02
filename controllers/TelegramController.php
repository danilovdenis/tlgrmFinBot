<?php

declare(strict_types=1);

namespace app\controllers;

use app\components\CommandProcessor;
use app\components\MessageService;
use app\components\TelegramBotService;
use Longman\TelegramBot\Exception\TelegramException;
use Longman\TelegramBot\Telegram;
use yii\base\InvalidConfigException;
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
	 * @throws TelegramException
	 * @throws InvalidConfigException
	 */
	public function actionSet() {
		$telegram = new Telegram((new TelegramBotService())->getToken(), 'exchageRatesBot');

		$telegram->handle();

		$input = json_decode($telegram->getCustomInput());

		//@todo валидация
		$userId = $input->message->from->id;
		$text   = $input->message->text;

		$answer = (new CommandProcessor)->init($userId, $text);

		$message = \Yii::createObject(MessageService::class);

		if (true === $answer['result']) {
			$message->sendTestMessage($userId, $answer['message']);
		}
	}
}
