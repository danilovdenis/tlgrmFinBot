<?php

declare(strict_types=1);

namespace app\models;

use yii\db\ActiveRecord;
use yii\validators\NumberValidator;
use yii\validators\RequiredValidator;
use yii\validators\StringValidator;

/**
 * Модель связь валют с пользователями.
 *
 * @property int $id
 * @property int $user_id
 * @property int $cur_id
 *
 * @package app\models
 */
class CurrenciesToUsers extends ActiveRecord {

	const ATTR_ID      = 'id';
	const ATTR_USER_ID = 'user_id';
	const ATTR_CUR_ID  = 'cur_id';

	/**
	 * @inheritDoc
	 */
	public function rules(): array {
		return [
			[static::ATTR_USER_ID, NumberValidator::class],
			[static::ATTR_USER_ID, RequiredValidator::class],
			[static::ATTR_CUR_ID, NumberValidator::class],
			[static::ATTR_CUR_ID, RequiredValidator::class],
		];
	}
}
