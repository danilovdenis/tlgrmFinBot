<?php

use yii\db\Migration;

/**
 * Class m200127_121614_create_table_users
 */
class m200127_121614_create_table_users extends Migration {
	const TABLE_NAME = 'users';

	/**
	 * {@inheritdoc}
	 */
	public function safeUp() {
		$this->createTable(static::TABLE_NAME, [
			'id'           => 'INT AUTO_INCREMENT PRIMARY KEY NOT NULL COMMENT "Идентификатор пользователя"',
			'telegramm_id' => 'INT NOT NULL COMMENT "Идентификатор пользователя в телеграмме"',
			'create_stamp' => 'DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT "Дата создания"',
			'update_date'  => 'DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT "Дата обновления"',
		]);
	}

	/**
	 * {@inheritdoc}
	 */
	public function safeDown() {
		$this->dropTable(static::TABLE_NAME);
	}
}
