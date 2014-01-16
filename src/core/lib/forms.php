<?php
/**
 * ${product.title} ${product.version}
 *
 * ${product.description}
 *
 * @copyright 2004-2007, Михаил Красильников <mihalych@vsepofigu.ru>
 * @copyright 2007-2008, Eresus Project, http://eresus.ru/
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
 */

/**
 * HTML-форма
 *
 * @package Eresus
 */
class Form
{
    var $form;
    var $values;
    var $hidden = '';
    var $onsubmit = '';
    var $validator = '';
    var $file = false;    # Признок наличия полей типа file
    var $html = false;    # Признак наличия WYSIWYG редакторов
    var $options = array();
    /**
     * Конструктор
     *
     * @param  array  $form    Описание формы
     * @param  array  $values  Значения полей по умолчанию (необязательно)
     */
    function __construct($form, $values=array())
    {
        $this->form = $form;
        $this->values = $values;
    }
    //------------------------------------------------------------------------------
    /**
     * Подготоваливает поле формы для дальнейшей обработки
     *
     * @access  private
     *
     * @param  &array  $item  Описание поле
     */
    function field_prep(&$item)
    {
        $item['type'] = strtolower($item['type']);
        # Метка
        if (!isset($item['label'])) $item['label'] = '';
        # Подсказка
        if (isset($item['hint'])) $item['label'] = '<span class="hint" title="'.$item['hint'].'">'.$item['label'].'</span>';
        # Маска значения
        if (isset($item['pattern']) && isset($item['name']))
            $this->validator .= "
				if (!form.".$item['name'].".value.match(".$item['pattern'].")) {
					alert('".(isset($item['errormsg'])?$item['errormsg']:sprintf(errFormPatternError, $item['name'], $item['pattern']))."');
					result = false;
					form.".$item['name'].".select();
				} else ";
        # Значение
        $item['value'] = isset($item['value']) ? $item['value']
            : (isset($item['name']) && isset($this->values[$item['name']]) ? $this->values[$item['name']]
                : (isset($item['default']) ? $item['default']
                    : '' )
            );
        # ID
        if (!isset($item['id'])) $item['id'] = '';
        # Элемент отключен
        if (!isset($item['disabled'])) $item['disabled'] = '';
        # Комментарий
        $item['comment'] = isset($item['comment']) ? ' '.$item['comment'] : '';
        # Стили
        $item['style'] = isset($item['style']) ? explode(';', $item['style']) : array();
        # Классы
        $item['class'] = isset($item['class']) ? explode(' ', $item['class']) : array();
        # Дополнительно
        if (!isset($item['extra'])) $item['extra'] = '';
    }
    //------------------------------------------------------------------------------
    /**
     * Отрисовывает атрибуты элемента
     *
     * @access  private
     *
     * @param  array  $item  Элемент
     *
     * @return  string  Отрисованные атрибуты
     */
    function attrs($item)
    {
        $result = '';
        if ($item['id']) $result .= ' id="'.$item['id'].'"';
        if ($item['disabled']) $result .= ' disabled="disabled"';
        if (count($item['class'])) $result .= ' class="'.implode(' ', $item['class']).'"';
        # Ширина
        if (isset($item['width'])) $item['style'][] = 'width: '.$item['width'];
        # Стили
        if (count($item['style'])) $result .= ' style="'.implode(';', $item['style']).'"';
        $result .= ' '.$item['extra'];
        return $result;
    }
    //------------------------------------------------------------------------------
    /**
     * Раделитель
     *
     * @access  protected
     *
     * @param  array  $item  Описание поля
     *
     * @return  string  Отрисованное поле
     */
    function render_divider($item)
    {
        $result = "\t\t<tr><td colspan=\"2\"><hr class=\"formDivider\" /></td></tr>\n";
        return $result;
    }
    //------------------------------------------------------------------------------
    /**
     * Текст
     *
     * @access  protected
     *
     * @param  array  $item  Описание поля
     *
     * @return  string  Отрисованное поле
     */
    function render_text($item)
    {
        $result = "\t\t".'<tr><td colspan="2" class="formText"'.$this->attrs($item).'>'.$item['value']."</td></tr>\n";
        return $result;
    }
    //------------------------------------------------------------------------------
    /**
     * Подзаголовок
     *
     * @access  protected
     *
     * @param  array  $item  Описание поля
     *
     * @return  string  Отрисованное поле
     */
    function render_header($item)
    {
        $result = "\t\t".'<tr><th colspan="2" class="formHeader"'.$this->attrs($item).'>'.$item['value']."</th></tr>\n";
        return $result;
    }
    //------------------------------------------------------------------------------
    /**
     * <input type="hidden" />
     *
     * @access  protected
     *
     * @param  array  $item  Описание поля
     *
     * @return  string  Отрисованное поле
     */
    function render_hidden($item)
    {
        if ($item['name'] === '')
        {
            Eresus_Kernel::app()->getPage()->addErrorMessage(
                sprintf(errFormFieldHasNoName, $item['type'], $this->form['name']));
        }
        $this->hidden .= '<input type="hidden" name="'.$item['name'].'" value="'.$item['value'].'" />'."\n";
        return '';
    }

