<?php

namespace WhatsappBundle\Admin;
use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;
use WhatsappBundle\Repository\ConfigurationRepository;
//use WhatsappBundle\Repository\SupportMemberRepository;
use Sonata\AdminBundle\Route\RouteCollection;

class WhatsappGroupAdmin extends Admin
{

     
    /**
     * Default Datagrid values
     *
     * @var array
     */
    protected $datagridValues = array(
        '_page' => 1,            // display the first page (default = 1)
        '_sort_order' => 'DESC', // reverse order (default = 'ASC')
        '_sort_by' => 'id'  // name of the ordered field
                                 // (default = the model's id field, if any)

        // the '_sort_by' key can be of the form 'mySubModel.mySubSubModel.myField'.
    );
    public function getExportFormats()
    {
        return array("xls");
    }
     public function getExportFields() {
        return array(
//            $this->trans('export.configuration') => 'configuration',
            $this->trans('export.name') => 'name',
            $this->trans('export.phone') => 'phoneNumber',
            $this->trans('export.phoneFixed') => 'phoneFixed',
            $this->trans('export.shop.email') => 'email',
            $this->trans('export.domicilio') => 'domicilio',
            $this->trans('export.localidad') => 'localidad',
            $this->trans('export.provincia') => 'provincia',
            $this->trans('export.codigoPostal') => 'codigoPostal',
        );
    }
    
// Fields to be shown on create/edit forms
    protected function configureFormFields(FormMapper $formMapper)
    {
        $em = $this->getModelManager()->getEntityManager($this->getClass());
        $user = $this->getConfigurationPool()->getContainer()->get('security.token_storage')->getToken()->getUser();
        $configurations = array();
        if($this->is_super()){
            $configurationList = $em->getRepository('WhatsappBundle:Configuration')->findAll();
            foreach ($configurationList as $value) {
                $configurations[] = $value->getId();
            }
        }
        else{
            $userCompanies = $user->getConfigurations();
            foreach ($userCompanies as $value) {
                if($value->getRol() == "Administrador")
                    $configurations[] = $value->getConfiguration()->getId();
            }
        }
        
        $formMapper
            ->add('name', null, array('label' => 'Nombre'))
            ->add('chatId', null, array('label' => 'Identificador en WhatsApp')) 
            ->add('phoneNumber', null, array('label' => 'Teléfono')) 
            ->add('phoneFixed', null, array('label' => 'Teléfono fijo')) 
            ->add('email', null, array('label' => 'E-mail'))
            ->add('domicilio', null, array('label' => 'Domicilio'))
            ->add('localidad', null, array('label' => 'Localidad'))
            ->add('provincia', null, array('label' => 'Provincia'))
            ->add('codigoPostal', null, array('label' => 'Código postal'))
            ->add('dateLastAutomaticMessage', null, array('label' => 'Fecha mensaje enviado automático'))
//            ->add('companyConfirmed', null, array('label' => 'Empresa confirmada')) 
//            ->add('supportMembers', null, array('label' => 'Miembros de soporte'))
//            ->add('clientMembers', null, array('label' => 'Miembros clientes'))
                
            
//            ->add('responsableSupportMember', null, array('label' => 'Responsable del grupo'))
//            ->add('configuration', 'entity', array('label' => 'Empresa',
//                'class' => 'WhatsappBundle:Configuration',
//                'query_builder' => function(ConfigurationRepository $er) use ($configurations) {
//                return $er->createQueryBuilder('c')
//                    ->where('c.id in (:configurations)')
//                    ->setParameter('configurations', $configurations);
//            }
//            ))
        ;
        
    }

    // Fields to be shown on filter forms
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $em = $this->getModelManager()->getEntityManager($this->getClass());
        $user = $this->getConfigurationPool()->getContainer()->get('security.token_storage')->getToken()->getUser();
        $configurationsFilter = array();
        $configurations = array();
        if($this->is_super()){
            $configurationList = $em->getRepository('WhatsappBundle:Configuration')->findAll();
            foreach ($configurationList as $value) {
                $configurations[] = $value->getId();
                $configurationsFilter[$value->getId()] = $value->getCompany();
            }
        }
        else{
            $userCompanies = $user->getConfigurations();
            foreach ($userCompanies as $value) {
                $configurations[] = $value->getConfiguration()->getId();
                $configurationsFilter[$value->getConfiguration()->getId()] = $value->getConfiguration()->getCompany();
            }
        }
        
        $datagridMapper
            ->add('companyConfirmed', null, array('label' => 'Empresa confirmada')) 
            ->add('name', null, array('label' => 'Nombre'))  
            ->add('phoneNumber', null, array('label' => 'Teléfono')) 
            ->add('phoneFixed', null, array('label' => 'Teléfono fijo')) 
//            ->add('configuration', 'doctrine_orm_choice', array('label' => 'Empresa',
//                    'field_options' => array(
//                        'required' => false,
//                        'choices' => $configurationsFilter
//                    ),
//                    'field_type' => 'choice',
//                    'show_filter' => true
//                ))
               
            ->add('email', null, array('label' => 'E-mail'))
            ->add('domicilio', null, array('label' => 'Domicilio'))
            ->add('localidad', null, array('label' => 'Localidad'))
            ->add('provincia', null, array('label' => 'Provincia'))
            ->add('codigoPostal', null, array('label' => 'Código postal'))
             
//            ->add('supportMembers', null, array('label' => 'Miembros de soporte'))
            
