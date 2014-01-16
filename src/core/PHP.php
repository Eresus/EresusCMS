<?php
/**
 * ${product.title}
 *
 * Работа с интерпретатором PHP
 *
 * @version ${product.version}
 * @copyright ${product.copyright}
 * @license ${license.uri} ${license.name}
 * @author Михаил Красильников <mihalych@vsepofigu.ru>
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
 * Работа с интерпретатором PHP
 *
 * @package Eresus
 * @since 3.00
 */
class Eresus_PHP
{
    /**
     * Переводит значение параметра настройки в целое число с учётом сокращений
     *
     * См. {@link http://www.php.net/faq.using.php#faq.using.shorthandbytes документацию}.
     *
     * @param string $size
     *
     * @return int
     */
    public static function iniSizeToInt($size)
    {
        assert('is_string($size)');

        preg_match('/([\d\.]+)\s*([KMG])?/', $size, $matches);
        $result = $matches[1];
        if (isset($matches[2]))
        {
            switch ($matches[2])
            {
                case 'K':
                    $result *= 1024;
                    break;
                case 'M':
                    $result *= 1024 * 1024;
                    break;
                case 'G':
                    $result *= 1024 * 1024 * 1024;
                    break;
            }
        }
        return $result;
    }

    /**
     * Возвращает максимально допустимый размер загружаемого файла в байтах
     *
     * Во внимание принимаются настройки upload_max_filesize и post_max_size
     *
     * @return int|bool  размер в байтах или false, если размер не ограничен
     */
    public static function getMaxUploadSize()
    {
        $limit = false;
        if ($value = self::iniSizeToInt(ini_get('upload_max_filesize')))
        {
            $limit = $value;
        }

        $value = self::iniSizeToInt(ini_get('post_max_size'));
        $reserveForHeadersAndBody = 1024;
        if ($value && $value - $reserveForHeadersAndBody < $limit)
        {
            $limit = $value - $reserveForHeadersAndBody;
        }

        /*
        $value = self::iniValueToInt(ini_get('memory_limit'));
        $reserveForAppItself = memory_get_peak_usage(true);
        if ($value && $value < $limit)
        {
            $limit = $value - $reserveForAppItself;
        }
        */
        return $limit;
    }
}

