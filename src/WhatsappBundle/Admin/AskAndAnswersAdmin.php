<?php

namespace WhatsappBundle\Admin;
use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\AdminBundle\Show\ShowMapper;
use PizarraBundle\Entity\Floor;


class AskAndAnswersAdmin extends Admin
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
            $this->trans('export.name') => 'question',
            $this->trans('export.answer') => 'answer',
        );
    }
// Fields to be shown on create/edit forms
    protected function configureFormFields(FormMapper $formMapper)
    {
    
        $formMapper
            ->add('question', null, array('label' => 'Pregunta'))
            ->add('answer', null, array('label' => 'Respuesta'))
//            ->add('normalizedQuestion', null, array('label' => 'Pregunta normalizada'))
        ;
        
    }

    // Fields to be shown on filter forms
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('question', null, array('label' => 'Pregunta'))
            ->add('answer', null, array('label' => 'Respuesta'))
        ;
    }

    // Fields to be shown on lists
    protected function configureListFields(ListMapper $listMapper)
    {
        unset($this->listModes['mosaic']);
        $listMapper
            ->addIdentifier('question', null, array('label' => 'Palbara clave'))
            ->add('answer', null, array('label' => 'Respuesta'))
//            ->add('normalizedQuestion', null, array('label' => 'Pregunta normalizada'))
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
    
    protected function configureRoutes(RouteCollection $collection)
    {
        $collection->add('askprocess');
    }
    
    public function configureActionButtons($action, $object = null)
    {
        $list = parent::configureActionButtons($action, $object);

//        $list['askprocess']['template'] = 'WhatsappBundle:ADMIN:askprocess_button.html.twig';

        return $list;
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
    public function prePersist($entity) {
        $em = $this->getModelManager()->getEntityManager($this->getClass());
        $isCreated = $em->getRepository('WhatsappBundle:Configuration')->findByCompany("Default");
        if(count($isCreated) != 0 && $entity->getConfiguration() == null) {
            $entity->setConfiguration($isCreated[0]);
        }
    }
}