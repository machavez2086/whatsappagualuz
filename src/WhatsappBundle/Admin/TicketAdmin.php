<?php

namespace WhatsappBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\AdminBundle\Show\ShowMapper;
use WhatsappBundle\Form\DateTimeRangePickerType;
use WhatsappBundle\Form\DateTimeRangePicker1Type;
use WhatsappBundle\Repository\SupportMemberRepository;
use WhatsappBundle\Repository\TicketTypeRepository;
use WhatsappBundle\Repository\SolutionTypeRepository;
use WhatsappBundle\Repository\WhatsappGroupRepository;
use WhatsappBundle\Repository\ConfigurationRepository;
use WhatsappBundle\Common\DoctrineORMQuerySourceIterator;

use Sonata\AdminBundle\Datagrid\ProxyQueryInterface;

class TicketAdmin extends Admin {

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
    protected $maxPerPage = 192;
    public function getExportFormats()
    {
        return array("xls");
    }
        public function getExportFields()
    {
        return array(
            $this->trans('export.configuration') => 'configuration',
            $this->trans('export.id') => 'id',
            $this->trans('export.name') => 'name',
            $this->trans('export.startTime') => 'startTimeText',
            $this->trans('export.weekday') => 'weekday',
            $this->trans('export.resolutionDate') => 'resolutionDateText',
            $this->trans('export.minutesAnswerTime') => 'minutesAnswerTime',
            $this->trans('export.solvedBySupportMember') => 'solvedBySupportMember',
            $this->trans('export.ticketType') => 'ticketType',
            $this->trans('export.endDate') => 'getEndDateText',
            $this->trans('export.validationCount') => 'validationCount',
            $this->trans('export.minutesDevTime') => 'minutesDevTime',
            $this->trans('export.minutesValidationWaitTime') => 'minutesValidationWaitTime',
            $this->trans('export.minutesSolutionTime') => 'minutesSolutionTime',
            // add your types
        );
    }
     public function getDataSourceIterator()
    {
         
         $user = $this->getConfigurationPool()->getContainer()->get('security.token_storage')->getToken()->getUser();
//         date_default_timezone_set($timezone);
//         dump(date_default_timezone_get());die;
        $iterator = parent::getDataSourceIterator();
        $iterator->setDateTimeFormat('d/m/Y H:i:s'); //change this to suit your needs
//        $iterator->setDateTimeFormat('d/m/y H:i:s'); //change this to suit your needs
        return $iterator;
    }
//     public function getDataSourceIterator()
//    {
//         $datagrid = $this->getDatagrid();
//        $datagrid->buildPager();
//
//        $fields = array();
//
//        foreach ($this->getExportFields() as $key => $field) {
//            $label = $this->getTranslationLabel($field, 'export', 'label');
//            $transLabel = $this->trans($label);
//
//            // NEXT_MAJOR: Remove this hack, because all field labels will be translated with the major release
//            // No translation key exists
//            if ($transLabel == $label) {
//                $fields[$key] = $field;
//            } else {
//                $fields[$transLabel] = $field;
//            }
//        }
// 
//
//        $datagrid->buildPager();
//        $query = $datagrid->getQuery();
//        $firstResult = null;
//        $maxResult = null;
//        $query->select('DISTINCT '.$query->getRootAlias());
//        $query->setFirstResult($firstResult);
//        $query->setMaxResults($maxResult);
//
//        if ($query instanceof ProxyQueryInterface) {
//            $query->addOrderBy($query->getSortBy(), $query->getSortOrder());
//
//            $query = $query->getQuery();
//        }
//        $user = $this->getConfigurationPool()->getContainer()->get('security.token_storage')->getToken()->getUser();
//        $timezone = $user->getTimeZone();
//        return new DoctrineORMQuerySourceIterator($query, $fields, 'd/m/Y H:i:s', $timezone);
//     }
    

// Fields to be shown on create/edit forms
    protected function configureFormFields(FormMapper $formMapper) {
        $em = $this->getModelManager()->getEntityManager($this->getClass());
        $user = $this->getConfigurationPool()->getContainer()->get('security.token_storage')->getToken()->getUser();
        $configurations = array();
        $thisConfigurations = array();
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
        }$userCompanies = $user->getConfigurations();
        $configurations = array();
        foreach ($userCompanies as $value) {
            if ($value->getRol() == "Administrador")
                $configurations[] = $value->getConfiguration()->getId();
        }
        
