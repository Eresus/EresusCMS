<?php
/**
 * ${product.title}
 *
 * Модульные тесты
 *
 * @version ${product.version}
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
 * @subpackage Tests
 *
 * $Id$
 */

require_once __DIR__ . '/../bootstrap.php';

require_once TESTS_SRC_DIR . '/core/kernel-legacy.php';


class Functions_Test extends PHPUnit_Framework_TestCase
{
	/**
	 * 
	 */
	public function test_ErrorBox()
	{
		$this->assertEquals(
			"<div class=\"errorBoxCap\">заголовок</div>\n<div class=\"errorBox\">\nтекст</div>\n",
			ErrorBox('текст','заголовок')); 
		$this->assertEquals("<div class=\"errorBox\">\nтекст</div>\n", ErrorBox('текст',''));
		$this->assertEquals(
			"<div class=\"errorBoxCap\">заголовок</div>\n<div class=\"errorBox\">\n</div>\n",
			ErrorBox('','заголовок'));
	}

	/**
	 *  
	 */
	public function test_encodeOptions_decodeOptions()
	{	
		$options = array('foo' => 'bar', 'baz' => false);
		$encoded = encodeOptions($options);
		$actual = decodeOptions($encoded);
		$this->assertEquals($options, $actual); 
		
		$options['key'] = 'value';
		$actual = decodeOptions($encoded, array('key' => 'value'));
		$this->assertEquals($options, $actual); 
		$this->assertEquals($options, decodeOptions('', $options));
		$this->assertEquals($options, decodeOptions('foo' . $encoded, $options));
	}
}
