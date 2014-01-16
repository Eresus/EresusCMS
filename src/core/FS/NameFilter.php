<?php
/**
 * Фильтр для имён файлов
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
 * Фильтр для имён файлов
 *
 * @package Eresus
 * @subpackage FS
 * @since 3.00
 */
class Eresus_FS_NameFilter
{
    /**
     * Символы, разрешённые в именах файлов
     *
     * @var string
     */
    protected $allowedChars = 'a-zA-Z0-1\.\-_';

    /**
     * Задаёт набор допустимых символов
     *
     * Набор символов по умолчанию задан в {@link $allowedChars}.
     *
     * Примеры:
     *
     * - setAllowedChars('a-z') — только строчные латинские буквы
     * - setAllowedChars('0-1\-') — только цифры и дефис
     *
     * @param string $pcreCharSet  набор допустимых символов в формате PCRE
     *
     * @throws InvalidArgumentException  если указан неправильный символьный класс
     *
     * @since 3.00
     */
    public function setAllowedChars($pcreCharSet)
    {
        assert('is_string($pcreCharSet)');

        if (@preg_match('/[^' . $pcreCharSet . ']/', '') === false)
        {
            throw new InvalidArgumentException(
                sprintf('"%s" must be a valid PCRE character class (without square brackets)',
                    $pcreCharSet));
        }

        $this->allowedChars = $pcreCharSet;
    }

    /**
     * Возвращает профильтрованное имя файла
     *
     * Имя файла фильтруется по правилам, задаваемым другими методами этого класса.
     *
     * @param string $filename  имя файла для фильтрации
     *
     * @return string
     *
     * @since 3.00
     */
    public function filter($filename)
    {
        assert('is_string($filename)');

        $filename = preg_replace('/[^' . $this->allowedChars . ']/', '', $filename);

        return $filename;
    }
}

