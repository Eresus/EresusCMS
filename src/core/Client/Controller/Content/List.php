<?php
/**
 * Контроллер КИ типа раздела «Список подразделов»
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
 */

/**
 * Контроллер КИ типа раздела «Список подразделов»
 *
 * @package Eresus
 */
class Eresus_Client_Controller_Content_List extends Eresus_Client_Controller_Content_Abstract
{
    /**
     * Возвращает разметку области контента
     *
     * @return string|Eresus_HTTP_Response
     * @since 3.01
     */
    public function getHtml()
    {
        $legacyKernel = Eresus_CMS::getLegacyKernel();
        /* Если в URL указано что-либо кроме адреса раздела, отправляет ответ 404 */
        if ($legacyKernel->request['file']
            || $legacyKernel->request['query']
            || $this->getPage()->subpage
            || $this->getPage()->topic)
        {
            $this->getPage()->httpError(404);
        }

        $subItems = $legacyKernel->db->select('pages', "(`owner`='" . $this->getPage()->id .
            "') AND (`active`='1') AND (`access` >= '" .
            ($legacyKernel->user['auth'] ?
                $legacyKernel->user['access'] : GUEST)."')", "`position`");
        if (empty($this->getPage()->content))
        {
            $this->getPage()->content = '$(items)';
        }
        $templates = Templates::getInstance();
        $template = $templates->get('SectionListItem', 'std');
        if (false === $template)
        {
            $template = '<h1><a href="$(link)" title="$(hint)">$(caption)</a></h1>$(description)';
        }
        $items = '';
        foreach ($subItems as $item)
        {
            $items .= str_replace(
                array(
                    '$(id)',
                    '$(name)',
                    '$(title)',
                    '$(caption)',
                    '$(description)',
                    '$(hint)',
                    '$(link)',
                ),
                array(
                    $item['id'],
                    $item['name'],
                    $item['title'],
                    $item['caption'],
                    $item['description'],
                    $item['hint'],
                    $legacyKernel->request['url'] .
                    ($this->getPage()->name == 'main' &&
                    !$this->getPage()->owner ? 'main/' : '').$item['name'].'/',
                ),
                $template
            );
        }
        $result = str_replace('$(items)', $items, $this->getPage()->content);
        return $result;
    }
}