        $subject = $this->getSubject();
        if($subject->getId()){
            $thisConfigurations[] = $subject->getConfiguration()->getId();
        }
        
//        $queryTicketsType = $em->createQueryBuilder("c")
//                ->select("c")
//                ->from("WhatsappBundle:TicketType")
//                ->where()
        $formMapper
                ->add('configuration', 'entity', array('label' => 'Empresa',
                    'class' => 'WhatsappBundle:Configuration',
                    'query_builder' => function(ConfigurationRepository $er) use ($configurations) {
                        return $er->createQueryBuilder('c')
                                ->where('c.id in (:configurations)')
                                ->setParameter('configurations', $configurations);
                    }
                ))
                ->add('whatsappGroup', 'entity', array('label' => 'Grupo de Whatsapp', 'required' => true,
                    'class' => 'WhatsappBundle:WhatsappGroup',
                    'query_builder' => function(WhatsappGroupRepository $er) use ($configurations) {
                        return $er->createQueryBuilder('c')
                                ->where('c.configuration in (:configurations)')
                                ->setParameter('configurations', $configurations);
                    }
                ))
                ->add('ticketTypes', 'sonata_type_model', array(
                    'label' => 'Categorías',
                    'query' => $em->createQueryBuilder('c')
                                ->select("c")
                                ->from("WhatsappBundle:TicketType", "c")
                                ->where('c.configuration in (:configurations)')
                                ->setParameter('configurations', $configurations)
                    ,
                    'required' => false,
                    'by_reference' => false,
                    'multiple' => true,
                    "btn_add" => false
                ))
                ->add('solvedBySupportMember', 'entity', array('label' => 'Recurso', 'required' => false,
                    'class' => 'WhatsappBundle:SupportMember',
                    'query_builder' => function(SupportMemberRepository $er) use ($configurations) {
                        return $er->createQueryBuilder('c')
                                ->where('c.configuration in (:configurations)')
                                ->setParameter('configurations', $configurations);
                    }
                ))
//                ->add('solvedBySupportMember', null, array('label' => 'Recurso'))
//                ->add('ticketType', 'entity', array('label' => 'Categoría', 'required' => false,
//                    'class' => 'WhatsappBundle:TicketType',
//                    'query_builder' => function(TicketTypeRepository $er) use ($thisConfigurations) {
//                        return $er->createQueryBuilder('c')
//                                ->where('c.configuration in (:configurations)')
//                                ->setParameter('configurations', $thisConfigurations);
//                    }
//                ))
//                ->add('ticketType', null, array('label' => 'Categoría'))
                ->add('solutionType', 'entity', array('label' => 'Tipo de solución', 'required' => false,
                    'class' => 'WhatsappBundle:SolutionType',
                    'query_builder' => function(SolutionTypeRepository $er) use ($configurations) {
                        return $er->createQueryBuilder('c')
                                ->where('c.configuration in (:configurations)')
                                ->setParameter('configurations', $configurations);
                    }
                ))
                ->add('name', null, array('label' => 'Nombre'))
                ->add('startTime', null, array('label' => 'Hora de inicio'))
//            ->add('whatsappGroup', null, array('label' => 'Grupo de Whatsapp'))
//            ->add('minutesSolutionTime', null, array('label' => 'Tiempo de solucion (minutos)'))
//            ->add('startDate', null, array('label' => 'Fecha de inicio'))
                ->add('endDate', null, array('label' => 'Fecha de fin', "with_seconds" => true))
                ->add('ticketended', null, array('label' => 'Petición finalizada'))
                ->add('nofollow', null, array('label' => 'No registro'))
                
//                ->add('solutionType', null, array('label' => 'Tipo de solución'))
                ->add('solution', null, array('label' => 'Descripción del ISSUE'))
                
//            ->add('solution', null, array('label' => 'Como se resolvió'))
//            ->add('solution', null, array('label' => 'Como se resolvió'))
                
                
//                ->add('whatsappGroup', null, array('label' => 'Grupo de Whatsapp'))


        ;
    }

