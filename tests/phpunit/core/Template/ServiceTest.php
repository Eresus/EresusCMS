<?php
/**
 * Тесты класса Eresus_Template_Service
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

require_once __DIR__ . '/../../bootstrap.php';

/**
 * Eresus_Template_Service
 * @package Eresus
 * @subpackage Tests
 */
class Eresus_Template_ServiceTest extends Eresus_TestCase
{
    /**
     * Путь к временной папке теста
     * @var null|string
     */
    private $tempDir = null;

    /**
     * Очищаем окружение после теста
     */
    protected function tearDown()
    {
        if (null !== $this->tempDir)
        {
            // TODO Если папка не пуста, она удалена не будет.
            @rmdir($this->tempDir);
        }
    }

    /**
     * @link https://github.com/Eresus/TemplateService/issues/1
     * @covers Eresus_Template_Service::remove
     */
    public function testSkipDotsOnRemove()
    {
        /*
         * Мы не можем использовать vfsStream, т. к. он не поддерживает «.» и «..», см.
         * https://github.com/mikey179/vfsStream/issues/50
         * Мы не можем использовать системную временную папку, т. к. она может располагаться
         * на файловой системе, также не поддерживающей эти спец. директории.
         */
        $root = __DIR__ . '/tmp';
        $folders = array("$root/templates", "$root/templates/foo");
        foreach ($folders as $folder)
        {
            if (!file_exists($folder))
            {
                mkdir($folder);
            }
        }
        $files = array("$root/templates/foo/foo1.html", "$root/templates/foo/foo2.html");
        foreach ($files as $file)
        {
            if (!file_exists($file))
            {
                file_put_contents($file, '');
            }
        }

        $Eresus_CMS = $this->getMock('stdClass', array('getFsRoot'));
        $Eresus_CMS->expects($this->any())->method('getFsRoot')->will($this->returnValue("$root/"));
        $this->setStaticProperty('Eresus_Kernel', $Eresus_CMS, 'app');

        $ts = Eresus_Template_Service::getInstance();
        $ts->remove('foo');
        $this->assertFileNotExists("$root/templates/foo");
    }
}

