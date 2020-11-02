<?php

declare(strict_types=1);

namespace app\models;

use yii\db\ActiveRecord;
use yii\validators\NumberValidator;
use yii\validators\RequiredValidator;
use yii\validators\StringValidator;

/**
 * Модель валют.
 *
 * @property int    $id
 * @property string $short_name
 * @property float  $value
 *
 * @package app\models
 */
class Currencies extends ActiveRecord {

	const ATTR_ID         = 'id';
	const ATTR_SHORT_NAME = 'short_name';
	const ATTR_VALUE      = 'value';

	/**
	 * @inheritDoc
	 */
	public function rules(): array {
		return [
			[static::ATTR_SHORT_NAME, StringValidator::class, 'length' => 3],
			[static::ATTR_SHORT_NAME, RequiredValidator::class],
			[static::ATTR_VALUE, NumberValidator::class],
		];
	}
}
