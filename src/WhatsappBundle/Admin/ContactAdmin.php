<?php

namespace WhatsappBundle\Admin;
use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\AdminBundle\Show\ShowMapper;
use PizarraBundle\Entity\Floor;


class ContactAdmin extends Admin
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
     public function getExportFields()
    {
        return array(
            
            $this->trans('export.message') => 'message',
            $this->trans('export.name') => 'name',            
            $this->trans('export.email') => 'email',
            // add your types
        );
    }
    
// Fields to be shown on create/edit forms
    protected function configureFormFields(FormMapper $formMapper)
    {
    
        $formMapper
            ->add('message', null, array('label' => 'Mensaje'))
            ->add('name', null, array('label' => 'Nombre'))
            ->add('email', null, array('label' => 'Correo'))
        ;
        
    }

    // Fields to be shown on filter forms
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
                
           ->add('message', null, array('label' => 'Mensaje'))
            ->add('name', null, array('label' => 'Nombre'))
            ->add('email', null, array('label' => 'Correo'))
                
        ;
    }

    // Fields to be shown on lists
    protected function configureListFields(ListMapper $listMapper)
    {
        unset($this->listModes['mosaic']);
        $listMapper
            ->addIdentifier('message', null, array('label' => 'Mensaje'))
            ->add('name', null, array('label' => 'Nombre'))
            ->add('email', null, array('label' => 'Correo'))
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