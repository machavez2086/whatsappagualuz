<?php

namespace WhatsappBundle\Controller;

use Sonata\AdminBundle\Controller\CRUDController as Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Sonata\AdminBundle\Datagrid\ProxyQueryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use WhatsappBundle\Form\TicketChangeGroupOneType;
use WhatsappBundle\FormDataClass\TicketChangeGroup;

class TicketCRUDController extends Controller {

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
        if($this->is_super())
            return parent::createAction();
        $user = $this->getUser();
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
    
    
    public function batchActionMerge(ProxyQueryInterface $selectedModelQuery) {
        if ($this->admin->isGranted('EDIT') === false || $this->admin->isGranted('DELETE') === false) {
            throw new AccessDeniedException();
        }

        $request = $this->get('request');
        $modelManager = $this->admin->getModelManager();

//        $target = $modelManager->find($this->admin->getClass(), $request->get('targetId'));
//
//        if ($target === null) {
//            $this->get('session')->setFlash('sonata_flash_info', 'flash_batch_merge_no_target');
//
//            return new RedirectResponse($this->admin->generateUrl('list', array('filter' => $this->admin->getFilterParameters())));
//        }

        $selectedModels = $selectedModelQuery->execute();
       
        // do the merge work here
        $task = new TicketChangeGroup();
        $id = $selectedModels[0]->getWhatsappGroup()->getId();
        foreach ($selectedModels as $value) {
            $permition = $this->verify_if_me_have_admin_role_with_configuration($value->getId());
            if($this->is_super())
                $permition = true;
            if(!$permition){
                $this->setFlash(
                    'sonata_flash_error', 'Usted no tiene permisos para administrar estos elementos.'
                        );
                return new RedirectResponse($this->admin->generateUrl('list', array('filter' => $this->admin->getFilterParameters())));
            }
//            if($value->getWhatsappGroup()->getId() != $group){
//                $this->setFlash(
//                            'sonata_flash_error', 'Los mensajes no son de un mismo grupo'
//                    );
//                return new RedirectResponse($this->admin->generateUrl('list', array('filter' => $this->admin->getFilterParameters())));
//
//            }
            $task->addPeticion($value->getId());
        }
        
//        $dateIni =clone $selectedModels[0]->getTicket()->getStartDate();
//        $dateIni->modify('-1 day');
//        $datefin = $selectedModels[0]->getTicket()->getStartDate();
//        $datefin->modify('+1 day');
        $options = array();
//        $options["group"] = $group;
        $options["id"] = $id;
//        $options["dateIni"] = $dateIni;
//        $options["datefin"] = $datefin;
        $user = $this->getUser();
        $configurations = array();
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
        }    
        $options["configuration"] = $configurations;
        
        $form = $this->createForm(new TicketChangeGroupOneType(), $task, $options);
        return $this->render('WhatsappBundle:ADMIN:change_ticket_to_group.html.twig', array(
            'form' => $form->createView(),
        ));
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
        if($this->is_super()){
            $selectedModels = $query->execute();
            foreach ($selectedModels as $value) {
                $this->get('whatsapp.sacspro.phonestatus')->sendMessageTicketDeleteOrNoFollow($value->getId(), true, $this->getUser()->getUsername());
                $this->get('whatsapp.sacspro.phonestatus')->copyTicketOnDelete($value->getId(), $this->getUser()->getUsername());
            }
            return parent::batchActionDelete($query);
        }
        $selectedModels = $query->execute();
        foreach ($selectedModels as $value) {
            $permition = $this->verify_if_me_have_admin_role_with_configuration($value->getId());
            if(!$permition){
                $this->setFlash(
                    'sonata_flash_error', 'Usted no tiene permisos para administrar estos elementos.'
                        );
                return new RedirectResponse($this->admin->generateUrl('list', array('filter' => $this->admin->getFilterParameters())));
            }
            $this->get('whatsapp.sacspro.phonestatus')->sendMessageTicketDeleteOrNoFollow($value->getId(), true, $this->getUser()->getUsername());
            $this->get('whatsapp.sacspro.phonestatus')->copyTicketOnDelete($value->getId(), $this->getUser()->getUsername());
            
        }
        
        return parent::batchActionDelete($query);
    }
    
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
//        dump("pepe");
        if($this->is_super()){      
            if ($this->getRestMethod() == 'DELETE') {
            // check the csrf token
                $this->get('whatsapp.sacspro.phonestatus')->copyTicketOnDelete($id, $this->getUser()->getUsername());
                $this->get('whatsapp.sacspro.phonestatus')->sendMessageTicketDeleteOrNoFollow($id, true, $this->getUser()->getUsername());
            }
            return parent::deleteAction($id);
        }
        $permition = $this->verify_if_me_have_admin_role_with_configuration($id);
        if(!$permition){
            $this->setFlash(
                'sonata_flash_error', 'Usted no tiene permisos para administrar este elemento.'
                    );
            return new RedirectResponse($this->admin->generateUrl('list', array('filter' => $this->admin->getFilterParameters())));
        }
