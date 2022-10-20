<?php

namespace WhatsappBundle\Controller;

use Sonata\AdminBundle\Controller\CRUDController as Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Sonata\AdminBundle\Datagrid\ProxyQueryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use WhatsappBundle\Form\TicketChangeGroupOneType;
use WhatsappBundle\FormDataClass\TicketChangeGroup;

class WhatsappGenericCRUDController extends Controller {


    /**
     * Create action.
     *
     * @param Request $request
     *
     * @return Response
     *
     * @throws AccessDeniedException If access is not granted
     */
    public function createAction()
    {
        $user = $this->getUser();
        if($this->is_super())
            return parent::createAction();
        $em = $this->getDoctrine()->getManager();
        $userCompanyList = $em->getRepository('WhatsappBundle:UserCompany')->findByUser($user);
        if (count($userCompanyList) > 0) {
            foreach ($userCompanyList as $value) {
                if ($value->getRol() == "Administrador")
                    return parent::createAction();
                
            }
        }
        $this->setFlash(
            'sonata_flash_error', 'Usted no tiene permisos para administrar este elemento.'
                );
        return new RedirectResponse($this->admin->generateUrl('list', array('filter' => $this->admin->getFilterParameters())));
        
        
    }
    public function editAction($id = null){
        if($this->is_super())
            return parent::editAction($id);
        $permition = $this->verify_if_me_have_admin_role_with_configuration($id);
        if(!$permition){
            $this->setFlash(
                'sonata_flash_error', 'Usted no tiene permisos para administrar este elemento.'
                    );
            return new RedirectResponse($this->admin->generateUrl('list', array('filter' => $this->admin->getFilterParameters())));
        }

        return parent::editAction($id);
    }
    /**
     * Execute a batch delete.
     *
     * @param ProxyQueryInterface $query
     *
     * @return RedirectResponse
     *
     * @throws AccessDeniedException If access is not granted
     */
    public function batchActionDelete(ProxyQueryInterface $query)
    {
        if($this->is_super())
            return parent::batchActionDelete($query);
        
        $selectedModels = $query->execute();
        foreach ($selectedModels as $value) {
            $permition = $this->verify_if_me_have_admin_role_with_configuration($value->getId());
            if(!$permition){
                $this->setFlash(
                    'sonata_flash_error', 'Usted no tiene permisos para administrar este elemento.'
                        );
                return new RedirectResponse($this->admin->generateUrl('list', array('filter' => $this->admin->getFilterParameters())));
            }
        }
        return parent::batchActionDelete($query);
    }
//    public function batchActionMerge(ProxyQueryInterface $selectedModelQuery) {
//        $permition = $this->verify_if_me_have_admin_role_with_configuration($id);
//        if(!$permition){
//            $this->setFlash(
//                'sonata_flash_error', 'Usted no tiene permisos para administrar este elemento.'
//                    );
//            return new RedirectResponse($this->admin->generateUrl('list', array('filter' => $this->admin->getFilterParameters())));
//        }
//        return parent::batchActionMerge($selectedModelQuery);
//    }
    /**
     * Delete action.
     *
     * @param int|string|null $id
     *
     * @return Response|RedirectResponse
     *
     * @throws NotFoundHttpException If the object does not exist
     * @throws AccessDeniedException If access is not granted
     */
    public function deleteAction($id)
    {
        if($this->is_super())
            return parent::deleteAction($id);
        
        $permition = $this->verify_if_me_have_admin_role_with_configuration($id);
        if(!$permition){
            $this->setFlash(
                'sonata_flash_error', 'Usted no tiene permisos para administrar este elemento.'
                    );
            return new RedirectResponse($this->admin->generateUrl('list', array('filter' => $this->admin->getFilterParameters())));
        }
        return parent::deleteAction($id);
    }
    
    
     /**
     * @param string $action
     * @param string $value
     */
    protected function setFlash($action, $value)
    {
        $this->get('session')->getFlashBag()->set($action, $value);
    }
    
    private function verify_if_me_have_admin_role_with_configuration($id) {
//        $id = $request->get($this->admin->getIdParameter());
        $object = $this->admin->getObject($id);
        $configuration = $object->getConfiguration();
//        dump($object);die;
        $user = $this->getUser();
        $em = $this->getDoctrine()->getManager();
        $userCompanyList = $em->getRepository('WhatsappBundle:UserCompany')->findByConfigurationByUser($configuration, $user);
        if (count($userCompanyList) > 0) {
            foreach ($userCompanyList as $value) {
                if ($value->getRol() == "Administrador")
                    return true;
            }
        }
        return false;
    }
    
    private function is_super() {
         if ($this->get('security.context')->isGranted('ROLE_SUPER_ADMIN')) {
             return true;
         }
         return false;        
     }

}
