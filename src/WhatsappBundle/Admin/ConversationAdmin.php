<?php

namespace WhatsappBundle\Admin;
use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;
use WhatsappBundle\Repository\ConfigurationRepository;
use WhatsappBundle\Entity\Message;

class ConversationAdmin extends Admin
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
            $this->trans('export.dtmmessage') => 'day',
            $this->trans('export.whatsappGroup') => 'whatsappGroup',
            $this->trans('export.conversationType') => 'conversationType',
            $this->trans('export.peticion') => 'peticion',
        );
    }
     public function getDataSourceIterator()
    {
         
//         $user = $this->getConfigurationPool()->getContainer()->get('security.token_storage')->getToken()->getUser();
//         date_default_timezone_set($timezone);
//         dump(date_default_timezone_get());die;
        $iterator = parent::getDataSourceIterator();
        $iterator->setDateTimeFormat('d/m/Y H:i:s'); //change this to suit your needs
//        $iterator->setDateTimeFormat('d/m/y H:i:s'); //change this to suit your needs
        return $iterator;
    }
    
// Fields to be shown on create/edit forms
    protected function configureFormFields(FormMapper $formMapper)
    {
        $isFromTicket = false;
        if ($ownerId = $this->getRequest()->query->get('owner-id')) {
            //create your admin differently
            $isFromTicket = true;
         }
        //Obtener recursos 
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
            
//            ->add('phone', null, array('label' => 'Teléfono'))
            ->add('whatsappGroup', null, array('label' => 'Contacto'))
            ->add('description', null, array('label' => 'Descripción'))
            ->add('conversationType', null, array('label' => 'Tipo de conversación'))
                
            ->add('peticion', null, array('label' => 'Ticket'))
            ->add('day', null, array('label' => 'Fecha inicio'))
            ->add('dateEnd', null, array('label' => 'Fecha fin'))
            ->add('ended', null, array('label' => 'Finalizada'))
                
            ->add('files', \Sonata\CoreBundle\Form\Type\CollectionType::class, array(
                    'required'      => false,
                    'label'         => 'Recursos',   
                    'btn_add'       => 'Agregar un recurso de comunicación',
                    'by_reference' => false,
                    'type_options'  => array(
                        'delete' => true,
                    ),
                ), array(
                    'edit'          => 'inline', // or standard
//                    'inline'        => 'table',  // or standard
                    'sortable'      => 'id',     // by any field in your entity
//                    'limit'         => 5,        // you can remove this - this is a limit of items
                    'allow_delete'  => true, 
                    'placeholder'   => $this->trans('admin.placeholder.no_media'), 
                    'link_parameters' => array(
                            'context' => 'content_block'
                        ),
                ))
//            ->add('files', 'sonata_type_collection', array(
//                    'required'      => false,
//                    'label'         => 'Recursos',   
//                    'btn_add'       => 'Agregar un recurso de comunicación',
//                    'by_reference' => false,
//                    'type_options'  => array(
//                        'delete' => true,
//                    ),
//                ), array(
//                    'edit'          => 'inline', // or standard
////                    'inline'        => 'table',  // or standard
//                    'sortable'      => 'id',     // by any field in your entity
////                    'limit'         => 5,        // you can remove this - this is a limit of items
//                    'allow_delete'  => true, 
//                    'placeholder'   => $this->trans('admin.placeholder.no_media'), 
//                    'link_parameters' => array(
//                            'context' => 'content_block'
//                        ),
//                ))
//                
                ;
//            ->add('configuration', 'entity', array('label' => 'Empresa',
//                'class' => 'WhatsappBundle:Configuration',
//                'query_builder' => function(ConfigurationRepository $er) use ($configurations) {
//                return $er->createQueryBuilder('c')
//                    ->where('c.id in (:configurations)')
//                    ->setParameter('configurations', $configurations);
//            }
//            ))
//            ->add('configuration', null, array('label' => 'Empresa'))
        ;
        if($isFromTicket){
            $formMapper->remove("peticion");
            $formMapper->remove("whatsappGroup");
        }
        
    }

    // Fields to be shown on filter forms
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $em = $this->getModelManager()->getEntityManager($this->getClass());
        $user = $this->getConfigurationPool()->getContainer()->get('security.token_storage')->getToken()->getUser();
        $configurationsFilter = array();
        if($this->is_super()){
            $configurationList = $em->getRepository('WhatsappBundle:Configuration')->findAll();
            foreach ($configurationList as $value) {
                $configurationsFilter[$value->getId()] = $value->getCompany();
            }
        }
        else{
            $userCompanies = $user->getConfigurations();
            foreach ($userCompanies as $value) {
                $configurationsFilter[$value->getConfiguration()->getId()] = $value->getConfiguration()->getCompany();
            }
        }
        $datagridMapper
