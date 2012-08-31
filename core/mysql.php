<?
#-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-#
# MYSQL.PHP - ����� ��� ������ � �� MySQL
# ������ 1.19
# ���� ����������: 10.05.06
# � ProCreat Syste,s (http://procreat.ru/)
#-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-#
# 1.19
#    + �������� ����� tableStatus
# 1.18
#    * �������������� ��������� � ������ insert()
# 1.17
#    * �������������� ���������
# 1.16
#    # ���������� ������ � ������ selectItem()
# 1.15
#    + ��������� �������������� ������������ ������ ���������� � ������ init
# 1.14
#    * �������� ������ insert() � updateItem() - ������� ����������� �������
#    * ������� ����� query_array() � ����������� �� ���� select() � selectItem() - ������� ��������� ����������� �������
# 1.13
#    + �������� ����� getInsertedID() - ������������ ������� AUTO_INCREMENT ���� ��� ���������� ������� INSERT
# 1.12
#    * �������� ��� ����� _log_queries �� logQueries
# 1.11
#    * ������� ������� ���������� � ������ select
# 1.10
#    + �������� ����� query_array
# 1.09
#    * � ������ count ��������� ����������� �����������
# 1.08
#    # � ������ select ������ ��������� �������������� ������ ������
# 1.07
#    + function count($table) - ���������� ���������� ������� � �������
#    + ��� ������������� DEBUG_MODE � ���������� �� ������� ������ ��������� � ���� ������ �������
# 1.06
#    * select ���������� ������ ������������� ������
#    + � ������ query �������� �������� $error_reporting=true
#-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-#

# ������� ������� (�������� ��� ������������� ����� DEBUG_MODE)
# ������������� ���������� ���������� 
#  $__MYSQL_QUERY_COUNT - ������� ���������� �������� � ��
#  $__MYSQL_QUERY_TIME - ������� ����� ����� �������� � ��
#  $__MYSQL_QUERY_LOG - ��� ��������� ������� (���������� ���������� ���� TMySQL->logQueries)
if (!defined('DEBUG_MODE')) define('DEBUG_MODE', false);

