<?php
/**
 * ${product.title} ${product.version}
 *
 * ${product.description}
 *
 * Таблица автозагрузки классов
 *
 * @copyright 2009, Eresus Project, http://eresus.ru/
 * @license ${license.uri} ${license.name}
 * @author Mikhail Krasilnikov <mk@procreat.ru>
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
 *
 * $Id$
 */

return array(
	'EresusForm' => 'core/EresusForm.php',
	'PaginationHelper' => 'core/classes/helpers/PaginationHelper.php',
	'Templates' => 'core/lib/templates.php',
	'WebServer' => 'core/classes/WebServer.php',
	'WebPage' => 'core/classes/WebPage.php',

	'TListContentPlugin' => 'core/backward/TListContentPlugin',
	'TContentPlugin' => 'core/backward/TContentPlugin',
	'TPlugin' => 'core/backward/TPlugin',
);
