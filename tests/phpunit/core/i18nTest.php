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
 * @package Eresus_CMS
 * @subpackage Tests
 * @author Михаил Красильников <mk@eresus.ru>
 *
 * $Id: CMS_Test.php 2187 2012-05-24 17:07:44Z mk $
 */

require_once __DIR__ . '/../bootstrap.php';

/**
 * @package Eresus_CMS
 * @subpackage Tests
 */
class Eresus_i18n_Test extends PHPUnit_Framework_TestCase
{
    /**
     * @covers i18n::getText
     */
    public function test_getText()
    {
        $i18n = new I18n('');

        $this->assertEquals('foo', $i18n->getText('foo'));

        define('Eresus_i18n_Test_Foo', 'bar');

        $this->assertEquals('bar', $i18n->getText('Eresus_i18n_Test_Foo'));
    }
    /* */
}

