<?php
/**
 * ${product.title}
 *
 * Учётная запись пользователя
 *
 * @version ${product.version}
 * @copyright ${product.copyright}
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

namespace Eresus\CmsBundle\Entity;

use LogicException;
use InvalidArgumentException;
use Eresus\ORMBundle\AbstractEntity;
use Doctrine\ORM\Mapping as ORM;

/**
 * Учётная запись пользователя
 *
 * @property       int       $id
 * @property       string    $login
 * @property       string    $hash
 * @property       bool      $active
 * @property       \DateTime $lastVisit
 * @property       int       $lastLoginTime
 * @property       int       $loginErrors
 * @property       int       $access
 * @property       string    $name
 * @property       string    $mail
 * @property       array     $profile
 * @property-write string    $password  свойство для установки нового пароля
 *
 * @package Eresus
 * @since 4.00
 *
 * @ORM\Entity
 * @ORM\Table(name="users")
 * @SuppressWarnings(PHPMD.UnusedPrivateField)
 */
class Account extends AbstractEntity
{
    /**
     * Идентификатор
     *
     * @var int
     *
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     */
    protected $id;

    /**
     * Имя входа
     *
     * @var string
     *
     * @ORM\Column(length=16)
     */
    protected $login;

    /**
     * Хэш пароля
     *
     * @var string
     *
     * @ORM\Column(length=32)
     */
    protected $hash;

    /**
     * Активность
     *
     * @var bool
     *
     * @ORM\Column(type="boolean")
     */
    protected $active;

    /**
     * Дата последнего визита
     *
     * @var \DateTime
     *
     * @ORM\Column(type="datetime")
     */
    protected $lastVisit;

    /**
     * Дата последней авторизации
     *
     * @var int
     *
     * @ORM\Column(type="integer")
     */
    protected $lastLoginTime;

    /**
     * Количество неудачных попыток входа
     *
     * @var int
     *
     * @ORM\Column(type="integer")
     */
    protected $loginErrors;

    /**
     * Уровень доступа
     *
     * @var int
     *
     * @ORM\Column(type="integer")
     */
    protected $access;

    /**
     * Имя
     *
     * @var string
     *
     * @ORM\Column(length=64)
     */
    protected $name;

    /**
     * E-mail
     *
     * @var string
     *
     * @ORM\Column(length=64)
     */
    protected $mail;

    /**
     * Профиль
     *
     * @var array
     *
     * @ORM\Column(type="array")
     */
    protected $profile;

    /**
     * Возвращает хэш пароля
     *
     * @param string $password  пароль
     *
     * @return string
     *
     * @since 4.00
     */
    public static function passwordHash($password)
    {
        return md5(md5($password));
    }

    /**
     * Устанавливает новый пароль
     *
     * @param string $password
     */
    public function setPassword($password)
    {
        $this->hash = self::passwordHash($password);
    }

    /**
     * Возвращает свойства учётной записи в виде массива
     *
     * @return array
     * @since 4.00
     */
    public function toArray()
    {
        return array(
            'id' => $this->id,
            'login' => $this->login,
            'name' => $this->name,
            'hash' => $this->hash,
            'access' => $this->access,
            'profile' => $this->profile,
        );
    }
}
