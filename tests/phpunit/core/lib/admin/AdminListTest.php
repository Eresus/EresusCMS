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

require_once __DIR__ . '/../../../bootstrap.php';

/**
 * @package Eresus_CMS
 * @subpackage Tests
 */
class Eresus_AdminList_Test extends PHPUnit_Framework_TestCase
{
    /**
     * @covers AdminList::setColumn
     */
    public function test_setColumn()
    {
        $AdminList = new AdminList();
        $AdminList->columns = array(0=>array(1, 2));
        $AdminList->setColumn(1, array(3, 4));
        $this->assertEquals(array(array(1, 2), array(3, 4)),$AdminList->columns);

        $AdminList->setColumn(0, array(3, 4));
        $this->assertEquals(array(array(1, 2, 3, 4), array(3, 4)),$AdminList->columns);

    }

    /**
     * @covers AdminList::addRow
     */
    public function test_addRow()
    {
        $AdminList = new AdminList();
        $cells=array(
            0=>'foo',
            1=>array('bar'),
            2=>array('text'=>'go')
        );

        $AdminList->addRow($cells);
        $this->assertEquals(array(
            0=>array(
                0=>array('text'=>'foo'),
                1=>array('text'=>'bar'),
                2=>array('text'=>'go')
            )),$AdminList->body);
    }

    /**
     * @covers AdminList::addRows
     */
    public function test_addRows()
    {
        $AdminList = new AdminList();
        $rows=array(
            0=>array(0=>'foo', 1=>array('bar'),	2=>array('text'=>'go')),
            1=>array(0=>'foo', 1=>array('bar'),	2=>array('text'=>'go'))
        );

        $AdminList->addRows($rows);
        $this->assertEquals(array(
            0=>array(
                0=>array('text'=>'foo'),
                1=>array('text'=>'bar'),
                2=>array('text'=>'go')),
            1=>array(
                0=>array('text'=>'foo'),
                1=>array('text'=>'bar'),
                2=>array('text'=>'go'))
        ),$AdminList->body);
    }

    /**
     * @covers AdminList::renderCell
     */
    public function test_renderCell()
    {
        $AdminList = new AdminList();
        $cell=array(
            'href'=>'foo',
            'align'=>'bar',
            'style'=>'go',
            'text'=>'world'
        );

        $this->assertEquals('<meta style="text-align: bar;go"><a href="foo">world</a></meta>',
            $AdminList->renderCell('meta', $cell));

        $cell=array();
        $this->assertEquals('<meta></meta>',	$AdminList->renderCell('meta', $cell));
    }

    /**
     * @covers AdminList::setHead
     */
    public function test_setHead()
    {
        $AdminList = new AdminList();

        $AdminList->setHead('foo', array('bar','text'=>'go'));
        $this->assertEquals( array(array('text'=>'foo'), array('bar','text'=>'go')),	$AdminList->head);

        $AdminList->setHead();
        $this->assertEquals( array(),	$AdminList->head);

        $AdminList->setHead(1, 2, 3);
        $this->assertEquals( array(),	$AdminList->head);
    }

    /* */
}
