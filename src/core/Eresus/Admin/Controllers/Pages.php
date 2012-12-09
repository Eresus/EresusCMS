<?php
/**
 * ${product.title}
 *
 * Управление разделами сайта
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

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Eresus\CmsBundle\Form\DataTransformer\OptionsTransformer;
use Eresus\CmsBundle\Form\DataTransformer\NullToStringTransformer;
use Eresus\CmsBundle\Entity\Section;
use Eresus\CmsBundle\AdminUI;
use Eresus\CmsBundle\Templates;

/**
 * Управление разделами сайта
 *
 * @package Eresus
 */
class Eresus_Admin_Controllers_Pages extends Eresus_Admin_Controllers_Abstract
{
    /**
     * Уровень доступа к этому модулю
     * @var int
     */
    public $access = ADMIN;

    /**
     * Возвращает разметку интерфейса или готовый ответ
     *
     * @param Request $request
     *
     * @return Response|string
     */
    public function adminRender(Request $request)
    {
        if (UserRights($this->access))
        {
            switch ($request->get('action'))
            {
                case 'add':
                    $result = $this->create($request);
                    break;
                case 'up':
                    $result = $this->moveUp($request);
                    break;
                case 'down':
                    $result = $this->moveDown($request);
                    break;
                case 'create':
                    $result = $this->create($request);
                    break;
                case 'move':
                    $result = $this->move($request);
                    break;
                case 'delete':
                    $result = $this->delete($request);
                    break;
                default:
                    if ($request->get('id') != null)
                    {
                        $result = $this->edit($request);
                    }
                    else
                    {
                        $result = $this->sectionIndex();
                    }
            }
        }
        else
        {
            $result = '';
        }
        return $result;
    }

    /**
     * Функция перемещает страницу вверх в списке
     *
     * @param Request $request
     *
     * @return Response
     */
    private function moveUp(Request $request)
    {
        /** @var \Doctrine\ORM\EntityManager $em */
        $em = $this->getDoctrine()->getManager();
        /** @var Section $section */
        $section = $em->find('CmsBundle:Section', $request->query->get('id'));
        if ($section->position > 0)
        {
            $q = $em->createQuery(
                'SELECT s FROM CmsBundle:Section s ' .
                'WHERE s.parent = :parent AND s.position < :position ' .
                'ORDER BY s.position DESC'
            );
            $q->setParameter('parent', $section->parent);
            $q->setParameter('position', $section->position);
            $q->setMaxResults(1);
            /** @var Section $swap */
            $swap = $q->getOneOrNullResult();
            if (null !== $swap)
            {
                $pos = $section->position;
                $section->position = $swap->position;
                $swap->position = $pos;
                $em->flush();
            }
        }
        return new RedirectResponse(Eresus_Kernel::app()->getPage()->url(array('id'=>'')));
    }

    /**
     * Функция перемещает страницу вниз в списке
     *
     * @param Request $request
     *
     * @return Response
     */
    private function moveDown(Request $request)
    {
        /** @var \Doctrine\ORM\EntityManager $em */
        $em = $this->getDoctrine()->getManager();
        /** @var Section $section */
        $section = $em->find('CmsBundle:Section', $request->query->get('id'));
        $q = $em->createQuery(
            'SELECT s FROM CmsBundle:Section s ' .
            'WHERE s.parent = :parent AND s.position > :position ' .
            'ORDER BY s.position ASC'
        );
        $q->setParameter('parent', $section->parent);
        $q->setParameter('position', $section->position);
        $q->setMaxResults(1);
        /** @var Section $swap */
        $swap = $q->getOneOrNullResult();
        if (null !== $swap)
        {
            $pos = $section->position;
            $section->position = $swap->position;
            $swap->position = $pos;
            $em->flush();
        }
        return new RedirectResponse(Eresus_Kernel::app()->getPage()->url(array('id'=>'')));
    }

