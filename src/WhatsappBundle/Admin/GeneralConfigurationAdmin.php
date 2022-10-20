<?php

namespace WhatsappBundle\Admin;
use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\AdminBundle\Show\ShowMapper;
use PizarraBundle\Entity\Floor;


class GeneralConfigurationAdmin extends Admin
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
    
// Fields to be shown on create/edit forms
    protected function configureFormFields(FormMapper $formMapper)
    {
    
        $formMapper
            ->with('Horario de trabajo')
            ->add('monday', null, array('label' => 'Lunes'))
            ->add('tuesday', null, array('label' => 'Martes'))
            ->add('wednesday', null, array('label' => 'Miércoles'))
            ->add('thursday', null, array('label' => 'Jueves'))
            ->add('friday', null, array('label' => 'Viernes'))
            ->add('saturday', null, array('label' => 'Sábado'))
            ->add('sunday', null, array('label' => 'Domingo'))
            ->add('restsCheduleStart', null, array('label' => 'Hora de inicio'))
            ->add('restsCheduleEnd', null, array('label' => 'Hora de fin'))
            ->add('restsCheduleAnswer', null, array('label' => 'Respuesta automática Horario de descanso'))
            ->end()
            ->with('Salida de emergencia')    
            ->add('isNotHere', null, array('label' => 'No estoy en estos momentos'))
            ->add('thereIsNotHereAnswer', null, array('label' => 'Respuesta automática No estoy en estoys momentos'))
            ->end()
//            ->add('users', null, array('label' => 'Company'))
//                ->add('users', 'sonata_type_collection', array('label' => 'Company',
//                            'by_reference' => false // true doesn't work neither
//                        ), array(
//                            'edit' => 'inline',
//                            'inline' => 'table'
//                        ))
        ;
        
    }
//
//    // Fields to be shown on filter forms
//    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
//    {
//        $datagridMapper
//                
//            ->with('Horario de trabajo')
//            ->add('monday', null, array('label' => 'Lunes'))
//            ->add('tuesday', null, array('label' => 'Martes'))
//            ->add('wednesday', null, array('label' => 'Miércoles'))
//            ->add('thursday', null, array('label' => 'Jueves'))
//            ->add('friday', null, array('label' => 'Viernes'))
//            ->add('saturday', null, array('label' => 'Sábado'))
//            ->add('sunday', null, array('label' => 'Martes'))
//            ->add('restsCheduleStart', null, array('label' => 'Hora de inicio'))
//            ->add('restsCheduleEnd', null, array('label' => 'Hora de fin'))
//            ->add('restsCheduleAnswer', null, array('label' => 'Respuesta automática Horario de descanso'))
//            ->end()
//            ->with('Salida de emergencia')    
//            ->add('isNotHere', null, array('label' => 'No estoy en estos momentos'))
//            ->add('thereIsNotHereAnswer', null, array('label' => 'Respuesta automática No estoy en estoys momentos'))
//            ->end()
//                
//        ;
//    }

    // Fields to be shown on lists
    protected function configureListFields(ListMapper $listMapper)
    {
        unset($this->listModes['mosaic']);
        $listMapper
            ->add('monday', null, array('label' => 'Lunes', 'editable' => true))
            ->add('tuesday', null, array('label' => 'Martes', 'editable' => true))
            ->add('wednesday', null, array('label' => 'Miércoles', 'editable' => true))
            ->add('thursday', null, array('label' => 'Jueves', 'editable' => true))
            ->add('friday', null, array('label' => 'Viernes', 'editable' => true))
            ->add('saturday', null, array('label' => 'Sábado', 'editable' => true))
            ->add('sunday', null, array('label' => 'Domingo', 'editable' => true))
            ->add('restsCheduleStart', null, array('label' => 'Hora de inicio', 'editable' => true))
            ->add('restsCheduleEnd', null, array('label' => 'Hora de fin', 'editable' => true))
            ->add('restsCheduleAnswer', null, array('label' => 'Respuesta automática Horario de descanso', 'editable' => true))
            ->add('isNotHere', null, array('label' => 'No estoy en estos momentos', 'editable' => true))
            ->add('thereIsNotHereAnswer', null, array('label' => 'Respuesta automática No estoy en estoys momentos', 'editable' => true))
        ;
//        $listMapper
//                ->add('_action', 'actions', array(
//            'label' => "Acciones",
//            'actions' => array(
////                'show' => array(),                
//                'edit' => array(),
//                
//            )
//        ));
    }
        protected function configureRoutes(RouteCollection $collection)
{
    // to remove a single route
    $collection->remove('delete');
//    $collection->remove('create');
    $collection->remove('edit');
    // OR remove all route except named ones
//    $collection->clearExcept(array('list', 'edit'));
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
   
   
//    public function prePersist($entity) {
//        $em = $this->getModelManager()->getEntityManager($this->getClass());
//        $isCreated = $em->getRepository('WhatsappBundle:Configuration')->findByCompany("Default");
//        if(count($isCreated) != 0 && $entity->getConfiguration() == null) {
//            $entity->setConfiguration($isCreated[0]);
//        }
//    }
}
