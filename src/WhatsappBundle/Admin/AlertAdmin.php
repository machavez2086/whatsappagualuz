<?php

namespace WhatsappBundle\Admin;
use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;
use WhatsappBundle\Repository\ConfigurationRepository;
use WhatsappBundle\Repository\WhatsappGroupRepository;
use WhatsappBundle\Repository\TicketRepository;
use Sonata\AdminBundle\Route\RouteCollection;


class AlertAdmin extends Admin
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
            $this->trans('export.configuration') => 'configuration',
//            $this->trans('export.id') => 'id',
            $this->trans('export.message') => 'message',
            $this->trans('export.sendDate') => 'sendDateText',
            $this->trans('export.type') => 'type',
            $this->trans('export.open') => 'openText',
                // add your types
        );
    }

    public function getDataSourceIterator() {
        $user = $this->getConfigurationPool()->getContainer()->get('security.token_storage')->getToken()->getUser();
        $iterator = parent::getDataSourceIterator();
        $iterator->setDateTimeFormat('d/m/Y H:i:s'); //change this to suit your needs
        return $iterator;
    }
    
// Fields to be shown on create/edit forms
    protected function configureFormFields(FormMapper $formMapper)
    {
        $em = $this->getModelManager()->getEntityManager($this->getClass());
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
            ->add('configuration', 'entity', array('label' => 'Empresa',
                'class' => 'WhatsappBundle:Configuration',
                'query_builder' => function(ConfigurationRepository $er) use ($configurations) {
                return $er->createQueryBuilder('c')
                    ->where('c.id in (:configurations)')
                    ->setParameter('configurations', $configurations);
            }
            ))
            ->add('whatsappGroup', 'entity', array('label' => 'Grupo de Whatsapp','required' =>false,
                'class' => 'WhatsappBundle:WhatsappGroup',
                'query_builder' => function(WhatsappGroupRepository $er) use ($configurations) {
                    return $er->createQueryBuilder('c')
                        ->where('c.configuration in (:configurations)')
                        ->setParameter('configurations', $configurations);
                    }
                ))
//            ->add('whatsappGroup', null, array('label' => 'Grupo'))
//            ->add('ticket', null, array('label' => 'Petición'))
            ->add('ticket', 'entity', array('label' => 'Petición','required' =>false,
                'class' => 'WhatsappBundle:Ticket',
                'query_builder' => function(TicketRepository $er) use ($configurations) {
                    return $er->createQueryBuilder('c')
                        ->where('c.configuration in (:configurations)')
                        ->setParameter('configurations', $configurations);
                    }
                ))   
            ->add('message', null, array('label' => 'Mensaje'))
            ->add('open', null, array('label' => 'Abierta'))
                 
            ->add('sendDate', null, array('label' => 'Fecha de envío'))
            ->add('type', 'choice', array('label' => 'Tipo de alerta', 
                'choices' =>array('Respuesta' => 'Respuesta', 'Resolución' => 'Resolución')))
            
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
        $groupList = $em->getRepository('WhatsappBundle:WhatsappGroup')->findByConfigurations($configurations);
        $choicesGroups = array();
        foreach ($groupList AS $recurso) {
            $choicesGroups[$recurso->getId()] = $recurso->getName();
        }
        
         //Obtener recursos
        $ticketList = $em->getRepository('WhatsappBundle:Ticket')->findByConfigurations($configurations);
        $choicesTickets = array();
        foreach ($ticketList AS $recurso) {
            $choicesTickets[$recurso->getId()] = $recurso->getName();
        }
        $datagridMapper
            ->add('configuration', 'doctrine_orm_choice', array('label' => 'Empresa',
                    'field_options' => array(
                        'required' => false,
                        'choices' => $configurationsFilter
                    ),
                    'field_type' => 'choice',
                    'show_filter' => true
                ))
            ->add('open', null, array('label' => 'Abierta'))
            ->add('message', null, array('label' => 'Mensaje'))
            ->add('whatsappGroup', 'doctrine_orm_choice', array('global_search' => false, 'label' => 'Grupo',
                'field_options' => array(
                    'required' => false,
                    'choices' => $choicesGroups
                ),
                'field_type' => 'choice'
            )) 
//            ->add('whatsappGroup', null, array('label' => 'Grupo'))
            ->add('ticket', 'doctrine_orm_choice', array('global_search' => false, 'label' => 'Petición',
                'field_options' => array(
                    'required' => false,
                    'choices' => $choicesTickets
                ),
                'field_type' => 'choice'
            ))
//            ->add('ticket', null, array('label' => 'Petición'))
            ->add('sendDate', null, array('label' => 'Fecha de envío'))
            //->add('type', null, array('label' => 'Tipo de alerta'))
           ->add('type', 'doctrine_orm_choice', array('global_search' => false, 
        'label' => 'Tipo de alerta'), 
        'choice', 
        array(
            'choices' => array(
                'Respuesta' => 'Respuesta',  // The key (value1) will contain the actual value that you want to filter on
                'Resolución' => 'Resolución',  // The 'Name Two' is the "display" name in the filter
        ), 
        'expanded' => false,    
        'multiple' => false))
        
                ;
                
        ;
    }

    // Fields to be shown on lists
    protected function configureListFields(ListMapper $listMapper)
    {
        unset($this->listModes['mosaic']);
        $listMapper
            ->add('configuration', "text", array('label' => 'Empresa'))
            ->addIdentifier('message', null, array('label' => 'Mensaje'))
            
            ->add('open', null, array('label' => 'Abierta'))
            ->add('whatsappGroup', null, array('label' => 'Grupo'))
            ->add('ticket', null, array('label' => 'Petición'))
            ->add('sendDate', null, array('label' => 'Fecha de envío'))
            ->add('type', null, array('label' => 'Tipo de alerta'))
        ;
        $listMapper
                ->add('_action', 'actions', array(
            'label' => "Acciones",
            'actions' => array(
//                'show' => array(),                
                'edit' => array(),
                'delete' => array(),
                
            )
        ));
    }
    
    
    public function getTemplate($name)
    {
        
        switch ($name) {
            
//            case 'list':
//                return 'WhatsappBundle:CRUD:mybase_list.html.twig';
//                break;
            
            case 'list':
                return 'WhatsappBundle:CRUD:base_list_alert.html.twig';
                break;
            case 'edit':
//                dump($name);die;
                return 'WhatsappBundle:CRUD:messages_base_edit.html.twig';
                break;
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
//    $query->addSelect('c');
//    $query->leftJoin(sprintf('%s.commission',$query->getRootAlias()), 'c');
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
     
      protected function configureRoutes(RouteCollection $collection)
    {
        $collection
                ->add('getTicketsByGroup', 'getTicketsByGroup')
                ->add('getGroupsByConfiguration', 'getGroupsByConfiguration')
                ->add('getOptionalsGroupsByConfiguration', 'getOptionalsGroupsByConfiguration')
                ->add('getOptionalsTicketsByConfiguration', 'getOptionalsTicketsByConfiguration')
                ;
    }
    
    public function prePersist($entity) {
        $em = $this->getModelManager()->getEntityManager($this->getClass());
        $isCreated = $em->getRepository('WhatsappBundle:Configuration')->findByCompany("Default");
        if(count($isCreated) != 0 && $entity->getConfiguration() == null) {
            $entity->setConfiguration($isCreated[0]);
        }
    }
}
