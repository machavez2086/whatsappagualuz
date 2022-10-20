<?php

namespace WhatsappBundle\Admin;
use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\AdminBundle\Show\ShowMapper;
use PizarraBundle\Entity\Floor;


class ConfigurationAdmin extends Admin
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
            $this->trans('export.company') => 'company',
            $this->trans('export.prefix') => 'prefix',
            $this->trans('export.enabledText') => 'enabledText',
            $this->trans('export.owner') => 'owner',
            $this->trans('export.minutesAnswerAlert') => 'minutesAnswerAlert',
            $this->trans('export.minutesResolutionAlert') => 'minutesResolutionAlert',
            $this->trans('export.timeZone') => 'timeZone',
            $this->trans('export.dayEndWeek') => 'dayEndWeek',
            $this->trans('export.hourEndWeek') => 'hourEndWeek',
        );
    }
    
// Fields to be shown on create/edit forms
    protected function configureFormFields(FormMapper $formMapper)
    {
    
        $formMapper
            ->add('company', null, array('required' => true, 'label' => 'Nombre de empresa'))
//            ->add('prefix', null, array( 'required' => true, 'label' =>"Sufijos de grupos",))
            ->add('timeZone', "timezone", array('label' => 'Zona Horaria', 'empty_value' => "America/Mexico_City"))
            ->add('enabled', null, array('label' => 'Habilitado'))
//            ->add('demo', null, array('label' => 'Demo'))
            ->add('owner', null, array('label' => 'Dueño'))
            ->add('minutesAnswerAlert', null, array('required' => true, 'label' => 'Minutos para alerta de respuesta'))
            ->add('minutesResolutionAlert', null, array('required' => true, 'label' => 'Minutos para alerta de resolución'))
            ->add('isAlertMail', null, array('label' => 'Alerta por correo'))
            ->add('isAlertCall', null, array('label' => 'Llamada de alerta'))
            ->add('isAlertSms', null, array('label' => 'Mensaje al telefono SMS'))
            ->add('dayEndWeek', 'choice', array('required' => true, 'label' => 'Último día de la semana',
                'choices' =>array('monday' => 'Lunes' , 'tuesday' => 'Martes', 'wednesday' => 'Miércoles', 'thursday' => 'Jueves', 'friday' => 'Viernes', 'saturday' => 'Sábado', 'sunday' => 'Domingo')
                ))
            ->add('hourEndWeek', null, array('label' => 'Hora del último día de la semana'))
            
//            ->add('users', null, array('label' => 'Company'))
//                ->add('users', 'sonata_type_collection', array('label' => 'Company',
//                            'by_reference' => false // true doesn't work neither
//                        ), array(
//                            'edit' => 'inline',
//                            'inline' => 'table'
//                        ))
        ;
        
    }

    // Fields to be shown on filter forms
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('company', null, array('label' => 'Nombre de empresa'))
            ->add('minutesAnswerAlert', null, array('label' => 'Minutos para alerta de respuesta'))
            ->add('minutesResolutionAlert', null, array('label' => 'Minutos para alerta de resolución'))
            ->add('owner', null, array('label' => 'Dueño'))
            ->add('dayEndWeek', null, array('required' => true, 'label' => 'Último día de la semana'))
            ->add('hourEndWeek', null, array('label' => 'Hora del último día de la semana'))
        ;
    }

    // Fields to be shown on lists
    protected function configureListFields(ListMapper $listMapper)
    {
        unset($this->listModes['mosaic']);
        $listMapper
            ->addIdentifier('id', null, array('label' => 'id'))
            ->addIdentifier('company', null, array('label' => 'Nombre de empresa'))
//            ->addIdentifier('prefix', null, array('label' => 'Sufijos de grupos'))
            ->add('enabled', null, array('label' => 'Habilitado', 'editable' => true))
            ->add('owner', null, array('label' => 'Dueño'))
//            ->add('demo', null, array('label' => 'Demo', 'editable' => true))
            ->add('minutesAnswerAlert', null, array('label' => 'Minutos para alerta de respuesta'))
            ->add('minutesResolutionAlert', null, array('label' => 'Minutos para alerta de resolución'))
            ->add('timeZone', null, array('label' => 'Zona Horaria'))
            ->add('dayEndWeek', null, array('required' => true, 'label' => 'Último día de la semana'))
            ->add('hourEndWeek', null, array('label' => 'Hora del último día de la semana'))
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
    
     public function postPersist($configuration) {
        $em = $this->getModelManager()->getEntityManager($this->getClass());
        $configuration->setPrefix("-Tre".$configuration->getId());
        $em->persist($configuration);
        $em->flush();
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
            case 'edit':
//                dump($name);die;
                return 'WhatsappBundle:CRUD:base_edit.html.twig';
                break;
            default:
                return parent::getTemplate($name);
                break;
        }
    }
   
    private function is_super() {
         if ($this->getConfigurationPool()->getContainer()->get('security.context')->isGranted('ROLE_SUPER_ADMIN')) {
             return true;
         }
         return false;        
     }
   
}
