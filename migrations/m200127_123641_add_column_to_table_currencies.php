<?php

use yii\db\Migration;

/**
 * Class m200127_123631_create_table_currencies
 */
class m200127_123641_add_column_to_table_currencies extends Migration {
	const TABLE_NAME = 'currencies';

	/**
	 * {@inheritdoc}
	 */
	public function safeUp() {
		$this->addColumn(static::TABLE_NAME, 'value', 'FLOAT NOT NULL DEFAULT 0.00 COMMENT "Значение валюты"');
	}

	/**
	 * {@inheritdoc}
	 */
	public function safeDown() {
		$this->dropTable(static::TABLE_NAME);
	}
}
