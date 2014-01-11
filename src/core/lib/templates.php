<?php
/**
 * Служба шаблонов
 *
 * @copyright 2004, Михаил Красильников <mihalych@vsepofigu.ru>
 * @copyright 2007, Eresus Project, http://eresus.ru/
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
 * Работа с шаблонами
 *
 * @package Eresus
 * @subpackage Templates
 *
 * @since 2.10
 */
class Templates
{
    private $pattern = '/^\{\*(.*?)\*\}.*$/s';

    /**
     * Экземпляр-одиночка
     * @var null|Templates
     * @since 3.01
     */
    private static $instance = null;

    /**
     * Возвращает экземпляр-одиночку
     *
     * @return Templates
     * @since 3.01
     */
    public static function getInstance()
    {
        if (null === self::$instance)
        {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * @deprecated с 3.01 используйте метод {@link getInstance()}
     */
    public function __construct()
    {
        // TODO Добавить генерацию сообщения об ошибке в будущих версиях
    }

    /**
     * Возвращает список шаблонов
     *
     * @param string $type Тип шаблонов (соответствует поддиректории в /templates)
     * @return array
     */
    public function enum($type = '')
    {
        $result = array();
        $legacyKernel = Eresus_Kernel::app()->getLegacyKernel();
        $dir = $legacyKernel->froot . 'templates/';
        if ($type)
        {
            $dir .= "$type/";
        }
        $list = glob("$dir*.html");
        if ($list)
        {
            foreach ($list as $filename)
            {
                $file = file_get_contents($filename);
                $title = trim(mb_substr($file, 0, mb_strpos($file, "\n")));
                if (preg_match('/^\{\*(.+)\*\}/', $title, $title))
                {
                    $title = trim($title[1]);
                }
                else
                {
                    $title = 'n/a';
                }
                $result[basename($filename, '.html')] = $title;
            }
        }
        return $result;
    }

    /**
     * Возвращает содержимое шаблона
     *
     * Если имя ($name) не указано, будет использовано имя «default».
     *
     * Если шаблон не найден и имя ($name) НЕ «default», будет предпринята попытка загрузить шаблон
     * с именем «default» из той же папки ($type).
     *
     * @param string $name   имя шаблона
     * @param string $type   тип шаблона (соответствует поддиректории в /templates)
     * @param bool   $array  вернуть шаблон в виде массива
     *
     * @return string|bool  содержимое шаблона или false, если шаблон не найден
     */
    public function get($name = '', $type = '', $array = false)
    {
        $filename = $this->composeFilename($name, $type);
        if (!file_exists($filename))
        {
            return false;
        }
        $result = file_get_contents($filename);
        if ($array)
        {
            $desc = preg_match($this->pattern, $result);
            $result = array(
                'name' => $name,
                'desc' => $desc ? trim(preg_replace($this->pattern, '$1', $result)) : ADM_NA,
                'code' => $desc ? trim(mb_substr($result, mb_strpos($result, "\n"))) : $result,
            );
        }
        else
        {
            if (preg_match($this->pattern, $result))
            {
                $result = trim(mb_substr($result, mb_strpos($result, "\n")));
            }
        }
        return $result;
    }

    /**
     * Возвращает шаблон
     *
     * Если имя ($name) не указано, будет использовано имя «default».
     *
     * Если шаблон не найден и имя ($name) НЕ «default», будет предпринята попытка загрузить шаблон
     * с именем «default» из той же папки ($type).
     *
     * @param string $name   имя шаблона
     * @param string $type   тип шаблона (соответствует поддиректории в /templates)
     *
     * @return Eresus_Template|null  шаблон или null, если шаблон не найден
     *
     * @since 3.01
     */
    public function load($name = '', $type = '')
    {
        $filename = $this->composeFilename($name, $type);
        if (!file_exists($filename))
        {
            return null;
        }
        $template = Eresus_Template::loadFromFile($filename);
        return $template;
    }

    /**
     * Новый шаблон
     *
     * @param string $name Имя шаблона
     * @param string $type Тип шаблона (соответствует поддиректории в /templates)
     * @param string $code Содержимое шаблона
     * @param string $desc Описание шаблона (необязательно)
     * @return bool Результат выполнения
     */
    public function add($name, $type, $code, $desc = '')
    {
        $legacyKernel = Eresus_Kernel::app()->getLegacyKernel();
        $filename = $legacyKernel->froot . 'templates/';
        if ($type)
        {
            $filename .= "$type/";
        }
        $filename .= "$name.html";
        $content = "{* $desc *}\n$code";
        $result = file_put_contents($filename, $content) > 0;
        return $result;
    }

    /**
     * Изменяет шаблон
     *
     * @param string $name Имя шаблона
     * @param string $type Тип шаблона (соответствует поддиректории в /templates)
     * @param string $code Содержимое шаблона
     * @param string $desc Описание шаблона (необязательно)
     * @return bool Результат выполнения
     */
    public function update($name, $type, $code, $desc = null)
    {
        $legacyKernel = Eresus_Kernel::app()->getLegacyKernel();
        $filename = $legacyKernel->froot . 'templates/';
        if ($type)
        {
            $filename .= "$type/";
        }
        $filename .= "$name.html";
        $item = $this->get($name, $type, true);
        $item['code'] = $code;
        if (!is_null($desc))
        {
            $item['desc'] = $desc;
        }
        $content = "{* {$item['desc']} *}\n{$item['code']}";
        $result = file_put_contents($filename, $content) > 0;
        return $result;
    }

    /**
     * Удаляет шаблон
     *
     * @param string $name Имя шаблона
     * @param string $type Тип шаблона (соответствует поддиректории в /templates)
     *
     * @return bool Результат выполнения
     */
    public function delete($name, $type = '')
    {
        $legacyKernel = Eresus_Kernel::app()->getLegacyKernel();
        $filename = $legacyKernel->froot . 'templates/';
        if ($type)
        {
            $filename .= "$type/";
        }
        $filename .= "$name.html";
        $result = filedelete($filename);
        return $result;
    }

    /**
     * Запрещаем клонирование объекта
     */
    private function __clone()
    {
    }

    /**
     * Строит имя файла по правилам, описанным в {@link get()}
     *
     * @param string $name  имя шаблона
     * @param string $type  тип шаблона
     *
     * @return string
     *
     * @since 3.01
     */
    private function composeFilename($name, $type)
    {
        if (empty($name))
        {
            $name = 'default';
        }
        $folder = Eresus_Kernel::app()->getFsRoot() . '/templates/';
        if ($type)
        {
            $folder .= $type . '/';
        }
        $filename = $folder . $name . '.html';
        if (!file_exists($filename) && 'default' != $name)
        {
            $filename = $folder . 'default.html';
        }
        return $filename;
    }
}

