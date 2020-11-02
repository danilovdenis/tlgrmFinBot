<?php

use yii\db\Migration;

/**
 * Class m200127_123631_create_table_currencies
 */
class m200127_123631_create_table_currencies extends Migration {
	const TABLE_NAME = 'currencies';

	/**
	 * {@inheritdoc}
	 */
	public function safeUp() {
		$this->createTable(static::TABLE_NAME, [
			'id'           => 'INT AUTO_INCREMENT PRIMARY KEY NOT NULL COMMENT "Идентификатор валюты"',
			'short_name'   => 'TEXT NOT NULL COMMENT "Короткий текст валюты"',
		]);
	}

	/**
	 * {@inheritdoc}
	 */
	public function safeDown() {
		$this->dropTable(static::TABLE_NAME);
	}
}