class TMySQL {
  var $Connection, $name, $prefix,
        $logQueries = false,
        $functionStack = array();
  #-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-#
  function functionStackPush($function_name)
  {
    array_push($this->functionStack, $function_name);
  }
  #-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-#
  function functionStackPop()
  {
    array_pop($this->functionStack);
  }
  #-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-#
  function ErrorMessage($msg = 'Unknown', $task = 'Unknown', $LINE= 'Unknown')
  # ������� ������� ��������� �� ������ MySQL � ���������� ������ �������
  #   $msg - �������� ������
  #   $task - ��������, ��� ���������� ������� ���������� ������
  #   $LINE - ����� ������, � ������� ���������� ������ (� �������� ���������, ������� __LINE__)
  {
  global $PHP_SELF;
    if (constant('DEBUG_MODE')) {
      foreach($this->functionStack as $func) $_stack .= "&nbsp;&nbsp;TMySQL.".$func."<br />\n";
      $_stack = "<br />Call stack:<br />\n".$_stack;
    }
    echo "<div align=\"center\">\n".
      "<table width=\"80%\" style=\"background-color: #79c; border-style: solid; border-width: 1; border-color: #acf #025 #025 #acf; font-family: verdana; font-size: 8pt;\">\n".
      "<tr><td bgcolor=\"black\" align=\"center\" style=\"color: yellow; font-weight: bold; border-style: solid; border-width: 1; border-color: #025 #acf #acf #025;\">MySQL Error</td></tr>\n".
      "<tr><td style=\"background-color: #79c; color: white; text-align: left; padding: 10; font-weight: bold; border-style: solid; border-width: 1; border-color: #025 #acf #acf #025;\">".
      "Error: $msg<br /> Action: ".$task."<br /> Adress: ".$PHP_SELF."<br /> Sript file: ".__FILE__."<br /> Line: ". $LINE.$_stack."</td></tr>\n".
      "</table></div>\n";
    exit();
  }
  #-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-#
  function init($mysqlHost, $mysqlUser, $mysqlPswd, $mysqlName, $mysqlPrefix='')
  # ��������� ���������� � ����� ������ MySQL � �������� ��������� ���� ������.
  {
    if (constant('DEBUG_MODE')) $this->functionStackPush("init('$mysqlHost', '$mysqlUser', '[PASSWORD]', '$mysqlName', '$mysqlPrefix')");
    $this->name = $mysqlName;
    $this->prefix = $mysqlPrefix;
    @$this->Connection = mysql_connect($mysqlHost, $mysqlUser, $mysqlPswd, true);
    if (!$this->Connection) $this->ErrorMessage("Can not connect","Connecting to MySQL server. Check login and password",__LINE__);
    if (!mysql_select_db($this->name, $this->Connection)) $this->ErrorMessage(mysql_error($this->Connection),"Selecting database \"".$this->name."\"",__LINE__);
    if (constant('DEBUG_MODE')) $this->functionStackPop();
  }
  #-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-#
  function query($query, $error_reporting=true)
  # ��������� ������ � ���� ������ � ������� ������������ ���������� ������� init().
  {
  global $__MYSQL_QUERY_COUNT, $__MYSQL_QUERY_TIME, $__MYSQL_QUERY_LOG;

    if (constant('DEBUG_MODE')) {
      $this->functionStackPush("query('$query', '$error_reporting')");
      $time_start = microtime();
      if ($this->logQueries) $__MYSQL_QUERY_LOG .= $query."\n";
    }
    $result = mysql_query($query, $this->Connection);
    if ($error_reporting && ($result == false)) $this->ErrorMessage(mysql_error($this->Connection),"Query \"".$query."\"",__LINE__);
    if (constant('DEBUG_MODE')) {
      $__MYSQL_QUERY_COUNT++;
      $__MYSQL_QUERY_TIME += microtime() - $time_start;
      $this->functionStackPop();
    }
    return $result;
  }
  #-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-#
  function query_array($query, $error_reporting=true)
  # ��������� ������ � ���� ������ � ���������� ������������� ������ ��������
  {
  global $__MYSQL_QUERY_COUNT, $__MYSQL_QUERY_TIME, $__MYSQL_QUERY_LOG;

    if (constant('DEBUG_MODE')) {
      $this->functionStackPush("query_array('$query', '$error_reporting')");
    }
    $result = $this->query($query, $error_reporting);
    $values = Array();
    while($row = mysql_fetch_assoc($result)) {
      if (count($row)) foreach($row as $key => $value) $row[$key] = StripSlashes($value);
      $values[] = $row;
    }
    #plaintext();
    #print_r($values);
    #exit;
    if (constant('DEBUG_MODE')) $this->functionStackPop();
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
    if (constant('DEBUG_MODE')) $this->functionStackPush("select('$tables', '$condition', '$order', '$desc', '$fields', '$lim_rows', '$lim_offset', '$group', '$distinct')");
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

    if (constant('DEBUG_MODE')) $this->functionStackPop();
    return $result;
  }
  #-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
  function insert($table, $item)
  # ��������� ������ INSERT � ���� ������ ��������� ����� query().
  #  $table - �������, � ������� ���� �������� ������
  #  $item - ������������� ������ ��������
  {
    if (constant('DEBUG_MODE')) $this->functionStackPush("insert('$table', '$item')");
    $hnd = mysql_list_fields($this->name, $this->prefix.$table, $this->Connection);
    $cols = '';
    $values = '';
    while (($field = @mysql_field_name($hnd, $i++))) if (isset($item[$field])) {
      $cols .= ", `$field`";
      $values .= " , '".mysql_escape_string($item[$field])."'";
    }
    $cols = substr($cols, 2);
    $values = substr($values, 2);
    $result = $this->query("INSERT INTO ".$this->prefix.$table." (".$cols.") VALUES (".$values.")");
    if (constant('DEBUG_MODE')) $this->functionStackPop();
    return $result; 
  }
  #-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-#
  function update($table, $set, $condition)
  # ��������� ������ UPDATE � ���� ������ ��������� ����� query().
  #  $table - �������, � ������� ���� ������� ���������
  #  $set - ���������� ��������
  #  $condition - ������� ��� ���������
  {
    if (constant('DEBUG_MODE')) $this->functionStackPush("update('$table', '$set', '$condition')");
    $result = $this->query("UPDATE `".$this->prefix.$table."` SET ".$set." WHERE ".$condition);
    if (constant('DEBUG_MODE')) $this->functionStackPop();
    return $result;
  }
  #-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-#
  function delete($table, $condition)
  # ��������� ������ DELETE � ���� ������ ��������� ����� query().
  #  $table - �������, �� ������� ��������� ������� ������
  #  $condition - �������� ��������� �������
  {
    if (constant('DEBUG_MODE')) $this->functionStackPush("delete('$table', '$condition')");
    $result = $this->query("DELETE FROM `".$this->prefix.$table."` WHERE ".$condition);
    if (constant('DEBUG_MODE')) $this->functionStackPop();
    return $result;
  }
  #-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-#
  function fields($table)
  # ���������� ������ ����� �������
  #  $table - �������, ��� ������� ���� �������� ������ �����
  {
    if (constant('DEBUG_MODE')) $this->functionStackPush("fields('$table')");
    $hnd = mysql_list_fields($this->name, $this->prefix.$table, $this->Connection);
    if ($hnd == false) $this->ErrorMessage(mysql_error($this->Connection),"Enumerating fields in \"".$prefix.$table."\"",__LINE__);
    while (($field = @mysql_field_name($hnd, $i++))) $result[] = $field;
    if (constant('DEBUG_MODE')) $this->functionStackPop();
    return $result;
  }
  #-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-#
  function selectItem($table, $condition, $fields = '')
  {
    if (constant('DEBUG_MODE')) $this->functionStackPush("selectItem('$table', '$condition', '$fields')");
    if ($table[0] != "`") $table = "`".$table."`";
    $tmp = $this->select($table, $condition, '', false, $fields);
    $tmp = isset($tmp[0])?$tmp[0]:null;
    if (constant('DEBUG_MODE')) $this->functionStackPop();
    return $tmp;
  }
  #-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-#
  function updateItem($table, $item, $condition)
  {
    if (constant('DEBUG_MODE')) $this->functionStackPush("updateItem('$table', '$item', '$condition')");
    $hnd = mysql_list_fields($this->name, $this->prefix.$table, $this->Connection);
    if ($hnd === false) $this->ErrorMessage(mysql_error($this->Connection),"Listing fields of \"".$this->dbname.'.'.$this->prefix.$table."\"",__LINE__);
    $values = '';
    $i = 0;
    while (($field = @mysql_field_name($hnd, $i++))) $values .= " , `$field`='".(isset($item[$field])?AddSlashes($item[$field]):'')."'";
    $values = substr($values, 2);
    $result = $this->query("UPDATE `".$this->prefix.$table."` SET ".$values." WHERE ".$condition); 
    if (constant('DEBUG_MODE')) $this->functionStackPop();
    return $result;
  }
  #-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-#
  function count($table, $condition='', $group='', $rows=false)
  # ���������� ���������� ������� � ������� ��������� ����� query().
  #  $table - �������, ��� ������� ��������� ��������� ���-�� �������
  {
    if (constant('DEBUG_MODE')) $this->functionStackPush("count('$table')");
    $result = $this->query("SELECT count(*) FROM `".$this->prefix.$table."`".(empty($condition)?'':'WHERE '.$condition).(empty($group)?'':' GROUP BY `'.$group.'`'));
    if ($rows) {
      $count = 0;
      while (mysql_fetch_row($result)) $count++;
      $result = $count;
    } else {
      $result = mysql_fetch_row($result);
      $result = $result[0];
    }
    if (constant('DEBUG_MODE')) $this->functionStackPop();
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