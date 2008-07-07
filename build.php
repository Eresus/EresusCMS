MBF/1.0
<?php
/**
 * Procreat Murash 1.0 Project
 *
 * @author Mikhail Krasilnikov <mk@procreat.ru>
 */

SET('BUILD_DATE', date('d.m.y'));
SET('VERSION', '2.11');
SET('LICENSE', 'GPL License 3');
SET('LICENSE_URI', 'http://www.gnu.org/licenses/gpl.txt');
SET('LICENSE_TEXT',<<<EOT
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
EOT
);


#define('TARGET', '');

class CopyFilesHook extends FunctionHook {
	function ondircopy($allow, $name)
	{
		if (preg_match('!/\.svn$!', $name)) $allow = false;
		return $allow;
	}
	//-----------------------------------------------------------------------------
	function onfilecopied($null, $name)
	{
		if (preg_match('!\.(php|js)$!', $name)) substitute($name);
		return $null;
	}
	//-----------------------------------------------------------------------------
}


new CopyFilesHook('copy_files_from');

create_target('distrib');
copy_files_from('main');

if (option('lang')) copy_files_from('lang', '/lang');
if (option('lib')) copy_files_from('lib', '/core/lib');
if (option('ext-3rd')) copy_files_from('ext-3rd', '/ext-3rd');
if (option('tools')) copy_files_from('tools', '/distrib');
if (option('tests')) copy_files_from('t', '/t');

#if (option('mod')) copy_files_from('lang', '/lang');
#if (option('sdk')) copy_files_from('lang', '/lang');
