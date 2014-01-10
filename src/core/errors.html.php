<?php
/**
 * ${product.title} ${product.version}
 *
 * ${product.description}
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

$messages = array(
    'missing' => 'Отсутствует файл или директория <b>%s</b>.',
    'non-writable' => 'Файл или директория <b>%s</b> должны быть доступны для записи.',
    'custom' => array(
        'cfg/main.php' => array(
            'missing' =>
                'Нет файла конфигурации <b>cfg/main.php</b>. Создайте его из шаблона <b>cfg/main.template.php</b>.'
        ),
    ),
);

/**
 * Возвращает сообщение об ошибке
 *
 * @param array  $messages
 * @param string $file
 * @param string $problem
 * @return string
 */
function getErrorMessage($messages, $file, $problem)
{
    $message = isset($messages[$problem]) ?
        $messages[$problem] :
        'Problem: ' . $problem . '. File: %s';

    if (isset($messages['custom'][$file][$problem]))
    {
        $message = $messages['custom'][$file][$problem];
    }

    $message = sprintf($message, $file);

    return $message;
}
?>
    <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
        "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

    <html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ru" lang="ru">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        <title>Установка ${product.title} ${product.version}</title>
        <style type="text/css">
            body
            {
                margin: 0;
                padding: 0;
                font-family: Verdana, Tahoma, sans-serif;
                font-size: 13px;
            }

            h1
            {
                margin: 0;
                padding: 5px 10px;
                background-color: #007acc;
                color: #fff;
                border: solid 1px;
                border-color: #6ad #005 #005 #6ad;
                font-size: 18px;
            }

            h2
            {
                color: red;
                margin: 5px;
                padding: 5px 10px;
                font-size: 17px;
            }
        </style>
    </head>
    <body>
    <h1>${product.title} ${product.version}</h1>
    <h2>Установка не завершена</h2>
    <ul>
        <?php
        if (isset($errors))
        {
            foreach ($errors as $error)
            {
                echo '<li>' . getErrorMessage($messages, $error['file'], $error['problem']) . '</li>';
            }
        }
        ?>
    </ul>
    </body>
    </html>
<?php
die;
?>

