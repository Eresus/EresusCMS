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
 *
 * @package Eresus
 */

/**
 * Событие «При разборе URL в нём найден раздел сайта»
 *
 * @since 3.01
 * @package Eresus
 */
class Eresus_Event_UrlSectionFound extends Eresus_Event
{
    /**
     * Описание найденного раздела
     *
     * @var array
     *
     * @since 3.01
     */
    private $sectionInfo;

    /**
     * Адрес найденного раздела
     *
     * @var string
     *
     * @since 3.01
     */
    private $url;

    /**
     * @param array  $sectionInfo  описание найденного раздела
     * @param string $url          адрес найденного раздела
     *
     * @since 3.01
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
     * @since 3.01
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
     * @since 3.01
     */
    public function getUrl()
    {
        return $this->url;
    }
}

