<?php
/**
 * Исключительная ситуация пользовательского уровня
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
 * Исключительная ситуация пользовательского уровня
 *
 * Все исключения этого класса и его потомков не приводят к прекращению выполнения программы,
 * а перехватываются на уровне клиентского интерфейса и выводятся пользователю в виде сообщений
 * об ошибках.
 *
 * @package Eresus
 * @since 3.01
 */
class Eresus_CMS_Exception extends RuntimeException
{
    /**
     * Исключение HTTP соответствующее этому исключению
     *
     * @var null|Eresus_HTTP_Exception
     * @since 3.01
     */
    private $httpException = null;

    /**
     * Возвращает исключение HTTP соответствующее этому исключению
     *
     * @return Eresus_HTTP_Exception
     */
    public function getHttpException()
    {
        if (null === $this->httpException)
        {
            $this->httpException = $this->createHttpException();
        }
        return $this->httpException;
    }

    /**
     * Создаёт исключение HTTP соответствующее этому исключению
     *
     * @return Eresus_HTTP_Exception
     *
     * @since 3.01
     */
    protected function createHttpException()
    {
        return new Eresus_HTTP_Exception($this->getMessage());
    }
}

