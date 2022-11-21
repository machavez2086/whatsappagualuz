<?php

namespace WhatsappBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;
use WhatsappBundle\Repository\ConfigurationRepository;
use WhatsappBundle\Repository\ProductRepository;
use WhatsappBundle\Repository\ConversationTypeRepository;
use WhatsappBundle\Entity\ConversationType;
use WhatsappBundle\Entity\Conversation;
use WhatsappBundle\Entity\File;
use WhatsappBundle\Entity\Message;
use WhatsappBundle\Form\Type\LoteType;

class PeticionAdmin extends Admin {

    /**
     * Default Datagrid values
     *
     * @var array
     */
    protected $datagridValues = array(
        '_page' => 1, // display the first page (default = 1)
        '_sort_order' => 'DESC', // reverse order (default = 'ASC')
        '_sort_by' => 'id'  // name of the ordered field
            // (default = the model's id field, if any)
            // the '_sort_by' key can be of the form 'mySubModel.mySubSubModel.myField'.
    );

    public function getExportFormats() {
        return array("xls");
    }
    
      public function getDataSourceIterator()
    {
         
        $iterator = parent::getDataSourceIterator();
        $iterator->setDateTimeFormat('d/m/Y H:i:s'); //change this to suit your needs
        return $iterator;
    }

