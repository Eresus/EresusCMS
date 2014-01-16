<?php
/**
 * Средство работы с файловыми путями
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
 * Средство работы с файловыми путями
 *
 * @package Eresus
 * @subpackage FS
 * @since 3.01
 */
class Eresus_FS_Tool
{
    /**
     * Нормализует имя файла
     *
     * Вызывает последовательно {@link expandParentLinks()} и {@link tidy()}.
     *
     * @param string $path  путь
     * @return string
     * @since 3.01
     */
    public static function normalize($path)
    {
        $path = self::expandParentLinks($path);
        $path = self::tidy($path);
        return $path;
    }

    /**
     * Раскрывает ссылки на родительские папки ('..')
     *
     * @param string $path
     * @return string
     * @since 3.01
     */
    public static function expandParentLinks($path)
    {
        if (strpos($path, '..') === false)
        {
            return $path;
        }

        if ($path)
        {
            $parts = explode('/', $path);
            for ($i = 0; $i < count($parts); $i++)
            {
                if ($parts[$i] == '..')
                {
                    if ($i > 1)
                    {
                        array_splice($parts, $i-1, 2);
                        $i -= 2;
                    }
                    else
                    {
                        array_splice($parts, $i, 1);
                        $i -= 1;
                    }
                }
            }
            $path = implode('/', $parts);
        }
        return $path;
    }

    /**
     * Исправляет некоторые ошибки в пути
     *
     * 1. Заменяет несколько идущих подряд слэшей одним
     * 2. Заменяет "/./" на "/"
     * 3. Удаляет финальный слэш "/"
     *
     * @param string $path
     * @return string
     * @since 3.01
     */
    public static function tidy($path)
    {
        $path = preg_replace('~/{2,}~', '/', $path);
        $path = str_replace('/./', '/', $path);
        $path = preg_replace('~^./~', '', $path);
        $path = rtrim($path, '/');
        return $path;
    }
}

