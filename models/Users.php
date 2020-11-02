<?php

declare(strict_types=1);

namespace app\models;

use yii\db\ActiveRecord;
use yii\validators\NumberValidator;
use yii\validators\RequiredValidator;

/**
 * Модель пользователей
 *
 * @property int    $id
 * @property int    $telegramm_id
 * @property string $create_stamp
 * @property string $update_date
 *
 * @package app\models
 */
class Users extends ActiveRecord {

	const ATTR_ID           = 'id';
	const ATTR_TELEGRAMM_ID = 'telegramm_id';
	const ATTR_CREATE_STAMP = 'creat_stamp';
	const ATTR_UPDATE_STAMP = 'update_stamp';

	/**
	 * @inheritDoc
	 */
	public function rules(): array {
		return [
			[static::ATTR_TELEGRAMM_ID, NumberValidator::class],
			[static::ATTR_TELEGRAMM_ID, RequiredValidator::class],
		];
	}
}
