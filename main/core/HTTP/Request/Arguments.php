<?php
/**
 * ${product.title} ${product.version}
 *
 * ${product.description}
 *
 * Аргументы запроса
 *
 * @copyright 2011, Eresus Project, http://eresus.ru/
 * @license ${license.uri} ${license.name}
 * @author Mikhail Krasilnikov <mihalych@vsepofigu.ru>
 *
 * Данная программа является свободным программным обеспечением. Вы
 * вправе распространять ее и/или модифицировать в соответствии с
 * условиями версии 3 либо (по вашему выбору) с условиями более поздней
 * версии Стандартной Общественной Лицензии GNU, опубликованной Free
 * Software Foundation.
 *
 * Мы распространяем эту программу в надежде на то, что она будет вам
 * полезной, однако НЕ ПРЕДОСТАВЛЯЕМ НА НЕЕ НИКАКИХ ГАРАНТИЙ, в том
 * числе ГАРАНТИИ ТОВАРНОГО СОСТОЯНИЯ ПРИ ПРОДАЖЕ и ПРИГОДНОСТИ ДЛЯ
 * ИСПОЛЬЗОВАНИЯ В КОНКРЕТНЫХ ЦЕЛЯХ. Для получения более подробной
 * информации ознакомьтесь со Стандартной Общественной Лицензией GNU.
 *
 * Вы должны были получить копию Стандартной Общественной Лицензии
 * GNU с этой программой. Если Вы ее не получили, смотрите документ на
 * <http://www.gnu.org/licenses/>
 *
 * @package HTTP
 *
 * $Id: Response.php 1652 2011-06-16 06:31:36Z mk $
 */

/**
 * Аргументы запроса
 *
 * @package HTTP
 * @since 2.16
 */
class Eresus_HTTP_Request_Arguments
{
	/**
	 * Аргументы
	 *
	 * @var array
	 */
	private $args = array();

	public function __construct(array $args = array())
	{
		$this->args = $args;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Возвращает аргумент
	 *
	 * $filter может быть:
	 * - строкой, содержащей ключевое слово 'int', 'integer' or 'float'
	 * - PCRE. Все символы, соответствующие выражению, будут удалены. Пример: '/\W/'
	 * - callback-функцией (или методом)
	 *
	 * @param string $name    имя аргумента
	 * @param mixed  $filter  опциональный фильтр
	 *
	 * @return void
	 *
	 * @since ?.??
	 */
	public function get($name, $filter = null)
	{
		$value = isset($this->args[$name]) ? $this->args[$name] : null;
		switch (true)
		{
			case is_callable($filter, false, $callback):
				if (is_array($filter) && is_object($filter[0]))
				{
					return $filter[0]->$filter[1]($value);
				}
				else
				{
					return $callback($value);
				}
			break;

			case is_string($filter):

				switch ($filter)
				{
					case 'int':
					case 'integer':
						return intval(filter_var($value, FILTER_SANITIZE_NUMBER_INT));
					break;
					case 'float':
						return floatval(filter_var($value, FILTER_SANITIZE_NUMBER_FLOAT,
							FILTER_FLAG_ALLOW_FRACTION | FILTER_FLAG_ALLOW_THOUSAND |
							FILTER_FLAG_ALLOW_SCIENTIFIC));
					break;
					default:
						return preg_replace($filter, '', $value);
					break;
				}

			break;
		}

		return $value;
	}
	//-----------------------------------------------------------------------------
}