    /**
     * Перемещает страницу из одной ветки в другую
     *
     * @param Request $request
     *
     * @return Response|string
     */
    private function move(Request $request)
    {
        /** @var \Doctrine\ORM\EntityManager $em */
        $em = $this->getDoctrine()->getManager();
        /** @var \Eresus\CmsBundle\Repository\SectionRepository $repo */
        $repo = $em->getRepository('CmsBundle:Section');
        /** @var Section $section */
        $section = $repo->find($request->get('id'));

        if ($request->getMethod() == 'POST')
        {
            /** @var Section $newParent */
            $newParent = $request->request->get('target')
                ? $em->find('CmsBundle:Section', $request->request->get('target'))
                : null /*$repo->getPseudoRoot()*/;

            /*
             * Проверяем, нет ли в разделе назначения раздела с таким же именем и вычисляем
             * новый порядковый номер
             */
            $section->position = 0;
            if ($newParent && $newParent->children)
            {
                foreach ($newParent->children as $child)
                {
                    /** @var Section $child */
                    if ($child->name == $section->name)
                    {
                        ErrorMessage('В разделе назначения уже есть раздел с таким же именем!');
                        return new RedirectResponse($_SERVER['HTTP_REFERER']);
                    }
                    if ($child->position <= $section->position)
                    {
                        $section->position = $child->position + 1;
                    }
                }
            }
            $section->parent = $newParent;
            $em->flush();
            return new RedirectResponse(Eresus_Kernel::app()->getPage()->url(array('id'=>'')));
        }
        else
        {
            return $this->renderView('CmsBundle:Sections:Move.html.twig',
                array('root' => $repo->getRoot(), 'section' => $section));

        }
    }

    /**
     * Удаляет страницу
     *
     * @param Request $request
     *
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     *
     * @return Response
     */
    private function delete(Request $request)
    {
        /** @var \Doctrine\ORM\EntityManager $em */
        $em = $this->getDoctrine()->getManager();
        $section = $em->find('CmsBundle:Section', $request->query->getInt('id'));
        if (null === $section)
        {
            throw $this->createNotFoundException();
        }

        $em->remove($section);
        return new RedirectResponse(Eresus_Kernel::app()->getPage()->url(array('id'=>'')));
    }

    /**
     * Возвращает список типов контента в виде, пригодном для построения выпадающего списка
     *
     * @return array
     */
    private function loadContentTypes()
    {
        /** @var \Eresus\CmsBundle\CmsBundle $cms */
        $cms = $this->get('cms');

        $result = array();
        foreach ($cms->getContentTypes() as $type)
        {
            $result[$type->getId()] = $type->getTitle();
        }

        return $result;
    }

    /**
     * Добавление нового раздела
     *
     * @param Request $request
     *
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     *
     * @return Response|string
     */
    private function create(Request $request)
    {
        $section = new Section();
        $section->description = '';
        $section->keywords = '';
        $section->hint = '';
        $section->content = '';
        $section->active = true;
        $section->visible = true;
        $section->access = GUEST;
        $section->created = new DateTime();
        $section->updated = new DateTime();

        $form = $this->getForm($section);

        if ($request->getMethod() == 'POST')
        {
            $form->bind($request);
            if ($form->isValid())
            {
                /** @var \Doctrine\ORM\EntityManager $em */
                $em = $this->getDoctrine()->getManager();

                $ownerId = $request->request->get('owner');
                if (0 == $ownerId)
                {
                    $parent = null;
                }
                else
                {
                    $parent = $em->find('CmsBundle:Section', $request->request->get('owner'));
                    if (null === $parent)
                    {
                        throw $this->createNotFoundException();
                    }
                }
                $section->parent = $parent;
                $em->persist($section);

                $q = $em->createQuery(
                    'SELECT MAX(s.position) FROM CmsBundle:Section s WHERE s.parent = :parent');
                $q->setParameter('parent', $parent);
                $max = $q->getSingleResult();
                $section->position = $max[1] + 1;

                $em->flush();
                return $this->redirect(Eresus_Kernel::app()->getPage()->url());
            }
        }

        return $this->renderView('CmsBundle:Sections:add.html.twig',
            array('form' => $form->createView(), 'ownerId' => $request->get('owner')));
    }

