<?php
/**
 * Служба шаблонов
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
 * Служба шаблонов
 *
 * @package Eresus
 * @since 3.01
 */
class Eresus_Template_Service
{
    /**
     * Одиночка
     * @var Eresus_Template_Service
     * @since 3.01
     */
    private static $instance = null;

    /**
     * Путь к корневой директории шаблонов
     * @var string
     * @since 3.01
     */
    private $rootDir;

    /**
     * Конструктор
     * @since 3.01
     */
    public function __construct()
    {
        $this->rootDir = Eresus_Kernel::app()->getFsRoot() . '/templates';
    }

    /**
     * Возвращает экземпляр класса
     *
     * @return Eresus_Template_Service
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
     * Устанавливает шаблоны в общую папку шаблонов
     *
     * @param string $filename  абсолютный путь к устанавливаемому шаблону
     * @param string $target    путь относительно общей директории шаблонов
     *
     * @throws RuntimeException
     *
     * @since 3.01
     */
    public function install($filename, $target)
    {
        if (!file_exists($filename))
        {
            throw new RuntimeException('Template file not found: ' . $filename);
        }

        $target = $this->rootDir . '/' . $target;
        if (!file_exists($target))
        {
            try
            {
                $umask = umask(0000);
                mkdir($target, 0777, true);
                umask($umask);
            }
            catch (Exception $e)
            {
                throw new RuntimeException(
                    'Can not create target directory: ' . $target, null, $e);
            }
        }

        $target .= '/' . basename($filename);

        if (file_exists($target))
        {
            throw new RuntimeException(sprintf('Template "%s" is already installed', $target));
        }

        if (!copy($filename, $target))
        {
            throw new RuntimeException(sprintf('Failed to copy "%s" to "%s"', $filename, $target));
        }
    }

    /**
     * Удаляет шаблон или папку шаблонов
     *
     * @param string $path  имя файла или папки относительно общей директории шаблонов
     *
     * @throws RuntimeException
     *
     * @since 3.01
     */
    public function remove($path)
    {
        $path = $this->rootDir . '/' . $path;
        if (!file_exists($path))
        {
            throw new RuntimeException(sprintf('Template file "%s" not found', $path));
        }

        if (is_file($path))
        {
            unlink($path);
        }
        else
        {
            $branch = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path),
                RecursiveIteratorIterator::SELF_FIRST);

            $files = array();
            $dirs = array();
            foreach ($branch as $file)
            {
                /** @var SplFileInfo $file */
                if (preg_match('/^\.{1,2}$/', $file->getFilename()))
                {
                    continue;
                }
                if ($file->isDir())
                {
                    $dirs []= $file->getPathname();
                }
                else
                {
                    $files []= $file->getPathname();
                }
            }

            /* Вначале удаляем все файлы */
            foreach ($files as $file)
            {
                unlink($file);
            }
            /* Теперь удаляем директории, начиная с самых глубоких */
            for ($i = count($dirs) - 1; $i >= 0; $i--)
            {
                rmdir($dirs[$i]);
            }
            rmdir($path);
        }
    }

    /**
     * Возвращает содержимое шаблона
     *
     * @param string $name    имя файла шаблона
     * @param string $prefix  опциональный префикс (путь относительно корня шаблонов)
     *
     * @throws RuntimeException
     *
     * @return string
     *
     * @since 3.01
     */
    public function getContents($name, $prefix = '')
    {
        $path = $this->rootDir . '/' . $this->getFilename($name, $prefix);

        if (!is_file($path))
        {
            throw new RuntimeException('Template not exists: ' . $path);
        }

        $contents = file_get_contents($path);

        return $contents;
    }

    /**
     * Записывает содержимое шаблона
     *
     * @param string $contents  содержимое шаблона
     * @param string $name      имя файла шаблона
     * @param string $prefix    опциональный префикс (путь относительно корня шаблонов)
     *
     * @throws RuntimeException
     *
     * @return void
     *
     * @since 3.01
     */
    public function setContents($contents, $name, $prefix = '')
    {
        $path = $this->rootDir . '/' . $this->getFilename($name, $prefix);

        if (!is_file($path))
        {
            throw new RuntimeException('Template not exists: ' . $path);
        }

        @file_put_contents($path, $contents);
    }

    /**
     * Возвращает путь к файлу шаблона
     *
     * @param string $name    имя файла шаблона
     * @param string $prefix  опциональный префикс (путь относительно корня шаблонов)
     *
     * @return string
     *
     * @since 1.00
     */
    public function getFilename($name, $prefix = '')
    {
        $path = $name;
        if ($prefix != '')
        {
            $path = $prefix . '/' . $path;
        }

        return $path;
    }

    /**
     * Возвращает объект шаблона
     *
     * @param string $name    имя файла шаблона
     * @param string $prefix  опциональный префикс (путь относительно корня шаблонов)
     *
     * @throws RuntimeException
     *
     * @return Eresus_Template
     *
     * @since 3.01
     */
    public function getTemplate($name, $prefix = '')
    {
        $path = $this->getFilename($name, $prefix);

        if (!is_file($this->rootDir . '/' . $path))
        {
            throw new RuntimeException('Template not exists: ' . $path);
        }

        $tmpl = new Eresus_Template('templates/' . $path);

        return $tmpl;
    }
}

