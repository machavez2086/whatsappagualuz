<?php

namespace WhatsappBundle\Admin;
use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\AdminBundle\Show\ShowMapper;
use PizarraBundle\Entity\Floor;


class UserCompanyAdmin extends Admin
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
            
            $this->trans('export.id') => 'id',
//            $this->trans('export.configuration') => 'configuration',
            $this->trans('export.user') => 'user',
            $this->trans('export.rol') => 'rol',
            // add your types
        );
    }
    
// Fields to be shown on create/edit forms
    protected function configureFormFields(FormMapper $formMapper)
    {
    
        $formMapper
            ->add('user', null, array('label' => 'Usuarios'))
            ->add('configuration', null, array('label' => 'Empresas'))
                
            ->add('rol', 'choice', array('label' => 'Rol', 
                'choices' =>array('Administrador' => 'Administrador', 'Usuario' => 'Usuario')))
        ;
        
    }

    // Fields to be shown on filter forms
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('configuration', null, array('label' => 'Empresa'))
            ->add('user', null, array('label' => 'Usuarios'))            
            ->add('rol', 'doctrine_orm_choice', array('label' => 'Rol', 'global_search' => false), 
            'choice', 
            array(
                'choices' => array(
                    'Administrador' => 'Administrador',  // The key (value1) will contain the actual value that you want to filter on
                    'Usuario' => 'Usuario',  // The 'Name Two' is the "display" name in the filter
            ), 
            'expanded' => false,    
            'multiple' => false))
            ;
                
    }

    // Fields to be shown on lists
    protected function configureListFields(ListMapper $listMapper)
    {
        unset($this->listModes['mosaic']);
        $listMapper
            ->addIdentifier('id', null, array('label' => 'Id'))
            ->add('user', null, array('label' => 'Usuarios'))
            ->add('configuration', null, array('label' => 'Empresas'))
            ->add('rol', null, array('label' => 'Rol'))
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
    
    
    public function preUpdate($userCompany)
    {
//        $userCompany->setConfigurations($userCompany->getConfigurations());
//        foreach ($userCompany->getConfiguration() as $score) {
//        $score->setEvent($object);
//    }
//        $ticket->setConfigurations(null);
//        dump($ticket->getConfigurations());die;
//        $em = $this->getModelManager()->getEntityManager($this->getClass());
//        $DM = $this->getConfigurationPool()->getContainer()->get('Doctrine')->getManager();
//    $uow = $DM->getUnitOfWork();
//    $OriginalEntityData = $uow->getOriginalEntityData( $ticket );
//           
//        $modifyEndDate = false;
//        $newEndDate = $ticket->getEndDate();
//        if($OriginalEntityData["endDate"] != $ticket->getEndDate())
//            $modifyEndDate = true;
//        
//        $ticket->recalculateResolutionTates();
//        if($modifyEndDate){
//            $ticket->recalculateResolutionByEndDate($newEndDate);
//        }
//        if($ticket->getTicketended()){
//           
//            $alerts = $em->getRepository('WhatsappBundle:Alert')->findByTicketIsEnabled($ticket);
//            foreach ($alerts as $alert) {
//                $alert->setOpen(false);
//                $em->persist($alert);
//            }
//            $em->flush();
//            if(!$ticket->getMinutesAnswerTime()){
//		$ticket->setMinutesAnswerTime($ticket->getMinutesSolutionTime());
//		}
//           
//        }
//       
    }
   
    private function is_super() {
         if ($this->getConfigurationPool()->getContainer()->get('security.context')->isGranted('ROLE_SUPER_ADMIN')) {
             return true;
         }
         return false;        
     }
     
     
   
}
