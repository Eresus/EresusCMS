<?php
/**
 * Страница "О программе"
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
 * Страница "О программе"
 *
 * @package Eresus
 */
class TAbout
{
    /**
     * Возвращает страницу "О программе"
     *
     * @return string  HTML
     */
    public function adminRender()
    {
        global $locale;

        $xml = new DOMDocument('1.0', 'UTF-8');
        $xml->load(Eresus_CMS::getLegacyKernel()->froot . 'core/about.xml');

        $data = array();

        /* @var DOMElement $product */
        $product = $xml->getElementsByTagName('product')->item(0);
        $data['product'] = array();
        $data['product']['title'] = $product->getAttribute('title');
        $data['product']['version'] = $product->getAttribute('version');

        $data['product']['copyrights'] = array();
        $copyrights = $product->getElementsByTagName('copyright');
        for ($i = 0; $i < $copyrights->length; $i++)
        {
            /* @var DOMElement $copyright */
            $copyright = $copyrights->item($i);
            $data['product']['copyrights'] []= array(
                'year' => $copyright->getAttribute('year'),
                'owner' => $copyright->getAttribute('owner'),
                'url' => $copyright->getAttribute('url'),
            );
        }

        $license = $xml->getElementsByTagName('license')->item(0);
        $data['license'] = array();
        $data['license']['text'] = $license->getElementsByTagName($locale['lang'])->item(0)->textContent;

        /** @var TAdminUI $page */
        $page = Eresus_Kernel::app()->getPage();
        $tmpl = $page->getUITheme()->getTemplate('misc/about.html');
        $html = $tmpl->compile($data);

        return $html;
    }
}