    /**
     * <input type="text" />
     *
     * @access  protected
     *
     * @param  array  $item  Описание поля
     *
     * @return  string  Отрисованное поле
     */
    function render_edit($item)
    {
        if ($item['name'] === '')
        {
            Eresus_Kernel::app()->getPage()->addErrorMessage(
                sprintf(errFormFieldHasNoName, $item['type'], $this->form['name']));
        }
        $result = "\t\t".'<tr><td class="formLabel">'.$item['label'].'</td><td><input type="text" name="'.$item['name'].'" value="'.EncodeHTML($item['value']).'"'.(empty($item['maxlength'])?'':' maxlength="'.$item['maxlength'].'"').$this->attrs($item).' />'.$item['comment']."</td></tr>\n";
        return $result;
    }
    //------------------------------------------------------------------------------
    /**
     * <input type="password" />
     *
     * @access  protected
     *
     * @param  array  $item  Описание поля
     *
     * @return  string  Отрисованное поле
     */
    function render_password($item)
    {
        if ($item['name'] === '')
        {
            Eresus_Kernel::app()->getPage()->addErrorMessage(
                sprintf(errFormFieldHasNoName, $item['type'], $this->form['name']));
        }
        $result = "\t\t".'<tr><td class="formLabel">'.$item['label'].'</td><td><input type="password" name="'.$item['name'].'" value="'.EncodeHTML($item['value']).'"'.(empty($item['maxlength'])?'':' maxlength="'.$item['maxlength'].'"').$this->attrs($item).' />'.$item['comment']."</td></tr>\n";
        if (isset($item['equal'])) $this->validator .= "if (form.".$item['name'].".value != form.".$item['equal'].".value) {\nalert('".errFormBadConfirm."');\nresult = false;\nform.".$item['name'].".value = '';\nform.".$item['equal'].".value = ''\nform.".$item['equal'].".select();\n} else ";
        return $result;
    }

    /**
     * <input type="checkbox" />
     *
     * @access  protected
     *
     * @param  array  $item  Описание поля
     *
     * @return  string  Отрисованное поле
     */
    function render_checkbox($item)
    {
        if ($item['name'] === '')
        {
            Eresus_Kernel::app()->getPage()->addErrorMessage(
                sprintf(errFormFieldHasNoName, $item['type'], $this->form['name']));
        }
        $result = "\t\t".'<tr><td><input type="hidden" name="'.$item['name'].'" value="" /></td><td><input type="checkbox" name="'.$item['name'].'" value="'.($item['value'] ? $item['value'] : true).'" '.($item['value'] ? 'checked' : '').$this->attrs($item).' style="background-color: transparent; border-style: none; margin:0px;" /><span style="vertical-align: baseline"> '.$item['label']."</span></td></tr>\n";
        return $result;
    }

    /**
     * <select>
     *
     * @access  protected
     *
     * @param  array  $item  Описание поля
     *
     * @return  string  Отрисованное поле
     */
    function render_select($item)
    {
        if ($item['name'] === '')
        {
            Eresus_Kernel::app()->getPage()->addErrorMessage(
                sprintf(errFormFieldHasNoName, $item['type'], $this->form['name']));
        }
        $result = "\t\t".'<tr><td class="formLabel">'.$item['label'].'</td><td><select name="'.$item['name'].'"'.$this->attrs($item).'>'."\n";
        if (!isset($item['items']) && isset($item['values'])) $item['items'] = $item['values'];
        for($i = 0; $i< count($item['items']); $i++) {
            if (isset($item['values'])) $value = $item['values'][$i]; else $value = $i;
            $result .= '<option value="'.$value.'" '.($value == (isset($this->values[$item['name']]) ? $this->values[$item['name']] : (isset($item['value'])?$item['value']:'')) ? 'selected = "selected"' : '').">".$item['items'][$i]."</option>\n";
        }
        $result .= '</select>'.$item['comment']."</td></tr>\n";
        return $result;
    }

