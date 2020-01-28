<?php

declare(strict_types=1);

namespace app\commands;

use app\components\MessageService;
use yii\console\Controller;

/**
 * Контроллер отправки сообщений.
 */
class CronController extends Controller {

	/**
	 * Отправить тестовое сообшение
	 */
	public function actionTest() {
		$messageService = \Yii::createObject(MessageService::class); /** @var  MessageService $messageService */

		$messageService->sendTestMessage(1, 'test');
	}

	/**
	 * Отправить курсы валбт пользователям.
	 */
	public function actionGetEx() {
		$messageService = \Yii::createObject(MessageService::class); /** @var  MessageService $messageService */

		$messageService->sendRatesToUsers();
	}

	/**
	 * Отправить курс валюты пользоватедям
	 */
	public function actionSendRateToUsers() {
		$messageService = \Yii::createObject(MessageService::class); /** @var  MessageService $messageService */

		$messageService->sendRateToUser();
	}
}
