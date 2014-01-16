<?php
/**
 * Описание сайта
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
 * Описание сайта
 *
 * <b>Внимание!</b> Не создавайте экземпляров этого класса самостоятельно, используйте метод
 * {@link Eresus_CMS::getSite()}.
 *
 * @property-read string $webRoot      адрес корня сайта (без слэша на конце)
 * @property-read string $webStyle     адрес папки оформления (без слэша на конце)
 * @property-read string $host         хост сайта
 * @property-read string $title        заголовок сайта
 * @property-read string $description  описание сайта
 * @property-read string $keywords     ключевые слова сайта
 *
 * @package Eresus
 * @since 3.01
 */
class Eresus_Site
{
    /**
     * Старое ядро
     * @var Eresus
     * @since 3.01
     */
    private $legacyKernel = null;

    /**
     * URL корня сайта
     * @var string
     * @since 3.01
     */
    private $webRoot = '';

    /**
     * URL папки стиля
     * @var string
     * @since 3.01
     */
    private $webStyle = '/styles';

    /**
     * Хост сайта
     * @var string
     * @since 3.01
     */
    private $host = 'localhost';

    /**
     * Заголовок сайта
     * @var string
     * @since 3.01
     */
    private $title = '';

    /**
     * Описание сайта
     * @var string
     * @since 3.01
     */
    private $description = '';

    /**
     * Ключевые слова сайта
     * @var string
     * @since 3.01
     */
    private $keywords = '';

    /**
     * Создаёт описание сайта
     *
     * @param Eresus $legacyKernel
     */
    public function __construct(Eresus $legacyKernel)
    {
        $this->legacyKernel = $legacyKernel;
        $this->webRoot = rtrim($this->legacyKernel->root, '/');
        $this->webStyle = $this->webRoot . '/style';
        $this->host = $this->legacyKernel->request['host'];
    }

    /**
     * Магический метод для обеспечения доступа к свойствам только на чтение
     *
     * @param string $property
     * @return mixed
     * @throws LogicException  если свойства $property нет
     */
    public function __get($property)
    {
        if (property_exists($this, $property))
        {
            return $this->{$property};
        }
        throw new LogicException(sprintf('Trying to access unknown property %s of %s',
            $property, __CLASS__));
    }

    /**
     * Задаёт заголовок сайта
     *
     * @param string $title  новый заголовок
     *
     * @throws Eresus_Exception_InvalidArgumentType
     * @since 3.01
     */
    public function setTitle($title)
    {
        if (!is_string($title))
        {
            throw Eresus_Exception_InvalidArgumentType::factory(__METHOD__, 1, 'string', $title);
        }
        $this->title = $title;
    }

    /**
     * Задаёт описание сайта
     *
     * @param string $description  новое описание
     *
     * @throws Eresus_Exception_InvalidArgumentType
     *
     * @since 3.01
     */
    public function setDescription($description)
    {
        if (!is_string($description))
        {
            throw Eresus_Exception_InvalidArgumentType::factory(__METHOD__, 1, 'string', $description);
        }
        $this->description = $description;
    }


    /**
     * Задаёт список ключевых слов
     *
     * @param string $keywords  новый список ключевых слов
     *
     * @throws Eresus_Exception_InvalidArgumentType
     *
     * @since 3.01
     */
    public function setKeywords($keywords)
    {
        if (!is_string($keywords))
        {
            throw Eresus_Exception_InvalidArgumentType::factory(__METHOD__, 1, 'string', $keywords);
        }
        $this->keywords = $keywords;
    }
}

