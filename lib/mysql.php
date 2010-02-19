<?php
/**
 * ${product.title} ${product.version}
 *
 * ���������� ��� ������ � ���� MySQL
 *
 * @copyright 2004, ProCreat Systems, http://procreat.ru/
 * @copyright 2007, Eresus Project, http://eresus.ru/
 * @license ${license.uri} ${license.name}
 * @author Mikhail Krasilnikov <mk@procreat.ru>
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
 * @package Eresus2
 *
 * $Id$
 */

class MySQL
{
	/**
	 * ���������� ����������
	 * @var resource
	 */
	protected $Connection;

	/**
	 * ��� ��
	 * @var string
	 */
	protected $name;

	/**
	 * ������� ������
	 * @var string
	 * @deprecated
	 */
	protected $prefix;

	/**
	 * ����� ��� ��������
	 * @var bool
	 */
	public $logQueries = false;

	/**
	 * ���� TRUE (�� ���������) � ������ ������ ������ ����� ������� � �������� ��������� �� ������
	 *
	 * @var bool
	 */
	public $error_reporting = true;

	/**
	 * ��������� ���������� �������� ������ � �������� ��������
	 *
	 * @param string $server    ������ ������
	 * @param string $username  ��� ������������ ��� ������� � �������
	 * @param string $password  ������ ������������
	 * @param string $source    ��� ��������� ������
	 *
	 * @return bool  ��������� ����������
	 */
	function init($server, $username, $password, $source, $prefix = '')
	{
		$dsn = "mysql://$username:$password@$server/$source";
		if (defined('LOCALE_CHARSET'))
			$dsn .= '?charset=' . LOCALE_CHARSET;

		try
		{
			DB::connect($dsn);
		}
			catch (DBRuntimeException $e)
		{
			return false;
		}

		return true;
	}
	//-----------------------------------------------------------------------------

	/**
	 * ��������� ������ � ���������
	 *
	 * @param string $query  ������ � ������� ���������
	 * @return mixed  ��������� �������. ��� ������� �� ���������, ������� � ����������
	 */
	function query($query)
	{
		$db = DB::getHandler();
		$db->exec($query);
		return true;
	}
	//-----------------------------------------------------------------------------

	/**
	 * ��������� ������ � ��������� � ���������� ������������� ������ ��������
	 *
	 * @param  string  $query    ������ � ������� ���������
	 *
	 * @return  array|bool  ����� � ���� ������� ��� FALSE � ������ ������
	 */
	function query_array($query)
	{
		$result = $this->query($query);
		$values = Array();
		while($row = mysql_fetch_assoc($result))
		{
			if (count($row))
				foreach($row as $key => $value)
					$row[$key] = $value;
			$values[] = $row;
		}
		return $values;
	}
	//------------------------------------------------------------------------------

	/**
	 * �������� ����� �������
	 *
	 * @param string $name       ��� �������
	 * @param string $structure  �������� ���������
	 * @param string $options    �����
	 *
	 * @return bool ���������
	 */
	function create($name, $structure, $options = '')
	{
		$result = false;
		$query = "CREATE TABLE `{$this->prefix}$name` ($structure) $options";
		$result = $this->query($query);
		return $result;
	}
	//------------------------------------------------------------------------------

	/**
	 * �������� �������
	 *
	 * @param string $name       ��� �������
	 *
	 * @return bool ���������
	 */
	function drop($name)
	{
		$result = false;
		$query = "DROP TABLE `{$this->prefix}$name`";
		$result = $this->query($query);
		return $result;
	}
	//------------------------------------------------------------------------------