//     public function getFilterParameters()
//    {
//        $this->datagridValues = array_merge(array(
//                'id' => array(
//                    'value' => 1,
//                )
//            ),
//            $this->datagridValues
//
//        );
//        return parent::getFilterParameters();
//    }
//    
//    public function getFilterParameters()
//    {
//        $this->datagridValues = array_merge(array(
//                'startDate' => array(
//                        'type' => 1,
//                        'value' => array(
//                                'date' => array('year' => date('Y'), 'month' => date('n'), 'day' => date('j')),
//                                
//                        ),
//                )
//        ),
//                $this->datagridValues
//
//        );
//
//        return parent::getFilterParameters();
//    }
    // Fields to be shown on filter forms
    protected function configureDatagridFilters(DatagridMapper $datagridMapper) {
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
        foreach ($groupList AS $group) {
            $choicesGroups[$group->getId()] = $group->getName();
        }

        //Obtener recursos
        $recursoList = $em->getRepository('WhatsappBundle:SupportMember')->findByConfigurations($configurations);
//        dump($configurations);die;
        $choicesRecursos = array();
        foreach ($recursoList AS $recurso) {
            $choicesRecursos[$recurso->getId()] = $recurso->getName();
        }

        //Obtener recursos
        $ticketTypeList = $em->getRepository('WhatsappBundle:TicketType')->findByConfigurations($configurations);
//        dump($configurations);die;
        $choicesTicketType = array();
        foreach ($ticketTypeList AS $ticketType) {
            $choicesTicketType[$ticketType->getId()] = $ticketType->getName();
        }

        //Obtener recursos
        $solutionTypeList = $em->getRepository('WhatsappBundle:SolutionType')->findByConfigurations($configurations);
//        dump($configurations);die;
        $choicesSolutionType = array();
        foreach ($solutionTypeList AS $solutionType) {
            $choicesSolutionType[$solutionType->getId()] = $solutionType->getName();
        }

        $choiceOptions = array("Lunes" => "Lunes", "Martes" => "Martes", "Miércoles" => "Miércoles", "Jueves" => "Jueves", "Viernes" => "Viernes", "Sábado" => "Sábado", "Domingo" => "Domingo");
        $datagridMapper
                ->add('configuration', 'doctrine_orm_choice', array('label' => 'Empresa',
                    'field_options' => array(
                        'required' => false,
                        'choices' => $configurationsFilter
                    ),
                    'field_type' => 'choice',
                    'show_filter' => true
                ))
                ->add('id', null, array('label' => 'Id'))
                ->add('name', null, array('label' => 'Nombre'))
//                ->add('startDate', 'doctrine_orm_date_range', array(), 'sonata_type_date_range', array('format' => 'dd-MM-yyyy', 'widget' => 'single_text', 'attr' => array('class' => 'datepicker'))) 
//            ->add('startDate', 'doctrine_orm_datetime_range', array('format' => 'dd-MM-yyyy', 'widget' => 'single_text'), 'sonata_type_date_range', array( 'attr' => array('class' => 'datepicker')))
//             ->add('startDate', 'doctrine_orm_datetime_range', array(), 'sonata_type_date_range', array('format' => 'dd-MM-yyyy', 'widget' => 'single_text', 'attr' => array('class' => 'datepicker')))
//             ->add('startDate', 'doctrine_orm_datetime_range', array(), new DateTimeRangePickerType())
//            ->add('startDate', 'doctrine_orm_datetime_range', array('label' => 'Fecha de registro' ))
//            ->add('startDate', 'doctrine_orm_date_range', array('label' => 'Fecha de registro','input_type' => 'text', 'field_type'                    => 'sonata_type_date_range_picker', ))   
//            ->add('startDate', null, array(), 'sonata_type_datetime_picker')
//            ->add('startDate', 'doctrine_orm_datetime_range', array('field_type'=>'sonata_type_datetime_range_picker',
//                'dp_use_current'=> false,       'dp_format'=>'dd/MM/yyyy H:i:s'
//                ))
//                     ->add('startDate', 'doctrine_orm_date_range', array(
//                'field_type' => 'sonata_type_date_range_picker',
//                'field_options' => array('date_format' => 'DD/MM/YYYY')
//            ))
                ->add('startDate', 'doctrine_orm_datetime_range', array('timezone' => 'UTC', 'label' => 'Fecha de registro', 'show_filter' => true), 'sonata_type_datetime_range_picker', array('field_options_start' => array('format' => 'dd-MM-yyyy HH:mm:SS', 'model_timezone' => 'UTC', 'view_timezone' => $user->getTimeZone()),
                    'field_options_end' => array('format' => 'dd-MM-yyyy HH:mm:SS', 'model_timezone' => 'UTC', 'view_timezone' => $user->getTimeZone()))
                )
                ->add('startTime', 'doctrine_orm_callback'
                        , array(
                    'label' => 'Horario',
                    'callback' => function($queryBuilder, $alias, $field, $value) {
                        $ini = $value["value"]['start'];
                        $end = $value["value"]['end'];
                        
                        if (!$ini && !$end)
                            return false;
                        if ($ini == "" && $end == "")
                            return false;
                        if ($ini == null or $ini == "") {
                            $ini = "00:00:00";
                        }
                        if ($end == null or $end == "") {
                            $end = "23:59:59";
                        }
                        $ini = trim($ini,":");
                        $end = trim($end,":");
                        $dateIniArray = explode(":",$ini);
                        if(count($dateIniArray) == 1)
                            $ini = $ini.":00:00";
                        if(count($dateIniArray) == 2)
                            $ini = $ini.":00";
                        
                        $dateEndArray = explode(":",$end);
                        if(count($dateEndArray) == 1)
                            $end = $end.":59:59";
                        if(count($dateEndArray) == 2)
                            $end = $end.":59";
                        
                        //cambiar ini y end por los numeros en utc
                        $dateIniArray = explode(":",$ini);
                        $dateEndArray = explode(":",$end);
                        $datetimeIni = new \DateTime("today");
                        
                        $datetimeEnd = new \DateTime("today");
                        
                        
                        $user = $this->getConfigurationPool()->getContainer()->get('security.token_storage')->getToken()->getUser();
                        $userTimezone = $user->getTimeZone();
                        $userTimezone = new \DateTimeZone($userTimezone);
                        $datetimeIni->setTimezone($userTimezone);
                        $datetimeEnd->setTimezone($userTimezone);
                        $datetimeIni->setTime($dateIniArray[0], $dateIniArray[1], $dateIniArray[2]);
                        $datetimeEnd->setTime($dateEndArray[0], $dateEndArray[1], $dateEndArray[2]);
                        $userTimezone = new \DateTimeZone("UTC");
                        $datetimeIni->setTimezone($userTimezone);
                        $datetimeEnd->setTimezone($userTimezone);
                        $ini = $datetimeIni->format("h:i:s");
                        $end = $datetimeEnd->format("h:i:s");
                        
                        $dateIniArray = explode(":",$ini);
                        $dateEndArray = explode(":",$end);
                        $datetimeIni = new \DateTime("today");
                        $datetimeIni->setTime($dateIniArray[0], $dateIniArray[1], $dateIniArray[2]);
                        
                        $datetimeEnd = new \DateTime("today");
                        $datetimeEnd->setTime($dateEndArray[0], $dateEndArray[1], $dateEndArray[2]);
                        
                        if ($datetimeIni <= $datetimeEnd) {
                            $queryBuilder->andWhere($alias . '.startTime >= :ini');
                            $queryBuilder->setParameter("ini", $ini);
                            $queryBuilder->andWhere($alias . '.startTime <= :end');
                            $queryBuilder->setParameter("end", $end);
                        } else {
                            $queryBuilder->andWhere($alias . '.startTime >= :ini or ' . $alias . '.startTime <= :end');
                            $queryBuilder->setParameter("ini", $ini);
                            $queryBuilder->setParameter("end", $end);
                        }
                        return true;
                    }
                        ), new DateTimeRangePicker1Type, array())
//                ->add('startDate', 'doctrine_orm_datetime_range', 
//       array('field_type' =>'sonata_type_datetime_range_picker', 'field_options' => array('format' => \IntlDateFormatter::SHORT)
//       ))
//            ->add('startDate', null, array('label' => 'Fecha de registro')) 
                ->add('weekday', null, array('label' => 'Día'), 'choice', array(
                    'choices' => $choiceOptions
                ))
                ->add('whatsappGroup', 'doctrine_orm_choice', array('label' => 'Cliente',
                    'field_options' => array(
                        'required' => false,
                        'choices' => $choicesGroups
                    ),
                    'field_type' => 'choice'
                ))
                ->add('solvedBySupportMember', 'doctrine_orm_choice', array('label' => 'Recurso',
                    'field_options' => array(
                        'required' => false,
                        'choices' => $choicesRecursos
                    ),
                    'field_type' => 'choice'
                ))
//                ->add('ticketType', 'doctrine_orm_choice', array('label' => 'Categoría',
//                    'field_options' => array(
//                        'required' => false,
//                        'choices' => $choicesTicketType
//                    ),
//                    'field_type' => 'choice'
//                ))
                ->add('solutionType', 'doctrine_orm_choice', array('label' => 'Tipo de resolución',
                    'field_options' => array(
                        'required' => false,
                        'choices' => $choicesSolutionType
                    ),
                    'field_type' => 'choice'
                ))
                
//                ->add('solvedBySupportMember', null, array('label' => 'Recurso'))
//                ->add('ticketType', null, array('label' => 'Categoría'))
//                ->add('solutionType', null, array('label' => 'Tipo de resolución'))
                ->add('endDate', null, array('label' => 'Fecha de fin', 'format' => 'd/m/y H:i:s'))
                ->add('firstanswer', null, array('label' => 'Petición atendida'))
//            ->add('nofollow', null, array('label' => 'No registro'))
                ->add('nullnofollow', 'doctrine_orm_callback'
                        , array(
                    'label' => 'No registro',
                    'callback' => function($queryBuilder, $alias, $field, $value) {
                        if ($value['value'] == 'no') {
                            $queryBuilder->andWhere($alias . '.nofollow IS NUll or ' . $alias . '.nofollow = false');
                            return true;
                        }
                        if ($value['value'] == 'si') {
                            $queryBuilder->andWhere($alias . '.nofollow = true ');
                            return true;
                        }
                    }
                        ), 'choice', array(
                    'choices' => array(
                        'no' => 'No',
                        'si' => 'Sí',
                    ),
                        )
                )
//            ->add('sendalert', null, array('label' => 'Alerta enviada', 'editable' => true))
                ->add('ticketended', null, array('label' => 'Petición finalizada', 'editable' => true))
//            ->add('satisfactiondDescritpion', null, array('label' => 'Satisfacción del cliente'))
                ->add('minutesAnswerTime', null, array('label' => 'Tiempo de respuesta (minutos)'))
                ->add('minutesDevTime', null, array('label' => 'Tiempo de desarrollo (minutos)'))
                ->add('minutesValidationWaitTime', null, array('label' => 'Tiempo de validación (minutos)'))
                ->add('minutesSolutionTime', null, array('label' => 'Tiempo de solución (minutos)'))
                ->add('validationCount', null, array('label' => 'Cantidad de validaciones'))
                ->add('sentimentAsureAllMessages', null, array('label' => 'Análisis de sentimiento (%)'))
//                ->add('sentimentasureSupportMessages', null, array('label' => 'Análisis de sentimiento del recurso (%)'))
//                ->add('sentimentAsureClientMessages', null, array('label' => 'Análisis de sentimiento del cliente (%)'))
        ;
//        dump($datagridMapper->("configuration"));die;
        $datagridMapper->add('fueraDeRango', 'doctrine_orm_callback'
                , array(
            'label' => 'Fuera de rango',
            'callback' => function($queryBuilder, $alias, $field, $value) use ($datagridMapper, $configurations){
            $entityChoiceFilter = $datagridMapper->get("configuration");
            
            $configurationValue = $entityChoiceFilter->getValue();
            $configurationId = null;
            if(count($configurations)> 0)
                $configurationId = $configurations[0];
            if($configurationValue["value"])
                $configurationId = $configurationValue["value"];
//            dump($configurationId);die;
            
                $em = $this->getModelManager()->getEntityManager($this->getClass());
                $configuration = null;
                if($configurationId)
                    $configuration = $em->getRepository('WhatsappBundle:Configuration')->find($configurationId);
                if ($configuration) {
                    if ($value['value'] == 'no') {
                        $queryBuilder->andWhere($alias . '.minutesAnswerTime <= :minutesAnswerAlert and ' . $alias . '.minutesSolutionTime <= :minutesResolutionAlert');
                        $queryBuilder->setParameter("minutesAnswerAlert", $configuration->getMinutesAnswerAlert());
                        $queryBuilder->setParameter("minutesResolutionAlert", $configuration->getMinutesResolutionAlert());
                        return true;
                    }
                    if ($value['value'] == 'si') {
                        $queryBuilder->andWhere($alias . '.minutesAnswerTime > :minutesAnswerAlert or ' . $alias . '.minutesSolutionTime > :minutesResolutionAlert');
                        $queryBuilder->setParameter("minutesAnswerAlert", $configuration->getMinutesAnswerAlert());
                        $queryBuilder->setParameter("minutesResolutionAlert", $configuration->getMinutesResolutionAlert());
                        return true;
                    }
                }
            }
                ), 'choice', array(
            'choices' => array(
                'no' => 'No',
                'si' => 'Sí',
            ),
                )
        );
//                dump($datagridMapper->get("startTime"));die;
    }

    // Fields to be shown on lists
    protected function configureListFields(ListMapper $listMapper) {
        unset($this->listModes['mosaic']);
        $user = $this->getConfigurationPool()->getContainer()->get('security.token_storage')->getToken()->getUser();
        $timezone = $user->getTimeZone();
//dump($timezone);die;
        $listMapper
                ->add('configuration', "text", array('label' => 'Empresa'))
//                ->add('evaluating', null, array('label' => 'evaluating'))
                ->addIdentifier('id', null, array('label' => 'Id'))
//            ->add('startDate', null, array('label' => 'Fecha de registro'))
                
                ->addIdentifier('name', null, array('label' => 'Nombre'))
//                ->add('startTime', "datetime", array('label' => 'Hora de inicio', 'pattern' => 'HH:mm:ss', 'timezone' => $timezone))                
                ->add('startDate', "datetime", array('label' => 'Hora de inicio', 'pattern' => 'HH:mm:ss', 'timezone' => $timezone))                
                //->add('whatsappGroup', null, array('label' => 'Grupo de Whatsapp'))
                ->add('weekday', null, array('label' => 'Día'))
//                ->add('resolutionDate', 'datetime', array('label' => 'Hora de respuesta', 'format' => 'd/m/y H:i:s', 'timezone' => $timezone))
                ->add('resolutionDate', 'datetime', array('label' => 'Hora de respuesta', 'pattern' => 'dd/MM/y HH:mm:ss',  'timezone' => $timezone))
                
                ->add('minutesAnswerTime', null, array('label' => 'Tiempo de respuesta (min)'))
                ->add('solvedBySupportMember', null, array('label' => 'Recurso'))
//                ->add('ticketType', null, array('label' => 'Categoría'))
                ->add('ticketTypes', null, array('label' => 'Categorías'))
//            ->add('solution', null, array('label' => 'Descripción del ISSUE'))
                ->add('endDate', 'datetime', array('label' => 'Hora de resolución', 'pattern' => 'dd/MM/y HH:mm:ss', 'timezone' => $timezone))
                ->add('validationCount', null, array('label' => 'Número de validaciones'))
                ->add('minutesDevTime', null, array('label' => 'Tiempo de desarrollo (min)'))
                ->add('minutesValidationWaitTime', null, array('label' => 'Tiempo de espera (min)'))
                ->add('minutesSolutionTime', null, array('label' => 'Tiempo de solución (min)'))
                ->add('firstanswer', null, array('label' => 'Petición atendida'))
//            ->add('solution', null, array('label' => 'Como se resolvió'))
                ->add('sendalert', null, array('label' => 'Alerta enviada'))
                ->add('ticketended', null, array('label' => 'Petición finalizada', 'editable' => true))
                ->add('nofollow', null, array('label' => 'No registro', 'editable' => true))
//            ->add('satisfactiondDescritpion', null, array('label' => 'Satisfacción del cliente'))
                ->add('solutionType', null, array('label' => 'Tipo de solución'))
//                ->add('sentimentVaderAllMessages', null, array('label' => 'sentimentVaderAllMessages (-1 a 1)'))
//                ->add('sentimentTextblobAllMessages', null, array('label' => 'sentimentTextblobAllMessages (-1 a 1)'))
//                ->add('sentimentSpahishAllMessages', null, array('label' => 'sentimentSpahishAllMessages'))                
                ->add('sentimentAsureAllMessages', null, array('label' => 'Análisis de sentimiento (%)'))
//                ->add('sentimentVaderSupportMessages', null, array('label' => 'sentimentVaderSupportMessages (-1 a 1)'))
//                ->add('sentimentTextblobSupportMessages', null, array('label' => 'sentimentTextblobSupportMessages (-1 a 1)'))
//                ->add('sentimentSpahishSupportMessages', null, array('label' => 'sentimentSpahishSupportMessages'))
//                ->add('sentimentasureSupportMessages', null, array('label' => 'Análisis de sentimiento del recurso (%)'))
//                ->add('sentimentVaderClientMessages', null, array('label' => 'sentimentVaderClientMessages (-1 a 1)'))
//                ->add('sentimentTextblobClientMessages', null, array('label' => 'sentimentTextblobClientMessages (-1 a 1)'))
//                ->add('sentimentSpahishClientMessages', null, array('label' => 'sentimentSpahishClientMessages'))
//                ->add('sentimentAsureClientMessages', null, array('label' => 'Análisis de sentimiento del cliente (%)'))
                ->add('satisfaction', null, array('label' => 'Evaluación de satisfacción'))
                
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
                        'Vermensajes' => array(
                            'template' => 'WhatsappBundle:ADMIN:list__action_vermensajes.html.twig'
                        ),
                        'addmessage' => array(
                            'template' => 'WhatsappBundle:ADMIN:list__action_addmessage.html.twig'
                        ),
                    )
        ));
    }

    public function preUpdate($ticket) {
        $em = $this->getModelManager()->getEntityManager($this->getClass());
        $configuraion = $ticket->getConfiguration();
        $configId = $configuraion->getId();
        $timezone = $em->getRepository('WhatsappBundle:Configuration')->getTimezoneFromConfiguration($configId);
        $timezone = $timezone[0]["timeZone"];
        
//        $oldTicket = $em->getRepository('WhatsappBundle:Ticket')->find($ticket->getId());
        $DM = $this->getConfigurationPool()->getContainer()->get('Doctrine')->getManager();
        $uow = $DM->getUnitOfWork();
        $OriginalEntityData = $uow->getOriginalEntityData($ticket);

        $modifyEndDate = false;
        $newEndDate = $ticket->getEndDate();
        if ($OriginalEntityData["endDate"] != $ticket->getEndDate())
            $modifyEndDate = true;

        $ticket->recalculateResolutionTates($timezone);
        if ($modifyEndDate) {
            $ticket->recalculateResolutionByEndDate($newEndDate);
//            die;
        }
        //verificar si tiene true en primera respuesta recalcular el tiempo en le ticket y buscar todos los mensajes de ese ticket y ponerle a todos primera respuesta en false
        if ($ticket->getTicketended() || $ticket->getNofollow()) {

            $alerts = $em->getRepository('WhatsappBundle:Alert')->findByTicketIsEnabled($ticket);
            foreach ($alerts as $alert) {
                $alert->setOpen(false);
                $em->persist($alert);
            }
            if (!$ticket->getMinutesAnswerTime()) {
                $ticket->setMinutesAnswerTime($ticket->getMinutesSolutionTime());
            }
            $em->flush();
        }
        
        if ($OriginalEntityData["nofollow"] != $ticket->getNofollow() and $ticket->getNofollow() == true){
            //Send message and save ticket on
            $this->getConfigurationPool()->getContainer()->get('whatsapp.sacspro.phonestatus')->sendMessageTicketDeleteOrNoFollow($ticket, false);
        }
    }

    // Fields to be shown on show action
