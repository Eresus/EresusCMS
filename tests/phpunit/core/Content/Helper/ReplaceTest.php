<?php
/**
 * Тесты класса Eresus_Content_Helper_Replace
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
 * Тесты класса Eresus_Content_Helper_Replace
 * @package Eresus
 * @subpackage Tests
 */
class Eresus_Content_Helper_ReplaceTest extends Eresus_TestCase
{
    /**
     * Базовая проверка
     *
     * @covers Eresus_Content_Helper_Replace::__construct
     * @covers Eresus_Content_Helper_Replace::replace
     */
    public function testBasics()
    {
        $replace = new Eresus_Content_Helper_Replace('/\$\(foo:(.+?)\)/',
            function ($match)
            {
                if ('bar' == $match[1])
                {
                    return 'FOO';
                }
                return false;
            }
        );
        $this->assertEquals('bla FOO bla $(foo:baz) bla',
            $replace->replace('bla $(foo:bar) bla $(foo:baz) bla'));
    }
}

