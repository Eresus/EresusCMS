<?php
/**
 * ${product.title} ${product.version}
 *
 * ${product.description}
 *
 * Интерфейс к веб-серверу
 *
 * @copyright 2010, Eresus Project, http://eresus.ru/
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
 * Интерфейс к веб-серверу
 *
 * Этот класс - кандидат на перенос в Eresus Core
 *
 * @package Eresus
 * @since 2.15
 */
class WebServer
{
	/**
	 * Экземпляр-одиночка
	 *
	 * @var WebServer
	 * @since 2.15
	 */
	private static $instance;

	/**
	 * Корневая директория веб-сервера (виртуального хоста)
	 *
	 * @var string
	 * @since 2.15
	 */
	private $documentRoot;

	/**
	 * Возвращает экземпляр класса
	 *
	 * @return WebServer
	 *
	 * @since 2.15
	 */
	public static function getInstance()
	{
		if (!self::$instance)
		{
			self::$instance = new self();
		}
		return self::$instance;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Возвращает корневую директорию веб-сервера
	 *
	 * @return string
	 *
	 * @since 2.15
	 */
	public function getDocumentRoot()
	{
		return $this->documentRoot;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Конструктор
	 *
	 * @return WebServer
	 *
	 * @uses FS::canonicalForm()
	 * @since 2.15
	 */
	private function __construct()
	{
		$path = realpath($_SERVER['DOCUMENT_ROOT']);
		if (DIRECTORY_SEPARATOR != '/')
		{
			$path = str_replace(DIRECTORY_SEPARATOR, '/', $path);
		}
		$this->documentRoot = $path;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Запрещаем клонирование
	 *
	 * @return void
	 *
	 * @since 2.15
	 */
	private function __clone()
	{
		// @codeCoverageIgnoreStart
	}
	// @codeCoverageIgnoreEnd
	//-----------------------------------------------------------------------------
}
