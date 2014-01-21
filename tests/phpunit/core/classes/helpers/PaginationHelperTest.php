<?php
/**
 * Тесты класса PaginationHelper
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

require_once __DIR__ . '/../../../bootstrap.php';

/**
 * Тесты класса PaginationHelper
 *
 * @package Eresus
 * @subpackage Tests
 */
class PaginationHelperTest extends Eresus_TestCase
{
    /**
     * Подготовка окружения
     * @see Framework/PHPUnit_Framework_TestCase::setUp()
     */
    protected function setUp()
    {
        $GLOBALS['Eresus'] = new stdClass();
        $GLOBALS['Eresus']->request = array('path' => '/root/');
        $dwoo = $this->getMock('stdClass', array('get'));
        $dwoo->expects($this->any())->method('get')->will($this->returnArgument(1));
        $this->setStaticProperty('Template', $dwoo, 'dwoo');
    }

    /**
     * Очистка окружения
     * @see Framework/PHPUnit_Framework_TestCase::tearDown()
     */
    protected function tearDown()
    {
        unset($GLOBALS['Eresus']);
    }

    /**
     * Проверяем установку и чтение свойства $total
     *
     * @covers PaginationHelper::setTotal
     * @covers PaginationHelper::getTotal
     */
    public function test_setgetTotal()
    {
        $test = new PaginationHelper();
        $test->setTotal(123);
        $this->assertEquals(123, $test->getTotal());
    }
    //-----------------------------------------------------------------------------

    /**
     * Проверяем установку и чтение свойства $current
     *
     * @covers PaginationHelper::setCurrent
     * @covers PaginationHelper::getCurrent
     */
    public function test_setgetCurrent()
    {
        $test = new PaginationHelper();
        $test->setCurrent(123);
        $this->assertEquals(123, $test->getCurrent());
    }
    //-----------------------------------------------------------------------------

    /**
     * Проверяем конструктор
     *
     * @covers PaginationHelper::__construct
     */
    public function test_construct_wo_args()
    {
        $test = new PaginationHelper();
        $this->assertNull($test->getTotal(), 'Case 1');
        $this->assertEquals(1, $test->getCurrent(), 'Case 1');

        $test = new PaginationHelper(10);
        $this->assertEquals(10, $test->getTotal(), 'Case 2');
        $this->assertEquals(1, $test->getCurrent(), 'Case 2');

        $test = new PaginationHelper(20, 5);
        $this->assertEquals(20, $test->getTotal(), 'Case 3');
        $this->assertEquals(5, $test->getCurrent(), 'Case 3');
    }
    //-----------------------------------------------------------------------------

    /**
     * Проверяем установку и чтение свойства $templatePath
     *
     * @covers PaginationHelper::setTemplate
     * @covers PaginationHelper::getTemplate
     */
    public function test_setgetTemplate()
    {
        $test = new PaginationHelper();
        $test->setTemplate('/path/to/file');
        $this->assertEquals('/path/to/file', $test->getTemplate());
    }
    //-----------------------------------------------------------------------------

    /**
     * Проверяем установку и чтение свойства $urlTemplate
     *
     * @covers PaginationHelper::setUrlTemplate
     * @covers PaginationHelper::getUrlTemplate
     */
    public function test_setgetUrlTemplate()
    {
        $test = new PaginationHelper();
        $test->setUrlTemplate('/%d/');
        $this->assertEquals('/%d/', $test->getUrlTemplate());
    }
    //-----------------------------------------------------------------------------

    /**
     * Проверяем установку и чтение свойства $size
     *
     * @covers PaginationHelper::setSize
     * @covers PaginationHelper::getSize
     */
    public function test_setgetSize()
    {
        $test = new PaginationHelper();
        $test->setSize(5);
        $this->assertEquals(5, $test->getSize());
    }
    //-----------------------------------------------------------------------------

