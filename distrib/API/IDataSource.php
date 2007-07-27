<?php
/**
* Eresus� 2, ��������� ����
*
* ���� ����� ��������� ��������� � ��������� ������ (�������� � ���� ������),
* ������������ Eresus.
* �������������� ��� ������ � ��������� ������� �� �������� � ������ � ����
* ����� ������������� �� SQL-�������� �����. 
*
* @author: Mikhail Krasilnikov <mk@procreat.ru>
* @modified: 2007-07-25
*/

class IDataSource {
  /**
  * ���� TRUE (�� ���������) � ������ ������ ������ ����� ������� � �������� ��������� �� ������
  *
  * @var  bool  $display_errors  
  */
  var $display_errors = true;

  /**
  * ��������� ���������� �������� ������ � �������� ��������
  *
  * @param  string  $server    ������ ������
  * @param  string  $username  ��� ������������ ��� ������� � �������
  * @param  string  $password  ������ ������������
  * @param  string  $source    ��� ��������� ������
  * @param  string  $prefix    ������� ��� ��� ������. �� ��������� ''
  *
  * @return  bool  ��������� ����������
  */
  function init($server, $username, $password, $source, $prefix='')

  /**
  * ��������� ������ � ���������
  *
  * @param  string  $query    ������ � ������� ���������
  *
  * @return  mixed  ��������� �������. ��� ������� �� ���������, ������� � ����������
  */
  function query($query)
  /**
  * ��������� ������ � ��������� � ���������� ������������� ������ ��������
  *
  * @param  string  $query    ������ � ������� ���������
  *
  * @return  array|bool  ����� � ���� ������� ��� FALSE � ������ ������
  */
  function query_array($query)

  /**
  * ���������� ������� ������ �� ���������
  *
  * @param  string   $tables    ������ ������ �� ������� ���������� �������
  * @param  string   $condition
  * @param  string   $order
  * @param  bool     $desc
  * @param  string   $fields
  * @param  integer  $rows
  * @param  integer  $offset
  * @param  string   $group
  * @param  bool     $distinct
  *
  * @return  array|bool  ��������� �������� � ���� ������� ��� FALSE � ������ ������
  */
  function select($tables, $condition = '', $order = '', $desc = false, $fields = '', $rows = 0, $offset = 0, $group = '', $distinct = false)

  /**
  * ������� ��������� � ��������
  *
  * @param  string  $table  �������, � ������� ���� �������� �������
  * @param  array   $item   ������������� ������ ��������
  *
  * @return  mixed  ��������� ���������� ��������
  */
  function insert($table, $item)

  function update($table, $set, $condition)
  # ��������� ������ UPDATE � ���� ������ ��������� ����� query().
  #  $table - �������, � ������� ���� ������� ���������
  #  $set - ���������� ��������
  #  $condition - ������� ��� ���������

  function delete($table, $condition)
  # ��������� ������ DELETE � ���� ������ ��������� ����� query().
  #  $table - �������, �� ������� ��������� ������� ������
  #  $condition - �������� ��������� �������

  function fields($table)
  # ���������� ������ ����� �������
  #  $table - �������, ��� ������� ���� �������� ������ �����

  function selectItem($table, $condition, $fields = '')

  function updateItem($table, $item, $condition)

  function count($table, $condition='', $group='', $rows=false)
  # ���������� ���������� ������� � ������� ��������� ����� query().
  #  $table - �������, ��� ������� ��������� ��������� ���-�� �������

  function getInsertedID()

  function tableStatus($table, $param='')
}
?>