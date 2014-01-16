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
class WebServerTest extends PHPUnit_Framework_TestCase
{
    /**
     * @see PHPUnit_Framework_TestCase::setUp()
     */
    protected function setUp()
    {
        parent::setUp();

        $instance = new ReflectionProperty('WebServer', 'instance');
        $instance->setAccessible(true);
        $instance->setValue('WebServer', null);
    }
    //-----------------------------------------------------------------------------

    /**
     * @see PHPUnit_Framework_TestCase::tearDown()
     */
    protected function tearDown()
    {
        parent::tearDown();

        $instance = new ReflectionProperty('WebServer', 'instance');
        $instance->setAccessible(true);
        $instance->setValue('WebServer', null);
    }
    //-----------------------------------------------------------------------------

    /**
     * @covers WebServer::__construct
     * @covers WebServer::getInstance
     * @covers WebServer::getDocumentRoot
     */
    public function test_getDocumentRoot()
    {
        $dir = __DIR__;
        $_SERVER['DOCUMENT_ROOT'] = $dir;
        $server = WebServer::getInstance();
        $docRoot = $server->getDocumentRoot();
        // Проверяем наличие прямых слэшей
        $this->assertRegExp('/^.*\/.*$/', $docRoot);
        // realpath необходим под Windows для приведения пути к системному виду
        $this->assertEquals($dir, realpath($docRoot));
    }
    //-----------------------------------------------------------------------------

    /* */
}