    /**
     * Проверяем метод rewind()
     *
     * @covers PaginationHelper::rewind
     * @covers PaginationHelper::count
     */
    public function test_rewind()
    {
        $test = new PaginationHelper(10, 1);
        $test->rewind();
        $this->assertEquals(10, count($test), 'Case 1');

        $test->setTotal(100);
        $test->rewind();
        $this->assertEquals(11, count($test), 'Case 2');

        $test->setCurrent(50);
        $test->rewind();
        $this->assertEquals(12, count($test), 'Case 3');

        $test->setCurrent(100);
        $test->rewind();
        $this->assertEquals(11, count($test), 'Case 4');
    }
    //-----------------------------------------------------------------------------

    /**
     * Обычное использование
     *
     */
    public function testCommonUseSimple()
    {
        $kernel = $this->getMock('stdClass', array('getFsRoot'));
        $GLOBALS['kernel'] = $kernel;
        $test = new PaginationHelper(10, 5);

        $data = $test->render();
        $this->assertArrayHasKey('pagination', $data, '$data does not contain "pagination" entry');

        $helper = $data['pagination'];

        $i = 1;
        foreach ($helper as $page)
        {
            if ($i > 10)
            {
                $this->fail('Too many iterations');
            }

            if ($i == 5)
            {
                $this->assertTrue($page['current']);
            }

            $this->assertEquals($i, $helper->key());
            $this->assertEquals($i, $page['title'], 'Ivalid page number');
            $this->assertEquals('/root/p' . $i . '/', $page['url'], 'Ivalid page url');
            $i++;
        }
    }

    /**
     * Обычное использование
     *
     */
    public function testCommonUseBeginning()
    {
        $kernel = $this->getMock('stdClass', array('getFsRoot'));
        $GLOBALS['kernel'] = $kernel;

        $test = new PaginationHelper(100, 1);

        $data = $test->render();
        $this->assertArrayHasKey('pagination', $data, '$data does not contain "pagination" entry');

        $helper = $data['pagination'];

        $i = 1;
        foreach ($helper as $page)
        {
            $this->assertEquals($i, $helper->key());

            switch (true)
            {
                case $i > 11:
                    $this->fail('Too many iterations');
                    break;
                case $i >= 1 && $i < 11:
                    $this->assertEquals($i, $page['title'], 'Ivalid page number');
                    $this->assertEquals('/root/p' . $i . '/', $page['url'], 'Ivalid page url');
                    break;
                case $i == 11:
                    $this->assertEquals('&rarr;', $page['title'], 'Invalid last element');
                    $this->assertEquals('/root/p11/', $page['url'], 'Ivalid last page url');
                    break;
            }

            $i++;
        }
    }

    /**
     * Обычное использование
     *
     */
    public function testCommonUseEnding()
    {
        $kernel = $this->getMock('stdClass', array('getFsRoot'));
        $GLOBALS['kernel'] = $kernel;

        $test = new PaginationHelper(100, 100);

        $data = $test->render();
        $this->assertArrayHasKey('pagination', $data, '$data does not contain "pagination" entry');

        $helper = $data['pagination'];

        $i = 1;
        foreach ($helper as $page)
        {
            $this->assertEquals($i, $helper->key());

            switch (true)
            {
                case $i > 11:
                    $this->fail('Too many iterations');
                    break;
                case $i == 1:
                    $this->assertEquals('&larr;', $page['title'], 'Invalid first element');
                    $this->assertEquals('/root/p90/', $page['url'], 'Ivalid first page url');
                    break;
                case $i > 1 && $i < 11:
                    $this->assertEquals($i + 89, $page['title'], 'Ivalid page number');
                    $this->assertEquals('/root/p' . ($i + 89) . '/', $page['url'], 'Ivalid page url');
                    break;
            }

            $i++;
        }
    }

