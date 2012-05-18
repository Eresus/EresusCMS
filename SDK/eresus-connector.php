<?php
/**
 * Коннектор к [название расширения]
 *
 * [Описание]
 *
 * @copyright [год], [владелец], [адрес, если нужен]
 * @license http://www.gnu.org/licenses/gpl.txt GPL License 3
 * @author [Автор1 <E-mail автора1>]
 * @author [АвторN <E-mail автораN>]
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
 * @package [Название пакета]
 *
 * $Id$
 *
 * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
 * Все строки 'ExtensionName' необходимо изменить на имя расширения. Имя расширения
 * должно совпадать с директорией, в которой оно будет расположено.
 *
 * Для каждой расширяемой функции должен быть определён метод с именем вида:
 *   класс_функция()
 * например:
 *   forms_html()
 */

/**
 * Класс-коннектор
 *
 * Класс-коннектор должен иметь имя вида 'ИмяРасширенияConnector' и наследоваться от
 * базового класса EresusExtensionConnector.
 *
 * @package [Название пакета]
 */
class ExtensionNameConnector extends EresusExtensionConnector
{

}
