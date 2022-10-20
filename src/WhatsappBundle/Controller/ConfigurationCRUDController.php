<?php

namespace WhatsappBundle\Controller;

use Sonata\AdminBundle\Controller\CRUDController as Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Sonata\AdminBundle\Datagrid\ProxyQueryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use WhatsappBundle\Form\TicketChangeGroupOneType;
use WhatsappBundle\FormDataClass\TicketChangeGroup;

class ConfigurationCRUDController extends Controller {


    
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
        $em = $this->getDoctrine()->getManager();
        $configuration = $id;
        $alerts = $em->getRepository('WhatsappBundle:Alert')->findByConfiguration($configuration);
        if (count($alerts) > 0) {
            foreach ($alerts as $value) {
                $em->remove($value);
                $em->flush();
            }
        }
        $configurationAlertEmail = $em->getRepository('WhatsappBundle:ConfigurationAlertEmail')->findByConfiguration($configuration);
        if (count($configurationAlertEmail) > 0) {
            foreach ($configurationAlertEmail as $value) {
                $em->remove($value);
                $em->flush();
            }
        }
        $configurationAlertPhone = $em->getRepository('WhatsappBundle:ConfigurationAlertPhone')->findByConfiguration($configuration);
        if (count($configurationAlertPhone) > 0) {
            foreach ($configurationAlertPhone as $value) {
                $em->remove($value);
                $em->flush();
            }
        }
        $firstAnswerKeyword = $em->getRepository('WhatsappBundle:FirstAnswerKeyword')->findByConfiguration($configuration);
        if (count($firstAnswerKeyword) > 0) {
            foreach ($firstAnswerKeyword as $value) {
                $em->remove($value);
                $em->flush();
            }
        }
        $firstNoFollowKeyword = $em->getRepository('WhatsappBundle:FirstNoFollowKeyword')->findByConfiguration($configuration);
        if (count($firstNoFollowKeyword) > 0) {
            foreach ($firstNoFollowKeyword as $value) {
                $em->remove($value);
                $em->flush();
            }
        }
        $lastAnswerKeyword = $em->getRepository('WhatsappBundle:LastAnswerKeyword')->findByConfiguration($configuration);
        if (count($lastAnswerKeyword) > 0) {
            foreach ($lastAnswerKeyword as $value) {
                $em->remove($value);
                $em->flush();
            }
        }
        $ticket = $em->getRepository('WhatsappBundle:Ticket')->findByConfiguration($configuration);
        if (count($ticket) > 0) {
            foreach ($ticket as $value) {
                $em->remove($value);
                $em->flush();
            }
        }
        $message = $em->getRepository('WhatsappBundle:Message')->findByConfiguration($configuration);
        if (count($message) > 0) {
            foreach ($message as $value) {
                $em->remove($value);
                $em->flush();
            }
        }
        $whatsappGroup = $em->getRepository('WhatsappBundle:WhatsappGroup')->findByConfiguration($configuration);
        if (count($whatsappGroup) > 0) {
            foreach ($whatsappGroup as $value) {
                $em->remove($value);
                $em->flush();
            }
        }


        $solutionType = $em->getRepository('WhatsappBundle:SolutionType')->findByConfiguration($configuration);
        if (count($solutionType) > 0) {
            foreach ($solutionType as $value) {
                $em->remove($value);
                $em->flush();
            }
        }
        $supportMember = $em->getRepository('WhatsappBundle:SupportMember')->findByConfiguration($configuration);
        if (count($supportMember) > 0) {
            foreach ($supportMember as $value) {
                $em->remove($value);
                $em->flush();
            }
        }

        $ticketType = $em->getRepository('WhatsappBundle:TicketType')->findByConfiguration($configuration);
        if (count($ticketType) > 0) {
            foreach ($ticketType as $value) {
                $em->remove($value);
                $em->flush();
            }
        }
        $userCompany = $em->getRepository('WhatsappBundle:UserCompany')->findByConfiguration($configuration);
        if (count($userCompany) > 0) {
            foreach ($userCompany as $value) {
                $em->remove($value);
                $em->flush();
            }
        }
        $validationKeyword = $em->getRepository('WhatsappBundle:ValidationKeyword')->findByConfiguration($configuration);
        if (count($validationKeyword) > 0) {
            foreach ($validationKeyword as $value) {
                $em->remove($value);
                $em->flush();
            }
        }
                        
        return parent::deleteAction($id);
    }
    
    

}
