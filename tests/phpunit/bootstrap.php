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

define('TESTS_SRC_DIR', realpath(__DIR__ . '/../../src'));
define('TESTS_TEST_DIR', __DIR__ );
define('TESTS_FIXT_DIR', __DIR__ . '/fixtures');

require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/stubs.php';

require_once TESTS_SRC_DIR . '/lang/ru.php';

/**
 * Вспомогательный инструментарий для тестов
 *
 * @package Eresus
 * @subpackage Tests
 * @since 3.00
 */
class Eresus_Tests
{
    /**
     * Устанавливает статическое приватное свойство класса
     *
     * @param string $className
     * @param mixed  $value
     * @param string $propertyName
     *
     * @return void
     *
     * @since 3.00
     */
    public static function setStatic($className, $value, $propertyName = 'instance')
    {
        $property = new ReflectionProperty($className, $propertyName);
        $property->setAccessible(true);
        $property->setValue($className, $value);
    }
}

