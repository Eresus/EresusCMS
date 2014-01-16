<?php
/**
 * ${product.title} ${product.version}
 *
 * ${product.description}
 *
 * Модуль интернационализации.
 *
 * @copyright 2004-2007, Михаил Красильников <mihalych@vsepofigu.ru>
 * @copyright 2007-2008, Eresus Project, http://eresus.ru/
 * @license ${license.uri} ${license.name}
 * @author Михаил Красильников <mihalych@vsepofigu.ru>
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
 * @package Eresus
 */


/**
 * Служба интернационализации
 *
 * @package Eresus
 */
class I18n
{

	/**
	 * Экземпляр-одиночка
	 *
	 * @var I18n
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
	 * Возвращает экземпляр-одиночку
	 *
	 * @return I18n
	 */
	static public function getInstance()
	{
		if (!self::$instance)
		{
			self::$instance = new I18n(Eresus_Kernel::app()->getFsRoot() . '/lang');
		}

		return self::$instance;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Конструктор
	 *
	 * @param string $path  Путь к файлам локализации
	 * @return I18n
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
		/** @noinspection PhpIncludeInspection */
		include_once $this->path . '/' . $this->locale . '.php';
	}
	//-----------------------------------------------------------------------------

	/**
	 * Возвращает текст в заданной локали
	 *
	 * @param string $key      Ключ искомой строки
	 * @param string $context  Контекст (пока не используется)
	 * @return string
	 */
	public function getText($key, /** @noinspection PhpUnusedParameterInspection */
		$context = null)
	{
		if (defined($key))
		{
			return constant($key);
		}

		return $key;
	}
	//-----------------------------------------------------------------------------
}
