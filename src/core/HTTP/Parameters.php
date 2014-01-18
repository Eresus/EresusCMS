<?php
/**
 * Параметры GET или POST
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

use Symfony\Component\HttpFoundation\ParameterBag;

if (!defined('FILTER_REGEXP'))
{
    /**
     * Фильтрация по регулярному выражению
     *
     * @since 3.01
     */
    define('FILTER_REGEXP', 2048);
}

/**
 * Параметры GET или POST
 *
 * @package Eresus
 * @subpackage HTTP
 *
 * @since 3.01
 */
class Eresus_HTTP_Parameters extends ParameterBag
{
    /**
     * Возвращает значение параметра
     *
     * @param string $path     ключ
     * @param mixed  $default  значение по умолчанию, если параметр отсутствует
     *
     * @return mixed
     *
     * @since 3.01
     */
    public function get($path, $default = null)
    {
        if ($this->has("wyswyg_$path"))
        {
            $path = "wyswyg_$path";
        }
        return parent::get($path, $default);
    }

    /**
     * Возвращает профильтрованное значение параметра $name
     *
     * @param string   $key      ключ
     * @param mixed    $default  значение по умолчанию, если параметр отсутствует
     * @param bool     $deep
     * @param int      $filter   фильтр (константа FILTER_*)
     * @param mixed    $options  опции фильтра
     *
     * @return mixed
     * @since 3.01
     */
    public function filter($key, $default = null, $deep = false, $filter = FILTER_DEFAULT,
        $options = array())
    {
        /* Совместимость с 3.01 */
        if (is_int($deep) && $deep > 1)
        {
            trigger_error('Deprecated argument list used in call ' . __METHOD__, E_USER_DEPRECATED);
            $options = $filter;
            $filter = $deep;
        }

        if (FILTER_REGEXP == $filter)
        {
            return preg_replace($options, '', $this->get($key));
        }
        return parent::filter($key, $default, $deep, $filter, $options);
    }

    /**
     * Проверяет наличие параметра
     *
     * @param string $path  ключ
     *
     * @return bool
     *
     * @since 3.01
     */
    public function has($path)
    {
        return parent::has($path) || parent::has("wyswyg_$path");
    }
}