    /**
     * Обычное использование
     *
     */
    public function testCommonUseMiddle()
    {
        $kernel = $this->getMock('stdClass', array('getFsRoot'));
        $GLOBALS['kernel'] = $kernel;

        $test = new PaginationHelper(100, 50);

        $data = $test->render();
        $this->assertArrayHasKey('pagination', $data, '$data does not contain "pagination" entry');

        $helper = $data['pagination'];

        $i = 1;
        foreach ($helper as $page)
        {
            $this->assertEquals($i, $helper->key());

            switch (true)
            {
                case $i > 12:
                    $this->fail('Too many iterations');
                    break;
                case $i == 1:
                    $this->assertEquals('&larr;', $page['title'], 'Invalid first element');
                    $this->assertEquals('/root/p40/', $page['url'], 'Ivalid first page url');
                    break;
                case $i > 1 && $i < 12:
                    $this->assertEquals($i + 43, $page['title'], 'Ivalid page number');
                    break;
                case $i == 12:
                    $this->assertEquals('&rarr;', $page['title'], 'Invalid last element');
                    $this->assertEquals('/root/p60/', $page['url'], 'Ivalid last page url');
                    break;
            }

            $i++;
        }
    }

    /**
     *
     */
    public function testSize2()
    {
        $kernel = $this->getMock('stdClass', array('getFsRoot'));
        $GLOBALS['kernel'] = $kernel;

        $test = new PaginationHelper(4, 4);
        $test->setSize(2);

        $data = $test->render();
        $helper = $data['pagination'];

        $i = 1;
        foreach ($helper as $page)
        {
            switch (true)
            {
                case $i > 3:
                    $this->fail('Too many iterations');
                    break;
                case $i == 1:
                    $this->assertEquals('&larr;', $page['title'], 'Invalid first element');
                    $this->assertEquals('/root/p2/', $page['url'], 'Ivalid first page url');
                    break;
                case $i > 1 && $i < 3:
                    $this->assertEquals($i + 1, $page['title'], 'Ivalid page number');
                    $this->assertEquals('/root/p' . ($i + 1) . '/', $page['url'], 'Ivalid page url');
                    break;

            }

            $i++;
        }
    }

    /**
     *
     */
    public function testSize2Beginning()
    {
        $kernel = $this->getMock('stdClass', array('getFsRoot'));
        $GLOBALS['kernel'] = $kernel;

        $test = new PaginationHelper(4, 1);
        $test->setSize(2);

        $data = $test->render();
        $helper = $data['pagination'];

        $i = 1;
        foreach ($helper as $page)
        {
            switch (true)
            {
                case $i > 4:
                    $this->fail('Too many iterations');
                    break;
                case $i >= 1 && $i < 3:
                    $this->assertEquals($i, $page['title'], 'Ivalid page number');
                    $this->assertEquals('/root/p' . $i . '/', $page['url'], 'Ivalid last page url');
                    break;
                case $i == 3:
                    $this->assertEquals('&rarr;', $page['title'], 'Invalid last element');
                    $this->assertEquals('/root/p3/', $page['url'], 'Ivalid last page url');
                    break;

            }

            $i++;
        }
    }

    /**
     *
     */
    public function testSize2Current3()
    {
        $kernel = $this->getMock('stdClass', array('getFsRoot'));
        $GLOBALS['kernel'] = $kernel;

        $test = new PaginationHelper(4, 3);
        $test->setSize(2);

        $data = $test->render();
        $helper = $data['pagination'];

        $i = 1;
        foreach ($helper as $page)
        {
            switch (true)
            {
                case $i > 4:
                    $this->fail('Too many iterations');
                    break;
                case $i == 1:
                    $this->assertEquals('&larr;', $page['title'], 'Invalid first element');
                    $this->assertEquals('/root/p1/', $page['url'], 'Ivalid first page url');
                    break;
                case $i > 1 && $i < 4:
                    $this->assertEquals($i, $page['title'], 'Invalid page number');
                    $this->assertEquals('/root/p' . $i . '/', $page['url'], 'Invalid last page url');
                    break;
                case $i == 4:
                    $this->assertEquals('&rarr;', $page['title'], 'Invalid last element');
                    $this->assertEquals('/root/p4/', $page['url'], 'Invalid last page url');
                    break;

            }

            $i++;
        }
    }
}

