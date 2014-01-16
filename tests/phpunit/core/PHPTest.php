<?php
/**
 * ${product.title}
 *
 * Модульные тесты
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

/**
 * @package Eresus
 * @subpackage Tests
 */
class Eresus_PHPTest extends PHPUnit_Framework_TestCase
{
    /**
     * @covers Eresus_PHP::iniSizeToInt
     */
    public function test_iniSizeToInt()
    {
        $this->assertEquals(1024, Eresus_PHP::iniSizeToInt('1024'));

        $this->assertEquals(2 * 1024, Eresus_PHP::iniSizeToInt('2K'));
        $this->assertEquals(2 * 1024, Eresus_PHP::iniSizeToInt('2 K'));

        $this->assertEquals(3 * 1024 * 1024, Eresus_PHP::iniSizeToInt('3M'));
        $this->assertEquals(3 * 1024 * 1024, Eresus_PHP::iniSizeToInt('3 M'));

        $this->assertEquals(4 * 1024 * 1024 * 1024, Eresus_PHP::iniSizeToInt('4G'));
        $this->assertEquals(4 * 1024 * 1024 * 1024, Eresus_PHP::iniSizeToInt('4 G'));
    }
}
