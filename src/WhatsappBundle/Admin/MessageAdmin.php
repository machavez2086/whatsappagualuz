<?php

namespace WhatsappBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\AdminBundle\Show\ShowMapper;
use WhatsappBundle\Entity\Ticket;
use WhatsappBundle\Repository\ConfigurationRepository;
use WhatsappBundle\Repository\TicketRepository;
use WhatsappBundle\Repository\WhatsappGroupRepository;

class MessageAdmin extends Admin {

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

    public function getExportFields() {
        return array(
//            $this->trans('export.configuration') => 'configuration',
//            $this->trans('export.id') => 'id',
            $this->trans('export.strmenssagetext') => 'strmenssagetext',
            $this->trans('export.whatsappGroup') => 'whatsappGroup',
//            $this->trans('export.supportFirstAnswer') => 'supportFirstAnswerText',
//            $this->trans('export.isValidationKeyword') => 'isValidationKeywordText',
//            $this->trans('export.ticket') => 'ticket',
            $this->trans('export.dtmmessage') => 'dtmmessageText',
//            $this->trans('export.whatsappNick') => 'mappedAuthorNick',
//            $this->trans('export.supportMember') => 'supportMember',
                // add your types
        );
    }

    public function getDataSourceIterator() {
//        $user = $this->getConfigurationPool()->getContainer()->get('security.token_storage')->getToken()->getUser();
        $iterator = parent::getDataSourceIterator();
        $iterator->setDateTimeFormat('d/m/Y H:i:s'); //change this to suit your needs
        return $iterator;
    }

// Fields to be shown on create/edit forms
    protected function configureFormFields(FormMapper $formMapper) {
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
//                ->add('configuration', 'entity', array('label' => 'Empresa',
//                    'class' => 'WhatsappBundle:Configuration',
//                    'query_builder' => function(ConfigurationRepository $er) use ($configurations) {
//                        return $er->createQueryBuilder('c')
//                                ->where('c.id in (:configurations)')
//                                ->setParameter('configurations', $configurations);
//                    }
//                ))
//                ->add('whatsappGroup', 'entity', array('label' => 'Grupo',
//                    'class' => 'WhatsappBundle:WhatsappGroup',
//                    'query_builder' => function(WhatsappGroupRepository $er) use ($configurations) {
//                        return $er->createQueryBuilder('c')
//                                ->where('c.configuration in (:configurations)')
//                                ->setParameter('configurations', $configurations);
//                    }
//                ))
//            ->add('id', null, array('label' => 'id'))
//            
            ->add('conversation', null, array('label' => 'Conversación'))
//                ->add('ticket', 'entity', array('label' => 'Petición',
//                    'class' => 'WhatsappBundle:Ticket',
//                    'query_builder' => function(TicketRepository $er) use ($configurations) {
//                        return $er->createQueryBuilder('c')
//                                ->where('c.configuration in (:configurations)')
//                                ->setParameter('configurations', $configurations);
//                    }
//                ))
//                ->add('procesed', null, array('label' => 'procesed', ))
//                ->add('dtmmessage', null, array('label' => 'Fecha', "with_seconds" => true))
                //->add('strmenssagetext', null, array('label' => 'Mensaje'))
//            ->add('whatsappGroup', null, array('label' => 'Grupo'))
//                ->add('mappedAuthorNick', null, array('label' => 'Nick'))

//            ->add('strcontactuid', null, array('label' => 'contacto'))
        ;
    }

