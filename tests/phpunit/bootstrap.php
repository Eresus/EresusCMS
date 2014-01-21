<?php
/**
 * Стартовый файл тестов
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
 * @subpackage Tests
 */

use Symfony\Component\DependencyInjection\ContainerBuilder;

define('TESTS_SRC_DIR', realpath(__DIR__ . '/../../src'));
define('TESTS_TEST_DIR', __DIR__ );
define('TESTS_FIXT_DIR', __DIR__ . '/fixtures');

require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/stubs.php';

require_once TESTS_SRC_DIR . '/lang/ru.php';
require_once TESTS_SRC_DIR . '/core/autoload.php';

$loader = require __DIR__ . '/../../vendor/autoload.php';
$loader->add('Eresus\\', __DIR__ . '/../../src');

/**
 * Базовый класс для тестов
 */
class Eresus_TestCase extends PHPUnit_Framework_TestCase
{
    /**
     * Установленные статические свойства
     *
     * @var array
     */
    private $staticProps = array();

    /**
     * Очищает окружение
     */
    protected function tearDown()
    {
        foreach ($this->staticProps as $className => $props)
        {
            foreach (array_keys($props) as $propertyName)
            {
                $property = new ReflectionProperty($className, $propertyName);
                $property->setAccessible(true);
                $property->setValue($className, null);
            }
        }
        $this->staticProps = array();
        parent::tearDown();
    }

    /**
     * Устанавливает статическое приватное свойство класса
     *
     * @param string $className
     * @param mixed  $value
     * @param string $propertyName
     *
     * @return void
     *
     * @since 3.01
     */
    protected function setStaticProperty($className, $value, $propertyName = 'instance')
    {
        $property = new ReflectionProperty($className, $propertyName);
        $property->setAccessible(true);
        $property->setValue($className, $value);
        if (!array_key_exists($className, $this->staticProps))
        {
            $this->staticProps[$className] = array();
        }
        $this->staticProps[$className][$propertyName] = true;
    }

    /**
     * Устанавливает приватное свойство объекта
     *
     * @param object $object
     * @param string $property
     * @param mixed  $value
     *
     * @return void
     *
     * @since x.xx
     */
    protected function setPrivateProperty($object, $property, $value)
    {
        $class = get_class($object);
        if (substr($class, 0, 5) == 'Mock_')
        {
            $class = preg_replace('/^Mock_(.+)_[^_]+$/', '$1', $class);
        }
        $property = new ReflectionProperty($class, $property);
        $property->setAccessible(true);
        $property->setValue($object, $value);
    }

    /**
     * Возвращает контейнер
     *
     * @param array $services
     *
     * @return ContainerBuilder
     *
     * @since x.xx
     */
    protected function getContainer(array $services = array())
    {
        $container = new ContainerBuilder();
        foreach ($services as $name => $service)
        {
            $container->set($name, $service);
        }
        return $container;
    }
}

