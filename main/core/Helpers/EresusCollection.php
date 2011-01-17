<?php
/**
 * ${product.title} ${product.version}
 *
 * ${product.description}
 *
 * Коллекция
 *
 * @copyright 2010, Eresus Project, http://eresus.ru/
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
 * @package EresusCMS
 *
 * $Id$
 */

/**
 * Коллекция
 *
 * @package EresusCMS
 *
 * @since 2.15
 */
class EresusCollection implements ArrayAccess, Countable, Serializable
{
	/**
	 * Значение, возвращаемое, при обращении к несуществующему элементу коллекции
	 *
	 * @var mixed
	 * @since 2.15
	 */
	protected $defaultValue = null;

	/**
	 * Данные коллекции
	 *
	 * @var array
	 * @since 2.15
	 */
	private $data = array();

	/**
	 * Конструктор
	 *
	 * @param array $data
	 *
	 * @return EresusCollection
	 *
	 * @throws InvalidArgumentException если $data не массив
	 * @since 2.15
	 */
	public function __construct($data = array())
	{
		if (!is_array($data))
		{
			throw new InvalidArgumentException(
				'First argument of EresusCollection::__construct must be an array and not ' .
				gettype($data));
		}
		$this->data = $data;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Устанавливает значение, возвращаемое при обращении к несуществующему элементу
	 *
	 * @param mixed $value
	 *
	 * @return void
	 *
	 * @since 2.15
	 */
	public function setDefaultValue($value)
	{
		$this->defaultValue = $value;
	}
	//-----------------------------------------------------------------------------

	/**
	 * (non-PHPdoc)
	 * @see ArrayAccess::offsetExists()
	 */
	public function offsetExists($offset)
	{
		$this->checkOffsetType($offset);
		return isset($this->data[$offset]);
	}
	//-----------------------------------------------------------------------------

	/**
	 * (non-PHPdoc)
	 * @see ArrayAccess::offsetGet()
	 */
	public function offsetGet($offset)
	{
		$this->checkOffsetType($offset);

		if (!$this->offsetExists($offset))
		{
			$this->offsetSet($offset, $this->defaultValue);
		}

		$value = $this->data[$offset];

		return $value;
	}
	//-----------------------------------------------------------------------------

	/**
	 * (non-PHPdoc)
	 * @see ArrayAccess::offsetSet()
	 */
	public function offsetSet($offset, $value)
	{
		if (is_array($value))
		{
			$value = new self($value);
			$value->setDefaultValue($this->defaultValue);
		}

		if (is_null($offset))
		{
			$this->data []= $value;
		}
		else
		{
			$this->checkOffsetType($offset);
			$this->data[$offset] = $value;
		}
	}
	//-----------------------------------------------------------------------------

	/**
	 * (non-PHPdoc)
	 * @see ArrayAccess::offsetUnset()
	 */
	public function offsetUnset($offset)
	{
		$this->checkOffsetType($offset);

		if (isset($this->data[$offset]))
		{
			unset($this->data[$offset]);
		}
	}
	//-----------------------------------------------------------------------------

	/**
	 * (non-PHPdoc)
	 * @see Countable::count()
	 */
	public function count()
	{
		return count($this->data);
	}
	//-----------------------------------------------------------------------------

	/**
	 * (non-PHPdoc)
	 * @see Serializable::serialize()
	 */
	public function serialize()
	{
		return serialize($this->data);
	}
	//-----------------------------------------------------------------------------

	/**
	 * (non-PHPdoc)
	 * @see Serializable::unserialize()
	 */
	public function unserialize($serialized)
	{
		$this->data = unserialize($serialized);
	}
	//-----------------------------------------------------------------------------

	/**
	 * Проверяет тип ключа
	 *
	 * @param mixed $offset
	 *
	 * @return void
	 *
	 * @throws InvalidArgumentException если у ключа не скалярное значение
	 * @since 2.15
	 */
	protected function checkOffsetType($offset)
	{
		if (!is_scalar($offset))
		{
			throw new InvalidArgumentException('Index must be a scalar value and not ' .
				gettype($offset));
		}
	}
	//-----------------------------------------------------------------------------

}