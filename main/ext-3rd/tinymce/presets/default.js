/**
 * ${product.title} ${product.version}
 *
 * ${product.description}
 *
 * Пресет настроек для TinyMCE
 *
 * @copyright 2004, ProCreat Systems, http://procreat.ru/
 * @copyright 2007, Eresus Project, http://eresus.ru/
 * @license ${license.uri} ${license.name}
 * @author Mikhail Krasilnikov <mihalych@vsepofigu.ru>
 *
 * Данная программа является свободным программным обеспечением. Вы
 * вправе распространять ее и/или модифицировать в соответствии с
 * условиями версии 3 либо (по вашему выбору) с условиями более поздней
 * версии Стандартной Общественной Лицензии GNU, опубликованной Free
 * Software Foundation.
 *
 * Мы распространяем эту программу в надежде на то, что она будет вам
 * полезной, однако НЕ ПРЕДОСТАВЛЯЕМ НА НЕЕ НИКАКИХ ГАРАНТИЙ, в том
 * числе ГАРАНТИИ ТОВАРНОГО СОСТОЯНИЯ ПРИ ПРОДАЖЕ и ПРИГОДНОСТИ ДЛЯ
 * ИСПОЛЬЗОВАНИЯ В КОНКРЕТНЫХ ЦЕЛЯХ. Для получения более подробной
 * информации ознакомьтесь со Стандартной Общественной Лицензией GNU.
 *
 * Вы должны были получить копию Стандартной Общественной Лицензии
 * GNU с этой программой. Если Вы ее не получили, смотрите документ на
 * <http://www.gnu.org/licenses/>
 *
 * @package CoreExtensionsAPI
 *
 * $Id$
 */

/* Загружаем стили сайта, после чего инициализирцем редактор */
jQuery.ajax({
	url: window.Eresus.siteRoot + "/style/styles.xml",
	success: mceInit,
	error: mceInit,
	dataType: "xml"
});

/**
 * Инициализация редактора
 *
 * @param {DOMDocumet|XMLHttpRequrest} data
 * @param {String}                     textStatus
 * @param {XMLHttpRequest}             xhr
 */
