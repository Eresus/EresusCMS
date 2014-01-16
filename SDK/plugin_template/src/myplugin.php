<?php
/**
 * [Краткое название плагина]
 *
 * [Описание плагина (допустимо несколько строк)]
 *
 * @version ${product.version}
 *
 * @copyright [год], [владелец], [адрес, если нужен]
 * @license http://www.gnu.org/licenses/gpl.txt	GPL License 3
 * @author [Автор1 <E-mail автора1>]
 * @author [АвторN <E-mail автораN>]
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
 * @package [Имя пакета]
 */

/**
 * Основной класс плагина
 *
 * @package [Имя пакета]
 */
class MyPlugin extends Eresus_Plugin
{
    /**
     * Версия плагина
     * @var string
     */
    public $version = '${product.version}';

    /**
     * Требуемая версия ядра
     * @var string
     */
    public $kernel = '3.xx';

    /**
     * Название плагина
     * @var string
     */
    public $title = 'Название';

    /**
     * Описание плагина
     * @var string
     */
    public $description = 'Описание';

    /**
     * Настройки плагина
     *
     * @var array
     */
    public $settings = array(
    );

    /**
     * Конструктор
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Диалог настроек плагина
     *
     * @return string  разметка формы настроек
     */
    public function settings()
    {
        $form = array(
            'name' => 'SettingsForm',
            'caption' => $this->title . ' ' . $this->version,
            'width' => '500px',
            'fields' => array (
                array('type' => 'hidden', 'name' => 'update', 'value' => $this->getName()),
                // Необходимые поля формы
            ),
            'buttons' => array('ok', 'apply', 'cancel'),
        );
        /** @var TAdminUI $page */
        $page = Eresus_Kernel::app()->getPage();
        $html = $page->renderForm($form, $this->settings);
        return $html;
    }
}

