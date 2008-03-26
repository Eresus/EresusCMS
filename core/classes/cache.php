<?php
/**
 * Eresus 2.11
 *
 * ������ ������� �����������
 *
 * @copyright		2004-2007, ProCreat Systems, http://procreat.ru/
 * @copyright		2007-2008, Eresus Group, http://eresus.ru/
 * @license     http://www.gnu.org/licenses/gpl.txt  GPL License 3
 * @author      Mikhail Krasilnikov <mk@procreat.ru>
 *
 * ������ ��������� �������� ��������� ����������� ������������. ��
 * ������ �������������� �� �/��� �������������� � ������������ �
 * ��������� ������ 3 ���� (�� ������ ������) � ��������� ����� �������
 * ������ ����������� ������������ �������� GNU, �������������� Free
 * Software Foundation.
 *
 * �� �������������� ��� ��������� � ������� �� ��, ��� ��� ����� ���
 * ��������, ������ �� ������������� �� ��� ������� ��������, � ���
 * ����� �������� ��������� ��������� ��� ������� � ����������� ���
 * ������������� � ���������� �����. ��� ��������� ����� ���������
 * ���������� ������������ �� ����������� ������������ ��������� GNU.
 *
 * �� ������ ���� �������� ����� ����������� ������������ ��������
 * GNU � ���� ����������. ���� �� �� �� ��������, �������� �������� ��
 * <http://www.gnu.org/licenses/>
 *
 */

/*************************************************************************************************
 *  ����� ������� �����������
 *************************************************************************************************/

class EresusCache {
 /**
	* ��� ���� �� ���������
	*
	* @var string
	*/
	var $default = null;
 /**
	* ���������� � ��������� ���� ����
	*
	* @var array
	*/
	var $caches = array();
 /**
	* �����������
	*
	* @return EresusCache
	*/
	function EresusCache()
	{
	}
	//-----------------------------------------------------------------------------
 /**
	* �������� ����� ���������� �����������
	*
	* @param string $name     ��� ����������
	* @param string $class		��� ������ ����������
	* @param array  $options  ����� ����������
	*/
	function create($name, $options = null)
	{
		$className = $options['driver'];
		$this->caches[$name] = new $className($options);
		if (count($this->caches) == 1) $this->default = $name;
	}
	//-----------------------------------------------------------------------------
 /**
	* ��������� ���������� ��������� ������ (� ������)
	*
	* @param string $target  ����� ����
	*
	* @return int
	*/
	function free($target = 'default')
	{
		if ($target == 'default') $target = $this->default;
		$result = isset($this->caches[$target]) ? $this->caches[$target]->free() : 0;
		return $result;
	}
	//-----------------------------------------------------------------------------
 /**
	* ��������� ������ � ���
	*
	* @param string $owner   �������� ������
	* @param string $key     ������������� ������
	* @param mixed  $value   ������
	* @param int    $lifetime  ���� ����� ������ (��������)
	* @param string $target  ���, ��� ��������� ��������� ������ (default)
	*/
	function put($owner, $key, $value, $lifetime = 0, $target = 'default')
	{
		if ($target == 'default') $target = $this->default;
		if (isset($this->caches[$target])) $this->caches[$target]->put("$owner.$key", $value, $lifetime);
	}
	//-----------------------------------------------------------------------------
 /**
	* �������� ������ �� ����
	*
	* @param string $owner   �������� ������
	* @param string $key     ������������� ������
	* @param string $target  ����� ����
	*
	* @return mixed
	*/
	function get($owner, $key, $target = 'default')
	{
		if ($target == 'default') $target = $this->default;
		$result = isset($this->caches[$target]) ? $this->caches[$target]->get("$owner.$key") : null;
		return $result;
	}
	//-----------------------------------------------------------------------------
 /**
	* ���������� ����� ��������� ����� ����� ������
	*
	* @param string $owner   �������� ������
	* @param string $key  ������������� ������
	* @param string $target  ����� ����
	*
	* @return int  ������� � ��������
	*/
	function expires($owner, $key, $target = 'default')
	{
		if ($target == 'default') $target = $this->default;
		$result = isset($this->caches[$target]) ? $this->caches[$target]->expires("$owner.$key") : 0xffffffff;
		return $result;
	}
	//-----------------------------------------------------------------------------

}