function mceInit(data, textStatus, xhr)
{
	/**
	 * Трансформирует свойства CSS в формат JavaScript
	 * @param {String} s  Знак "-" плюс буква в нижнем регистре
	 * @returns Букву в верхнем регистре
	 */
	function css2js(s)
	{
		return s.substr(1).toUpperCase();
	}

	// В этой переменной будем собирать стили
	var siteStyles = [];

	// Вспомогательные переменные
	var style, styleFormat, styleType, css, cssParam, cssValue;

	if (textStatus == "success")
	{
		var styles = data.childNodes[0].childNodes;
		for (var i = 0; i < styles.length; i++)
		{
			style = styles[i];
			if (style.nodeType != 1)
			{
				continue;
			}
			styleFormat = {};
			styleFormat.title = style.getAttribute('ru');
			styleType = style.nodeName.toLowerCase();
			styleFormat[styleType] = style.getAttribute("tag");
			styleFormat.styles = {};
			css = style.textContent.split("\n");
			for (var j = 0; j < css.length; j++)
			{
				css[j] = css[j].replace(/^\s+|\s+$/g, '');
				if (css[j] == "")
				{
					continue;
				}
				css[j] = css[j].split(":");
				cssParam = css[j][0].replace(/-./g, css2js);
				cssValue = css[j][1].replace(/^\s+|\s+$/g, '');
				cssValue = cssValue.replace(/;$/, '');
				styleFormat.styles[cssParam] = cssValue;
			}
			siteStyles.push(styleFormat);
		}
	}

	tinyMCE.init(
	{
		/****************************************************************************
		 * Параметры редактора
		 ****************************************************************************/

		/*
		 * Режим превращения textarea в WYSISYG
		 *
		 * - textareas          - Все textarea
		 * - specific_textareas - Выбранные textarea (см. editor_selector)
		 */
		mode : "specific_textareas",

		/*
		 * Встраивание файлового менеджера в диалог добавления изображений
		 */
		file_browser_callback : function (fieldName, url, type, win)
		{
			var w = window.open(window.Eresus.siteRoot + '/ext-3rd/elfinder/datapopup.php', null,
				'width=600,height=500');
			w.tinymceFileField = fieldName;
			w.tinymceFileWin = win;
		},

		/*
		 * Класс CSS, который должен быть у textarea для превращения её в WYSIWYG
		 */
		editor_selector : "tinymce_default",

		/*
		 * Язык редактора
		 */
		language: "ru",

		/*
		 * Подключаемые плагины
		 *
		 * - advimage     - Расширенный диалог вставки картинок
		 * - advlist      - Дополнительные опции для списков
		 * - fullscreen   - Позволяет разворачивать редактор на всю страницу.
		 *                  Кнопки: fullscreen
		 * - inlinepopups - Открывает диалоги во всплывающих слоях, а не в новых окнах
		 * - paste        - Расширенные возможности вставки
		 * - safari       - Исправляет разные проблемы совместимости в Safari
		 * - table        - Работа с таблицами.
		 *                  Кнопки: tablecontrols, table, row_props, cell_props, delete_col, delete_row,
		 *                  delete_table, col_after, col_before, row_after, row_before, split_cells,
		 *                  merge_cells.
		 */
		plugins : "advimage,advlist,fullscreen,inlinepopups,paste,safari,table",

		/*
		 * Тема оформления
		 */
		theme : "advanced",

		/*
		 * Расположение панели кнопок относительно области редактирования
		 */
		theme_advanced_toolbar_location : "top",

		/*
		 * Выравнивание кнопок на панели
		 */
		theme_advanced_toolbar_align : "left",

		/*
		 * Расположение строки состояния относительно области редактирования
		 */
		theme_advanced_statusbar_location : "bottom",

		/*
		 * Разрешено ли изменение размера области редактирования
		 */
		theme_advanced_resizing : true,

		/*
		 * Список тегов, отображаемых в списке "Формат"
		 */
		theme_advanced_blockformats: "p,h1,h2,h3,h4,h5,h6",

		theme_advanced_buttons1 :
			"fullscreen,|,"+
			"undo,redo,|,"+
			//"formatselect,styleselect,|,"+
			"formatselect,styleselect,|,"+
			"bold,italic,strikethrough,|,"+
			"bullist,numlist,|,outdent,indent,|,"+
			"blockquote,sub,sup,|,"+
			"justifyleft,justifycenter,justifyright,justifyfull",
		theme_advanced_buttons2 :
			"link,unlink,anchor,image,hr,charmap,|,"+
			"tablecontrols,|,"+
			"cut,copy,paste,pastetext,pasteword,|,"+
			"cleanup,code",
		theme_advanced_buttons3 : "",

		/****************************************************************************
		 * Параметры создаваемой разметки
		 ****************************************************************************/

		/*
		 * DOCTYPE, применяемый к документу
		 */
		doctype: '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">',

		/*
		 * Формат тегов: html или xhtml
		 */
		element_format: "xhtml",

		/*
		 * Элементы (теги и атрибуты) разрешённые к использованию в дополнение к стандартным.
		 * См. http://wiki.moxiecode.com/index.php/TinyMCE:Configuration/valid_elements
		 */
		extended_valid_elements :
			"iframe[class|height|id|longdesc|name|src|style|title|width]",

		/*
		 * Файл(ы) стилей, применяемые для оформления контента
		 */
		content_css: window.Eresus.siteRoot + "/style/default.css",

		/*
		 * This option enables you to specify that list elements UL/OL is to be converted to valid XHTML.
		 */
		fix_list_elements: true,

		/*
		 * This option enables you to specify that table elements should be moved outside paragraphs or
		 * other block elements.
		 */
		fix_table_elements: true,

		/*
		 * This option controls if invalid contents should be corrected before insertion in IE. IE has a
		 * bug that produced an invalid DOM tree if the input contents aren't correct so this option tries
		 * to fix this using preprocessing of the HTML string.
		 */
		fix_nesting: true,

		/*
		 * If this option is set to true, all URLs returned from the MCFileManager will be relative from
		 * the specified document_base_url. If it's set to false all URLs will be converted to absolute
		 * URLs.
		 */
		relative_urls: false,

		/*
		 * If this option is enabled the protocol and host part of the URLs returned from the
		 * MCFileManager will be removed. This option is only used if the relative_urls option is set to
		 * false.
		 */
		remove_script_host: false,


		style_formats : siteStyles,


		dummy: null
	});

}