//         dump("pepe2");
//         die;
        if ($this->getRestMethod() == 'DELETE') {
        // check the csrf token
            $this->get('whatsapp.sacspro.phonestatus')->copyTicketOnDelete($id, $this->getUser()->getUsername());
            $this->get('whatsapp.sacspro.phonestatus')->sendMessageTicketDeleteOrNoFollow($id, true, $this->getUser()->getUsername());
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
     
     
     public function getSupportMemberByConfigurationAction(Request $request) {
        $configurationId = $request->get("configurationId");
        $ticketId = $request->get("ticketId");
        $em = $this->getDoctrine()->getManager();
        
        $html = '<option value=""></option>';
        $selected = "";
        if ($configurationId) {
            $selectFirst = true;
            $configuration = $em->getRepository('WhatsappBundle:Configuration')->find($configurationId);
            $ticket = $em->getRepository('WhatsappBundle:Ticket')->find($ticketId);
            $grupos = $configuration->getSupportMembers();
//            if(count($grupos) > 0)
//                $selected = $grupos[0]->getId();
            foreach ($grupos as $res) {
                if(!$ticketId && $selectFirst){
                    $html .= "<option value=" . $res->getId() . ">" . $res->getName() . "</option>";
                    $selectFirst = false;
//                    $selected = $res->getId();
                }
                else{
                    if($ticketId){
                        if($res->getId() == $ticket->getSolvedBySupportMember()->getId()){
                            $html .= "<option selected value=" . $res->getId() . ">" . $res->getName() . "</option>";
                            $selected = $res->getId();
                        }
                        else{
                            $html .= "<option value=" . $res->getId() . ">" . $res->getName() . "</option>";
                        }
                    }
                    else{
                        $html .= "<option value=" . $res->getId() . ">" . $res->getName() . "</option>";
                    }
                }
            }
        }
//        dump($html)
        $result = array('html' => $html, 'value' => $selected);

        return new \Symfony\Component\HttpFoundation\JsonResponse($result);
    }
     public function getTicketTypeByConfigurationAction(Request $request) {
        $configurationId = $request->get("configurationId");
        $ticketId = $request->get("ticketId");
        $em = $this->getDoctrine()->getManager();
        
        $html = '<option value=""></option>';
        $selected = "";
        if ($configurationId) {
            $selectFirst = true;
            $configuration = $em->getRepository('WhatsappBundle:Configuration')->find($configurationId);
            $ticket = $em->getRepository('WhatsappBundle:Ticket')->find($ticketId);
            $ticketsType = $configuration->getTicketTypes();
//            if(count($ticketsType) > 0)
//                $selected = $ticketsType[0]->getId();
            foreach ($ticketsType as $res) {
                if(!$ticketId && $selectFirst){
                    $html .= "<option value=" . $res->getId() . ">" . $res->getName() . "</option>";
                    $selectFirst = false;
//                    $selected = $res->getId();
                }
                else{
                    if($ticketId){
                        if($res->getId() == $ticket->getTicketType()->getId()){
                            $html .= "<option selected value=" . $res->getId() . ">" . $res->getName() . "</option>";
                            $selected = $res->getId();
                        }
                        else{
                            $html .= "<option value=" . $res->getId() . ">" . $res->getName() . "</option>";
                        }
                    }
                    else{
                        $html .= "<option value=" . $res->getId() . ">" . $res->getName() . "</option>";
                    }
                }
            }
        }
//        dump($html)
        $result = array('html' => $html, 'value' => $selected);

        return new \Symfony\Component\HttpFoundation\JsonResponse($result);
    }
     public function getSolutionTypeByConfigurationAction(Request $request) {
        $configurationId = $request->get("configurationId");
        $ticketId = $request->get("ticketId");
        $em = $this->getDoctrine()->getManager();
        
        $html = '<option value=""></option>';
        $selected = "";
        if ($configurationId) {
            $selectFirst = true;
            $configuration = $em->getRepository('WhatsappBundle:Configuration')->find($configurationId);
            $ticket = $em->getRepository('WhatsappBundle:Ticket')->find($ticketId);
            $solutionTypes = $configuration->getSolutionTypes();
//            if(count($solutionTypes) > 0)
//                $selected = $solutionTypes[0]->getId();
            foreach ($solutionTypes as $res) {
                if(!$ticketId && $selectFirst){
                    $html .= "<option value=" . $res->getId() . ">" . $res->getName() . "</option>";
                    $selectFirst = false;
//                    $selected = $res->getId();
                }
                else{
                    if($ticketId){
                        if($res->getId() == $ticket->getSolutionType()->getId()){
                            $html .= "<option selected value=" . $res->getId() . ">" . $res->getName() . "</option>";
                            $selected = $res->getId();
                        }
                        else{
                            $html .= "<option value=" . $res->getId() . ">" . $res->getName() . "</option>";
                        }
                    }
                    else{
                        $html .= "<option value=" . $res->getId() . ">" . $res->getName() . "</option>";
                    }
                }
            }
        }
//        dump($html)
        $result = array('html' => $html, 'value' => $selected);

        return new \Symfony\Component\HttpFoundation\JsonResponse($result);
    }
     public function getOptionalsGroupsByConfigurationAction(Request $request) {
        $configurationId = $request->get("configurationId");
        $ticketId = $request->get("ticketId");
        $em = $this->getDoctrine()->getManager();
        
        $html = '<option value=""></option>';
        $selected = "";
        if ($configurationId) {
            $selectFirst = true;
            $configuration = $em->getRepository('WhatsappBundle:Configuration')->find($configurationId);
            $ticket = $em->getRepository('WhatsappBundle:Ticket')->find($ticketId);
            $groups = $configuration->getWhatsappGroups();
            foreach ($groups as $res) {
                $html .= "<option value=" . $res->getId() . ">" . $res->getName() . "</option>";
            }
        }
//        dump($html)
        $result = array('html' => $html, 'value' => $selected);

        return new \Symfony\Component\HttpFoundation\JsonResponse($result);
    }
     public function getOptionalsSolvedBySupportMemberByConfigurationAction(Request $request) {
        $configurationId = $request->get("configurationId");
        $ticketId = $request->get("ticketId");
        $em = $this->getDoctrine()->getManager();
        
        $html = '<option value=""></option>';
        $selected = "";
        if ($configurationId) {
            $selectFirst = true;
            $configuration = $em->getRepository('WhatsappBundle:Configuration')->find($configurationId);
            $ticket = $em->getRepository('WhatsappBundle:Ticket')->find($ticketId);
            $groups = $configuration->getSupportMembers();
            foreach ($groups as $res) {
                $html .= "<option value=" . $res->getId() . ">" . $res->getName() . "</option>";
            }
        }
//        dump($html)
        $result = array('html' => $html, 'value' => $selected);

        return new \Symfony\Component\HttpFoundation\JsonResponse($result);
    }
    
     public function getOptionalsTicketTypeByConfigurationAction(Request $request) {
        $configurationId = $request->get("configurationId");
        $ticketId = $request->get("ticketId");
        $em = $this->getDoctrine()->getManager();
        
        $html = '<option value=""></option>';
        $selected = "";
        if ($configurationId) {
            $selectFirst = true;
            $configuration = $em->getRepository('WhatsappBundle:Configuration')->find($configurationId);
            $ticket = $em->getRepository('WhatsappBundle:Ticket')->find($ticketId);
            $groups = $configuration->getTicketTypes();
            foreach ($groups as $res) {
                $html .= "<option value=" . $res->getId() . ">" . $res->getName() . "</option>";
            }
        }
//        dump($html)
        $result = array('html' => $html, 'value' => $selected);

        return new \Symfony\Component\HttpFoundation\JsonResponse($result);
    }
     public function getOptionalsSolutionTypeByConfigurationAction(Request $request) {
        $configurationId = $request->get("configurationId");
        $ticketId = $request->get("ticketId");
        $em = $this->getDoctrine()->getManager();
        
        $html = '<option value=""></option>';
        $selected = "";
        if ($configurationId) {
            $selectFirst = true;
            $configuration = $em->getRepository('WhatsappBundle:Configuration')->find($configurationId);
            $ticket = $em->getRepository('WhatsappBundle:Ticket')->find($ticketId);
            $groups = $configuration->getSolutionTypes();
            foreach ($groups as $res) {
                $html .= "<option value=" . $res->getId() . ">" . $res->getName() . "</option>";
            }
        }
//        dump($html)
        $result = array('html' => $html, 'value' => $selected);

        return new \Symfony\Component\HttpFoundation\JsonResponse($result);
    }
    
     public function getGroupsByConfigurationAction(Request $request) {
        $configurationId = $request->get("configurationId");
        $ticketId = $request->get("ticketId");
        $em = $this->getDoctrine()->getManager();
        
        $html = "";
        $selected = 0;
        if ($configurationId) {
            $selectFirst = true;
            $configuration = $em->getRepository('WhatsappBundle:Configuration')->find($configurationId);
            $ticket = $em->getRepository('WhatsappBundle:Ticket')->find($ticketId);
            $grupos = $configuration->getWhatsappGroups();
            if(count($grupos) > 0)
                $selected = $grupos[0]->getId();
            foreach ($grupos as $res) {
                if(!$ticketId && $selectFirst){
                    $html .= "<option selected value=" . $res->getId() . ">" . $res->getName() . "</option>";
                    $selectFirst = false;
                    $selected = $res->getId();
                }
                else{
                    if($ticketId){
                        if($res->getId() == $ticket->getWhatsappGroup()->getId()){
                            $html .= "<option selected value=" . $res->getId() . ">" . $res->getName() . "</option>";
                            $selected = $res->getId();
                        }
                        else{
                            $html .= "<option value=" . $res->getId() . ">" . $res->getName() . "</option>";
                        }
                    }
                    else{
                        $html .= "<option value=" . $res->getId() . ">" . $res->getName() . "</option>";
                    }
                }
            }
        }
        $result = array('html' => $html, 'value' => $selected);

        return new \Symfony\Component\HttpFoundation\JsonResponse($result);
    }
    
    protected function preDelete(Request $request, $object)
    {
        
    }

}
