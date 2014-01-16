<?php
/**
 * Шаблоны плагина
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
 * Шаблоны плагина
 *
 * @package Eresus
 * @since 3.01
 */
class Eresus_Plugin_Templates
{
    /**
     * Плагин
     * @var Eresus_Plugin
     * @since 3.01
     */
    private $plugin;

    /**
     * @param Eresus_Plugin $plugin
     * @since 3.01
     */
    public function __construct(Eresus_Plugin $plugin)
    {
        $this->plugin = $plugin;
    }

    /**
     * Возвращает шаблон для административного интерфейса
     *
     * @param string $filename  имя файла шаблона относительно папки административных шаблонов
     *
     * @return Eresus_Template
     *
     * @since 3.01
     */
    public function admin($filename)
    {
        return Eresus_Template::loadFromFile($this->adminPath($filename, false));
    }

    /**
     * Возвращает шаблон для клиентского интерфейса
     *
     * @param string $filename  имя файла шаблона относительно папки клиентских шаблонов
     *
     * @return Eresus_Template
     *
     * @since 3.01
     */
    public function client($filename)
    {
        $path = $this->clientPath($filename, false);
        return Eresus_Template::loadFromFile($path);
    }

    /**
     * Возвращает содержимое шаблона для клиентского интерфейса
     *
     * @param string $filename  имя файла шаблона относительно папки клиентских шаблонов
     *
     * @return string
     *
     * @since 3.01
     */
    public function clientRead($filename)
    {
        $path = $this->clientPath($filename);
        return file_get_contents($path);
    }

    /**
     * Записывает новый исходный код в шаблон для клиентского интерфейса
     *
     * @param string $filename  имя файла шаблона относительно папки клиентских шаблонов
     * @param string $source    новый исходный код шаблона
     *
     * @return void
     *
     * @since 3.01
     */
    public function clientWrite($filename, $source)
    {
        $path = $this->clientPath($filename);
        file_put_contents($path, $source);
    }

    /**
     * Возвращает путь к шаблону КИ
     *
     * @param string $filename  имя файла относительно папки шаблонов
     * @param bool   $absolute  если false, то вернёт путь относительно корня сайта
     *
     * @return string  путь к файлу шаблона
     */
    public function clientPath($filename, $absolute = true)
    {
        $path = "templates/{$this->plugin->getName()}/$filename";
        if ($absolute)
        {
            $path = $this->getFullPath($path);
        }
        return $path;
    }

    /**
     * Возвращает путь к шаблону АИ
     *
     * @param string $filename  имя файла относительно папки шаблонов
     * @param bool   $absolute  если false, то вернёт путь относительно корня сайта
     *
     * @return string  путь к файлу шаблона
     */
    public function adminPath($filename, $absolute = true)
    {
        $path = "ext/{$this->plugin->getName()}/admin/templates/$filename";
        if ($absolute)
        {
            $path = $this->getFullPath($path);
        }
        return $path;
    }

    /**
     * Возвращает полный путь к шаблону
     *
     * @param string $filename
     * @return string
     *
     * @since 3.01
     */
    private function getFullPath($filename)
    {
        return Eresus_Kernel::app()->getFsRoot() . '/' . $filename;
    }
}