 /**
	* ���������� ������� ������ �� ���������
	*
	* @param  string   $tables     ������ ������ �� ������� ���������� �������
	* @param  string   $condition  ������� ��� ������� (WHERE)
	* @param  string   $order      ���� ��� ���������� (ORDER BY)
	* @param  string   $fields     ������ ����� ��� ���������
	* @param  int      $limit      ����������� ���������� ���������� �������
	* @param  int      $offset     ��������� �������� ��� �������
	* @param  string   $group      ���� ��� �����������
	* @param  bool     $distinct   ������� ������ ���������� ������
	*
	* @return  array|bool  ��������� �������� � ���� ������� ��� FALSE � ������ ������
	*/
	function select($tables, $condition = '', $order = '', $fields = '', $limit = 0, $offset = 0, $group = '', $distinct = false)
	{
		$db = DB::getHandler();
		$q = $db->createSelectQuery();
		$e = $q->expr;

		if (empty($fields))
			$fields = '*';

		if ($distinct)
			$q->selectDistinct($fields);
		else
			$q->select($fields);

		$tables = explode(',', $tables);
		$q->from($tables);

		if ($condition)
			$q->where($condition);

		if (strlen($order))
		{
			$order = explode(',', $order);
			for($i = 0; $i < count($order); $i++)
				switch ($order[$i]{0})
				{
					case '+':
						$q->orderBy(substr($order[$i], 1));
					break;

					case '-':
						$q->orderBy(substr($order[$i], 1), ezcQuerySelect::DESC);
					break;

					default:
						$q->orderBy($order[$i]);
					break;
				}
		}

		if ($limit && $offset)
			$q->limit($limit, $offset);
		elseif ($limit)
			$q->limit($limit);

		if ($group)
			$q->groupBy($group);

		$result = DB::fetchAll($q);

		return $result;
	}
	//-----------------------------------------------------------------------------

	/**
	 * ������� ��������� � ��������
	 *
	 * @param  string  $table  �������, � ������� ���� �������� �������
	 * @param  array   $item   ������������� ������ ��������
	 *
	 * @return  mixed  ��������� ���������� ��������
	 */
	function insert($table, $item)
	{
		$hnd = mysql_list_fields($this->name, $this->prefix.$table, $this->Connection);
		$cols = '';
		$values = '';
		while (($field = @mysql_field_name($hnd, $i++))) if (isset($item[$field])) {
			$cols .= ", `$field`";
			$values .= " , '{$item[$field]}'";
		}
		$cols = substr($cols, 2);
		$values = substr($values, 2);
		$result = $this->query("INSERT INTO ".$this->prefix.$table." (".$cols.") VALUES (".$values.")");
		return $result;
	}
	//-----------------------------------------------------------------------------

	/**
	 * ��������� ���������� ���������� � ���������
	 *
	 * @param string $table      �������
	 * @param mixed  $set        ���������
	 * @param string $condition  �������
	 * @return unknown
	 */
	function update($table, $set, $condition)
	{
		$q = DB::getHandler()->createUpdateQuery();
		$q->update($table)
			->where($condition);

		$set = explode(',', $set);
		foreach ($set as $each)
		{
			list($key, $value) = explode('=', $each);
			$key = str_replace('`', '', $key);
			$value = preg_replace('/(^\'|\'$)/', '', $value);
			$q->set($key, $q->bindValue($value));
		}

		DB::execute($q);
		//$result = $this->query("UPDATE `".$this->prefix.$table."` SET ".$set." WHERE ".$condition);
		//return $result;
	}
	//-----------------------------------------------------------------------------
	function delete($table, $condition)
	# ��������� ������ DELETE � ���� ������ ��������� ����� query().
	#  $table - �������, �� ������� ��������� ������� ������
	#  $condition - �������� ��������� �������
	{
		$result = $this->query("DELETE FROM `".$this->prefix.$table."` WHERE ".$condition);
		return $result;
	}
	//-----------------------------------------------------------------------------
 /**
	* ��������� ������ ����� �������
	*
	* @param string $table  ��� �������
	* @return array  �������� �����
	*/
	function fields($table, $info = false)
	{
		global $Eresus;

		$fields = $this->query_array("SHOW COLUMNS FROM `{$this->prefix}$table`");
		if ($fields) {
			$result = array();
			foreach($fields as $item) {
				if ($info) {
					$result[$item['Field']] = array(
						'name' => $item['Field'],
						'type' => $item['Type'],
						'size' => 0,
						'signed' => false,
						'default' => $item['Default'],
					);
					switch (true) {
						case $item['Type'] == 'text':
							$result[$item['Field']]['size'] = 65535;
						break;
						case $item['Type'] == 'longtext':
							$result[$item['Field']]['type'] = 'text';
							$result[$item['Field']]['size'] = 4294967295;
						break;
						case substr($item['Type'], 0, 3) == 'int':
							$result[$item['Field']]['signed'] = strpos($result[$item['Field']]['type'], 'unsigned') === false;
							$item['Type'] = str_replace(' unsigned', '', $item['Type']);
							$result[$item['Field']]['type'] = 'int';
							$result[$item['Field']]['size'] = substr($item['Type'], 4, -1);
						break;
						case substr($item['Type'], 0, 8) == 'smallint':
							$result[$item['Field']]['signed'] = strpos($result[$item['Field']]['type'], 'unsigned') === false;
							$item['Type'] = str_replace(' unsigned', '', $item['Type']);
							$result[$item['Field']]['type'] = 'int';
							$result[$item['Field']]['size'] = substr($item['Type'], 9, -1);
						break;
						case substr($item['Type'], 0, 7) == 'tinyint':
							$result[$item['Field']]['signed'] = strpos($result[$item['Field']]['type'], 'unsigned') === false;
							$item['Type'] = str_replace(' unsigned', '', $item['Type']);
							$result[$item['Field']]['type'] = 'int';
							$result[$item['Field']]['size'] = substr($item['Type'], 8, -1);
						break;
						case substr($item['Type'], 0, 7) == 'varchar':
							$result[$item['Field']]['type'] = 'string';
							$result[$item['Field']]['size'] = substr($item['Type'], 8, -1);
						break;
					}
				} else $result[] = $item['Field'];
			}
		} else FatalError(mysql_error($this->Connection));
		return $result;
	}
	//-----------------------------------------------------------------------------
	function selectItem($table, $condition, $fields = '')
	{
		if ($table{0} != "`") $table = "`".$table."`";
		$tmp = $this->select($table, $condition, '', false, $fields);
		$tmp = isset($tmp[0])?$tmp[0]:null;
		return $tmp;
	}
	//-----------------------------------------------------------------------------

