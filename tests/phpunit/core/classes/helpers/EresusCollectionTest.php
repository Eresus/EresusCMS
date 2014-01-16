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
 */

require_once __DIR__ . '/../../../bootstrap.php';

/**
 * @package EresusCMS
 * @subpackage Tests
 */
class EresusCollectionTest extends PHPUnit_Framework_TestCase
{
    /**
     * @covers EresusCollection::__construct
     */
    public function test_construct()
    {
        $test = new EresusCollection(array(1, 2, 3));
    }
    //-----------------------------------------------------------------------------

    /**
     * @covers EresusCollection::__construct
     * @expectedException Eresus_Exception_InvalidArgumentType
     */
    public function test_construct_not_array()
    {
        $test = new EresusCollection(1);
    }
    //-----------------------------------------------------------------------------

    /**
     * @covers EresusCollection::offsetExists
     * @covers EresusCollection::checkOffsetType
     */
    public function test_offsetExists()
    {
        $test = new EresusCollection(array(1));
        $this->assertTrue(isset($test[0]), 'Case 1');
        $this->assertFalse(isset($test[1]), 'Case 2');

        $test = new EresusCollection(array('a' => 'b'));
        $this->assertTrue(isset($test['a']), 'Case 3');
        $this->assertFalse(isset($test['b']), 'Case 4');
    }
    //-----------------------------------------------------------------------------

    /**
     * @covers EresusCollection::checkOffsetType
     * @expectedException Eresus_Exception_InvalidArgumentType
     */
    public function test_offsetExists_nonScalar()
    {
        $test = new EresusCollection();
        /** @noinspection PhpIllegalArrayKeyTypeInspection */
        return isset($test[new stdClass()]);
    }
    //-----------------------------------------------------------------------------

    /**
     * @covers EresusCollection::offsetGet
     */
    public function test_offsetGet()
    {
        $test = new EresusCollection(array(12, 34, 56));
        $this->assertEquals(34, $test[1]);
    }
    //-----------------------------------------------------------------------------

    /**
     * @covers EresusCollection::offsetSet
     */
    public function test_offsetSet()
    {
        $test = new EresusCollection();
        $test['a'] = 'b';
        $this->assertEquals('b', $test['a']);
    }
    //-----------------------------------------------------------------------------

    /**
     * @covers EresusCollection::offsetSet
     */
    public function test_append()
    {
        $test = new EresusCollection(array('a'));
        $test []= 'b';

        $this->assertEquals('b', $test[1]);
    }
    //-----------------------------------------------------------------------------

    /**
     * @covers EresusCollection::offsetGet
     */
    public function test_get_unexistent()
    {
        $test = new EresusCollection();
        $this->assertNull($test['unexistent']);
    }
    //-----------------------------------------------------------------------------

    /**
     * @covers EresusCollection::offsetGet
     * @covers EresusCollection::setDefaultValue
     */
    public function test_get_defaultValue()
    {
        $test = new EresusCollection();
        $test->setDefaultValue(true);
        $this->assertTrue($test['unexistent']);
    }
    //-----------------------------------------------------------------------------

    /**
     * @covers EresusCollection::offsetUnset
     */
    public function test_offsetUnset()
    {
        $test = new EresusCollection(array('a' => 'b'));
        $this->assertEquals('b', $test['a']);
        unset($test['a']);
        $this->assertNull($test['a']);
    }
    //-----------------------------------------------------------------------------

    /**
     * @covers EresusCollection::offsetGet
     * @covers EresusCollection::offsetSet
     * @covers EresusCollection::setDefaultValue
     */
    public function test_addToUnexistentArray()
    {
        $test = new EresusCollection();
        $test->setDefaultValue(array());
        $test['a']['b'] = 'c';
        $this->assertEquals('c', $test['a']['b']);
    }
    //-----------------------------------------------------------------------------

    /**
     * @covers EresusCollection::count
     */
    public function test_count()
    {
        $test = new EresusCollection(array('a', 'b', 'c'));
        $this->assertEquals(3, count($test));
    }
    //-----------------------------------------------------------------------------

    /**
     * @covers EresusCollection::serialize
     * @covers EresusCollection::unserialize
     */
    public function test_serializable()
    {
        $test1 = new EresusCollection(array('a', 'b', 'c'));
        $s = serialize($test1);
        $test2 = unserialize($s);
        $this->assertEquals('b', $test2[1]);
    }
    //-----------------------------------------------------------------------------

    /* */
}

