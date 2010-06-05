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
	 * ???
	 * @var ezcDbSchema
	 */
	private $dbSchema = null;

	/**
	 * ��������� ���������� �������� ������ � �������� ��������
	 *
	 * @param string $server    ������ ������
	 * @param string $username  ��� ������������ ��� ������� � �������
	 * @param string $password  ������ ������������
	 * @param string $source    ��� ��������� ������
	 *
	 * @return bool  ��������� ����������
	 * @deprecated
	 */
	public function init($server, $username, $password, $source, $prefix = '')
	{
		eresus_log(__METHOD__, LOG_NOTICE, 'This method is deprecated');
		$dsn = "mysql://$username:$password@$server/$source";
		if (defined('LOCALE_CHARSET'))
			$dsn .= '?charset=' . LOCALE_CHARSET;

		try
		{
			$db = DB::connect($dsn);
		}
			catch (DBRuntimeException $e)
		{
			return false;
		}

		$options = new ezcDbOptions(array('tableNamePrefix' => $prefix));
		$db->setOptions($options);

		return true;
	}
	//-----------------------------------------------------------------------------

	/**
	 * ���������� ������-�������� ����� ��
	 * @return ezcDbSchema
	 */
	public function getSchema()
	{
		if (!$this->dbSchema)
		{
			$db = DB::getHandler();
			$options = new ezcDbSchemaOptions(array('tableNamePrefix' => $db->options->tableNamePrefix));
			ezcDbSchema::setOptions($options);

			$this->dbSchema = ezcDbSchema::createFromDb($db);
		}

		return $this->dbSchema;
	}
	//-----------------------------------------------------------------------------

	/**
	 * ��������� ������ � ���������
	 *
	 * @param string $query  ������ � ������� ���������
	 * @return mixed  ��������� �������. ��� ������� �� ���������, ������� � ����������
	 * @deprecated
	 */
	public function query($query)
	{
		eresus_log(__METHOD__, LOG_NOTICE, 'This method is deprecated');
		$db = DB::getHandler();
		$db->exec($query);
		return true;
	}
	//-----------------------------------------------------------------------------

	/**
	 * ��������� ������ � ��������� � ���������� ������������� ������ ��������
	 *
	 * @param  string  $query    ������ � ������� ���������
	 * @return  array|bool  ����� � ���� ������� ��� FALSE � ������ ������
	 * @deprecated
	 */
	public function query_array($query)
	{
		eresus_log(__METHOD__, LOG_NOTICE, 'This method is deprecated');
		$db = DB::getHandler();
		$stmt = $db->prepare($query);
		if (!$stmt->execute())
			return false;

		$values = $stmt->fetchAll(PDO::FETCH_ASSOC);
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
	 * @deprecated
	 */
	public function create($name, $structure, $options = '')
	{
		eresus_log(__METHOD__, LOG_NOTICE, 'This method is deprecated');
		$db = DB::getHandler();
		$name = $db->options->tableNamePrefix . $name;
		$query = "CREATE TABLE `$name` ($structure) $options";
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
	 * @deprecated
	 */
	public function drop($name)
	{
		eresus_log(__METHOD__, LOG_NOTICE, 'This method is deprecated');
		$db = DB::getHandler();
		$name = $db->options->tableNamePrefix . $name;
		$query = "DROP TABLE `$name`";
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
	* @deprecated
	*/
	public function select($tables, $condition = '', $order = '', $fields = '', $limit = 0, $offset = 0, $group = '', $distinct = false)
	{
		eresus_log(__METHOD__, LOG_NOTICE, 'This method is deprecated');
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
	 * ������� �������� � ��
	 *
	 * @param string $table  �������, � ������� ���� �������� �������
	 * @param array  $item   ������������� ������ ��������
	 *
	 * @return mixed  ��������� ���������� ��������
	 * @deprecated
	 */
	public function insert($table, $item)
	{
		eresus_log(__METHOD__, LOG_NOTICE, 'This method is deprecated');
		$fields = $this->fields($table);
		if (!$table)
			return false;

		$q = DB::getHandler()->createInsertQuery();
		$q->insertInto($table);

		foreach ($fields as $field)
			if (isset($item[$field]))
				$q->set($field, $q->bindValue($item[$field]));

		DB::execute($q);
		return true;
	}
	//-----------------------------------------------------------------------------

	/**
	 * ��������� ���������� ���������� � ���������
	 *
	 * @param string $table      �������
	 * @param mixed  $set        ���������
	 * @param string $condition  �������
	 * @return unknown
	 * @deprecated
	 */
	public function update($table, $set, $condition)
	{
		eresus_log(__METHOD__, LOG_NOTICE, 'This method is deprecated');
		$q = DB::getHandler()->createUpdateQuery();
		$q->update($table)
			->where($condition);

		$set = explode(',', $set);
		foreach ($set as $each)
		{
			list($key, $value) = explode('=', $each);
			$key = str_replace('`', '', trim($key));
			$value = preg_replace('/(^\'|\'$)/', '', trim($value));
			$q->set($key, $q->bindValue($value));
		}

		DB::execute($q);
	}
	//-----------------------------------------------------------------------------

	/**
	 * ��������� ������ DELETE � ���� ������
	 *
	 * @param string $table      �������, �� ������� ��������� ������� ������
	 * @param string $condition  �������� ��������� �������
	 * @return mixed
	 * @deprecated
	 */
	public function delete($table, $condition)
	{
		eresus_log(__METHOD__, LOG_NOTICE, 'This method is deprecated');
		$q = DB::getHandler()->createDeleteQuery();
		$q->deleteFrom($table)
			->where($condition);
		DB::execute($q);
		return null;
	}
	//-----------------------------------------------------------------------------
 /**
	* ��������� ������ ����� �������
	*
	* @param string $table            ��� �������
	* @param bool   $info [optional]
	* @return array  ������ �����, � ���������, ���� $info = true
	* @deprecated
	*/
	public function fields($table, $info = false)
	{
		eresus_log(__METHOD__, LOG_NOTICE, 'This method is deprecated');
		$schm = $this->getSchema()->getSchema();
		return array_keys($schm[$table]->fields);
/*		global $Eresus;

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
		return $result;*/
	}
	//-----------------------------------------------------------------------------

	/**
	 * ������� ���� ������ �� ��
	 *
	 * @param string $table      ��� �������
	 * @param string $condition  SQL-�������
	 * @param string $fields     ���������� ����
	 * @return array|false
	 * @deprecated
	 */
	public function selectItem($table, $condition, $fields = '')
	{
		eresus_log(__METHOD__, LOG_NOTICE, 'This method is deprecated');
		$q = DB::getHandler()->createSelectQuery();

		if ($fields == '')
			$fields = '*';

		$q->select($fields)
			->from($table)
			->where($condition)
			->limit(1);

		$item = DB::fetch($q);

		return $item;
	}
	//-----------------------------------------------------------------------------

	/**
	 * ��������� ���� ������
	 *
	 * @param string $table
	 * @param array  $item
	 * @param string $condition
	 * @return void
	 * @deprecated
	 */
	public function updateItem($table, $item, $condition)
	{
		eresus_log(__METHOD__, LOG_NOTICE, 'This method is deprecated');
		$fields = $this->fields($table);
		if (!$table)
			return false;

		$q = DB::getHandler()->createUpdateQuery();
		$q->update($table)
			->where($condition);

		foreach ($fields as $field)
			if (isset($item[$field]))
				$q->set($field, $q->bindValue($item[$field]));

		DB::execute($q);
		return true;
	}
	//-----------------------------------------------------------------------------

	/**
	 * ���������� ���������� ������� � �������
	 *
	 * @param string $table      �������, ��� ������� ��������� ��������� ���-�� �������
	 * @param string $condition
	 * @param string $group
	 * @param bool   $rows
	 * @return int
	 * @deprecated
	 */
	public function count($table, $condition = false, $group = false, $rows = false)
	{
		eresus_log(__METHOD__, LOG_NOTICE, 'This method is deprecated');
		$q = DB::getHandler()->createSelectQuery();
		$e = $q->expr;

		$q->select($q->alias($e->count('*'), 'count'))
			->from($table);

		if ($condition)
			$q->where($condition);

		if ($group)
			$q->groupBy($group);

		$result = DB::fetchAll($q);
		if ($rows)
			return count($result);
		else
			return intval($result[0]['count']);
	}
	//-----------------------------------------------------------------------------

	/**
	 * ���������� ������������� ��������� ����������� ������
	 *
	 * @return mixed
	 * @deprecated
	 */
	public function getInsertedID()
	{
		eresus_log(__METHOD__, LOG_NOTICE, 'This method is deprecated');
		$db = DB::getHandler();
		return $db->lastInsertId();
	}
	//-----------------------------------------------------------------------------

	/**
	 *
	 * @param unknown_type $table
	 * @param unknown_type $param
	 * @return unknown_type
	 * @deprecated
	 */
	public function tableStatus($table, $param='')
	{
		eresus_log(__METHOD__, LOG_NOTICE, 'This method is deprecated');
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
	 * ������� � 2.13 ����� ������ �� ������.
	 *
	 * @param mixed $src  ������� ������
	 * @return mixed
	 * @deprecated
	 */
	public function escape($src)
	{
		eresus_log(__METHOD__, LOG_NOTICE, 'This method is deprecated');
		return $src;
	}
	//-----------------------------------------------------------------------------
}