    // Fields to be shown on filter forms
    protected function configureDatagridFilters(DatagridMapper $datagridMapper) {
        $em = $this->getModelManager()->getEntityManager($this->getClass());
        $user = $this->getConfigurationPool()->getContainer()->get('security.token_storage')->getToken()->getUser();
        $userCompanies = $user->getConfigurations();
        $configurationsFilter = array();
        $configurations = array();
        if ($this->is_super()) {
            $configurationList = $em->getRepository('WhatsappBundle:Configuration')->findAll();
            foreach ($configurationList as $value) {
                $configurations[] = $value->getId();
                $configurationsFilter[$value->getId()] = $value->getCompany();
            }
        } else {
            $userCompanies = $user->getConfigurations();
            foreach ($userCompanies as $value) {
                $configurations[] = $value->getConfiguration()->getId();
                $configurationsFilter[$value->getConfiguration()->getId()] = $value->getConfiguration()->getCompany();
            }
        }
        //Obtener recursos
        $ticketList = $em->getRepository('WhatsappBundle:Ticket')->findByConfigurations($configurations);
//        dump($configurations);die;
        $choicesTickets = array();
        foreach ($ticketList AS $recurso) {
            $choicesTickets[$recurso->getId()] = $recurso->getName();
        }

        $groupList = $em->getRepository('WhatsappBundle:WhatsappGroup')->findByConfigurations($configurations);
//        dump($configurations);die;
        $choicesGroups = array();
        foreach ($groupList AS $recurso) {
            $choicesGroups[$recurso->getId()] = $recurso->getName();
        }

        $datagridMapper
//                ->add('configuration', 'doctrine_orm_choice', array('label' => 'Empresa',
//                    'field_options' => array(
//                        'required' => false,
//                        'choices' => $configurationsFilter
//                    ),
//                    'field_type' => 'choice',
////                    'show_filter' => true
//                ))
                ->add('strmenssagetext', null, array('label' => 'Mensaje'))
            ->add('conversation', null, array('label' => 'Conversación'))
//                ->add('supportFirstAnswer', null, array('label' => 'Soporte primera respuesta?', 'editable' => true))
//                ->add('ticket', 'doctrine_orm_choice', array('label' => 'Petición',
//                    'field_options' => array(
//                        'required' => false,
//                        'choices' => $choicesTickets
//                    ),
//                    'field_type' => 'choice'
//                ))
//                ->add('ticket', null, array('label' => 'Petición'))   
//                ->add('dtmmessage', null, array('label' => 'Fecha'))
//            ->add('whatsappGroup', null, array('label' => 'Grupo'))
//                ->add('mappedAuthorNick', null, array('label' => 'Nick'))
//            ->add('supportMember', null, array('label' => 'Miembro cliente'))
//            ->add('clientMember', null, array('label' => 'Miembro cliente'))
//                ->add('strcontactname', null, array('label' => 'Nombre de contacto'))
                
//                ->add('sentimentNumber', null, array('label' => 'Sentimiento'))

        //->add('struid', null, array('label' => 'struid'))
        //->add('strcontactuid', null, array('label' => 'strcontactuid'))
        //->add('strcontactname', null, array('label' => 'strcontactname'))
        //->add('strcontacttype', null, array('label' => 'strcontacttype'))
        //->add('dtmmessage', null, array('label' => 'Hora del mensaje'))
        //->add('strmenssageuid', null, array('label' => 'strmenssageuid'))
        //->add('strmenssagecuid', null, array('label' => 'strmenssagecuid'))
        //->add('strmenssagedir', null, array('label' => 'strmenssagedir'))
        //->add('strmenssagetype', null, array('label' => 'strmenssagetype'))
        //->add('intconversation', null, array('label' => 'intconversation'))
        //->add('intdiference', null, array('label' => 'intdiference'))
        //->add('strchat', null, array('label' => 'strchat'))

        ;
    }

