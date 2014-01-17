<?php
/**
 * Событие «При разборе URL в нём найден раздел сайта»
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
 */

namespace Eresus\Events;

use Symfony\Component\EventDispatcher\Event;

/**
 * Событие «При разборе URL в нём найден раздел сайта»
 *
 * @api
 * @since x.xx
 */
class UrlSectionFoundEvent extends Event
{
    /**
     * Описание найденного раздела
     *
     * @var array
     *
     * @since x.xx
     */
    private $sectionInfo;

    /**
     * Адрес найденного раздела
     *
     * @var string
     *
     * @since x.xx
     */
    private $url;

    /**
     * @param array  $sectionInfo  описание найденного раздела
     * @param string $url          адрес найденного раздела
     *
     * @since x.xx
     */
    public function __construct(array $sectionInfo, $url)
    {
        $this->sectionInfo = $sectionInfo;
        $this->url = $url;
    }

    /**
     * Возвращает описание найденного раздела
     *
     * @return array
     *
     * @since x.xx
     */
    public function getSectionInfo()
    {
        return $this->sectionInfo;
    }

    /**
     * Возвращает адрес найденного раздела
     *
     * @return string
     *
     * @since x.xx
     */
    public function getUrl()
    {
        return $this->url;
    }
}

