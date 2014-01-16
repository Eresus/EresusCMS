<?php
/**
 * Тесты класса Eresus_HTTP_Parameters
 *
 * @version ${product.version}
 * @copyright ${product.copyright}
 * @license ${license.uri} ${license.name}
 * @author Михаил Красильников <m.krasilnikov@yandex.ru>
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

require_once __DIR__ . '/../../bootstrap.php';

/**
 * Тесты класса Eresus_HTTP_Parameters
 * @package Eresus
 * @subpackage Tests
 */
class Eresus_HTTP_ParametersTest extends PHPUnit_Framework_TestCase
{
    /**
     * @covers Eresus_HTTP_Parameters::__construct
     * @covers Eresus_HTTP_Parameters::all
     * @covers Eresus_HTTP_Parameters::add
     * @covers Eresus_HTTP_Parameters::get
     * @covers Eresus_HTTP_Parameters::set
     * @covers Eresus_HTTP_Parameters::has
     * @covers Eresus_HTTP_Parameters::replace
     */
    public function testBrief()
    {
        $params = new Eresus_HTTP_Parameters(array('foo' => 'bar'));
        $this->assertEquals(array('foo' => 'bar'), $params->all());
        $params->add(array('bar' => 'baz'));
        $this->assertEquals(array('foo' => 'bar', 'bar' => 'baz'), $params->all());

        $this->assertTrue($params->has('foo'));
        $this->assertEquals('bar', $params->get('foo', 123));
        $this->assertFalse($params->has('baz'));
        $this->assertEquals(123, $params->get('baz', 123));
        $params->set('baz', 321);
        $this->assertEquals(321, $params->get('baz', 123));

        $params->replace(array('foo' => 'bar'));
        $this->assertEquals(array('foo' => 'bar'), $params->all());
    }

    /**
     * @covers Eresus_HTTP_Parameters::filter
     */
    public function testFilter()
    {
        $params = new Eresus_HTTP_Parameters(array(
            'foo' => '1fwrf2fer3',
            'bar' => 'b%@^a*(@#r'
        ));
        $this->assertEquals(123, $params->filter('foo', null, FILTER_SANITIZE_NUMBER_INT));
        $this->assertEquals('bar', $params->filter('bar', null, FILTER_CALLBACK,
            function ($value)
            {
                return preg_replace('/\W/', '', $value);
            }
        ));
        $this->assertEquals('bar', $params->filter('bar', null, FILTER_REGEXP, '/\W/'));
    }
}