    /**
     * Изменение свойств раздела
     *
     * @param Request $request
     *
     * @return Response|string
     */
    private function edit(Request $request)
    {
        /** @var \Doctrine\ORM\EntityManager $em */
        $em = $this->getDoctrine()->getManager();
        /** @var \Eresus\CmsBundle\Entity\Section $section */
        $section = $em->find('CmsBundle:Section', $request->get('id'));
        $form = $this->getForm($section);

        if ($request->getMethod() == 'POST')
        {
            $form->bind($request);
            if ($form->isValid())
            {
                $em->flush();
                return $this->redirect(Eresus_Kernel::app()->getPage()->url(array('id' => '')));
            }
        }

        // Вычисляем адрес страницы
        $urlAbs = Eresus_Kernel::app()->getPage()->clientURL($section->id);

        return $this->renderView('CmsBundle:Sections:edit.html.twig',
            array('form' => $form->createView(), 'pageURL' => $urlAbs));
    }

    /**
     * Возвращает дерево разделов
     *
     * @return string
     */
    private function sectionIndex()
    {
        /** @var \Doctrine\ORM\EntityManager $em */
        $em = $this->getDoctrine()->getManager();
        /** @var \Eresus\CmsBundle\Repository\SectionRepository $repo */
        $repo = $em->getRepository('CmsBundle:Section');

        return $this->renderView('CmsBundle:Sections:Index.html.twig',
            array('section' => $repo->getRoot()));
    }

    /**
     * Возвращает форму добавления/изменения
     *
     * @param Section $section  раздел сайта
     *
     * @return Symfony\Component\Form\Form
     *
     * @since 4.00
     */
    private function getForm(Section $section)
    {
        $templates = new Templates();

        $null2string = new NullToStringTransformer();

        $builder = $this->createFormBuilder($section);
        if (null !== $section->parent)
        {
            $builder->add('name', 'text', array('label'  => 'Имя'));
        }
        $builder
            ->add('title', 'text', array('label'  => 'Заголовок'))
            ->add('caption', 'text', array('label'  => 'Пункт меню'))
            ->add($builder->create('hint', 'text', array('label'  => 'Подсказка',
                'required' => false))->addModelTransformer($null2string))
            ->add($builder->create('description', 'text',
                array('label'  => 'Описание', 'required' => false))
                ->addModelTransformer($null2string))
            ->add($builder->create('keywords', 'text',
                array('label'  => 'Ключевые слова', 'required' => false))
                ->addModelTransformer($null2string))
            ->add('template', 'choice', array('label'  => 'Шаблон',
                'choices' => $templates->enum()))
            ->add('type', 'choice', array('label'  => 'Тип раздела',
                'choices' => $this->loadContentTypes()))
            ->add('active', 'checkbox', array('label'  => 'Включить'))
            ->add('visible', 'checkbox', array('label'  => 'Показывать в меню'))
            ->add('access', 'choice', array('label'  => 'Уровень доступа',
                'choices' => array(
                    ROOT => ACCESSLEVEL1,
                    ADMIN => ACCESSLEVEL2,
                    EDITOR => ACCESSLEVEL3,
                    USER => ACCESSLEVEL4,
                    GUEST => ACCESSLEVEL5
                )));
        if ($section->id)
        {
            $builder->add('position', 'integer', array('label'  => 'Порядковый номер'));
        }
        $builder
            ->add($builder->create('options', 'textarea',
                array('label'  => 'Опции', 'required' => false))
                ->addModelTransformer(new OptionsTransformer()))
            ->add('created', 'datetime', array('label'  => 'Дата создания',
            'widget' => 'single_text', 'format' => IntlDateFormatter::SHORT))
            ->add('updated', 'datetime', array('label'  => 'Дата изменения',
            'widget' => 'single_text', 'format' => IntlDateFormatter::SHORT));

        return $builder->getForm();
    }
}

