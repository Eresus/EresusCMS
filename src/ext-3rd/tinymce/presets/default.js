/**
 * ${product.title} ${product.version}
 *
 * ${product.description}
 *
 * Набор настроек для TinyMCE
 *
 * @copyright 2004, Михаил Красильников <mihalych@vsepofigu.ru>
 * @copyright 2007, Eresus Project, http://eresus.ru/
 * @license ${license.uri} ${license.name}
 * @author Михаил Красильников <mihalych@vsepofigu.ru>
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
 * @package EresusCMS
 */

var Eresus = Eresus || {siteRoot : '/'};

/* Загружаем стили сайта, после чего инициализируем редактор */
jQuery.ajax({
	url: Eresus.siteRoot + "/style/styles.xml",
	success: mceInit,
	error: mceInit,
	dataType: "xml"
});

//noinspection JSUnusedLocalSymbols
/**
 * Инициализация редактора
 *
 * @param {Document||XMLHttpRequest} data
 * @param {String}                   textStatus
 * @param {XMLHttpRequest}           xhr
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
		var styles = data.getElementsByTagName('styles')[0].childNodes;
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
			css = style.textContent ? style.textContent.split("\n") : style.text.split("\n");
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
		 * Режим превращения textarea в WYSIWYG
		 *
		 * - textareas          - Все textarea
		 * - specific_textareas - Выбранные textarea (см. editor_selector)
		 */
		mode : "specific_textareas",

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
		 * - images       - Менеджер изображений
		 * - inlinepopups - Открывает диалоги во всплывающих слоях, а не в новых окнах
		 * - paste        - Расширенные возможности вставки
		 * - safari       - Исправляет разные проблемы совместимости в Safari
		 * - table        - Работа с таблицами.
		 *                  Кнопки: tablecontrols, table, row_props, cell_props, delete_col, delete_row,
		 *                  delete_table, col_after, col_before, row_after, row_before, split_cells,
		 *                  merge_cells.
		 */
		plugins : "advimage,advlist,fullscreen,images,inlinepopups,paste,safari,table",

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
			"link,unlink,anchor,images,image,hr,charmap,|,"+
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
		doctype: '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Strict//EN">',

		/*
		 * Формат тегов: html или xhtml
		 */
		element_format: "html",

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
		 * This option enables you to make sure that any non block elements or text nodes are wrapped in
		 * block elements. For example <strong>something</strong> will result in output like:
		 * <p><strong>something</strong></p>. This option is enabled by default as of 3.0a1.
		 */
		forced_root_block: '',

		/*
		 * If this option is set to true, all URLs returned from the MCFileManager will be relative from
		 * the specified document_base_url. If it's set to false all URLs will be converted to absolute
		 * URLs.
		 */
		relative_urls: false,

		/*
		 * This option enables you to control whether TinyMCE is to be clever and restore URLs to their
		 * original values. URLs are automatically converted (messed up) by default because the built-in
		 * browser logic works this way.
		 */
		convert_urls : false,

		/*
		 * If this option is enabled the protocol and host part of the URLs returned from the
		 * MCFileManager will be removed. This option is only used if the relative_urls option is set to
		 * false.
		 */
		remove_script_host: false,

		/*
		 * This option enables or disables the element cleanup functionality. If you set this option to
		 * false, all element cleanup will be skipped but other cleanup functionality such as URL
		 * conversion will still be executed.
		 */
		verify_html: false,

		style_formats : siteStyles,


		dummy: null
	});

}

