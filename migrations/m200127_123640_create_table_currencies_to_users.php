<?php

use yii\db\Migration;

/**
 * Class m200127_123640_create_table_currencies_to_users
 */
class m200127_123640_create_table_currencies_to_users extends Migration {
	const TABLE_NAME = 'currencies_to_users';

	/**
	 * {@inheritdoc}
	 */
	public function safeUp() {
		$this->createTable(static::TABLE_NAME, [
			'id'      => 'INT AUTO_INCREMENT PRIMARY KEY NOT NULL COMMENT "Идентификатор связи"',
			'user_id' => 'INT NOT NULL COMMENT "Идентификатор пользовалля"',
			'cur_id'  => 'INT NOT NULL COMMENT "Идентификатор валюты"',
		]);
	}

	/**
	 * {@inheritdoc}
	 */
	public function safeDown() {
		$this->dropTable(static::TABLE_NAME);
	}
}
