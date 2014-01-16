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

require_once __DIR__ . '/../../bootstrap.php';

/**
 * @package Eresus_CMS
 * @subpackage Tests
 */
class Eresus_Form_Test extends PHPUnit_Framework_TestCase
{
    /**
     * @covers Form::__construct
     */
    public function test__construct()
    {
        $values = array('foo' => 'bar');
        $Form = new Form($values);

        $this->assertEquals('bar', $Form->form['foo']);


        $Form->__construct($values, $values);
        $this->assertEquals('bar', $Form->form['foo']);
        $this->assertEquals('bar', $Form->values['foo']);

    }
    /* */

    /**
     * @covers Form::render_divider
     */
    public function test_render_divider()
    {
        $Form = new Form(array());
        $item= array('type'=>'edit','name'=>'name','label'=>admUsersName,'maxlength'=>32);

        $this->assertEquals("\t\t<tr><td colspan=\"2\"><hr class=\"formDivider\" /></td></tr>\n",
            $Form->render_divider($item));
    }
    /* */

    /**
     * @covers Form::attrs
     */
    public function test_attrs()
    {
        $Form = new Form(array());
        $item = array(
            'id'=> 1,
            'disabled'=>'go',
            'class' => array('foo'=>'bar'),
            'width' =>'50',
            'style'=> array(),
            'extra'=>'foo'
        );

        $this->assertEquals(" id=\"1\" disabled=\"disabled\" class=\"bar\" style=\"width: 50\" foo",
            $Form->attrs($item));

        $item = array(
            'id'=>'',
            'disabled'=>'',
            'class' => array(),
            'style'=> array(),
            'extra'=>''
        );
        $this->assertEquals(' ',	$Form->attrs($item));
    }
    /* */

    /**
     * @covers Form::render_text
     */
    public function test_render_text()
    {
        $Form = new Form(array());
        $item = array(
            'id'=> 1,
            'disabled'=>'go',
            'class' => array('foo'=>'bar'),
            'width' =>'50',
            'style'=> array(),
            'extra'=>'foo',
            'value'=>'bar'
        );
        $html_tag='<tr><td colspan="2" class="formText"';
        $resalt_attrs=" id=\"1\" disabled=\"disabled\" class=\"bar\" style=\"width: 50\" foo";

        $this->assertEquals("\t\t".$html_tag.$resalt_attrs.'>'.$item['value']."</td></tr>\n",
            $Form->render_text($item));
    }
    /* */

    /**
     * @covers Form::render_header
     */
    public function test_render_header()
    {
        $Form = new Form(array());
        $item = array(
            'id'=> 1,
            'disabled'=>'go',
            'class' => array('foo'=>'bar'),
            'width' =>'50',
            'style'=> array(),
            'extra'=>'foo',
            'value'=>'bar'
        );
        $html_tag='<tr><th colspan="2" class="formHeader"';
        $resalt_attrs=" id=\"1\" disabled=\"disabled\" class=\"bar\" style=\"width: 50\" foo";

        $this->assertEquals("\t\t".$html_tag.$resalt_attrs.'>'.$item['value']."</th></tr>\n",
            $Form->render_header($item));
    }
    /* */

}