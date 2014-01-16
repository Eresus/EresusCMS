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
class Eresus_TSettings_Test extends PHPUnit_Framework_TestCase
{
    /**
     * @covers TSettings::mkstr
     */
    public function test_mkstr()
    {
        $mkstr = new ReflectionMethod('TSettings', 'mkstr');
        $mkstr->setAccessible(true);

        $settings = new TSettings('');
        $this->assertEquals("  define('foo', '');\n", $mkstr->invoke($settings, 'foo'));
        $this->assertEquals("  define('foo', false);\n", $mkstr->invoke($settings, 'foo', 'bool'));
        $this->assertEquals("  define('foo', 0);\n", $mkstr->invoke($settings, 'foo', 'int'));

        $_POST['foo'] = "' \\ \" \r \n";

        $options = array('nobr' => true);
        $this->assertEquals("  define('foo', '\\' \\\\ \"    ');\n",
            $mkstr->invoke($settings, "foo", 'string', $options));

        $options = array('savebr' => true);
        $this->assertEquals("  define('foo', \"' \\\\ \\\\\\\" \\\\r \\\\n\");\n",
            $mkstr->invoke($settings, "foo", 'string', $options));
    }
    /* */
}

