<?php
/**
 * Eresus 2 ���� ������������
 */

# �������� ��� ��������� ����� �������. �� ���������: false
$Eresus->conf['debug']['enable'] = false;

#-------------------------------------------------------------------------------
#  ��������� ��������� ������ (����)
#-------------------------------------------------------------------------------

# ������������ ����. �� ���������: 'mysql'
#$Eresus->conf['db']['engine'] = 'mysql';

# ���� ������� ����. �� ���������: 'localhost'
#$Eresus->conf['db']['host'] = 'localhost';

# ��� ������������ ����
$Eresus->conf['db']['user'] = '';

# ������ ��� ������� � ����
$Eresus->conf['db']['password'] = '';

# ��� ���� ������
$Eresus->conf['db']['name'] = '';

# ������� ������. �� ���������: '' (��� ��������)
#$Eresus->conf['db']['prefix']   = '';

#-------------------------------------------------------------------------------
#  ������������ ���������
#-------------------------------------------------------------------------------

# ��� ����� �� ���������. �� ���������: 'ru'
#$Eresus->conf['lang'] = 'ru';

# ��������� ���� �� ��������� (��� PHP 5.1.0+)
$Eresus->conf['timezone'] = 'Europe/Moscow';

#-------------------------------------------------------------------------------
#  ��������� ������
#-------------------------------------------------------------------------------

# ������� ������ � �������. �� ���������: 30
#$Eresus->conf['session']['timeout'] = 30;

#-------------------------------------------------------------------------------
#  ���������� � URL
#-------------------------------------------------------------------------------

# �������� ���������� �����. ���������������� ���� Eresus �� ����� ���������� ���� ��������������
#$Eresus->froot = '/usr/home/site.tld/htdocs/';

# ���� �����. ���������������� ���� Eresus �� ����� ���������� ��� ��������������
#$Eresus->host = 'site.tld';

# ���� �� ����� �� ����� �����. ���������������� ���� Eresus �� ����� ���������� ��� ��������������
#$Eresus->path = '/site_path/';

#-------------------------------------------------------------------------------
#  �������� �������������
#-------------------------------------------------------------------------------
# ���� �������� ��������� ����������� �������� �������������
$Eresus->conf['backward'] = array(
	# ������ ��������� ����� TPlugin � ������� ������ 2.10b
  'TPlugin' => true,
  # ������ ��������� ����� TContentPlugin � ������� ������ 2.10b
	'TContentPlugin' => true,
	# ������ ��������� ����� TListContentPlugin � ������� ������ 2.10b
	'TListContentPlugin' => true,
	# ��������� ��������� ����������� ������, ��� ������������� �� ������� ������ �������������
	'weak_password' => false,
);

#-------------------------------------------------------------------------------
#  ������� � ������
#-------------------------------------------------------------------------------

# $Eresus->conf['debug']['mail'] - ���������� �������� �����
# = false       - ���������
# = true        - ���������� ��� ������
# = <���_�����> - ���������� � ����

if ($Eresus->conf['debug']['enable']) {
  ini_set('display_errors', true);
  error_reporting(E_ALL);
  $Eresus->conf['debug']['mail'] = realpath(dirname(__FILE__)).'/../data/.sent';
} else {
  ini_set('display_errors', false);
  error_reporting(0);
}
