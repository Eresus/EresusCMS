<?php
/**
 * Eresus 2.10
 *
 * ���������� ������� ����������
 *
 * ������� ���������� ��������� Eresus� 2
 * � 2004-2007, ProCreat Systems, http://procreat.ru/
 * � 2007-2008, Eresus Group, http://eresus.ru/
 *
 * @author Mikhail Krasilnikov <mk@procreat.ru>
 */

class TLanguages {
  var $access = ROOT;
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------#
  function adminRender()
  {

    if (UserRights($this->access)) {
    }
    return '������ ������� ���������� � ���� ������';
  }
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------#
}
?>