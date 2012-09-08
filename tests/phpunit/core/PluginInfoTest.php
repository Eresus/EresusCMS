<?php
/**
 * ${product.title}
 *
 * Тесты
 *
 * @version ${product.version}
 * @copyright ${product.copyright}
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
 */

require_once __DIR__ . '/../bootstrap.php';
require_once TESTS_SRC_DIR . '/core/Eresus/PluginInfo.php';

/**
 * @package Eresus
 * @subpackage Tests
 */
class Eresus_PluginInfo_Test extends PHPUnit_Framework_TestCase
{
	/**
	 * @covers Eresus_PluginInfo::loadFromXmlFile
	 */
	public function test_loadFromXmlFile()
	{
		$method = new ReflectionMethod('Eresus_PluginInfo', 'loadFromXmlFile');
		$method->setAccessible(true);

		$method->invoke(null, TESTS_FIXT_DIR . '/core/PluginInfo/no_reqs/myplugin/plugin.xml');
	}

	/**
	 * @covers Eresus_PluginInfo::loadFromFile
	 * @covers Eresus_PluginInfo::loadFromPhpFile
	 * @covers Eresus_PluginInfo::getRequiredKernel
	 */
	public function test_kernel_req()
	{
		$info = Eresus_PluginInfo::loadFromFile(TESTS_FIXT_DIR .
			'/core/PluginInfo/kernel_php/myplugin.php');
		$this->assertEquals(array('3.00', ''), $info->getRequiredKernel());
	}
}
