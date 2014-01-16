<?php
/**
 * Поиск и замена подстроки в тексте
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
 * Поиск и замена подстроки в тексте
 *
 * Осуществляет поиск в тексте подстроки, соответствующей шаблону, и вызывает callback-функцию для
 * каждого найденного вхождения.
 *
 * @package Eresus
 * @since 3.01
 */
class Eresus_Content_Helper_Replace
{
    /**
     * Шаблон искомой строки
     *
     * @var string
     *
     * @since 3.01
     */
    protected $pattern;

    /**
     * Функция замены
     *
     * @var string
     *
     * @since 3.01
     */
    protected $callback;

    /**
     * Конструктор
     *
     * При каждом обнаружении $pattern (см. {@link replace()}) будет вызвана функция $callback,
     * которой в качестве аргумента будет переда массив, соответствующий найденной строке. При этом
     * 0-й элемент будет содержать всю подстроку, 1-й — часть, соответствующую первому подшаблону
     * и т. д.
     *
     * Функция $callback должна возвращать строку, которой следует заменить найденную, либо null или
     * false, если заменять строку не требуется.
     *
     * @param string   $pattern   подстрока, которую надо заменить
     * @param callable $callback  метод или функция, вызываемые для каждого найденного $token
     *
     * @since 3.01
     */
    public function __construct($pattern, $callback)
    {
        $this->pattern = $pattern;
        $this->callback = $callback;
    }

    /**
     * Производит замены в переданном тексте и возвращает изменённый текст
     *
     * @param string $text
     *
     * @return string
     *
     * @since 3.01
     */
    public function replace($text)
    {
        preg_match_all($this->pattern, $text, $matches, PREG_SET_ORDER | PREG_OFFSET_CAPTURE);
        $delta = 0;
        foreach ($matches as $match)
        {
            $stringsOnly = array();
            foreach ($match as $info)
            {
                $stringsOnly []= $info[0];
            }
            $replace = call_user_func($this->callback, $stringsOnly);
            if (false === $replace || null === $replace)
            {
                continue;
            }

            $text = substr_replace($text, $replace, $match[0][1] + $delta, strlen($match[0][0]));
            $delta += strlen($replace) - strlen($match[0][0]);
        }

        return $text;
    }
}