//    protected function configureShowFields(ShowMapper $showMapper)
//    {
//        $showMapper
//           ->add('name', null, array())
//           ->add('address', null, array())
//       ;
//    }
    protected function configureRoutes(RouteCollection $collection)
    {
        $collection
                ->add('getSupportMemberByConfiguration', 'getSupportMemberByConfiguration')
                ->add('getGroupsByConfiguration', 'getGroupsByConfiguration')
                ->add('getSolutionTypeByConfiguration', 'getSolutionTypeByConfiguration')
                ->add('getTicketTypeByConfiguration', 'getTicketTypeByConfiguration')
                ->add('getOptionalsGroupsByConfiguration', 'getOptionalsGroupsByConfiguration')
                ->add('getOptionalsSolvedBySupportMemberByConfiguration', 'getOptionalsSolvedBySupportMemberByConfiguration')
                ->add('getOptionalsTicketTypeByConfiguration', 'getOptionalsTicketTypeByConfiguration')
                ->add('getOptionalsSolutionTypeByConfiguration', 'getOptionalsSolutionTypeByConfiguration')
                ;
    }

    public function preRemove($ticket) {
//        $em = $this->getModelManager()->getEntityManager($this->getClass());
//        $ticketPrew = $em->getRepository('WhatsappBundle:Ticket')->findPreThisId($ticket->getId(), $ticket->getWhatsappGroup());
//        if(count($ticketPrew) == 0)
//            $ticketPrew = $em->getRepository('WhatsappBundle:Ticket')->findPosThisId($ticket->getId(), $ticket->getWhatsappGroup());
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

//            case 'list':
//                return 'WhatsappBundle:CRUD:mybase_list.html.twig';
//                break;

            case 'list':
                return 'WhatsappBundle:CRUD:base_list_ticket.html.twig';
                break;
            
            case 'edit':
//                dump($name);die;
                return 'WhatsappBundle:CRUD:ticket_base_edit.html.twig';
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
                $this->hasRoute('edit') && $this->isGranted('EDIT') &&
                $this->hasRoute('delete') && $this->isGranted('DELETE')
        ) {
            $actions['merge'] = array(
                'label' => $this->trans('action_group_change', array(), 'SonataAdminBundle'),
                'ask_confirmation' => false
            );
        }

        return $actions;
    }

    public function getFilterParameters() {

//         dump($this->datagridValues);die;
//        $this->datagridValues = array_merge(
//            array(
//                
//                // exemple with date range
//                'startDate' => array(
//                   
//                    'value' => array(
//                        'start' => array(
//                            'date' => array(
//                                
//                            'day' => date('d'),
//                            'month' => date('m'),
//                            'year' => date('Y')
////                            'hours' => 0,
////                            'minutes' => 0
//                            )
//                            
//                            ),
//                        'end' => array( 'date' => array(
//                            'day' => date('d'),
//                            'month' => date('m'),
//                            'year' => date('Y')
//                            )
//                            )
//                        ),
//                    )
//                ),
//        $this->datagridValues = array_merge(
//            array(
//                
//                'startDate' => array(
//                   'type' => 1,
//                        'value' => array(
//                            'start' => array(
//                                'date' => array('year' => date('Y'), 'month' => date('n'), 'day' => date('j')),
//                                'time' => array('hour' => 0, 'minute' => 0)
//                        ),
//                            'end' => array(
//                                'date' => array('year' => date('Y'), 'month' => date('n'), 'day' => date('j')),
//                                'time' => array('hour' => 23, 'minute' => 59)
//                        ),
//                        ),
//                    )
//                ),
//            $this->datagridValues
//            );
        $user = $this->getConfigurationPool()->getContainer()->get('security.token_storage')->getToken()->getUser();
        $userTimezone = $user->getTimeZone();
        $userTimezone = new \DateTimeZone($userTimezone);
        $ini = new \DateTime("today", $userTimezone);
        
        $fin = new \DateTime("now", $userTimezone);
//        $ini->setTime(0, 0, 0);
//        dump($ini);die;
        
        
//         dump(parent::getFilterParameters());die;
//        $ini->setTimezone($userTimezone);
        $ini->setTime(0, 0, 0);
//        $fin->setTimezone($userTimezone);
        $em = $this->getModelManager()->getEntityManager($this->getClass());
        $userCompanyList = $em->getRepository('WhatsappBundle:UserCompany')->findByUser($user);
        $configuration = null;
        foreach ($userCompanyList as $value) {
            $configuration = $value->getConfiguration()->getId();
            break;
        }
        
        $this->datagridValues = array_merge(
                array(
            'startDate' => array(
                'type' => 1,
                'value' => array(
                    'start' => $ini->format("d-m-Y H:i:s"),
                    'end' => $fin->format("d-m-Y H:i:s")
                ),
            )
                ), $this->datagridValues
        );
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

}
