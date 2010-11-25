<?php
/**
 * ${product.title} ${product.version}
 *
 * ${product.description}
 *
 * Активная запись
 *
 * @copyright 2010, Eresus Project, http://eresus.ru/
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
 * @package EresusCMS
 *
 * $Id$
 */

/**
 * Активная запись
 *
 * @package EresusCMS
 * @since #548
 */
class EresusActiveRecord extends Doctrine_Record
{

	/**
	 * Десериализатор
	 *
	 * @param string $value
	 *
	 * @return array
	 *
	 * @since #548
	 */
	public function unserializeAccessor($value)
	{
		if (!is_string($value) || strlen($value) == 0)
		{
			return array();
		}
		return unserialize($value);
	}
	//-----------------------------------------------------------------------------

	/**
	 * Сериализатор
	 *
	 * @param array $value
	 *
	 * @return string
	 *
	 * @since #548
	 */
	public function serializeMutator($value)
	{
		return serialize($value);
	}
	//-----------------------------------------------------------------------------
}
