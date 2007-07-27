<?php
/**
* Eresus� 2
*
* ���������� ��� ������ � �������� �������� �������������
*
* @author: Mikhail Krasilnikov <mk@procreat.ru>
* @version: 0.0.1
* @modified: 2007-07-22
*/

class TUsers {
  var $table = 'users';
  var $cache;
  /**
  * ���������� ������� ������
  *
  * @access  public
  *
  * @param  int  $id  ������������� ������������, ���� �� ������, ������������ �������
  *
  * @return  array  ������� ������
  */
  function get($id)
  {
    global $db;
    
    if (!$this->fieldset) $this->fieldset = $db->fields($this->table);
    if ($force || !$this->index) {
      $items = $db->select($this->table, '', '`id`', false, '`id`,`owner`');
      if ($items) {
        $this->index = array();
        foreach($items as $item) $this->index[$item['owner']][] = $item['id'];
      }
    }
  }
  //------------------------------------------------------------------------------
  /**
  * ������ ������ ID �������� ����������� �����
  *
  * @access  private
  *
  * @param  int  $owner  ID ��������� ������� �����
  *
  * @return  array  ������ ID ��������
  */
  function brunch_ids($owner)
  {
    $result = array();
    if (isset($this->index[$owner])) {
      $result = $this->index[$owner];
      foreach($result as $section) $result = array_merge($result, $this->brunch_ids($section));
    }
    return $result;
  }
  //------------------------------------------------------------------------------
  /**
  * �������� ������� ����������� �����
  *
  * @access  public
  *
  * @param  int  $owner   ������������� ��������� ������� �����
  * @param  int  $access  ����������� ������� �������
  *
  * @return  array  �������� ��������
  */
  function brunch($owner, $access = GUEST)
  {
    global $db;
    
    $result = array();
    # ������ ������
    if (!$this->index) $this->index();
    # ������� ID �������� �����.
    $set = $this->brunch_ids($owner);
    if (count($set)) {
      # ������ �� ����
      for($i=0; $i < count($set); $i++) if (isset($this->cache[$set[$i]])) {
        $result[] = $this->cache[$set[$i]];
        array_splice($set, $i, 1);
        $i--;
      }
      if (count($set)) {
        $fieldset = implode(',', array_diff($this->fieldset, array('content')));
        # ������ �� ��
        $set = implode(',', $set);
        $items = $db->select($this->table, "FIND_IN_SET(`id`, '$set') AND `access` >= $access", 'position', false, $fieldset);
        for($i=0; $i<count($items); $i++) {
          $this->cache[$items[$i]['id']] = $items[$i];
          $result[] = $items[$i];
        }
      }
    }
    return $result;
  }
  //------------------------------------------------------------------------------
  /**
  * ���������� �������� ������� ����������
  *
  * @access public
  *
  * @param  int  $owner   ������������� ��������� ������� �����
  * @param  int  $access  ����������� ������� �������
  *
  * @return  array  �������� ��������
  */
  function children($owner, $access = GUEST)
  {
    $items = $this->brunch($owner, $access);
    $result = array();
    for($i=0; $i<count($items); $i++) if ($items[$i]['owner'] == $owner) $result[] = $items[$i];
    return $result;
  }
  //------------------------------------------------------------------------------
}

?>