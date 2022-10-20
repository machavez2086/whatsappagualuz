<?php

/*
 * This file is part of the Sonata Project package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Application\Sonata\UserBundle\Admin\Entity;

use Sonata\UserBundle\Admin\Model\UserAdmin as BaseUserAdmin;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;

class UserAdmin extends BaseUserAdmin
{
    
      public function getDataSourceIterator() {
        $iterator = parent::getDataSourceIterator();
//        $iterator->setDateTimeFormat('d/m/Y H:i:s'); //change this to suit your needs
        return $iterator;
    }
    public function getExportFormats()
    {
        return array("xls");
    }
     public function getExportFields()
    {
        return array(
            
            $this->trans('export.id') => 'id',
            $this->trans('export.username') => 'username',            
            $this->trans('export.email') => 'email',
            $this->trans('export.enabled') => 'enabledText',
            $this->trans('export.lastLogin') => 'lastLogin',
            $this->trans('export.createdAt') => 'createdAt',
            $this->trans('export.firstname') => 'firstname',
            $this->trans('export.lastname') => 'lastname',
            $this->trans('export.gender') => 'gender',
            $this->trans('export.timezone') => 'timezone',
            $this->trans('export.phone') => 'phone',
            $this->trans('export.aprovedplan') => 'aprovedplanText',
            $this->trans('export.plantype') => 'plantype',
            // add your types
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        // define group zoning
        $formMapper
            ->tab('Usuario')
                ->with('Perfil', array('class' => 'col-md-6'))->end()
                ->with('General', array('class' => 'col-md-6'))->end()
//                ->with('Social', array('class' => 'col-md-6'))->end()
            ->end()
            ->tab('Seguridad')
                ->with('Estado', array('class' => 'col-md-4'))->end()
                ->with('Groups', array('class' => 'col-md-4'))->end()
//                ->with('Keys', array('class' => 'col-md-4'))->end()
//                ->with('Roles', array('class' => 'col-md-12'))->end()
            ->end()
        ;

        $now = new \DateTime();

        $formMapper
            ->tab('Usuario')
                ->with('General')
                    ->add('username')
                    ->add('email')
                    ->add('plainPassword', 'text', array(
                        'required' => (!$this->getSubject() || is_null($this->getSubject()->getId())),
                    ))
                ->end()
                ->with('Perfil')
                    ->add('dateOfBirth', 'Sonata\CoreBundle\Form\Type\DatePickerType', array(
//                    ->add('dateOfBirth', 'Sonata\CoreBundle\Form\Type\DatePickerType', array(
                        'years' => range(1900, $now->format('Y')),
                        'dp_min_date' => '1-1-1900',
//                        'dp_min_date' => '1-1-1900',
                        'dp_max_date' => $now->format('c'),
//                        'format' => "d/m/Y",
                        'format' => 'dd/MM/y',
                        'required' => false,
                    ))
                    ->add('firstname', null, array('required' => true))
                    ->add('lastname', null, array('required' => true))
//                    ->add('website', 'url', array('required' => false))
//                    ->add('biography', 'text', array('required' => false))
                    ->add('gender', 'sonata_user_gender', array(
                        'required' => true,
                        'translation_domain' => $this->getTranslationDomain(),
                    ))
//                    ->add('locale', 'locale', array('required' => false))
//                    ->add('timezone', 'timezone', array('required' => true))
                    ->add('phone', null, array('required' => false))
                ->end()
//                ->with('Social')
//                    ->add('facebookUid', null, array('required' => false))
//                    ->add('facebookName', null, array('required' => false))
//                    ->add('twitterUid', null, array('required' => false))
//                    ->add('twitterName', null, array('required' => false))
//                    ->add('gplusUid', null, array('required' => false))
//                    ->add('gplusName', null, array('required' => false))
//                ->end()
            ->end()
            ->tab('Seguridad')
                ->with('Estado')
                    ->add('locked', null, array('required' => false))
                    ->add('expired', null, array('required' => false))
                    ->add('enabled', null, array('required' => false))
                    ->add('credentialsExpired', null, array('required' => false))
                ->end()
                ->with('Groups')
                    ->add('groups', null, array(
                        'required' => false,
                        'expanded' => true,
                        'multiple' => true,
                    ))
                ->end()
//                ->with('Roles')
//                    ->add('realRoles', 'sonata_security_roles', array(
//                        'label' => 'form.label_roles',
//                        'expanded' => true,
//                        'multiple' => true,
//                        'required' => false,
//                    ))
//                ->end()
//                ->with('Keys')
//                    ->add('token', null, array('required' => false))
//                    ->add('twoStepVerificationCode', null, array('required' => false))
//                ->end()
            ->end()
//            ->tab('Negocios')
//                ->with('Plan')
//                    ->add('plantype', 'choice', array('label' =>'Tipo de plan', 'required' => false,
//                        'choices' =>array('Gratis' => 'Gratis', 'Básico' => 'Básico', 'Ilimitado' => 'Ilimitado')
//                        ))
//                    ->add('aprovedplan', null, array('label' =>'Aprobado', 'required' => false))
//                ->end()
//            ->end()
        ;
    }
    
    
    /**
     * {@inheritdoc}
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        unset($this->listModes['mosaic']);
        $listMapper
            ->addIdentifier('username')
            ->add('email')
//            ->add('plantype', null, array('label' =>'Plan'))
//            ->add('aprovedplan', null, array('label' =>'Plan aprobado', 'editable' => true))
            ->add('groups')
            ->add('enabled', null, array('editable' => true))
            ->add('locked', null, array('editable' => true))
            ->add('createdAt')
        ;

        if ($this->isGranted('ROLE_ALLOWED_TO_SWITCH')) {
            
            $listMapper
                ->add('impersonating', 'string', array('template' => 'SonataUserBundle:Admin:Field/impersonating.html.twig'))
            ;
        }
         $listMapper
                ->add('_action', 'actions', array(
            'label' => "Acciones",
            'actions' => array(
//                'show' => array(),                
                'edit' => array(),
                'delete' => array(),
                'enviarenlace' => array(
                    'template' => 'WhatsappBundle:SHOP:list__action_enviar_enlace_usuario.html.twig'
                ),
                
            )
        ));
    }
    public function getTemplate($name)
    {
            
        switch ($name) {
            
            case 'list':
                return 'WhatsappBundle:CRUD:base_list.html.twig';
                break;
            case 'edit':
                return 'WhatsappBundle:CRUD:base_edit.html.twig';
                break;
            default:
                return parent::getTemplate($name);
                break;
        }
    }
    
    public function prePersist($entity) {
        $em = $this->getModelManager()->getEntityManager($this->getClass());
        $isCreated = $em->getRepository('WhatsappBundle:Configuration')->findByCompany("Default");
//        if(count($isCreated) == 0 && $entity->getConfiguration() == null) {
        $config = $isCreated[0];
        $userCompany = new \WhatsappBundle\Entity\UserCompany();
        $userCompany->setConfiguration($config);
        $userCompany->setUser($entity);
        $userCompany->setRol("Administrador");
        $em->persist($userCompany);
        $em->flush();
//        }
    }
}