        ;
    }

    // Fields to be shown on lists
    protected function configureListFields(ListMapper $listMapper)
    {
        unset($this->listModes['mosaic']);
        $listMapper
//            ->add('configuration', "text", array('label' => 'Empresa'))
            ->addIdentifier('name', null, array('label' => 'Nombre'))    
            ->add('phoneNumber', null, array('label' => 'Teléfono')) 
            ->add('phoneFixed', null, array('label' => 'Teléfono fijo')) 
            ->add('email', null, array('label' => 'E-mail'))
            ->add('domicilio', null, array('label' => 'Domicilio'))
            ->add('localidad', null, array('label' => 'Localidad'))
            ->add('provincia', null, array('label' => 'Provincia'))
            ->add('codigoPostal', null, array('label' => 'Código postal'))
            ->add('chatId', null, array('label' => 'Identificador en WhatsApp')) 
//            ->add('companyConfirmed', null, array('label' => 'Empresa confirmada')) 
//            ->add('supportMembers', null, array('label' => 'Miembros de soporte'))
//            ->add('clientMembers', null, array('label' => 'Miembros clientes')) 
//            ->add('floors', null, array('label' => 'Pisos'))
//            ->add('_action', 'actions', array(
//                'actions' => array(
//                    'Clone' => array(
//                        'template' => 'DeepwebClasificadosBundle:CRUD:list__action_clone.html.twig'
//                    )
//                )
//            ))
        ;
        $listMapper
                ->add('_action', 'actions', array(
            'label' => "Acciones",
            'actions' => array(
//                'show' => array(),                
                'edit' => array(),
                'delete' => array(),
                'Chat' => array(
                    'template' => 'WhatsappBundle:SHOP:list__action_chat_contact.html.twig'
                ),
                
            )
        ));
    }
      
    // Fields to be shown on show action
//    protected function configureShowFields(ShowMapper $showMapper)
//    {
//        $showMapper
//           ->add('name', null, array())
//           ->add('address', null, array())
//       ;
//    }
//    protected function configureRoutes(RouteCollection $collection)
//    {
//        $collection
//        ->add('getSupportMemberByConfiguration', 'getSupportMemberByConfiguration')
//        ;
//    }
    
    
    
    public function getTemplate($name)
    {
            
        switch ($name) {
            
//            case 'list':
//                return 'WhatsappBundle:CRUD:mybase_list.html.twig';
//                break;
            
            case 'list':
                return 'WhatsappBundle:CRUD:base_list.html.twig';
                break;
            
//            case 'edit':
////                dump($name);die;
//                return 'WhatsappBundle:CRUD:whatsapp_group_base_edit.html.twig';
//                break;
            default:
                return parent::getTemplate($name);
                break;
        }
    }
    
    
    public function getFilterParameters() {
        $user = $this->getConfigurationPool()->getContainer()->get('security.token_storage')->getToken()->getUser();
        $em = $this->getModelManager()->getEntityManager($this->getClass());
        $userCompanyList = $em->getRepository('WhatsappBundle:UserCompany')->findByUser($user);
        $configuration = null;
        foreach ($userCompanyList as $value) {
            $configuration = $value->getConfiguration()->getId();
            break;
        }
//        if($configuration){
//            $this->datagridValues = array_merge(
//                    array(
//                'configuration' => array(
//                    'type' => 1,
//                    'value' =>$configuration
//                ),
//                    ), $this->datagridValues
//            );
//        }
        if(count($userCompanyList) == 1 && !$this->is_super()){
            $this->datagridValues = array_merge(
                    array(
                'configuration' => array(
                    'type' => 1,
                    'value' =>$configuration
                ),
                    ), $this->datagridValues
            );
        }

        return parent::getFilterParameters();
    }
    
    public function createQuery($context = 'list') {
        $configurations = array();
        $em = $this->getModelManager()->getEntityManager($this->getClass());
        $user = $this->getConfigurationPool()->getContainer()->get('security.token_storage')->getToken()->getUser();
        $userCompanyList = $em->getRepository('WhatsappBundle:UserCompany')->findByUser($user);
        foreach ($userCompanyList as $value) {
            $configurations[] = $value->getConfiguration()->getId();
        }
        $query = parent::createQuery($context);
        if($this->is_super())
            return $query;
        $alias = $query->getRootAliases();
        $alias = $alias[0];

        $query->andWhere($alias . ".configuration in (:configurations)"
        );
        $query->setParameter('configurations', $configurations);
        return $query;
    }
    
    private function is_super() {
         if ($this->getConfigurationPool()->getContainer()->get('security.context')->isGranted('ROLE_SUPER_ADMIN')) {
             return true;
         }
         return false;        
     }
     
     
    public function prePersist($entity) {
        $em = $this->getModelManager()->getEntityManager($this->getClass());
        $isCreated = $em->getRepository('WhatsappBundle:Configuration')->findByCompany("Default");
        if(count($isCreated) != 0 && $entity->getConfiguration() == null) {
            $entity->setConfiguration($isCreated[0]);
        }
    }
}