	/**
	 * ��������� ���� ������
	 *
	 * @param string $table
	 * @param array  $item
	 * @param string $condition
	 * @return void
	 */
	function updateItem($table, $item, $condition)
	{
		$fields = $this->fields($table, true);

		$values = array();
		foreach($fields as $field => $info) if (isset($item[$field])) {
			switch ($info['type']) {
				case 'int':
					$value = $item[$field];
					if (!$value) $value = 0;
				break;
				default: $value = "'".$item[$field]."'";
			}
			$values[] = "`$field` = $value";
		}
		$values = implode(', ', $values);
		$result = $this->query("UPDATE `".$this->prefix.$table."` SET ".$values." WHERE ".$condition);
		return $result;
	}
	//-----------------------------------------------------------------------------
	function count($table, $condition='', $group='', $rows=false)
	# ���������� ���������� ������� � ������� ��������� ����� query().
	#  $table - �������, ��� ������� ��������� ��������� ���-�� �������
	{
		$result = $this->query("SELECT count(*) FROM `".$this->prefix.$table."`".(empty($condition)?'':'WHERE '.$condition).(empty($group)?'':' GROUP BY `'.$group.'`'));
		if ($rows) {
			$count = 0;
			while (mysql_fetch_row($result)) $count++;
			$result = $count;
		} else {
			$result = mysql_fetch_row($result);
			$result = $result[0];
		}
		return $result;
	}
	//-----------------------------------------------------------------------------
	function getInsertedID()
	{
		return mysql_insert_id($this->Connection);
	}
	//-----------------------------------------------------------------------------
	function tableStatus($table, $param='')
	{
		$result = $this->query_array("SHOW TABLE STATUS LIKE '".$this->prefix.$table."'");
		if ($result) {
			$result = $result[0];
			if (!empty($param)) $result = $result[$param];
		}
		return $result;
	}
	//-----------------------------------------------------------------------------
 /**
	* ���������� ������������ ������� �������
	*
	* @param mixed $src  ������� ������
	*
	* @return mixed
	*/
	function escape($src)
	{
		switch (true) {
			case is_string($src): $src = mysql_real_escape_string($src); break;
			case is_array($src): foreach($src as $key => $value) $src[$key] = mysql_real_escape_string($value); break;
		}
		return $src;
	}
	//-----------------------------------------------------------------------------
}