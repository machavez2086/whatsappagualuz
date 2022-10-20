<?php

namespace WhatsappBundle\Controller;

use Sonata\AdminBundle\Controller\CRUDController as Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Sonata\AdminBundle\Datagrid\ProxyQueryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use WhatsappBundle\Form\MessageChangeTicketOneType;
use WhatsappBundle\FormDataClass\MessagesChangeTicket;

class MessageCRUDController extends Controller {

// In Acme/Controller/CRUDController.php

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
        $task = new MessagesChangeTicket();
        $group = $selectedModels[0]->getWhatsappGroup()->getId();
        $id = 0;
        if($selectedModels[0]->getTicket())
            $id = $selectedModels[0]->getTicket()->getId();
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
            if($value->getWhatsappGroup()->getId() != $group){
                $this->setFlash(
                            'sonata_flash_error', 'Los mensajes no son de un mismo grupo'
                    );
                return new RedirectResponse($this->admin->generateUrl('list', array('filter' => $this->admin->getFilterParameters())));

            }
            $task->addMessage($value->getId());
            
        }
        $dateIni = new \Datetime();
        if($selectedModels[0]->getTicket())
            $dateIni =clone $selectedModels[0]->getTicket()->getStartDate();
        $dateIni->modify('-1 day');
        $datefin = new \Datetime();
        if($selectedModels[0]->getTicket())
        $datefin = $selectedModels[0]->getTicket()->getStartDate();
        $datefin->modify('+1 day');
        $options = array();
        $options["group"] = $group;
        $options["id"] = $id;
        $options["dateIni"] = $dateIni;
        $options["datefin"] = $datefin;
        $user = $this->getUser();
        $userCompanies = $user->getConfigurations();
        $configurations = array();
        foreach ($userCompanies as $value) {
            $configurations[] = $value->getConfiguration()->getId();
        }
        $options["configuration"] = $configurations;
        $form = $this->createForm(new MessageChangeTicketOneType(), $task, $options);
        return $this->render('WhatsappBundle:ADMIN:change_messages_to_ticket.html.twig', array(
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
        if($this->is_super())
            return parent::batchActionDelete($query);
        $selectedModels = $query->execute();
        foreach ($selectedModels as $value) {
            $permition = $this->verify_if_me_have_admin_role_with_configuration($value->getId());
            if(!$permition){
                $this->setFlash(
                    'sonata_flash_error', 'Usted no tiene permisos para administrar estos elementos.'
                        );
                return new RedirectResponse($this->admin->generateUrl('list', array('filter' => $this->admin->getFilterParameters())));
            }
        }
        return parent::batchActionDelete($query);
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
     private function is_super() {
         if ($this->get('security.context')->isGranted('ROLE_SUPER_ADMIN')) {
             return true;
         }
         return false;        
     }
     
     public function getTicketsByGroupAction(Request $request) {
        $groupId = $request->get("groupId");
        $messageId = $request->get("messageId");
        $em = $this->getDoctrine()->getManager();
        
        $html = "";
        $selected = 0;
        if ($groupId) {
            $selectFirst = true;
            $group = $em->getRepository('WhatsappBundle:WhatsappGroup')->find($groupId);
            $message = $em->getRepository('WhatsappBundle:Message')->find($messageId);
            $tickets = $group->getTickets();
            if(count($tickets) > 0)
                $selected = $tickets[0]->getId();
            foreach ($tickets as $res) {
                if(!$messageId && $selectFirst){
                    $html .= "<option selected value=" . $res->getId() . ">" . $res->getName() . "</option>";
                    $selectFirst = false;
                    $selected = $res->getId();
                }
                else{
                    if($messageId){
                        if($res->getId() == $message->getTicket()->getId()){
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
     public function getGroupsByConfigurationAction(Request $request) {
        $configurationId = $request->get("configurationId");
        $messageId = $request->get("messageId");
        $em = $this->getDoctrine()->getManager();
        
        $html = "";
        $selected = 0;
        if ($configurationId) {
            $selectFirst = true;
            $configuration = $em->getRepository('WhatsappBundle:Configuration')->find($configurationId);
            $message = $em->getRepository('WhatsappBundle:Message')->find($messageId);
            $grupos = $configuration->getWhatsappGroups();
            if(count($grupos) > 0)
                $selected = $grupos[0]->getId();
            foreach ($grupos as $res) {
                if(!$messageId && $selectFirst){
                    $html .= "<option selected value=" . $res->getId() . ">" . $res->getName() . "</option>";
                    $selectFirst = false;
                    $selected = $res->getId();
                }
                else{
                    if($messageId){
                        if($res->getId() == $message->getWhatsappGroup()->getId()){
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
    
    
     public function getOptionalsGroupsByConfigurationAction(Request $request) {
        $configurationId = $request->get("configurationId");
        $em = $this->getDoctrine()->getManager();
        $html = '<option value=""></option>';
        $selected = "";
        if ($configurationId) {
            $configuration = $em->getRepository('WhatsappBundle:Configuration')->find($configurationId);
            $groups = $configuration->getWhatsappGroups();
            foreach ($groups as $res) {
                $html .= "<option value=" . $res->getId() . ">" . $res->getName() . "</option>";
            }
        }
        $result = array('html' => $html, 'value' => $selected);
        return new \Symfony\Component\HttpFoundation\JsonResponse($result);
    }
     public function getOptionalsTicketsByConfigurationAction(Request $request) {
        $configurationId = $request->get("configurationId");
        $em = $this->getDoctrine()->getManager();
        $html = '<option value=""></option>';
        $selected = "";
        if ($configurationId) {
            $configuration = $em->getRepository('WhatsappBundle:Configuration')->find($configurationId);
            $groups = $configuration->getTickets();
            foreach ($groups as $res) {
                $html .= "<option value=" . $res->getId() . ">" . $res->getName() . "</option>";
            }
        }
        $result = array('html' => $html, 'value' => $selected);
        return new \Symfony\Component\HttpFoundation\JsonResponse($result);
    }
     public function getOptionalsSolvedBySupportMemberByConfigurationAction(Request $request) {
        $configurationId = $request->get("configurationId");
        $em = $this->getDoctrine()->getManager();
        $html = '<option value=""></option>';
        $selected = "";
        if ($configurationId) {
            $configuration = $em->getRepository('WhatsappBundle:Configuration')->find($configurationId);
            $groups = $configuration->getSupportMembers();
            foreach ($groups as $res) {
                $html .= "<option value=" . $res->getId() . ">" . $res->getName() . "</option>";
            }
        }
        $result = array('html' => $html, 'value' => $selected);
        return new \Symfony\Component\HttpFoundation\JsonResponse($result);
    }
     public function getOptionalsClientMemberByConfigurationAction(Request $request) {
        $configurationId = $request->get("configurationId");
        $em = $this->getDoctrine()->getManager();
        $html = '<option value=""></option>';
        $selected = "";
//        if ($configurationId) {
//            $configuration = $em->getRepository('WhatsappBundle:Configuration')->find($configurationId);
//            $groups = $configuration->getClientMembers();
//            foreach ($groups as $res) {
//                $html .= "<option value=" . $res->getId() . ">" . $res->getName() . "</option>";
//            }
//        }
        $result = array('html' => $html, 'value' => $selected);
        return new \Symfony\Component\HttpFoundation\JsonResponse($result);
    }
     public function getOptionalsTicketByGroupAction(Request $request) {
        $whatsappGroupId = $request->get("whatsappGroupId");
        $em = $this->getDoctrine()->getManager();
        $html = '<option value=""></option>';
        $selected = "";
        if ($whatsappGroupId) {
            $group = $em->getRepository('WhatsappBundle:WhatsappGroup')->find($whatsappGroupId);
            $groups = $group->getTickets();
            foreach ($groups as $res) {
                $html .= "<option value=" . $res->getId() . ">" . $res->getName() . "</option>";
            }
        }
        $result = array('html' => $html, 'value' => $selected);
        return new \Symfony\Component\HttpFoundation\JsonResponse($result);
    }

}
