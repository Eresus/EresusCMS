/**
 * ${product.title} ${product.version}
 *
 * ${product.description}
 *
 * @copyright 2004-2007, ProCreat Systems, http://procreat.ru/
 * @copyright 2007-2008, Eresus Project, http://eresus.ru/
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
 * $Id$
 */

var objRowSel = null;
var httpRoot;
var slctPanel;

function filesInit(root, panel)
{
	httpRoot = root;
	slctPanel = panel;
	var obj = $('#' + panel + 'Panel');
	if (obj.length)
	{
		rowSelect($('tr', obj).eq(2));
	}
}

function setPanel(url)
{
	url = url.toString();
	if (url.indexOf('&sp=') != -1)
	{
		url = url.replace(/sp=([lr])/,'sp='+slctPanel);
	}
	else
	{
		url += '&sp='+slctPanel;
	}
	return url;
}

function keyboardEvents()
{
	alert('test');
}

function getCurrentFolder()
{
	var folder = $('tr', objRowSel.closest('table')).eq(0).text().substr(2);
	return folder;
}


/**
 *
 * @param {jQuery} objRow
 */
function rowSelect(objRow)
{
	if (objRowSel != null)
	{
		objRowSel.css('background-color', 'white');
		objRowSel.css('color', '#000050');
	}
	objRow.css('background-color', '#4682b4');
	objRow.css('color', 'white');
	objRowSel = objRow;
	var objStatus = $('#SelFileName');
	objStatus.val(httpRoot + getCurrentFolder() + $('td', objRowSel).eq(1).text());
	slctPanel = objRowSel.closest('table').attr('id').substr(0,1);
	document.upload.action = document.upload.action.replace(/sp=\w/, 'sp=' + slctPanel);
}

function Copy(strControlName)
{
	var ua = navigator.userAgent.toLowerCase();
	var isIE = ((ua.indexOf("msie") != -1) && (ua.indexOf("opera") == -1) && (ua.indexOf("webtv") == -1));
	if (isIE) {
		var objControl = document.getElementById(strControlName);
		objControl.createTextRange().execCommand("Copy");
		objControl.focus();
	} else alert('��� ������� �������� ������ Internet Explorer :(');
}

function filesCD(url)
{
	window.location = setPanel(url);
}

/**
 * �������� ������ �� �������� ����������
 *
 * @return void
 */
function filesMkDir()
{
	var folder = prompt('��� �����','');
	if (folder != undefined && folder.length)
		window.location = setPanel(window.location)+'&mkdir='+folder;
}

function filesRename()
{
	if (objRowSel != null)
	{
		var filename = $('td', objRowSel).eq(1).text();
		if (filename.substr(-2) != '..')
		{
			var newname = prompt('�������������',filename);
			if (newname != undefined && newname.length && newname != filename)
			{
				window.location = setPanel(window.location)+'&rename='+filename+'&newname='+newname;
			}
		}
	}
}

function filesChmod()
{
	if (objRowSel != null)
	{
		var filename = $('td', objRowSel).eq(0).text();
		if (filename.substr(-2) != '..')
		{
			var perms = $('td', objRowSel).eq(4).text();
			var a = new Array(perms.substr(0, 3), perms.substr(3, 3), perms.substr(6, 3));
			perms = '0';
			var value;
			for (var i=0; i < 3; i++)
			{
				value = 0;
				if (a[i].substr(0, 1) == 'r') value += 4;
				if (a[i].substr(1, 1) == 'w') value += 2;
				if (a[i].substr(2, 1) == 'x') value += 1;
				perms += value.toString();
			}
			var newperms = prompt('���������� �����', perms);
			if (newperms != undefined && newperms.length && newperms != perms)
			{
				window.location = setPanel(window.location)+'&chmod='+filename+'&perms='+newperms;
			}
		}
	}
}

function filesCopy()
{
	if (objRowSel != null)
	{
		var filename = $('td', objRowSel).eq(1).text();
		if (filename.substr(-2) != '..')
		{
			var obj = $('#' + (slctPanel=='l'?'r':'l')+'Panel');
			obj = $('tr', obj);
			for (var i = 2; i < obj.length; i++)
			{
				if ($('td', obj.eq(i)).eq(1).text() == filename)
				{
					if (confirm('���� "'+filename+'" ��� ����������. ����������?'))
					{
						break;
					}
					else
					{
						return;
					}
				}
			}
			window.location = setPanel(window.location)+'&copyfile='+filename;
		}
	}
}

function filesMove()
{
	if (objRowSel != null) {
		var filename = $('td', objRowSel).eq(1).text();
		if (filename.substr(-2) != '..')
		{
			var obj = $('#' + (slctPanel=='l'?'r':'l')+'Panel');
			obj = $('tr', obj);
			for (var i = 2; i < obj.length; i++)
			{
				if ($('td', obj.eq(i)).eq(1).text() == filename)
				{
					if (confirm('���� "'+filename+'" ��� ����������. ����������?'))
					{
						break;
					}
					else
					{
						return;
					}
				}
			}
			window.location = setPanel(window.location)+'&movefile='+filename;
		}
	}
}

function filesDelete()
{
	if (objRowSel != null)
	{
		var filename = $('td', objRowSel).eq(1).text();
		if ((filename.substr(-2) != '..') && confirm('������������� �������� "'+filename+'"?'))
		{
			window.location = setPanel(window.location)+'&delete='+filename;
		}
	}
}