    public function getExportFields() {
        return array(
//            $this->trans('export.configuration') => 'configuration',
            $this->trans('export.nroReclamo') => 'nroReclamo',
            $this->trans('export.whatsappGroup') => 'whatsappGroup',
            $this->trans('export.email') => 'whatsappGroup.email',
            $this->trans('export.peticionType') => 'peticionType',
            $this->trans('export.motive') => 'motive',
            $this->trans('export.category') => 'category',
            $this->trans('export.clientActitud') => 'clientActitud',
            $this->trans('export.clientName') => 'clientName',
            $this->trans('export.phone') => 'phone',
            $this->trans('export.shop.email') => 'email',
            $this->trans('export.domicilio') => 'domicilio',
            $this->trans('export.localidad') => 'localidad',
            $this->trans('export.provincia') => 'provincia',
            $this->trans('export.codigoPostal') => 'codigoPostal',
            $this->trans('export.product') => 'product',
            $this->trans('export.expirationDateStr') => 'expirationDateStr',
            $this->trans('export.lote') => 'lote',
            $this->trans('export.cant') => 'cant',
            $this->trans('export.area') => 'area',
            $this->trans('export.elementType') => 'elementType',
            $this->trans('export.observations') => 'observations',
            $this->trans('export.createdAt') => 'createdAt',
            $this->trans('export.loteNo') => 'loteNo',
            $this->trans('export.loteHour') => 'loteHour',
            $this->trans('export.loteMaquina') => 'loteMaquina',
        );
    }
//     public function getDataSourceIterator()
//    {
//         
////         $user = $this->getConfigurationPool()->getContainer()->get('security.token_storage')->getToken()->getUser();
////         date_default_timezone_set($timezone);
////         dump(date_default_timezone_get());die;
//        $iterator = parent::getDataSourceIterator();
//        $iterator->setDateTimeFormat('d/m/Y H:i:s'); //change this to suit your needs
////        $iterator->setDateTimeFormat('d/m/y H:i:s'); //change this to suit your needs
//        return $iterator;
//    }

// Fields to be shown on create/edit forms
    protected function configureFormFields(FormMapper $formMapper) {
        //Obtener recursos 
        $em = $this->getModelManager()->getEntityManager($this->getClass());
        $user = $this->getConfigurationPool()->getContainer()->get('security.token_storage')->getToken()->getUser();
        $configurations = array();
        if ($this->is_super()) {
            $configurationList = $em->getRepository('WhatsappBundle:Configuration')->findAll();
            foreach ($configurationList as $value) {
                $configurations[] = $value->getId();
            }
        } else {
            $userCompanies = $user->getConfigurations();
            foreach ($userCompanies as $value) {
                if ($value->getRol() == "Administrador")
                    $configurations[] = $value->getConfiguration()->getId();
            }
        }

        $formMapper
                ->with('General', array('class' => 'col-md-6'))->end()
                ->with('Contacto', array('class' => 'col-md-6'))->end()
                ->with('Estados', array('class' => 'col-md-6'))->end()
//                ->with('Producto', array('class' => 'col-md-6'))->end()
//                ->with('Lote', array('class' => 'col-md-6'))->end()
                
                ->with('Conversaciones', array('class' => 'col-md-12'))->end()
        ;

        $formMapper
                ->with('General')
//            ->add('nroReclamo', null, array('label' => 'Nº de reclamo'))
                ->add('peticionType', null, array('label' => 'Tipo'))
//                ->add('claimTypes', null, array('label' => 'Tipo de reclamo', 'expanded' => true, 'by_reference' => false, 'multiple' => true))
                ->add('category', null, array('label' => 'Categoría'))
                ->add('motive', null, array('label' => 'Motivo del reclamo'))
//            ->add('area', null, array('label' => 'Tipo de elemento'))
                ->add('elementType', null, array('label' => 'Apertura de Motivos'))
                ->add('observations', null, array('label' => 'Observaciones'))
                ->end()
                ->with('Contacto')
                ->add('whatsappGroup', 'sonata_type_model', array('label' => 'Contacto'))
                ->add('clientActitud', null, array('label' => 'Actitud del cliente/consumidor'))
                ->add('timeDisponibility', null, array('label' => 'Horario de disponibilidad'))
                ->end()
//            ->add('clientName', null, array('label' => 'Nombre y apellido'))
//            ->add('phone', null, array('label' => 'Celular'))
//            ->add('email', null, array('label' => 'E-mail'))
//            ->add('domicilio', null, array('label' => 'Domicilio'))
//            ->add('localidad', null, array('label' => 'Localidad'))
//            ->add('provincia', null, array('label' => 'Provincia'))
//            ->add('codigoPostal', null, array('label' => 'Código postal'))
//                ->with('Producto')
////                ->add('product', null, array('label' => 'Producto'))
//                ->add('product', 'entity', array('label' => 'Producto', 'required' => false,
//                    'class' => 'WhatsappBundle:Product',
//                    'query_builder' => function(ProductRepository $er) {
//                        return $er->createQueryBuilder('c')->orderBy('c.name', 'ASC');
//                    }
//                ))
//                ->add('cant', null, array('label' => 'Cantidad'))
////                ->add('presentation', null, array('label' => 'Presentación'))
//                ->end()
                ->with('Estados')
//                ->add('firsConversationType', 'entity', array('label' => 'Tipo de Conversación', 'required' => false,
//                    'class' => 'WhatsappBundle:ConversationType',
//                    'query_builder' => function(ConversationTypeRepository $er) use ($configurations) {
//                        return $er->createQueryBuilder('c')
//                                ->where('c.configuration in (:configurations)')
//                                ->setParameter('configurations', $configurations);
//                    }
//                ))
                  
//                ->add('files', \Sonata\CoreBundle\Form\Type\CollectionType::class, array(
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
//                ->add('firsConversationType', null, array('label' => 'Estado de petición'))
                ->add('peticionStatus', null, array('label' => 'Estado de petición'))
                ->add('isFininshed', null, array('label' => 'Terminado'))
                ->end()
//                ->with('Lote')
//                ->add('expirationDateStr', 'text', array('label' => 'VTO', 'required' => false))
////            ->add('lote', new LoteType(), array('label' => 'No', 'attr' => array('class' => 'col-md-4')))
//                ->add('loteNo', null, array('label' => 'No', 'attr' => array('class' => 'col-md-4')))
//                ->add('loteHour', null, array('label' => 'Hora'))
//                ->add('loteMaquina', null, array('label' => 'Máquina'))
//                ->end()
                
                
                ->with('Conversaciones')
                ->add('conversations', \Sonata\CoreBundle\Form\Type\CollectionType::class, array(
                    'required'      => false,
                    'label'         => 'Conversación',   
                    'btn_add'       => 'Agregar conversación',
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
                            'context' => 'content_block',
                            'owner-id' => 'owner-id'
                        ),
                ))  
                ->end()
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
    }

    // Fields to be shown on filter forms
    protected function configureDatagridFilters(DatagridMapper $datagridMapper) {
        $em = $this->getModelManager()->getEntityManager($this->getClass());
        $user = $this->getConfigurationPool()->getContainer()->get('security.token_storage')->getToken()->getUser();
        $configurationsFilter = array();
        if ($this->is_super()) {
            $configurationList = $em->getRepository('WhatsappBundle:Configuration')->findAll();
            foreach ($configurationList as $value) {
                $configurationsFilter[$value->getId()] = $value->getCompany();
            }
        } else {
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
                ->add('nroReclamo', null, array('label' => 'Nº de ticket'))
                ->add('createdAt', 'doctrine_orm_datetime_range', array('timezone' => 'UTC', 'label' => 'Fecha de registro'), \Sonata\CoreBundle\Form\Type\DateTimeRangePickerType::class, array('field_options_start' => array('format' => 'dd-MM-yyyy HH:mm:SS'),
                    'field_options_end' => array('format' => 'dd-MM-yyyy HH:mm:SS'))
                )
//                ->add('createdAt', 'doctrine_orm_datetime_range', array('timezone' => 'UTC', 'label' => 'Fecha de registro', 'show_filter' => true), 'sonata_type_datetime_range_picker', array('field_options_start' => array('format' => 'dd-MM-yyyy HH:mm:SS', 'model_timezone' => 'UTC', 'view_timezone' => $user->getTimeZone()),
//                    'field_options_end' => array('format' => 'dd-MM-yyyy HH:mm:SS', 'model_timezone' => 'UTC', 'view_timezone' => $user->getTimeZone()))
//                )
                ->add('whatsappGroup', null, array('label' => 'Contacto'))
                ->add('peticionType', null, array('label' => 'Tipo'))
                ->add('category', null, array('label' => 'Categoría'))
                ->add('clientActitud', null, array('label' => 'Actitud del cliente'))
//            ->add('clientName', null, array('label' => 'Nombre y apellido'))
//            ->add('phone', null, array('label' => 'Celular'))
//            ->add('email', null, array('label' => 'E-mail'))
//            ->add('domicilio', null, array('label' => 'Domicilio'))
//            ->add('localidad', null, array('label' => 'Localidad'))
//            ->add('provincia', null, array('label' => 'Provincia'))
//            ->add('codigoPostal', null, array('label' => 'Código postal'))
                ->add('product', null, array('label' => 'Producto'))
                ->add('expirationDateStr', null, array('label' => 'VTO'))
//                ->add('lote', null, array('label' => 'Lote'))
//                ->add('presentation', null, array('label' => 'Presentación'))
                ->add('cant', null, array('label' => 'Cantidad'))
                ->add('motive', null, array('label' => 'Motivo del reclamo'))
                ->add('area', null, array('label' => 'Área'))
                ->add('elementType', null, array('label' => 'Apertura de Motivos'))
                ->add('observations', null, array('label' => 'Observaciones'))
                ->add('isFininshed', null, array('label' => 'Terminado'))

        ;
    }

    // Fields to be shown on lists
    protected function configureListFields(ListMapper $listMapper) {
        unset($this->listModes['mosaic']);
        $listMapper
//            ->add('configuration', "text", array('label' => 'Empresa'))
                ->addIdentifier('nroReclamo', null, array('label' => 'Nº de ticket'))
//                ->add('createdAt', null, array('label' => 'Fecha', ))
                ->add('createdAt', null, array('label' => 'Fecha', 'pattern' => 'd/M/y H:mm:s'))
                ->add('whatsappGroup', null, array('label' => 'Contacto'))
                ->add('whatsappGroup.email', null, array('label' => 'Correo'))
                ->add('peticionType', null, array('label' => 'Tipo'))
                ->add('category', null, array('label' => 'Categoría'))
                ->add('clientActitud', null, array('label' => 'Actitud del cliente/consumidor'))
                ->add('timeDisponibility', null, array('label' => 'Horario de disponibilidad'))
                ->add('conversationTypes', null, array('label' => 'Tipos de conversaciones',
                    
                    'template' => 'WhatsappBundle:SHOP:conversation_type.html.twig'
                    ))
//            ->add('clientName', null, array('label' => 'Nombre y apellido'))
//            ->add('phone', null, array('label' => 'Celular'))
//            ->add('email', null, array('label' => 'E-mail'))
//            ->add('domicilio', null, array('label' => 'Domicilio'))
//            ->add('localidad', null, array('label' => 'Localidad'))
//            ->add('provincia', null, array('label' => 'Provincia'))
//            ->add('codigoPostal', null, array('label' => 'Código postal'))
//                ->add('product', null, array('label' => 'Producto'))
//            ->add('expirationDate', null, array('label' => 'Fecha de vencimiento'))
//            ->add('lote', null, array('label' => 'Lote'))
//            ->add('presentation', null, array('label' => 'Presentación'))
//            ->add('cant', null, array('label' => 'Cantidad'))
                ->add('motive', null, array('label' => 'Motivo del reclamo'))
//            ->add('area', null, array('label' => 'Area'))
//            ->add('elementType', null, array('label' => 'Apertura de Motivos'))
//                ->add('observations', null, array('label' => 'Observaciones'))
                
                ->add('sumaryObservations', null, array('label' => 'Observaciones'))
                ->add('expirationDateStr', null, array('label' => 'Fecha de exp.'))
//                ->add('loteNo', null, array('label' => 'No.'))
//                ->add('loteHour', null, array('label' => 'Hora'))
//                ->add('loteMaquina', null, array('label' => 'Máquina'))
                ->add('isFininshed', null, array('label' => 'Terminado'))
                ->add('filesUploads', null, array('label' => 'Adjuntos',
                    'template' => 'WhatsappBundle:SHOP:files_tickets.html.twig'
                    ))

        ;
        $listMapper
                ->add('_action', 'actions', array(
                    'label' => "Acciones",
                    'actions' => array(
//                'show' => array(),                
                        'edit' => array(),
                        'delete' => array(),
                        'verpanel' => array(
                            'template' => 'WhatsappBundle:SHOP:list__action_peticion_ver_panel.html.twig'
                        ),
                    )
        ));
    }

    public function getTemplate($name) {

        switch ($name) {

//            case 'list':
//                return 'WhatsappBundle:CRUD:mybase_list.html.twig';
//                break;

            case 'list':
                return 'WhatsappBundle:CRUD:base_list.html.twig';
                break;

            case 'edit':
//                dump($name);die;
                return 'WhatsappBundle:CRUD:base_edit.html.twig';
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
        if (count($userCompanyList) == 1 && !$this->is_super()) {
            $this->datagridValues = array_merge(
                    array(
                'configuration' => array(
                    'type' => 1,
                    'value' => $configuration
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
        if ($this->is_super())
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
//        dump($entity);die;
        $user = $this->getConfigurationPool()->getContainer()->get('security.token_storage')->getToken()->getUser();
        $em = $this->getModelManager()->getEntityManager($this->getClass());
        $isCreated = $em->getRepository('WhatsappBundle:Configuration')->findByCompany("Default");
        $noDelDia = 1;
        if (count($isCreated) != 0 && $entity->getConfiguration() == null) {
            $entity->setConfiguration($isCreated[0]);
        }
        $lastPeticionList = $em->getRepository('WhatsappBundle:Peticion')->findLastTicketToday($entity->getConfiguration());
        if (count($lastPeticionList) > 0) {
            $lastPeticion = $lastPeticionList[0];
            $noDelDia = $lastPeticion->getNoDelDia() + 1;
        }
        $entity->setNoDelDia($noDelDia);
        //Convertir numoero del dia a string
        $noDelDiaStr = "";
        if ($noDelDia < 10) {
            $noDelDiaStr = "0" . $noDelDia;
        }
        else{
            $noDelDiaStr = $noDelDia;
        }
        $noTicket = $entity->getCreatedAt()->format("dmy") . $noDelDiaStr;
        $entity->setNroReclamo($noTicket);
        if ($entity->getExpirationDateStr() != null) {
            $fecha = \DateTime::createFromFormat('m-Y', $entity->getExpirationDateStr());
//            dump($fecha->format('Y-m-d'));
//            die;
        }
        foreach ($entity->getConversations() as $conversation) {
//                dump($conversation->getPeticion());die;
                if($conversation->getPeticion() == null){
                    $conversation->setPeticion($entity);
                }
                if($conversation->getWhatsappGroup() == null){
                    $conversation->setWhatsappGroup($entity->getWhatsappGroup());
                }
                $isCreated = $em->getRepository('WhatsappBundle:Configuration')->findByCompany("Default");
                if(count($isCreated) != 0 && $conversation->getConfiguration() == null) {
                    $conversation->setConfiguration($isCreated[0]);
                }
                $files = $conversation->getFiles();
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
                            $message->setConversation($conversation);
                            $message->setIsLoadedAudio(true);
                            $message->setUser($user);
                            //obtener el tipo de conversacion
                            if($conversation->getConversationType() != null){
                                $message->setStrmenssagetext($conversation->getConversationType()->getName());
                            }
                            else{
                                $message->setStrmenssagetext("Mensaje tipo audio");
                            }
                            $message->setDtmmessage($conversation->getDay());
                            $message->setEnabled(true);
                            $message->setProcesed(true);
                            $message->setFromMe(true);
                            $message->setWhatsappGroup($entity->getWhatsappGroup());
                            $em->persist($message);
                        }
                    }
                }
                $em->flush();
                $em->persist($conversation);
                $em->flush();
                
            }
        if ($entity->getFirsConversationType() != null) {
            
            
            $conversationType = $entity->getFirsConversationType();
            $conversation = new Conversation();
            $conversation->setConfiguration($entity->getConfiguration());
            $conversation->setConversationType($conversationType);
            $conversation->setPeticion($entity);
            $conversation->setWhatsappGroup($entity->getWhatsappGroup());
            $conversation->setDescription($entity->getObservations());
            $conversation->setEnded($entity->getIsFininshed());
            $conversation->setDay(new \DateTime("now"));
            $conversation->setFiles($entity->getFiles());
            
            
            $em->persist($conversation);
            $em->flush();
            
            
            foreach ($entity->getFiles() as $file) {
                $message = new Message();
                $message->setFile($file);
                $message->setConfiguration($entity->getConfiguration());
                $message->setConversation($conversation);
                $message->setIsLoadedAudio(true);
                $message->setUser($user);
                if($conversation->getConversationType() != null){
                    $message->setStrmenssagetext($conversation->getConversationType()->getName());
                }
                else{
                    $message->setStrmenssagetext("Mensaje tipo audio");
                }
                $message->setDtmmessage($conversation->getDay());
                $message->setEnabled(true);
                $message->setProcesed(true);
                $message->setFromMe(true);
                $message->setWhatsappGroup($conversation->getWhatsappGroup());
                $em->persist($message);
                $file->setPeticion(null);
                $em->persist($file);
            }
            $em->flush();
        }
        
    }

    public function preUpdate($entity) {
        if ($entity->getIsFininshed()){
            $em = $this->getModelManager()->getEntityManager($this->getClass());
            foreach ($entity->getConversations() as $conversation) {
                $conversation->setEnded(true);
                $em->persist($conversation);
                $em->flush();
            }
        }
        $em = $this->getModelManager()->getEntityManager($this->getClass());
//        dump(count($entity->getConversations()));die;
        foreach ($entity->getConversations() as $conversation) {
//            dump($entity->getConversations());die;
            if($conversation->getPeticion() == null)
                $conversation->setPeticion($entity);
                $em->persist($conversation);
                $em->flush();
//            dump($conversation->getPeticion()->getId());
            $em = $this->getModelManager()->getEntityManager($this->getClass());
            $files = $conversation->getFiles();
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
                        $message->setConversation($conversation);
                        $message->setIsLoadedAudio(true);
                        $message->setUser($user);
                        if($conversation->getConversationType() != null){
                            $message->setStrmenssagetext($conversation->getConversationType()->getName());
                        }
                        else{
                            $message->setStrmenssagetext("Mensaje tipo audio");
                        }
                        $message->setDtmmessage($conversation->getDay());
                        $message->setEnabled(true);
                        $message->setProcesed(true);
                        $message->setFromMe(true);
                        $message->setWhatsappGroup($conversation->getWhatsappGroup());
                        $em->persist($message);
                        $em->flush();
                    }
                }
            }
        }
//        if ($entity->getExpirationDateStr() != null) {
//            $fecha = \DateTime::createFromFormat('m/Y', $entity->getExpirationDateStr());
////            dump($fecha->format('Y-m-d'));die;
//        }
    }

}
