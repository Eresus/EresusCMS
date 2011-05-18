<?php
/**
 * ${product.title} ${product.version}
 *
 * ${product.description}
 *
 * Модуль интернационализации
 *
 * @copyright 2004, Eresus Project, http://eresus.ru/
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
 * @package i18n
 *
 * $Id$
 */


/**
 * Служба интернационализации
 *
 * @package i18n
 */
class Eresus_i18n
{

	/**
	 * Экземпляр-одиночка
	 *
	 * @var Eresus_i18n
	 */
	static private $instance;

	/**
	 * Путь к файлам локализации
	 * @var string
	 */
	private $path;

	/**
	 * Локаль
	 * @var string
	 */
	private $locale;

	/**
	 * Строковые данные
	 *
	 * @var array
	 */
	private $data = array();

	/**
	 * Возвращает экземпляр-одиночку
	 *
	 * @return Eresus_i18n
	 */
	static public function getInstance()
	{
		if (!self::$instance)
		{
			self::$instance = new self(Eresus_CMS::app()->getFsRoot() . '/lang');
		}

		return self::$instance;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Конструктор
	 *
	 * @param string $path  Путь к файлам локализации
	 * @return Eresus_i18n
	 */
	public function __construct($path)
	{
		$this->path = $path;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Выбор локали
	 *
	 * @param string $locale
	 * @return void
	 */
	public function setLocale($locale)
	{
		$this->locale = $locale;
		if (!isset($this->data[$this->locale]))
		{
			$this->data[$this->locale] = include $this->path . '/' . $this->locale . '.php';
		}
	}
	//-----------------------------------------------------------------------------

	/**
	 * Возвращает текст в заданной локали
	 *
	 * @param string $text     Искомый текст
	 * @param string $context  Контекст
	 * @return string
	 */
	public function getText($text, $context = null)
	{
		if (isset($this->data[$this->locale]))
		{
			if ($context && isset($this->data[$this->locale]['messages'][$context]))
			{
				$messages = $this->data[$this->locale]['messages'][$context];
			}
			else
			{
				$messages = $this->data[$this->locale]['messages']['global'];
			}
			if (isset($messages[$text]))
			{
				return $messages[$text];
			}
		}

		return $text;
	}
	//-----------------------------------------------------------------------------
}


/**
 * Сокращение для "Eresus_i18n::getInstance()->getText()"
 *
 * @since 2.16
 */
function i18n($text, $context = null)
{
	return Eresus_i18n::getInstance()->getText($text, $context);
}
//-----------------------------------------------------------------------------
