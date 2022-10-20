<?php

namespace WhatsappBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;
use WhatsappBundle\Repository\ConfigurationRepository;
use Sonata\AdminBundle\Route\RouteCollection;

class ObleaEnvioAdmin extends Admin {

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
            $this->trans('Fecha') => 'fechastr',
//            $this->trans('export.name') => 'name',
            $this->trans('export.noReclamo') => 'noReclamo',
            $this->trans('export.bulto') => 'bulto',
            $this->trans('export.paquetesRetirar') => 'paquetesRetirar',
            $this->trans('export.peso') => 'peso',
            $this->trans('export.entregaEnDomicilio') => 'entregaEnDomicilio',
            $this->trans('export.localidad') => 'localidad',
            $this->trans('export.provincia') => 'provincia',
            $this->trans('export.cp') => 'cp',
            $this->trans('export.destinatario') => 'destinatario',
            $this->trans('export.observaciones') => 'observaciones',
            $this->trans('export.product') => 'product',
            $this->trans('export.remitente') => 'remitente',
            $this->trans('export.remitenteContact') => 'remitenteContact',
        );
    }

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
                ->add('name', null, array('label' => 'Nombre'))
                ->add('noReclamo', null, array('label' => 'N° Reclamo'))
                ->add('bulto', null, array('label' => 'Bulto'))
                ->add('paquetesRetirar', null, array('label' => 'Paquetes de envío'))
                ->add('peso', null, array('label' => 'Peso'))
                ->add('entregaEnDomicilio', null, array('label' => 'Entrega en domicilio'))
                ->add('localidad', null, array('label' => 'Localidad'))
                ->add('provincia', null, array('label' => 'Provincia'))
                ->add('cp', null, array('label' => 'CP'))
                ->add('destinatario', null, array('label' => 'Destinatario'))
                ->add('observaciones', null, array('label' => ' Observaciones de entrega'))
                ->add('product', null, array('label' => 'Producto'))
                ->add('remitente', null, array('label' => 'Remitente'))
                ->add('remitenteContact', null, array('label' => 'Remitente datos de contacto'))
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
                ->add('name', null, array('label' => 'Nombre'))
                ->add('noReclamo', null, array('label' => 'N° Reclamo'))
                ->add('bulto', null, array('label' => 'Bulto'))
                ->add('paquetesRetirar', null, array('label' => 'Paquetes de envío'))
                ->add('peso', null, array('label' => 'Peso'))
                ->add('entregaEnDomicilio', null, array('label' => 'Entrega en domicilio'))
                ->add('localidad', null, array('label' => 'Localidad'))
                ->add('provincia', null, array('label' => 'Provincia'))
                ->add('cp', null, array('label' => 'CP'))
                ->add('destinatario', null, array('label' => 'Destinatario'))
                ->add('observaciones', null, array('label' => ' Observaciones de entrega'))
                ->add('product', null, array('label' => 'Producto'))
                ->add('remitente', null, array('label' => 'Remitente'))
                ->add('remitenteContact', null, array('label' => 'Remitente datos de contacto'))

        ;
    }

    // Fields to be shown on lists
    protected function configureListFields(ListMapper $listMapper) {
        unset($this->listModes['mosaic']);
        $listMapper
//            ->add('configuration', "text", array('label' => 'Empresa'))
                ->addIdentifier('name', null, array('label' => 'Nombre'))
                ->add('noReclamo', null, array('label' => 'N° Reclamo'))
                ->add('bulto', null, array('label' => 'Bulto'))
                ->add('paquetesRetirar', null, array('label' => 'Paquetes de envío'))
                ->add('peso', null, array('label' => 'Peso'))
                ->add('entregaEnDomicilio', null, array('label' => 'Entrega en domicilio'))
                ->add('localidad', null, array('label' => 'Localidad'))
                ->add('provincia', null, array('label' => 'Provincia'))
                ->add('cp', null, array('label' => 'CP'))
                ->add('destinatario', null, array('label' => 'Destinatario'))
                ->add('observaciones', null, array('label' => ' Observaciones de entrega'))
                ->add('product', null, array('label' => 'Producto'))
                ->add('remitente', null, array('label' => 'Remitente'))
                ->add('remitenteContact', null, array('label' => 'Remitente datos de contacto'))
        ;
        $listMapper
                ->add('_action', 'actions', array(
                    'label' => "Acciones",
                    'actions' => array(
//                'show' => array(),                
                        'edit' => array(),
                        'delete' => array(),
                        'preview' => array(
                            'template' => 'WhatsappBundle:SHOP:list__action_ticket_oblea_envio_print_preview.html.twig'
                        ),
                        'pdf' => array(
                            'template' => 'WhatsappBundle:SHOP:list__action_ticket_oblea_envio_print.html.twig'
                        ),
                        'xls' => array(
                            'template' => 'WhatsappBundle:SHOP:list__action_ticket_oblea_envio_export_xls.html.twig'
                        ),
                        //estod de abajo no porque hay que rescribir el layout para incluir css y js, Si esos elementos se incluyen en el elemento se cargan muchas veces en la pagina
//                        'print' => array(
//                            'template' => 'WhatsappBundle:SHOP:list__action_ticket_oblea_envio_print_print.html.twig'
//                        ),
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
        $em = $this->getModelManager()->getEntityManager($this->getClass());
        $isCreated = $em->getRepository('WhatsappBundle:Configuration')->findByCompany("Default");
        if (count($isCreated) != 0 && $entity->getConfiguration() == null) {
            $entity->setConfiguration($isCreated[0]);
        }
    }

    protected function configureRoutes(RouteCollection $collection) {
        // to remove a single route
        $collection->remove('delete');
        $collection->remove('create');
//        $collection->remove('edit');
        // OR remove all route except named ones
//    $collection->clearExcept(array('list', 'edit'));
    }

}