    // Fields to be shown on lists
    protected function configureListFields(ListMapper $listMapper) {

        unset($this->listModes['mosaic']);
        $listMapper
//                ->add('configuration', "text", array('label' => 'Empresa'))
//            ->add('id', null, array('label' => 'Id'))
//                ->addIdentifier('id', null, array('label' => 'Id'))
                ->add('whatsappGroup', null, array('label' => 'Contacto'))
                ->add('fromMe', null, array('label' => 'Mensaje propio'))
                ->add('strmenssagetext', null, array('label' => 'Mensaje'))
                //->add('enabled', null, array('label' => 'Habilitada', 'editable' => true))
//                ->add('supportFirstAnswer', null, array('label' => 'Soporte primera respuesta?', 'editable' => true))
//                ->add('isValidationKeyword', null, array('label' => 'Validación?', 'editable' => true))
//                ->add('ticket', null, array('label' => 'Petición'))
                //->add('strcontactname', null, array('label' => 'Nombre de contacto'))
                ->add('dtmmessage', null, array('label' => 'Fecha', 'format' => 'd/m/y H:i:s'))
                
            
            ->add('conversation', null, array('label' => 'Conversación'))
            ->add('urlMedia', null, array('label' => 'Url de imagen',
                'template' => 'WhatsappBundle:SHOP:show_imagen_whatsapp.html.twig'
                ))
            ->add('messageNumber', null, array('label' => '# Mensaje'))
                ->add('procesed', null, array('label' => 'Mensaje procesado', 'editable' => true))
//                ->add('mappedAuthorNick', null, array('label' => 'Nick'))
//            ->add('problemPart', null, array('label' => 'Parte del problema', 'editable' => true))
//            ->add('solutionPart', null, array('label' => 'Parte de la Solución', 'editable' => true))
//                ->add('supportMember', null, array('label' => 'Recurso'))
//                ->add('clientMember', null, array('label' => 'Miembro cliente'))
//                ->add('sentimentVader', null, array('label' => 'Sentimiento (-1 a 1)'))
//                ->add('sentimentTextblob', null, array('label' => 'sentimentTextblob (-1 a 1)'))
//                ->add('sentimentSpahish', null, array('label' => 'sentimentSpahish (0 a 1)'))                
//                ->add('sentimentAsure', null, array('label' => 'Análisis de sentimiento (0 a 1)'))
//                ->add('messageNumber', null, array('label' => '# Mensaje segùn API'))

//            ->add('strcontactname', null, array('label' => 'Nombre de contacto'))
//            ->add('struid', null, array('label' => 'struid'))
//            ->add('strcontactuid', null, array('label' => 'strcontactuid'))
//            ->add('strcontacttype', null, array('label' => 'strcontacttype'))
//            
//            ->add('strmenssageuid', null, array('label' => 'strmenssageuid'))
//            ->add('strmenssagecuid', null, array('label' => 'strmenssagecuid'))
//            ->add('strmenssagedir', null, array('label' => 'strmenssagedir'))
//            ->add('strmenssagetype', null, array('label' => 'strmenssagetype'))
//            ->add('intconversation', null, array('label' => 'intconversation'))
//            ->add('intdiference', null, array('label' => 'intdiference'))
//            ->add('strchat', null, array('label' => 'strchat'))
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
                            'template' => 'WhatsappBundle:SHOP:list__action_chat_from_message.html.twig'
                        ),
//                        'newticket' => array(
//                            'template' => 'WhatsappBundle:ADMIN:list__action_divideticket.html.twig'
//                        ),
//                        'divideticket' => array(
//                            'template' => 'WhatsappBundle:ADMIN:list__action_divideticket.html.twig'
//                        ),
//                        'newticket' => array(
//                            'template' => 'WhatsappBundle:ADMIN:list__action_message_to_new_ticket.html.twig'
//                        ),
//                        'unlinkticket' => array(
//                            'template' => 'WhatsappBundle:ADMIN:list__action_message_unlink_ticket.html.twig'
//                        ),
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
    protected function configureRoutes(RouteCollection $collection) {
        $collection
                ->add('getTicketsByGroup', 'getTicketsByGroup')
                ->add('getGroupsByConfiguration', 'getGroupsByConfiguration')
                ->add('getOptionalsGroupsByConfiguration', 'getOptionalsGroupsByConfiguration')
                ->add('getOptionalsTicketsByConfiguration', 'getOptionalsTicketsByConfiguration')
                ->add('getOptionalsSolvedBySupportMemberByConfiguration', 'getOptionalsSolvedBySupportMemberByConfiguration')
                ->add('getOptionalsClientMemberByConfiguration', 'getOptionalsClientMemberByConfiguration')
                ->add('getOptionalsTicketByGroup', 'getOptionalsTicketByGroup')
        ;
        $collection->remove('create');
//        $collection->remove('delete');
    }

    public function prePersist($building) {
        
    }