    /**
     * <select multiple>
     *
     * @access  protected
     *
     * @param  array  $item  Описание поля
     *
     * @return  string  Отрисованное поле
     */
    function render_listbox($item)
    {
        if ($item['name'] === '')
        {
            Eresus_Kernel::app()->getPage()->addErrorMessage(
                sprintf(errFormFieldHasNoName, $item['type'], $this->form['name']));
        }
        $result = "\t\t".'<tr><td class="formLabel">'.$item['label'].'</td><td><select multiple name="'.$item['name'].'[]"'.(isset($item['height'])?' size="'.$item['height'].'"':'').$this->attrs($item).">\n";
        if (!isset($item['items']) && isset($item['values'])) $item['items'] = $item['values'];
        for($i = 0; $i< count($item['items']); $i++) {
            if (isset($item['values'])) $value = $item['values'][$i]; else $value = $i;
            $result .= '<option value="'.$value.'" '.(count($this->values) && in_array($value, $this->values[$item['name']]) ? 'selected = "selected"' : '').">".$item['items'][$i]."</option>\n";
        }
        $result .= '</select>'.$item['comment']."</td></tr>\n";
        return $result;
    }

    /**
     * <textarea></textarea>
     *
     * @access  protected
     *
     * @param  array  $item  Описание поля
     *
     * @return  string  Отрисованное поле
     */
    function render_memo($item)
    {
        if ($item['name'] === '')
        {
            Eresus_Kernel::app()->getPage()->addErrorMessage(
                sprintf(errFormFieldHasNoName, $item['type'], $this->form['name']));
        }
        if (empty($item['width'])) $item['width'] = '100%';
        if (strpos($item['width'], '%') === false) {
            $cols = $item['width'];
            $item['width'] = '';
        } else $cols = '50';
        if (isset($item['syntax'])) {
            $extension = Eresus_CMS::getLegacyKernel()->extensions->load(
                'forms',
                'memo_syntax',
                isset($item['syntax_extension']) ? $item['syntax_extension'] : null
            );
            if ($extension) $item = $extension->forms_memo_syntax($this, $item);
        }
        $result = "\t\t".'<tr><td colspan="2">'.(empty($item['label'])?'':'<span class="formLabel">'.$item['label'].'</span><br />').'<textarea name="'.$item['name'].'" cols="'.$cols.'" rows="'.(empty($item['height'])?'3':$item['height']).'" '.$this->attrs($item).'>'.EncodeHTML($item['value'])."</textarea></td></tr>\n";
        return $result;
    }
    //------------------------------------------------------------------------------
    /**
     * <textarea html>
     *
     * @access  protected
     *
     * @param  array  $item  Описание поля
     *
     * @return  string  Отрисованное поле
     */
    function render_html($item)
    {
        if ($item['name'] === '')
        {
            Eresus_Kernel::app()->getPage()->addErrorMessage(
                sprintf(errFormFieldHasNoName, $item['type'], $this->form['name']));
        }

        $result = '';
        $extension = Eresus_CMS::getLegacyKernel()->extensions->load(
            'forms',
            'html',
            isset($item['html_extension']) ? $item['html_extension'] : null
        );
        if ($extension) $result = $extension->forms_html($this, $item);

        return $result;
    }

    /**
     * <input type="file" />
     *
     * @access  protected
     *
     * @param  array  $item  Описание поля
     *
     * @return  string  Отрисованное поле
     */
    function render_file($item)
    {
        if ($item['name'] === '')
        {
            Eresus_Kernel::app()->getPage()->addErrorMessage(
                sprintf(errFormFieldHasNoName, $item['type'], $this->form['name']));
        }
        $result = "\t\t".'<tr><td class="formLabel">'.$item['label']."</td><td><input type=\"file\" name=\"".$item['name']."\"".(isset($item['width']) ? ' size="'.$item['width'].'"':'').$this->attrs($item)." />".$item['comment']."</td></tr>\n";
        $this->file = true;
        return $result;
    }

    /**
     * <input type="image" />
     *
     * @access  private
     *
     * @param  array  $item  Описание поля
     *
     * @return  string  Отрисованное поле
     */
    function render_image($item)
    {

        if ($item['name'] === '')
        {
            Eresus_Kernel::app()->getPage()->addErrorMessage(
                sprintf(errFormFieldHasNoName, $item['type'], $this->form['name']));
        }
        $result = "\t\t".'<tr><td class="formImage">'."</td><td><input type=\"image\" name=\"".$item['name']."\" src=\"".$item['src']."\" ".$this->attrs($item)." alt='".$item['label']."' />".$item['comment']."</td></tr>\n";
        $this->file = true;
        return $result;
    }

