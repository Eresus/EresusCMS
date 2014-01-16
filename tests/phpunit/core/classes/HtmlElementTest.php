<?php
/**
 * ${product.title}
 *
 * @version ${product.version}
 *
 * PhpUnit Tests
 *
 * @copyright 2010, Eresus Project, http://eresus.ru/
 * @license ${license.uri} ${license.name}
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @package EresusCMS
 * @subpackage Tests
 * @author Михаил Красильников <mk@eresus.ru>
 *
 * $Id$
 */

require_once __DIR__ . '/../../bootstrap.php';

/**
 * @package EresusCMS
 * @subpackage Tests
 */
class HtmlElementTest extends PHPUnit_Framework_TestCase
{
    /**
     * Проверяем простую конструкцию
     *
     * @covers HtmlElement::__construct
     * @covers HtmlElement::getHTML
     */
    public function test_simple()
    {
        $test = new HtmlElement('a');
        $this->assertEquals('<a>', $test->getHTML());
    }
    //-----------------------------------------------------------------------------

    /**
     * Проверяем установку атрибутов
     *
     * @covers HtmlElement::setAttribute
     * @covers HtmlElement::getHTML
     */
    public function test_setAttribute()
    {
        $test = new HtmlElement('a');

        $test->setAttribute('href', '#');
        $this->assertEquals('<a href="#">', $test->getHTML());

        $test->setAttribute('boolean');
        $this->assertEquals('<a href="#" boolean>', $test->getHTML());

        $test->setAttribute('href', 'http://example.org/');
        $test->setAttribute('class', 'external');
        $this->assertEquals('<a href="http://example.org/" boolean class="external">', $test->getHTML());
    }
    //-----------------------------------------------------------------------------

    /**
     * Проверяем чтение атрибутов
     *
     * @covers HtmlElement::getAttribute
     */
    public function test_getAttribute()
    {
        $test = new HtmlElement('a');

        $test->setAttribute('href', '#');
        $this->assertEquals('#', $test->getAttribute('href'));

        $this->assertNull($test->getAttribute('b'));
    }
    //-----------------------------------------------------------------------------

    /**
     * Проверяем установку содержимого
     *
     * @covers HtmlElement::setContents
     * @covers HtmlElement::getHTML
     */
    public function test_setContents()
    {
        $test = new HtmlElement('a');

        $test->setContents('some text');
        $this->assertEquals('<a>some text</a>', $test->getHTML());
    }
    //-----------------------------------------------------------------------------

    /* */
}