    public function preUpdate($message) {

        $em = $this->getModelManager()->getEntityManager($this->getClass());
        //verificar si tiene true en primera respuesta recalcular el tiempo en le ticket y buscar todos los mensajes de ese ticket y ponerle a todos primera respuesta en false
        if ($message->getSupportFirstAnswer()) {
            $messagesFromTicket = $em->getRepository('WhatsappBundle:Message')->findByTicket($message->getTicket());
            foreach ($messagesFromTicket as $value) {
                if ($value->getSupportFirstAnswer() and $value->getId() != $message->getId()) {
                    $value->setSupportFirstAnswer(false);
                    $em->persist($value);
                }
            }
            $ticket = $message->getTicket();
            $ticket->recalculeFirstAnswer($message->getId());
            $em->persist($ticket);
            $em->flush();
        }

        //verificar si tiene true en $isValidationKeyword recalcular el tiempo en le ticket y buscar todos los mensajes de ese ticket y ponerle a todos $isValidationKeyword en false
        $DM = $this->getConfigurationPool()->getContainer()->get('Doctrine')->getManager();
        $uow = $DM->getUnitOfWork();
        $OriginalEntityData = $uow->getOriginalEntityData($message);

        $modifyValidation = false;
        if ($OriginalEntityData["isValidationKeyword"] != $message->getIsValidationKeyword()) {
            $ticket = $message->getTicket();
            $ticket->recalculeValidation($message);
            $em->persist($ticket);
            $em->flush();
        }

        $id = $message->getId();

        $em->getUnitOfWork()->computeChangeSets($message);
        $updates = $em->getUnitOfWork()->getEntityChangeSet($message);
        $ticketOld = $message->getTicket();
        $updates = $em->getUnitOfWork()->getEntityChangeSet($message);

        foreach ($updates as $entity) {
            if ($entity[1] instanceof Ticket) {
                $ticketOld = $entity[0];
            }
        }
//        dump($ticketOld);
//        die;
//        $messageOld = $em->getRepository('WhatsappBundle:Message')->find($id);
//        $configuraion = $message->getConfiguration();
//        $configId = $configuraion->getId();
//        $timezone = $em->getRepository('WhatsappBundle:Configuration')->getTimezoneFromConfiguration($configId);
//        $timezone = $timezone[0]["timeZone"];
//        
//        $newTicket = $message->getTicket();
//        if ($ticketOld != $newTicket) {
//
//            $messagesAfterThisFromTicket = array();
//            if ($ticketOld)
//                $messagesAfterThisFromTicket = $em->getRepository('WhatsappBundle:Message')->findPosThisIdDate($message->getDtmmessage(), $ticketOld);
//            foreach ($messagesAfterThisFromTicket as $value) {
//                $newTicket->addMessages($value);
//            }
//            $newTicket->addMessages($message);
//            $newTicket->recalculateResolutionTates($timezone);
//            $em->persist($newTicket);
//            if ($ticketOld) {
//                $ticketOld->recalculateResolutionTates($timezone);
//                $em->persist($ticketOld);
//            }
//            $em->flush();
//        }
////        else if(array_key_exists("enabled", $updates)){
////            dump($message);die;
//        $ticket = $message->getTicket();
//        $ticket->recalculateResolutionTates($timezone);
//        $em->persist($ticket);
//        $em->flush();
//        }
//            
//        if(count($ticketPrew) > 0){
//            $ticketP = $ticketPrew[0];
//            foreach ($ticket->getMessages() as $value) {
//                $ticketP->addMessages($value);
//            }
//            dump($ticketP);
//            $ticketP->recalculateResolutionTates();
//            dump($ticketP);
//            $em->persist($ticketP);
//            $em->flush();
//        }
    }

    public function getTemplate($name) {

        switch ($name) {

            case 'layout':
                return 'WhatsappBundle:MESSAGE:standard_layout.html.twig';
                break;

            case 'list':
                return 'WhatsappBundle:CRUD:base_list_message.html.twig';
                break;

            case 'edit':
//                dump($name);die;
                return 'WhatsappBundle:CRUD:messages_base_edit.html.twig';
                break;

            case 'inner_list_row':
//                dump($name);die;
                return 'WhatsappBundle:MESSAGE:base_list_inner_row.html.twig';
                break;
            case 'base_list_field':
                return 'WhatsappBundle:MESSAGE:base_list_field.html.twig';
                break;
            default:
                return parent::getTemplate($name);
                break;
        }
    }

    public function getBatchActions() {
        // retrieve the default batch actions (currently only delete)
        $actions = parent::getBatchActions();

        if (
                $this->hasRoute('edit') && $this->isGranted('EDIT') 
//                &&
//                $this->hasRoute('delete') && $this->isGranted('DELETE')
        ) {
//            $actions['merge'] = array(
//                'label' => $this->trans('action_ticket_change', array(), 'SonataAdminBundle'),
//                'ask_confirmation' => false
//            );
        }

        return $actions;
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

}
