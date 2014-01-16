<?php
/**
 * ${product.title} ${product.version}
 *
 * ${product.description}
 *
 * Коннектор для TinyMCE
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
 * @package Eresus
 */

/**
 * Класс-коннектор
 *
 * @package Eresus
 */
class TinyMCEConnector extends EresusExtensionConnector
{
	/**
	 * Признак того, что скрипты уже установлены
	 *
	 * @var bool
	 * @since 2.16
	 */
	private static $scriptsInstalled = false;

	/**
	 * Возвращает разметку для подключения WYSIWYG-редактора
	 * @param Form  $form
	 * @param array $field
	 * @return array
	 */
	function forms_html($form, $field)
	{
		$value = isset($form->values[$field['name']]) ?
			$form->values[$field['name']] :
			(isset($field['value'])?$field['value']:'');
		$preset = isset($field['preset']) ? $field['preset'] : 'default';
		$result = "\t\t" . '<tr><td colspan="2">' . $field['label'] .
			'<br /><textarea name="wyswyg_' . $field['name'] . '" class="tinymce_' . $preset .
			'" cols="80" rows="25" style="width: 100%; height: ' .
			$field['height'].';">'.str_replace('$(httpRoot)', Eresus_CMS::getLegacyKernel()->root,
			EncodeHTML($value)).'</textarea></td></tr>'."\n";

		if (!self::$scriptsInstalled)
		{
			Eresus_Kernel::app()->getPage()->linkScripts($this->root.'tiny_mce.js');
			Eresus_Kernel::app()->getPage()->linkScripts($this->root.'presets/'.$preset.'.js');
			self::$scriptsInstalled = true;
		}

		return $result;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Возвращает разметку редактора
	 *
	 * @param array $field
	 *
	 * @return string
	 *
	 * @since 2.16
	 */
	public function getWYSIWYG(array $field)
	{
		$value = isset($field['value']) ? $field['value'] : '';
		$preset = isset($field['preset']) ? $field['preset'] : 'default';
		$html = '<textarea name="wyswyg_' . $field['name'] . '" class="tinymce_' . $preset .
			'" cols="80" rows="25" style="height: ' . $field['height'] . ';">' .
			str_replace('$(httpRoot)', Eresus_CMS::getLegacyKernel()->root, EncodeHTML($value)) . '</textarea>';

		if (!self::$scriptsInstalled)
		{
			$html .=
				'<script type="text/javascript" src="' . $this->root . 'tiny_mce.js"></script>' .
				'<script type="text/javascript" src="' . $this->root . 'presets/' . $preset .
				'.js"></script>';
			self::$scriptsInstalled = true;
		}
		return $html;
	}
	//-----------------------------------------------------------------------------
}