/*************************************************************************************************
 *  ������� ����� ���������� �����������
 *************************************************************************************************/

class EresusCacheSubsystem {
 /* * * * * * * * * * * * * * * * * * * * * * * * *
	* PRIVATE
	* * * * * * * * * * * * * * * * * * * * * * * * */
 /**
	* ������ ���� (�����)
	*
	* @var int
	*
	* @access protected
	*/
	var $size = 0xffffffff;
 /**
	* ������ �������������� ������ (� ������)
	*
	* @var int
	*
	* @access protected
	*/
	var $used = 0;
 /**
	* ��������� ������ � ���
	*
	* @param string $key    ������������� ������
	* @param string $value  ������
	*
	* @access protected
	* @abstract
	*/
	function data_put($key, $value)
	{
	}
	//-----------------------------------------------------------------------------
 /**
	* �������� ������ �� ����
	*
	* @param string $key  ������������� ������
	*
	* @return string  ������ �� ���� ��� NULL
	*
	* @access protected
	* @abstract
	*/
	function data_get($key)
	{
		return null;
	}
	//-----------------------------------------------------------------------------
 /**
	* �������� ������ �� ����
	*
	* @param string $key  ������������� ������
	*
	* @access protected
	* @abstract
	*/
	function data_drop($key)
	{
	}
	//-----------------------------------------------------------------------------
 /**
	* ���������������� ������
	*
	* @param string $key       ������������� ������
	* @param int    $lifetime  ���� ����� ������ (�������)
	*
	* @access protected
	* @abstract
	*/
	function index_put($key, $lifetime)
	{
	}
	//-----------------------------------------------------------------------------
 /**
	* �������� ��������� ���������� ������ � ������� � �� �������
	*
	* @param bool $force  �������� true ���������� ������� �������� ������, �� ��� �� ���������� ������
	*
	* @return string  ������������� ������
	*
	* @access protected
	* @abstract
	*/
	function index_get($force = false)
	{
		return null;
	}
	//-----------------------------------------------------------------------------
 /**
	* ������������ ������
	*
	* @param mixed $value
	* @return string
	*
	* @access protected
	*/
	function serialize($value)
	{
		$result = serialize($value);
		return $result;
	}
	//-----------------------------------------------------------------------------
 /**
	* �������������� ������
	*
	* @param string $value
	* @return mixed
	*
	* @access protected
	*/
	function unserialize($value)
	{
		$result = unserialize($value);
		return $result;
	}
	//-----------------------------------------------------------------------------
 /**
	* ������� ������
	*
	* @param int $free  ��������� ���������� ��������� ������
	*
	* @access protected
	*/
	function cleanup($free = 0)
	{
		while ($this->free() < $free && !is_null($key = $this->index_get($free > 0)))
			$this->data_drop($key);
	}
	//-----------------------------------------------------------------------------

 /* * * * * * * * * * * * * * * * * * * * * * * * *
	* PUBLIC
	* * * * * * * * * * * * * * * * * * * * * * * * */
 /**
	* ������������ ������ ���� (� ����������)
	*
	* ����������� ��������:
	* -1 - ��������� �����������
	*  0 - ��� �����������
	*
	* @var int
	* @access public
	*/
	var $limit = 0;
 /**
	* �����������
	*
	* @return EresusAbstractCache
	*/
	function EresusCacheSubsystem()
	{
	}
	//-----------------------------------------------------------------------------
 /**
	* ��������� ���������� ��������� ������ (� ������)
	*
	* @return int
	*/
	function free()
	{
		switch ($this->limit) {
			case -1: $result = 0; break;
			case  0: $result = $this->size - $this->used; break;
			default: $result = $this->limit - $this->used;
		}
		return $result;
	}
	//-----------------------------------------------------------------------------
 /**
	* ��������� ������ � ���
	*
	* @param string $key       ������������� ������
	* @param mixed  $value     ������
	* @param int    $lifetime  ���� ����� ������ (��������)
	*/
	function put($key, $value, $lifetime = 0)
	{
		$value = $this->serialize($value);
		$size = strlen($value);
		if ($size > $this->free() && $size < $this->size) $this->cleanup($size);
		if ($size <= $this->free()) {
			$this->index_put($key, $lifetime);
			$this->data_put($key, $value);
		}
	}
	//-----------------------------------------------------------------------------
 /**
	* �������� ������ �� ����
	*
	* @param string $key       ������������� ������
	*
	* @return mixed
	*/
	function get($key)
	{
		$result = $this->data_get($key);
		$result = $this->unserilize($result);
		return $result;
	}
	//-----------------------------------------------------------------------------
 /**
	* ���������� ����� ��������� ����� ����� ������
	*
	* @param string $key  ������������� ������
	* @return int  ������� � ��������
	*
	* @abstract
	*/
	function expires($key)
	{
		return 0xffffffff;
	}
	//-----------------------------------------------------------------------------
}

