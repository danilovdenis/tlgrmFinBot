<?php

declare(strict_types=1);

namespace app\components;

use app\models\Currencies;
use DateTime;
use SimpleXMLElement;
use SoapClient;
use SoapFault;

/**
 * API получения данных из сервиса
 */
class ApiService {

	const URL = 'http://www.cbr.ru/DailyInfoWebServ/DailyInfo.asmx?wsdl';

	/**
	 * ОТправить запрос.
	 *
	 * @param string   $url  Адрес
	 * @param DateTime $date Дата
	 *
	 * @return SimpleXMLElement
	 * @throws SoapFault
	 */
	protected function sendRequest(string $url, DateTime $date): SimpleXMLElement {
		$soap = new SoapClient($url);

		$result = $soap->GetCursOnDate(['On_date' => $date->format('Y-m-d')]);

		return new SimpleXMLElement($result->GetCursOnDateResult->any);
	}

	/**
	 * Получение данных о курсах валют
	 *
	 * @param DateTime|null $date Дата
	 *
	 * @return bool
	 * @throws SoapFault
	 */
	public function updateRates(?DateTime $date = NULL): bool {
		if (NULL === $date) {
			$date = new DateTime('now');
		}

		$response = $this->sendRequest(static::URL, $date);

		$existsCurrencies = Currencies::find()
			->select([
				Currencies::ATTR_SHORT_NAME,
				Currencies::ATTR_VALUE,
			])
			->indexBy(Currencies::ATTR_SHORT_NAME)
			->all();

		// @todo транзакция
		foreach ($response->ValuteData->ValuteCursOnDate as $rate) {
			$code  = $rate->VchCode[0]->__toString();
			$value = (float)$rate->Vcurs[0]->__toString();

			if (array_key_exists($code, $existsCurrencies)) {
				if ($existsCurrencies[$code] === $value) {
					continue;
				}

				Currencies::updateAll([Currencies::ATTR_VALUE => $value],
					[
						Currencies::ATTR_SHORT_NAME => $code,
					]
				);
			}

			$currency             = new Currencies();
			$currency->short_name = $code;
			$currency->value      = $value;

			$currency->save();
		}

		return true;
	}
}
