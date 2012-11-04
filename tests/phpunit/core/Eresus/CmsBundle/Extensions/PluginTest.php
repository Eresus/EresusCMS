<?php
/**
 * ${product.title}
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
 */

namespace Tests\Eresus\CmsBundle\Extensions;

use Eresus\CmsBundle\Extensions\Plugin;
use Eresus_Tests;

require_once __DIR__ . '/../../../../bootstrap.php';

/**
 * @package Eresus
 * @subpackage Tests
 */
class PluginTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers Eresus\CmsBundle\Extensions\Plugin::getDataURL
     */
    public function testGetDataUrl()
    {
        $Eresus = new \stdClass();
        $Eresus->froot = '/home/exmaple.org/';
        $Eresus->fdata = '/home/exmaple.org/data/';
        $Eresus->fstyle = '/home/exmaple.org/style/';
        $Eresus->root = 'http://exmaple.org/';
        $Eresus->data = 'http://exmaple.org/data/';
        $Eresus->style = 'http://exmaple.org/style/';
        Eresus_Tests::setStatic('Eresus_CMS', $Eresus, 'legacyKernel');
        $test = new Plugin();
        $this->assertEquals('http://exmaple.org/data/plugin/',
            $test->getDataURL());
    }

    /**
     * @covers Eresus\CmsBundle\Extensions\Plugin::getCodeURL
     */
    public function testGetCodeUrl()
    {
        $Eresus = new \stdClass();
        $Eresus->froot = '/home/exmaple.org/';
        $Eresus->fdata = '/home/exmaple.org/data/';
        $Eresus->fstyle = '/home/exmaple.org/style/';
        $Eresus->root = 'http://exmaple.org/';
        $Eresus->data = 'http://exmaple.org/data/';
        $Eresus->style = 'http://exmaple.org/style/';
        Eresus_Tests::setStatic('Eresus_CMS', $Eresus, 'legacyKernel');
        $test = new Plugin();
        $this->assertEquals('http://exmaple.org/ext/plugin/',
            $test->getCodeURL());
    }

    /**
     * @covers Eresus\CmsBundle\Extensions\Plugin::getStyleURL
     */
    public function testGetStyleUrl()
    {
        $Eresus = new \stdClass();
        $Eresus->froot = '/home/exmaple.org/';
        $Eresus->fdata = '/home/exmaple.org/data/';
        $Eresus->fstyle = '/home/exmaple.org/style/';
        $Eresus->root = 'http://exmaple.org/';
        $Eresus->data = 'http://exmaple.org/data/';
        $Eresus->style = 'http://exmaple.org/style/';
        Eresus_Tests::setStatic('Eresus_CMS', $Eresus, 'legacyKernel');
        $test = new Plugin();
        $this->assertEquals('http://exmaple.org/style/plugin/',
            $test->getStyleURL());
    }
}

