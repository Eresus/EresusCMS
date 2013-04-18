<?php
/**
 * Установка CMS
 *
 * @version ${product.version}
 * @copyright 2013, Михаил Красильников <m.krasilnikov@yandex.ru>
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
 */

namespace Eresus\CmsBundle\Command;

use Eresus\CmsBundle\Kernel;
use Exception;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Установка CMS
 */
class InstallCommand extends ContainerAwareCommand
{
    /**
     * @var OutputInterface
     */
    private $output;

    /**
     * Регистрирует команду
     *
     * @see Symfony\Component\Console\Command.Command::configure()
     */
    protected function configure()
    {
        $this
            ->setName('eresus:install')
            ->setDescription('Install CMS');
    }

    /**
     * Выполняет команду
     *
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return void
     *
     * @see Symfony\Component\Console\Command.Command::execute()
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        try
        {
            $this->output = $output;
            $this->subCommand('doctrine:schema:create');
            $this->subCommand('doctrine:query:sql', array(
                'sql' =>
                    "INSERT INTO accounts (username, salt, password, is_active, email) " .
                    "VALUES ('root', '', SHA1('pass'), '1', 'support@example.org');")
            );
            $this->subCommand('doctrine:query:sql', array(
                'sql' =>
                    "INSERT INTO sections (id, parent_id, name, title, caption, description, " .
                    "keywords, position, enabled, visible, template, type, options, created) " .
                    "VALUES ('1', NULL, '', 'Главная страница', 'Главная', '', '', '0', '1', " .
                    "'1', 'default', 'Eresus.Html.Default', 'a:0:{}', '0000-00-00 00:00:00');")
            );
        }
        catch (Exception $e)
        {
            $output->writeln($e->getMessage());
        }
    }

    /**
     * Выполняет команду
     *
     * @param string $command
     * @param array  $args
     *
     * @return void
     */
    private function subCommand($command, array $args = array())
    {
        /** @var Kernel $kernel */
        $kernel = $this->getContainer()->get('kernel');
        $app = new Application($kernel);
        $app->setAutoExit(false);

        $input = new StringInput($command);
        foreach ($args as $name => $value)
        {
            $input->setArgument($name, $value);
        }
        $app->run($input, $this->output);
    }
}

