<?php
/**
 * ${product.title} ${product.version}
 *
 * ${product.description}
 *
 * Коннектор к EditArea (Подсветка синтаксиса)
 *
 * @copyright 2010, Eresus Project, http://eresus.ru/
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
 * @package Eresus
 */

/**
 * Класс-коннектор
 *
 * @package Eresus
 */
class EditAreaConnector extends EresusExtensionConnector
{
	/**
	 * Обработка поля "syntax" для старых форм
	 *
	 * @param Form  $form
	 * @param array $field
	 * @return array
	 */
	public function forms_memo_syntax($form, $field)
	{
		// Проверяем, не были ли уже выполнены эти действия ранее
		if (!isset($form->options['editarea']))
		{
	    // Подключаем EditArea
			Eresus_Kernel::app()->getPage()->linkScripts($this->root . 'edit_area_full.js');

	    $form->options['editarea'] = true;
		}

		if (!$field['id'])
		{
			$field['id'] = $form->form['name'] . '_' . $field['name'];
		}

		Eresus_Kernel::app()->getPage()->addScripts("
			editAreaLoader.init({
				id : '{$field['id']}',
				syntax: '{$field['syntax']}',
				start_highlight: true,
				language: 'ru',
				toolbar: 'search,go_to_line,undo,redo,reset_highlight,highlight,word_wrap,help'
			});
		");

		return $field;
	}
	//-----------------------------------------------------------------------------
}
