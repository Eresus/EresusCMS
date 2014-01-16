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

/**
 * Фильтрация по регулярному выражению
 *
 * @since 3.01
 */
define('FILTER_REGEXP', 2048);

/**
 * Параметры GET или POST
 *
 * @package Eresus
 * @subpackage HTTP
 *
 * @since 3.01
 */
class Eresus_HTTP_Parameters
{
    /**
     * Параметры
     * @var array
     * @since 3.01
     */
    private $data = array();

    /**
     * @param array $data
     * @since 3.01
     */
    public function __construct(array $data = array())
    {
        $this->data = $data;
    }

    /**
     * Возвращает все параметры в виде ассоциативного массива
     *
     * @return array
     * @since 3.01
     */
    public function all()
    {
        return $this->data;
    }

    /**
     * Заменяет текущий набор параметров новым
     *
     * @param array $parameters
     *
     * @since 3.01
     */
    public function replace(array $parameters)
    {
        $this->data = $parameters;
    }

    /**
     * Добавляет новый набор параметров
     *
     * @param array $parameters
     *
     * @since 3.01
     */
    public function add(array $parameters)
    {
        $this->data += $parameters;
    }

    /**
     * Возвращает значение параметра
     *
     * @param string $name     имя параметра
     * @param mixed  $default  значение по умолчанию, если параметр отсутствует
     *
     * @return mixed
     *
     * @since 3.01
     */
    public function get($name, $default = null)
    {
        return $this->has($name) ? (@$this->data[$name] ?: $this->data["wyswyg_$name"]) : $default;
    }

    /**
     * Возвращает профильтрованное значение параметра $name
     *
     * @param string $name     имя параметра
     * @param mixed  $default  значение по умолчанию, если параметр отсутствует
     * @param int    $filter   фильтр (константа FILTER_*)
     * @param mixed  $options  опции фильтра
     *
     * @return mixed
     * @since 3.01
     */
    public function filter($name, $default = null, $filter = FILTER_DEFAULT, $options = null)
    {
        $value = $this->get($name, $default);
        if (FILTER_REGEXP == $filter)
        {
            return preg_replace($options, '', $value);
        }
        if (FILTER_CALLBACK == $filter && is_callable($options))
        {
            $options = array('options' => $options);
        }
        return filter_var($value, $filter, $options);
    }

    /**
     * @param string $name
     * @param int     $default
     *
     * @return int|null
     * @since 3.01
     */
    public function getInt($name, $default = null)
    {
        $value = $this->get($name, $default);
        return null === $value ? null : intval($value);
    }

    /**
     * Задаёт значение параметра
     *
     * @param string $name   имя параметра
     * @param mixed  $value  значение
     *
     * @since 3.01
     */
    public function set($name, $value)
    {
        $this->data[$name] = $value;
    }

    /**
     * Проверяет наличие параметра
     *
     * @param string $name     имя параметра
     *
     * @return bool
     *
     * @since 3.01
     */
    public function has($name)
    {
        return array_key_exists($name, $this->data)
            || array_key_exists("wyswyg_$name", $this->data);
    }
}

