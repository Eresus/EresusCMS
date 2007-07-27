<?php
/**
* Eresus� 2
*
* ���������� ��� ������ � ��������� �����
*
* @author: Mikhail Krasilnikov <mk@procreat.ru>
* @version: 0.0.2
* @modified: 2007-07-25
*/

define('SECTIONS_ACTIVE',  0x0001);
define('SECTIONS_VISIBLE', 0x0002);

class TSections {
  var $table = 'pages';
  var $index = array();
  var $cache = array();
  var $fieldset;
  /**
  * ������ ������ ��������
  *
  * @access  private
  *
  * @param  bool  $force  ������������ �������������� ������
  */
  function index($force = false)
  {
    global $Eresus;
    
    if ($force || !$this->index) {
      if (!$this->fieldset) $this->fieldset = $Eresus->db->fields($this->table);
      $items = $Eresus->db->select($this->table, '', '`position`', false, '`id`,`owner`');
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
  * @param  int  $flags   ����� (��. SECTIONS_XXX)
  *
  * @return  array  �������� ��������
  */
  function brunch($owner, $access = GUEST, $flags = 0)
  {
    global $Eresus;
    
    $result = array();
    # ������ ������
    if (!$this->index) $this->index();
    # ������� ID �������� �����.
    $set = $this->brunch_ids($owner);
    if (count($set)) {
      $list = array();
      # ������ �� ����
      for($i=0; $i < count($set); $i++) if (isset($this->cache[$set[$i]])) {
        $list[] = $this->cache[$set[$i]];
        array_splice($set, $i, 1);
        $i--;
      }
      if (count($set)) {
        $fieldset = implode(',', array_diff($this->fieldset, array('content')));
        # ������ �� ��
        $set = implode(',', $set);
        $items = $Eresus->db->select($this->table, "FIND_IN_SET(`id`, '$set') AND `access` >= $access", 'position', false, $fieldset);
        for($i=0; $i<count($items); $i++) {
          $this->cache[$items[$i]['id']] = $items[$i];
          $list[] = $items[$i];
        }
      }
      if ($flags) { 
        for($i=0; $i<count($list); $i++) if 
          (
            (!($flags & SECTIONS_ACTIVE) || $list[$i]['active']) &&
            (!($flags & SECTIONS_VISIBLE) || $list[$i]['visible'])
          ) $result[] = $list[$i];
      } else $result = $list;
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
  * @param  int  $flags   ����� (��. SECTIONS_XXX)
  *
  * @return  array  �������� ��������
  */
  function children($owner, $access = GUEST, $flags = 0)
  {
    $items = $this->brunch($owner, $access, $flags);
    $result = array();
    for($i=0; $i<count($items); $i++) if ($items[$i]['owner'] == $owner) $result[] = $items[$i];
    return $result;
  }
  //------------------------------------------------------------------------------
  /**
  * ���������� ��� ������������ ������� ����������
  *
  * @access public
  *
  * @param  int  $id   ������������� ��������� ������� �����
  *
  * @return  array  �������� ��������
  */
  function parents($id)
  {
    $this->index();
    $result = array();
    while ($id) {
      foreach($this->index as $key => $value) if (in_array($id, $value)) {
        $result[] = $id = $key;
        break;
      }
    }
    $result = array_reverse($result);
    return $result;
  }
  //------------------------------------------------------------------------------
  /**
  * ���������� ������ �����
  *
  * @access public
  *
  * @return  array  ������ �����
  */
  function fields()
  {
    global $Eresus;
    
    if (isset($this->cache['fields'])) $result = $this->cache['fields']; else {
      $result = $Eresus->db->fields($this->table);
      $this->cache['fields'] = $result;
    }
    return $result;
  }
  //------------------------------------------------------------------------------
  /**
  * ���������� ������
  *
  * @access public
  *
  * @param  int     $id  ID �������
  * ���
  * @param  string  $id  SQL-�������
  *
  * @return  array  �������� �������
  */
  function get($id)
  {
    global $Eresus;
    
    if (is_numeric($id)) $id = "`id`=$id";
    $result = $Eresus->db->selectItem($this->table, $id);
    if ($result) $result['options'] = decodeOptions($result['options']);
    return $result;
  }
  //------------------------------------------------------------------------------
  /**
  * ��������� ������
  *
  * @access public
  *
  * @param  array  $item  ������
  *
  * @return  mixed  �������� ������ ������� ��� false � ������ �������
  */
  function add($item)
  {
    global $Eresus;
    
    $result = false;
    if (isset($item['id'])) unset($item['id']);
    if (!isset($item['owner'])) $item['owner'] = 0;
    $item['created'] = gettime('Y-m-d H:i:s');
    $item['updated'] = $item['created'];
    $item['options'] = isset($item['options']) ? trim($item['options']) : '';
    $item['options'] = (empty($item['options']))?'':encodeOptions(text2array($item['options'], true));
    if (!isset($item['position']) || $item['position'] === '') $item['position'] = isset($this->index[$item['owner']]) ? count($this->index[$item['owner']]) : 0;
    if ($Eresus->db->insert($this->table, $item)) {
      $item['id'] = $Eresus->db->getInsertedId();
      $result = $item;
    }
    return $result;
  }
  //------------------------------------------------------------------------------
}

?>