//            ->add('configuration', 'doctrine_orm_choice', array('label' => 'Empresa',
//                    'field_options' => array(
//                        'required' => false,
//                        'choices' => $configurationsFilter
//                    ),
//                    'field_type' => 'choice',
////                    'show_filter' => true
//                ))
//            ->add('day', null, array('label' => 'Fecha'))
//            ->add('phone', null, array('label' => 'Celular'))
            ->add('whatsappGroup', null, array('label' => 'Contacto'))
            ->add('conversationType', null, array('label' => 'Tipo de conversación'))            
        ;
    }

    // Fields to be shown on lists
    protected function configureListFields(ListMapper $listMapper)
    {
        unset($this->listModes['mosaic']);
        $listMapper
//            ->add('configuration', "text", array('label' => 'Empresa'))
            ->addIdentifier('day', null, array('label' => 'Fecha inicio'))
            ->add('dateLastMessage', null, array('label' => 'Último mensaje'))
            ->add('unreadMessage', null, array('label' => 'Mensaje nuevo'))
            ->add('whatsappGroup', null, array('label' => 'Contacto'))
//            ->add('phone', null, array('label' => 'Teléfono'))
            ->add('conversationType', null, array('label' => 'Tipo de conversación'))
            ->add('peticion', null, array('label' => 'Ticket'))
            ->add('dateEnd', null, array('label' => 'Fecha fin'))
            ->add('ended', null, array('label' => 'Finalizada', 'editable' => true))
            ->add('files1', null, array(
                            'label'         => 'Descargar Recursos',
                'template' => 'WhatsappBundle:SHOP:my_files.html.twig'
            ))
            ->add('files', null, array(
                            'label'         => 'Escuchar audios',
                'template' => 'WhatsappBundle:SHOP:escuchar_audio.html.twig'
            ));
        ;
        $listMapper
                ->add('_action', 'actions', array(
            'label' => "Acciones",
            'actions' => array(
//                'show' => array(),                
                'edit' => array(),
                'delete' => array(),
                'Vermensajes' => array(
                    'template' => 'WhatsappBundle:SHOP:list__action_vermensajes.html.twig'
                ),
//                'Enviarmensaje' => array(
//                    'template' => 'WhatsappBundle:SHOP:list__action_enviarmensaje.html.twig'
//                ),
                'Chat' => array(
                    'template' => 'WhatsappBundle:SHOP:list__action_chat.html.twig'
                ),
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
                return 'WhatsappBundle:CRUD:base_list.html.twig';
                break;
            
//            case 'edit':
////                dump($name);die;
//                return 'WhatsappBundle:CRUD:base_edit.html.twig';
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
        $user = $this->getConfigurationPool()->getContainer()->get('security.token_storage')->getToken()->getUser();
        $isCreated = $em->getRepository('WhatsappBundle:Configuration')->findByCompany("Default");
        if(count($isCreated) != 0 && $entity->getConfiguration() == null) {
            $entity->setConfiguration($isCreated[0]);
        }
        $files = $entity->getFiles();
        $em = $this->getModelManager()->getEntityManager($this->getClass());
        foreach ($files as $key => $value) {
            if($value->getMedia()){
                $value->setName($value->getMedia()->getName());
                $em->persist($value);
                //Buscar si el file ya tiene un mensaje, si no se debe crear y asociar a la conversacion
                if($value->getMessage() == null){
                    $message = new Message();
                    $message->setFile($value);
                    $message->setConfiguration($isCreated[0]);
                    $message->setConversation($entity);
                    $message->setIsLoadedAudio(true);
                    $message->setUser($user);
                    if($entity->getConversationType() != null){
                        $message->setStrmenssagetext($entity->getConversationType()->getName());
                    }
                    else{
                        $message->setStrmenssagetext("Mensaje tipo audio");
                    }
                    $message->setDtmmessage($entity->getDay());
                    $message->setEnabled(true);
                    $message->setProcesed(true);
                    $message->setFromMe(true);
                    $message->setWhatsappGroup($entity->getWhatsappGroup());
                    $em->persist($message);
                }
            }
        }
        $em->flush();
    }
    
    
     public function preUpdate($entity) {
        $em = $this->getModelManager()->getEntityManager($this->getClass());
        $files = $entity->getFiles();
        $isCreated = $em->getRepository('WhatsappBundle:Configuration')->findByCompany("Default");
        $user = $this->getConfigurationPool()->getContainer()->get('security.token_storage')->getToken()->getUser();
        foreach ($files as $key => $value) {
            if($value->getMedia()){
                $value->setName($value->getMedia()->getName());
                $em->persist($value);
                $em->flush();
                //Buscar si el file ya tiene un mensaje, si no se debe crear y asociar a la conversacion
                if($value->getMessage() == null){
                    $message = new Message();
                    $message->setFile($value);
                    $message->setConfiguration($isCreated[0]);
                    $message->setConversation($entity);
                    $message->setIsLoadedAudio(true);
                    $message->setUser($user);
                    if($entity->getConversationType() != null){
                        $message->setStrmenssagetext($entity->getConversationType()->getName());
                    }
                    else{
                        $message->setStrmenssagetext("Mensaje tipo audio");
                    }
                    $message->setDtmmessage($entity->getDay());
                    $message->setEnabled(true);
                    $message->setProcesed(true);
                    $message->setFromMe(true);
                    $message->setWhatsappGroup($entity->getWhatsappGroup());
                    $em->persist($message);
                    $em->flush();
                }
            }
        }
        
    }
}