/*************************************************************************************************
 *  ����������� � ����������� ������
 *************************************************************************************************/

class EresusMemoryCache extends EresusCacheSubsystem {
 /**
	* ��������� ������
	*
	* @var array
	*/
	var $storage = array();
 /**
	* ������ ������
	*
	* @var array
	*/
	var $index = array(
		'expires' => array(),
		'keys' => array(),
	);
 /**
	* ��������� ������ � ���
	*
	* @param string $key    ������������� ������
	* @param string $value  ������
	*
	* @access protected
	*/
	function data_put($key, $value)
	{
		$this->storage[$key] = $value;
		$this->used += strlen($value);
	}
	//-----------------------------------------------------------------------------
 /**
	* �������� ������ �� ����
	*
	* @param string $key  ������������� ������
	*
	* @return string  ������ �� ���� ��� NULL
	*
	* @access protected
	*/
	function data_get($key)
	{
		$result = isset($this->storage[$key]) ? $this->storage[$key] : null;
		return $result;
	}
	//-----------------------------------------------------------------------------
 /**
	* �������� ������ �� ����
	*
	* @param string $key  ������������� ������
	*
	* @access protected
	*/
	function data_drop($key)
	{
		if (isset($this->storage[$key])) {
			$this->used -= strlen($this->storage[$key]);
			unsert($this->storage[$key]);
		}
	}
	//-----------------------------------------------------------------------------
 /**
	* ���������������� ������
	*
	* @param string $key       ������������� ������
	* @param int    $lifetime  ���� ����� ������ (�������)
	*
	* @access protected
	*/
	function index_put($key, $lifetime)
	{
		$expires = time() + $lifetime;

		$lb = $pos = 0;
		$ub = count($this->index['expires']) - 1;

		while ($lb <= $ub) {
			$pos = floor(($lb + $ub) / 2);
			if ($expires < $this->index['expires'][$pos]) {
				$lb = $pos + 1;
			} elseif ($expires > $this->index['expires'][$pos]) {
				$ub = $pos - 1;
			} else break;
		}

		if (count($this->index['expires']) && $expires < $this->index['expires'][$pos]) $pos++;

		array_splice($this->index['keys'], $pos, 0, array($key));
		array_splice($this->index['expires'], $pos, 0, array($expires));

		return null;
	}
	//-----------------------------------------------------------------------------
 /**
	* �������� ��������� ���������� ������ � ������� � �� �������
	*
	* @param bool $force  �������� true ���������� ������� �������� ������, �� ��� �� ���������� ������
	*
	* @return string  ������������� ������
	*
	* @access protected
	*/
	function index_get($force = false)
	{
		if ($force) {
			$result = count($this->index['keys']) ? array_pop($this->index['keys']) : null;
			array_pop($this->index['expires']);
		} else {
			$expires = end($this->index['expires']);
			if ($expires !== false && $expires < time()) {
				$result = array_pop($this->index['keys']);
				array_pop($this->index['expires']);
			} else $result = null;
		}
		return $result;
	}
	//-----------------------------------------------------------------------------
 /**
	* ���������� ����� ��������� ����� ����� ������
	*
	* @param string $key  ������������� ������
	*
	* @return int  UNIX timestamp
	*/
	function expires($key)
	{
		$index = array_search($key, $this->index['keys']);
		$result = $index !== false ? $this->index['expires'][$index] : false;
		return $result;
	}
	//-----------------------------------------------------------------------------
}

?>