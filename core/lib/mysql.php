<?php
/**
* Eresus� 2
*
* ���������� ��� ������ � ���� MySQL
*
* @author: Mikhail Krasilnikov <mk@procreat.ru>
* @version: 1.2.0
* @modified: 2007-07-22
*/

# ������� ������� (�������� ��� ������������� ����� $Eresus->conf['debug'])
# ������������� ���������� ���������� 
#  $__MYSQL_QUERY_COUNT - ������� ���������� �������� � ��
#  $__MYSQL_QUERY_TIME - ������� ����� ����� �������� � ��
#  $__MYSQL_QUERY_LOG - ��� ��������� ������� (���������� ���������� ���� TMySQL->logQueries)

class MySQL {
  var $Connection, $name, $prefix;
  var $logQueries = false;
  var $error_reporting = true;
  #-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-#
  function init($mysqlHost, $mysqlUser, $mysqlPswd, $mysqlName, $mysqlPrefix='')
  # ��������� ���������� � ����� ������ MySQL � �������� ��������� ���� ������.
  {
    $this->name = $mysqlName;
    $this->prefix = $mysqlPrefix;
    @$this->Connection = mysql_connect($mysqlHost, $mysqlUser, $mysqlPswd, true);
    if (!$this->Connection) FatalError("Can not connect to MySQL server. Check login and password");
    if (defined('LOCALE_CHARSET')) {
      $version = preg_replace('/[^\d\.]/', '', mysql_get_server_info());
      if (version_compare($version, '4.1') >= 0) $this->query("SET NAMES '".LOCALE_CHARSET."'");
    }
    if (!mysql_select_db($this->name, $this->Connection)) FatalError(mysql_error($this->Connection));
  }
  #-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-#
  function query($query)
  # ��������� ������ � ���� ������ � ������� ������������ ���������� ������� init().
  {
    global $Eresus, $__MYSQL_QUERY_COUNT, $__MYSQL_QUERY_TIME, $__MYSQL_QUERY_LOG;

    if ($Eresus->conf['debug']) {
      $time_start = microtime();
      if ($this->logQueries) $__MYSQL_QUERY_LOG .= $query."\n";
    }
    $result = mysql_query($query, $this->Connection);
    if ($this->error_reporting && !$result) FatalError(mysql_error($this->Connection)."<br />Query \"$query\"");
    if ($Eresus->conf['debug']) {
      $__MYSQL_QUERY_COUNT++;
      $__MYSQL_QUERY_TIME += microtime() - $time_start;
    }
    return $result;
  }
  #-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-#
  function query_array($query)
  # ��������� ������ � ���� ������ � ���������� ������������� ������ ��������
  {
    global $__MYSQL_QUERY_COUNT, $__MYSQL_QUERY_TIME, $__MYSQL_QUERY_LOG;

    $result = $this->query($query);
    $values = Array();
    while($row = mysql_fetch_assoc($result)) {
      if (count($row)) foreach($row as $key => $value) $row[$key] = $value;
      $values[] = $row;
    }
    return $values;
  }
  #-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-#
  function select($tables, $condition = '', $order = '', $desc = false, $fields = '', $lim_rows = 0, $lim_offset = 0, $group = '', $distinct = false)
  # ��������� ������ SELECT � ���� ������ ��������� ����� query().
  #  $tables - �������, �� ������� ��������� ������� ������� (FROM)
  #  $contidion - ������� ��� ������� (WHERE)
  #  $order - ����, �� ������� ������� ��������� ���������� (OREDER BY)
  #  $desc - ���� ����� true, �� ���������� ���� � �������� �������
  #  $fields - ������ �����, ������� ��������� ��������
  #  $lim_rows - ���-�� ����� ��� �������
  #  $lim_offset - ������ ������ ��� �������
  #  $group - ���� ��� �����������
  #  $distinct - ���� ����� true, �� ����� ������� ������ ���������� ��������.
  {
    $query = 'SELECT ';
    if ($distinct) $query .= 'DISTINCT ';
    if (!strlen($fields)) $fields = '*';
    $tables = str_replace('`','',$tables);
    $tables = preg_replace('/([\w.]+)/i', '`'.$this->prefix.'$1`', $tables);
    $query .= $fields." FROM ".$tables;
    if (strlen($condition)) $query .= " WHERE ".$condition;
    if (strlen($group)) $query .= " GROUP BY ".$group."";
    if (strlen($order)) {
      $query .= " ORDER BY ".$order;
      if ($desc) $query .= ' DESC';
    }
    if ($lim_rows) {
      $query .= ' LIMIT ';
      if ($lim_offset) $query .= "$lim_offset, ";
      $query .= $lim_rows;
    }

    $result = $this->query_array($query);

    return $result;
  }
  #-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
  function insert($table, $item)
  # ��������� ������ INSERT � ���� ������ ��������� ����� query().
  #  $table - �������, � ������� ���� �������� ������
  #  $item - ������������� ������ ��������
  {
    $hnd = mysql_list_fields($this->name, $this->prefix.$table, $this->Connection);
    $cols = '';
    $values = '';
    while (($field = @mysql_field_name($hnd, $i++))) if (isset($item[$field])) {
      $cols .= ", `$field`";
      $values .= " , '".mysql_real_escape_string($item[$field], $this->Connection)."'";
    }
    $cols = substr($cols, 2);
    $values = substr($values, 2);
    $result = $this->query("INSERT INTO ".$this->prefix.$table." (".$cols.") VALUES (".$values.")");
    return $result; 
  }
  #-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-#
  function update($table, $set, $condition)
  # ��������� ������ UPDATE � ���� ������ ��������� ����� query().
  #  $table - �������, � ������� ���� ������� ���������
  #  $set - ���������� ��������
  #  $condition - ������� ��� ���������
  {
    $result = $this->query("UPDATE `".$this->prefix.$table."` SET ".$set." WHERE ".$condition);
    return $result;
  }
  #-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-#
  function delete($table, $condition)
  # ��������� ������ DELETE � ���� ������ ��������� ����� query().
  #  $table - �������, �� ������� ��������� ������� ������
  #  $condition - �������� ��������� �������
  {
    $result = $this->query("DELETE FROM `".$this->prefix.$table."` WHERE ".$condition);
    return $result;
  }
  #-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-#
  function fields($table)
  # ���������� ������ ����� �������
  #  $table - �������, ��� ������� ���� �������� ������ �����
  {
    $hnd = mysql_list_fields($this->name, $this->prefix.$table, $this->Connection);
    if ($hnd == false) FatalError(mysql_error($this->Connection));
    while (($field = @mysql_field_name($hnd, $i++))) $result[] = $field;
    return $result;
  }
  #-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-#
  function selectItem($table, $condition, $fields = '')
  {
    if ($table[0] != "`") $table = "`".$table."`";
    $tmp = $this->select($table, $condition, '', false, $fields);
    $tmp = isset($tmp[0])?$tmp[0]:null;
    return $tmp;
  }
  #-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-#
  function updateItem($table, $item, $condition)
  {
    $hnd = mysql_list_fields($this->name, $this->prefix.$table, $this->Connection);
    if ($hnd === false) FatalError(mysql_error($this->Connection));
    $values = array();
    $i = 0;
    while (($field = @mysql_field_name($hnd, $i++))) if (isset($item[$field])) $values[] = "`$field`='".mysql_real_escape_string($item[$field], $this->Connection)."'";
    $values = implode(', ', $values);
    $result = $this->query("UPDATE `".$this->prefix.$table."` SET ".$values." WHERE ".$condition); 
    return $result;
  }
  #-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-#
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
  #-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-#
  function getInsertedID()
  {
    return mysql_insert_id($this->Connection);
  }
  #-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-#
  function tableStatus($table, $param='')
  {
    $result = $this->query_array("SHOW TABLE STATUS LIKE '".$table."'");
    if (!empty($param)) $result = $result[0][$param];
    return $result;
  }
  #-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-#
}
?>