    /**
     * Создание HTML-кода
     *
     * @access  public
     *
     * @return  string  HTML-код формы
     */
    function render()
    {
        $result = '';     # Выходной код

        if (empty($this->form['name'])) $result .= ErrorBox(errFormHasNoName);
        if (count($this->form['fields'])) foreach($this->form['fields'] as $item) {
            # Проверяем права доступа к элементу
            if ((!isset($item['access'])) || (UserRights($item['access']))) {
                $this->field_prep($item);
                $control = 'render_'.$item['type'];
                #if (method_exists($this, $control)) $result .= call_user_func(array($this, $control), $item);
                if (method_exists($this, $control)) {
                    $result .= $this->$control($item);
                }
                else
                {
                    Eresus_Kernel::app()->getPage()->addErrorMessage(
                        sprintf(errFormUnknownType, $item['type'], $this->form['name']));
                }
            }
        }
        $this->onsubmit .= $this->validator;
        if (!empty($this->onsubmit))
        {
            Eresus_Kernel::app()->getPage()->addScripts("
				function ".$this->form['name']."Submit()
				{
					var result = true;
					var form = document.forms.namedItem('".$this->form['name']."');
					".$this->onsubmit.";
					return result;
				}
			");
        }
        # FIXME: sub_id - устаревший элемент
        $referer = arg('sub_id') ?
            Eresus_Kernel::app()->getPage()->url(array('sub_id'=>'')) :
            Eresus_Kernel::app()->getPage()->url(array('id'=>''));
        $this->hidden .= "\t\t".'<input type="hidden" name="submitURL" value="'.$referer.'" />';
        $this->hidden = "\t<div class=\"hidden\">\n\t\t{$this->hidden}\n\t</div>";
        $result =
            "<form ".(empty($this->form['name'])?'':'name="'.$this->form['name'].'" id="'.$this->form['name'].'" ')."action=\"".
            Eresus_Kernel::app()->getPage()->url()."\" method=\"post\"".(empty($this->onsubmit)?'':' onsubmit="return '.$this->form['name'].'Submit();"').($this->file?' enctype="multipart/form-data"':'').">\n".
            $this->hidden.
            "\n\t<table width=\"100%\">\n".
            "\t\t<tr><td style=\"height: 0px; font-size: 0px; padding: 0px;\">".img('style/dot.gif')."</td><td style=\"width: 100%; height: 0px; font-size: 0px; padding: 0px;\">".img('style/dot.gif')."</td>\n\t\t</tr>\n".
            $result.
            "\t\t<tr><td colspan=\"2\" class=\"ui-button-box\"><br />".
            ((isset($this->form['buttons']) && isset($this->form['buttons']['ok']))?'<button name="form_ok" type="submit">'.$this->form['buttons']['ok'].'</button> ':'').
            (!isset($this->form['buttons']) || in_array('ok', $this->form['buttons'])?"<button name=\"form_ok\" type=\"submit\">".strOk."</button> ":'').

            ((isset($this->form['buttons']) && isset($this->form['buttons']['apply']))?'<button name="form_apply" type="submit" onclick="formApplyClick(\''.$this->form['name'].'\')">'.$this->form['buttons']['apply']."</button> ":'').
            (!isset($this->form['buttons']) || in_array('apply', $this->form['buttons'])?"<button name=\"form_apply\" type=\"submit\" onclick=\"formApplyClick('".$this->form['name']."')\">" . strApply . '</button> ':'').

            ((isset($this->form['buttons']) && isset($this->form['buttons']['reset']))?'<button name="form_reset" type="reset">'.$this->form['buttons']['reset'].'</button> ':'').
            (isset($this->form['buttons']) && in_array('reset', $this->form['buttons'])?"<button name=\"form_reset\" type=\"reset\">".strReset."</button> ":'').

            ((isset($this->form['buttons']) && isset($this->form['buttons']['cancel']) && (!is_array($this->form['buttons']['cancel'])))?'<input name="form_cancel" type="button" onclick="javascript:history.back();">' . $this->form['buttons']['cancel'] . '</button>' : '').
            ((!isset($this->form['buttons']) || (in_array('cancel', $this->form['buttons'])))?"<button name=\"form_cancel\" type=\"button\" onclick=\"javascript:history.back();\">".strCancel . '</button>' :'').
            ((isset($this->form['buttons']['cancel']) && (is_array($this->form['buttons']['cancel'])))?"<button name=\"form_cancel\" type=\"button\" onclick=\"window.location.href='".$this->form['buttons']['cancel']['url']."'\">" . $this->form['buttons']['cancel']['label'] . '</button>':'').

            "</td>\n\t\t</tr>\n".
            "\t</table>\n</form>\n";

        return $result;
    }
    //------------------------------------------------------------------------------
}
