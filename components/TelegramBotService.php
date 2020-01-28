<?php

declare(strict_types=1);

namespace app\components;

/**
 * Сервис для работы с ботом телеграмма.
 */
class TelegramBotService {

	const URL        = 'https://api.telegram.org/bot';
	const TOKEN_FILE = '/data/token';

	const COMMAND_GET_ME       = 'getMe';
	const COMMAND_GET_UPDATES  = 'getUpdates';
	const COMMAND_SEND_MESSAGE = 'sendMessage';

	/**
	 * Получить url api телеграмм бота
	 *
	 * @return string
	 */
	protected function getUrl(): string {
		return static::URL . $this->getToken() . '/';
	}

	/**
	 * Получить token
	 *
	 * @return string
	 */
	public function getToken(): string {
		return file_get_contents(__DIR__ . static::TOKEN_FILE);
	}

	/**
	 * Получение информации о боте.
	 *
	 * @return mixed
	 */
	public function getInfo() {
		return $this->send($this->getUrl() . static::COMMAND_GET_ME);
	}

	/**
	 * Получение обновлений.
	 *
	 * @return mixed
	 */
	public function getUpdates() {
		return $this->send($this->getUrl() . static::COMMAND_GET_UPDATES);
	}

	/**
	 * Отправка сообщения пользователю.
	 *
	 * @param int    $userId Идентификатор пользователя
	 * @param string $text   Текст сообщения
	 *
	 * @return mixed
	 */
	public function sendUserMessage($userId, $text) {
		return $this->send($this->getUrl() . static::COMMAND_SEND_MESSAGE . '?chat_id=' . $userId . '&text=' . $text);
	}

	/**
	 * Отправка сообщения.
	 *
	 * @param string $url Адрес
	 *
	 * @return mixed
	 */
	protected function send(string $url) {
		$ch = curl_init();

		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_HEADER, 0);

		$output = curl_exec($ch);

		if (curl_error($ch)) {
			echo curl_error($ch);
		}
		curl_close($ch);

		$response = json_decode($output);

		//@todo проверки

		return $response->result;
	}
}
