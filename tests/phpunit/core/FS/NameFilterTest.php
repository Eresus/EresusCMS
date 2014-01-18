<?php
/**
 * ${product.title} ${product.version}
 *
 * Модульные тесты
 *
 * @copyright 2011, Eresus Project, http://eresus.ru/
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

require_once __DIR__ . '/../../bootstrap.php';

/**
 * @package Eresus
 * @subpackage Tests
 */
class Eresus_FS_NameFilterTest extends PHPUnit_Framework_TestCase
{
    /**
     * @cover Eresus_FS_NameFilter::setAllowedChars
     * @expectedException InvalidArgumentException
     */
    public function test_setAllowedChars_barRegexp()
    {
        $filter = new Eresus_FS_NameFilter();
        $filter->setAllowedChars('/');
    }

    /**
     * @cover Eresus_FS_NameFilter::setAllowedChars
     * @cover Eresus_FS_NameFilter::filter
     */
    public function test_filter()
    {
        $filter = new Eresus_FS_NameFilter();

        $this->assertEquals('foo', $filter->filter('%@f*oo$'));
        $filter->setAllowedChars('a-z%');
        $this->assertEquals('%foo', $filter->filter('%@f*oo$'));
    }
}
