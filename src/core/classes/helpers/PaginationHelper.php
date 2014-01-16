<?php
/**
 * ${product.title} ${product.version}
 *
 * ${product.description}
 *
 * Помощник нумерации
 *
 * @copyright 2010, Eresus Project, http://eresus.ru/
 * @license ${license.uri} ${license.name}
 * @author Михаил Красильников <mk@eresus.ru>
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
 * Помощник нумерации
 *
 * @package Eresus
 *
 * @since 2.14
 */
class PaginationHelper
    implements Iterator, Countable
{
    /**
     * Общее количество страниц
     *
     * @var int
     * @since 2.14
     */
    private $total;

    /**
     * Номер текущей страницы
     *
     * @var int
     * @since 2.14
     */
    private $current;

    /**
     * Шаблон URL для ссылок
     *
     * @var string
     * @since 2.14
     */
    private $urlTemplate;

    /**
     * Путь к шаблону
     *
     * @var string
     * @since 2.14
     */
    private $templatePath = 'templates/std/pagination.html';

    /**
     * Размер переключателя в количестве выводимых страниц
     *
     * @var int
     * @since 2.14
     */
    private $size = 10;

    /**
     * Номер итерации
     *
     * Часть реализации интерфейса Iterator
     *
     * @var int
     * @since 2.14
     */
    private $iteration = 0;

    /**
     * Элементы нумерации
     *
     * @var array
     * @since 2.14
     */
    private $items = array();

    /**
     * Создаёт нового помощника
     *
     * Принимаемые параметры можно указать и позднее, при помощи соответствующих методов setXXX.
     *
     * @param int    $total        Общее количество страниц.
     * @param int    $current      Номер текущей страницы. По умолчанию 1.
     * @param string $urlTemplate  Шаблон URL. Используйте "%d" для подстановки страницы
     *
     * @return PaginationHelper
     *
     * @since 2.14
     */
    public function __construct($total = null, $current = 1, $urlTemplate = null)
    {
        $this->setTotal($total);
        $this->setCurrent($current);
        if ($urlTemplate)
        {
            $this->urlTemplate = $urlTemplate;
        }
    }
    //-----------------------------------------------------------------------------

    /**
     * Устанавливает общее количество страниц
     *
     * @param int $value
     * @return void
     *
     * @since 2.14
     */
    public function setTotal($value)
    {
        $this->total = $value;
    }
    //-----------------------------------------------------------------------------

    /**
     * Возвращает общее количество страниц
     *
     * @return int
     *
     * @since 2.14
     */
    public function getTotal()
    {
        return $this->total;
    }
    //-----------------------------------------------------------------------------

    /**
     * Устанавливает номер текущей страницы
     *
     * @param int $value
     * @return void
     *
     * @since 2.14
     */
    public function setCurrent($value)
    {
        $this->current = $value;
    }
    //-----------------------------------------------------------------------------

    /**
     * Возвращает номер текущей страницы
     *
     * @return int
     *
     * @since 2.14
     */
    public function getCurrent()
    {
        return $this->current;
    }
    //-----------------------------------------------------------------------------

    /**
     * Устанавливает шаблон URL
     *
     * @param string $value
     * @return void
     *
     * @since 2.14
     */
    public function setUrlTemplate($value)
    {
        $this->urlTemplate = $value;
    }
    //-----------------------------------------------------------------------------

    /**
     * Возвращает шаблон URL
     *
     * @return string
     *
     * @since 2.14
     */
    public function getUrlTemplate()
    {
        return $this->urlTemplate;
    }
    //-----------------------------------------------------------------------------

    /**
     * Устанавливает путь к шаблону
     *
     * @param string $value
     * @return void
     *
     * @since 2.14
     */
    public function setTemplate($value)
    {
        $this->templatePath = $value;
    }
    //-----------------------------------------------------------------------------

    /**
     * Возвращает путь к шаблону
     *
     * @return string
     *
     * @since 2.14
     */
    public function getTemplate()
    {
        return $this->templatePath;
    }
    //-----------------------------------------------------------------------------

    /**
     * Устанавливает максимальное количество отображаемых страниц
     *
     * @param int $value
     * @return void
     *
     * @since 2.14
     */
    public function setSize($value)
    {
        $this->size = $value;
    }
    //-----------------------------------------------------------------------------

    /**
     * Возвращает максимальное количество отображаемых страниц
     *
     * @return int
     *
     * @since 2.14
     */
    public function getSize()
    {
        return $this->size;
    }
    //-----------------------------------------------------------------------------

    /**
     * Возвращает текущий элемент списка страниц
     *
     * @return array
     *
     * @since 2.14
     * @see Iterator::current()
     * @internal
     */
    public function current()
    {
        return $this->items[$this->iteration - 1];
    }
    //-----------------------------------------------------------------------------

    /**
     * Возвращает номер итерации
     *
     * @return int
     *
     * @since 2.14
     * @see Iterator::key()
     * @internal
     */
    public function key()
    {
        return $this->iteration;
    }
    //-----------------------------------------------------------------------------

    /**
     * (non-PHPdoc)
     *
     * @return void
     *
     * @since 2.14
     * @see Iterator::next()
     * @internal
     */
    public function next()
    {
        $this->iteration++;
    }
    //-----------------------------------------------------------------------------

    /**
     * Подготовливает объект к первой итерации
     *
     * @return void
     *
     * @since 2.14
     * @see Iterator::rewind()
     * @internal
     */
    public function rewind()
    {
        $this->items = array();

        $this->prepareUrlTemplate();

        /*
         * Если страниц больше чем задано показывать за один раз, то будем показывать только часть
         * страниц, наиболее близких к текущей.
         */
        if ($this->total > $this->size)
        {
            // Начинаем показ с текущей, минус половину видимых
            $first = (int) floor($this->current - $this->size / 2);
            if ($first < 1)
            {
                // Страниц меньше 1-й не существует
                $first = 1;
            }

            $last = $first + $this->size - 1;
            if ($last > $this->total)
            {
                // Но если это больше чем страниц всего, вносим исправления
                $last = $this->total;
                $first = $last - $this->size + 1;
            }
        }
        else
        {
            $first = 1;
            $last = $this->total;
        }


        if ($first > 1)
        {
            $pageNumber = $this->current - $this->size;
            if ($pageNumber < 1)
            {
                $pageNumber = 1;
            }
            $this->items []= $this->itemFactory($pageNumber, '&larr;');
        }

        for ($pageNumber = $first; $pageNumber <= $last; $pageNumber++)
        {
            $item = $this->itemFactory($pageNumber);
            $item['current'] = $pageNumber == $this->current;
            $this->items []= $item;
        }

        if ($last < $this->total)
        {
            $pageNumber = $this->current + $this->size;
            if ($pageNumber > $this->total)
            {
                $pageNumber = $this->total;
            }
            $this->items []= $this->itemFactory($pageNumber, '&rarr;');
        }

        $this->iteration = 1;
    }
    //-----------------------------------------------------------------------------

    /**
     * (non-PHPdoc)
     *
     * @return bool
     *
     * @since 2.14
     * @see Iterator::valid()
     * @internal
     */
    public function valid()
    {
        return $this->iteration - 1 < count($this->items);
    }
    //-----------------------------------------------------------------------------

    /**
     * (non-PHPdoc)
     *
     * @return int
     *
     * @since 2.14
     * @see Countable::count()
     * @internal
     */
    public function count()
    {
        return count($this->items);
    }
    //-----------------------------------------------------------------------------

    /**
     * Создаёт разметку переключателя страниц
     *
     * @return string  HTML
     *
     * @since 2.14
     */
    public function render()
    {
        $tmpl = new Eresus_Template($this->getTemplate());

        $data = array('pagination' => $this);

        return $tmpl->compile($data);
    }

    /**
     * Подготавливает свойство $urlTemplate для использования
     *
     * @return void
     *
     * @since 2.14
     */
    private function prepareUrlTemplate()
    {
        if (!$this->urlTemplate)
        {
            $this->urlTemplate = Eresus_CMS::getLegacyKernel()->request['path'] . 'p%d/';
        }
    }
    //-----------------------------------------------------------------------------

    /**
     * Создаёт элемент нумерации
     *
     * @param int    $pageNumber       Номер страницы
     * @param string $title[optional]  Название страницы
     *
     * @return array
     *
     * @since 2.14
     */
    private function itemFactory($pageNumber, $title = null)
    {
        return array(
            'url' => sprintf($this->urlTemplate, $pageNumber),
            'title' => $title ? $title : $pageNumber,
            'current' => false
        );
    }
    //-----------------------------------------------------------------------------
}

