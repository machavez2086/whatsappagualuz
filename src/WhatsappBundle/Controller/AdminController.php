<?php

namespace WhatsappBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use WhatsappBundle\Form\MessageToTicketFormType;
use WhatsappBundle\Form\FechaRangeType;
use WhatsappBundle\Entity\Message;
use WhatsappBundle\Entity\Ticket;
use WhatsappBundle\Entity\Configuration;
use WhatsappBundle\Form\MessageChangeTicketType;
use WhatsappBundle\Form\TicketChangeGroupType;
use WhatsappBundle\FormDataClass\MessagesChangeTicket;
use WhatsappBundle\FormDataClass\TicketChangeGroup;
use WhatsappBundle\Form\ConfigurationType;
use WhatsappBundle\Form\UserCompanyType;
use WhatsappBundle\Form\UserCompanyEditType;
use WhatsappBundle\Form\DeleteUserCompanyType;
use WhatsappBundle\Form\ConfirmType;
use WhatsappBundle\Form\AlertEmailType;
use WhatsappBundle\Form\AlertPhoneType;
use WhatsappBundle\Form\UserType;
use WhatsappBundle\Form\Type\TicketSendendAreaType;
use WhatsappBundle\Form\Type\AnwerTicketSendendAreaType;
use WhatsappBundle\Form\Type\ProductReceptedType;
use WhatsappBundle\Form\Type\ProductSendedType;
use WhatsappBundle\Form\Type\ObleaRetiroType;
use WhatsappBundle\Form\Type\ObleaEnvioType;
use WhatsappBundle\Form\Type\MensajeToConversationType;
use WhatsappBundle\Entity\UserCompany;
use WhatsappBundle\Entity\ConfigurationAlertEmail;
use WhatsappBundle\Entity\ConfigurationAlertPhone;
use WhatsappBundle\Entity\SupportMember;
use WhatsappBundle\Entity\ClientMember;
use WhatsappBundle\Entity\WhatsappGroup;
use WhatsappBundle\Entity\Peticion;
use WhatsappBundle\Entity\TicketSendedArea;
use WhatsappBundle\Entity\ProductRecepted;
use WhatsappBundle\Entity\ProductSended;
use WhatsappBundle\Entity\ObleaEnvio;
use WhatsappBundle\Entity\ObleaRetiro;
use WhatsappBundle\Entity\MessageSended;
use WhatsappBundle\Entity\Conversation;
use Application\Sonata\UserBundle\Entity\User;
use Knp\Bundle\SnappyBundle\Snappy\Response\PdfResponse;
use Gos\Bundle\WebSocketBundle\Pusher\PusherInterface;
use Gos\Bundle\WebSocketBundle\Pusher\Amqp\AmqpPusher;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

use Symfony\Component\HttpFoundation\JsonResponse;

class AdminController extends Controller {

    /**
     * @Route("/admin/show_peticion_panel/{peticion}", name="show_peticion_panel" )
     */
    public function showPeticionPanel(Peticion $peticion) {
        $request = $this->getRequest();
        $activeTab = $request->query->get('active-tab');
        if($activeTab == null){
            $activeTab = "generaldata";
        }
//        dump($activeTab);die;
        $id = $peticion->getConfiguration()->getId();
        if (!$this->verify_if_me_have_admin_role_with_configuration($id)) {
            $this->setFlash(
                    'sonata_flash_error', 'No tiene permisos suficientes para realizar la acción ejecutada.'
            );
            return $this->redirectToRoute('admin_whatsapp_peticion_list');
        }
        $admin_pool = $this->get('sonata.admin.pool');
        return $this->render('WhatsappBundle:SHOP:peticion_panel.html.twig', array(
                    'admin_pool' => $admin_pool,
//                    'admin' => $admin_pool,
                    'peticion' => $peticion,
                    'myRole' => "Administrador",
                    'activeTab' => $activeTab,
        ));
    }

    /**
     * @Route("/admin/new_ticket_sendend_area_user/{peticion}", name="new_ticket_sendend_area_user" )
     */
    public function newTicketSendendAreaUserAction(Peticion $peticion) {
        $id = $peticion->getConfiguration()->getId();
        if (!$this->verify_if_me_have_admin_role_with_configuration($id)) {
            $this->setFlash(
                    'sonata_flash_error', 'No tiene permisos suficientes para realizar la acción ejecutada.'
            );
            return $this->redirectToRoute('show_peticion_panel', array("peticion" => $peticion->getId()));
        }
        $request = $this->getRequest();
        $admin_pool = $this->get('sonata.admin.pool');
        $em = $this->getDoctrine()->getManager();
        $ticketSendedArea = new TicketSendedArea();
        $ticketSendedArea->setCreatedBy($this->getUser());
        $form = $this->createForm(new TicketSendendAreaType(), $ticketSendedArea);
        //$ticket = $message->getTicket();
        if ($request->getMethod() == 'POST') {
            $form->bind($request);
            if ($form->isValid()) {
                $configuraion = $peticion->getConfiguration();
                $configId = $configuraion->getId();
                $timezone = $em->getRepository('WhatsappBundle:Configuration')->getTimezoneFromConfiguration($configId);
                $timezone = $timezone[0]["timeZone"];
                $ticketSendedArea->setConfiguration($configuraion);
                $ticketSendedArea->setPeticion($peticion);

//                dump($ticketSendedArea->getArea());
//                die;
                $em->persist($ticketSendedArea);
                $em->flush();
                $userAreaList = $em->getRepository('WhatsappBundle:AreaUser')->findByArea($ticketSendedArea->getArea());
                foreach ($userAreaList as $value) {
                    //TODO enviar notificacion por correo.
                    $message = new \Swift_Message('Mensaje recibido Call Center');
                    $message
                            ->setFrom('busquedas@gmail.com')
                            ->setReplyTo("busquedas@gmail.com")
                            ->setSender("busquedas@gmail.com")
                            ->setTo($value->getUser()->getEmail())
                            ->setSubject("Call Center: ".$peticion->getNroReclamo())
                            ->setBody(
                                    $this->renderView(
                                            'WhatsappBundle:SHOP:email_new_ticket_sendend_area.html.twig', array(
                                        "peticion" => $peticion,
                                        "message" => $ticketSendedArea->getAsk()
                                            )
                                    ), 'text/html'
                            )
                    ;
                    $this->get('mailer')->send($message);
                }
                $this->addFlash('sonata_flash_success', 'Mensaje enviado al area.');
                return $this->redirectToRoute('show_peticion_panel', array("peticion" => $peticion->getId(), "active-tab" => "messagearea"));
            }
        }
        return $this->render('WhatsappBundle:SHOP:new_ticket_sendend_area.html.twig', array(
                    'form' => $form->createView(),
                    'admin_pool' => $admin_pool,
                    'peticion' => $peticion,
        ));
    }

    /**
     * @Route("/admin/edit_ticket_sendend_area_user/{ticketSendedArea}", name="edit_ticket_sendend_area_user" )
     */
    public function editTicketSendendAreaUserAction(TicketSendedArea $ticketSendedArea) {
        $peticion = $ticketSendedArea->getPeticion();
        $id = $ticketSendedArea->getConfiguration()->getId();
        if (!$this->verify_if_me_have_admin_role_with_configuration($id)) {
            $this->setFlash(
                    'sonata_flash_error', 'No tiene permisos suficientes para realizar la acción ejecutada.'
            );
            return $this->redirectToRoute('show_peticion_panel', array("peticion" => $peticion->getId()));
        }
        $request = $this->getRequest();
        $admin_pool = $this->get('sonata.admin.pool');
        $em = $this->getDoctrine()->getManager();
//        $ticketSendedArea = new TicketSendedArea();
        $form = $this->createForm(new TicketSendendAreaType(), $ticketSendedArea);
        //$ticket = $message->getTicket();
        if ($request->getMethod() == 'POST') {
            $form->bind($request);
            if ($form->isValid()) {
                $configuraion = $peticion->getConfiguration();
//                $configId = $configuraion->getId();
//                $timezone = $em->getRepository('WhatsappBundle:Configuration')->getTimezoneFromConfiguration($configId);
//                $timezone = $timezone[0]["timeZone"];
                $ticketSendedArea->setConfiguration($configuraion);
                $ticketSendedArea->setUpdatedBy($this->getUser());
                $ticketSendedArea->setPeticion($peticion);
                $em->persist($ticketSendedArea);
                $em->flush();
                //TODO enviar notificacion por correo.
                $this->addFlash('sonata_flash_success', 'Mensaje enviado al area.');
                return $this->redirectToRoute('show_peticion_panel', array("peticion" => $peticion->getId()));
            }
        }
        return $this->render('WhatsappBundle:SHOP:edit_ticket_sendend_area.html.twig', array(
                    'form' => $form->createView(),
                    'admin_pool' => $admin_pool,
                    'peticion' => $peticion,
                    'ticketSendedArea' => $ticketSendedArea,
        ));
    }

    /**
     * @Route("/admin/answer_ticket_sendend_area_user/{ticketSendedArea}", name="answer_ticket_sendend_area_user" )
     */
    public function answerTicketSendendAreaUserAction(TicketSendedArea $ticketSendedArea) {
        $peticion = $ticketSendedArea->getPeticion();
        $em = $this->getDoctrine()->getManager();
        $id = $ticketSendedArea->getConfiguration()->getId();
        if (!$this->verify_if_me_have_admin_role_with_configuration($id)) {
            $this->setFlash(
                    'sonata_flash_error', 'No tiene permisos suficientes para realizar la acción ejecutada.'
            );
            return $this->redirectToRoute('show_peticion_panel', array("peticion" => $peticion->getId()));
        }
        //Verificar si el usuario es miembro del area y puede responder
        $userAreaList = $em->getRepository('WhatsappBundle:AreaUser')->findByUserByArea($this->getUser(), $ticketSendedArea->getArea());
        if (count($userAreaList) == 0) {
            $this->setFlash(
                    'sonata_flash_error', 'No tiene permisos suficientes para realizar la acción ejecutada.'
            );
            return $this->redirectToRoute('show_peticion_panel', array("peticion" => $peticion->getId()));
        }
        $request = $this->getRequest();
        $admin_pool = $this->get('sonata.admin.pool');

//        $ticketSendedArea = new TicketSendedArea();
        $form = $this->createForm(new AnwerTicketSendendAreaType(), $ticketSendedArea);
        //$ticket = $message->getTicket();
        if ($request->getMethod() == 'POST') {
            $form->bind($request);
            if ($form->isValid()) {
                $ticketSendedArea->setUser($this->getUser());
                $ticketSendedArea->setAnsweredAt(new \DateTime("now"));
                
                $em->persist($ticketSendedArea);
                $em->flush();
                $messageTxt = "Se ha dado respuesta a una pregunta. (Pregunta: ".$ticketSendedArea->getAsk().") (Respuesta: ".$ticketSendedArea->getAnswer().")";
                $message = new \Swift_Message('Mensaje recibido Call Center');
                    $message
                            ->setTo("busquedas@gmail.com")
                            ->setSender("machavez2086@gmail.com")
//                            ->setTo("busquedas@gmail.com")
                            ->setSubject("Call Center: Respuesta de Area. Ticket ".$peticion->getNroReclamo())
                            ->setBody(
                                    $this->renderView(
                                            'WhatsappBundle:SHOP:email_new_ticket_sendend_area.html.twig', array(
                                        "peticion" => $peticion,
                                        "message" => $messageTxt
                                            )
                                    ), 'text/html'
                            )
                    ;
                    $this->get('mailer')->send($message);
                $this->addFlash('sonata_flash_success', 'Mensaje enviado al area.');
                return $this->redirectToRoute('show_peticion_panel', array("peticion" => $peticion->getId(), "active-tab" => "messagearea"));
            }
        }
        return $this->render('WhatsappBundle:SHOP:answer_ticket_sendend_area.html.twig', array(
                    'form' => $form->createView(),
                    'admin_pool' => $admin_pool,
                    'peticion' => $peticion,
                    'ticketSendedArea' => $ticketSendedArea,
        ));
    }

    /**
     * @Route("/admin/show_peticion_panel/delete_ticket_sendend_area/{ticketSendedArea}", name="delete_ticket_sendend_area" )
     */
    public function deleteTicketSendendAreaAction(TicketSendedArea $ticketSendedArea) {
        $request = $this->getRequest();
        $peticion = $ticketSendedArea->getPeticion();
        $admin_pool = $this->get('sonata.admin.pool');
//        $alertEmailList = $em->getRepository('WhatsappBundle:UserCompany')->findByConfigurationByUser($alertEmail, $user);
        $em = $this->getDoctrine()->getManager();
        $configuration = $ticketSendedArea->getConfiguration()->getId();
        if (!$this->verify_if_me_have_admin_role_with_configuration($configuration)) {
            $this->setFlash(
                    'sonata_flash_error', 'No tiene permisos suficientes para realizar la acción ejecutada.'
            );
            return $this->redirectToRoute('my_companies', array("action" => "list"));
        }

        $form = $this->createForm(new DeleteUserCompanyType(), $ticketSendedArea);

        if ($request->getMethod() == 'POST') {
            $form->bind($request);
            if ($form->isValid()) {
                $em->remove($ticketSendedArea);
                $em->flush();
                $this->setFlash(
                        'sonata_flash_success', 'Elemento eliminado correctamente.'
                );

                return $this->redirectToRoute('show_peticion_panel', array("peticion" => $peticion->getId()));
            }
        }
        return $this->render('WhatsappBundle:SHOP:delete_ticket_sendend_area_confirm.html.twig', array(
                    'admin_pool' => $admin_pool,
                    'form' => $form->createView(),
                    'ticketSendedArea' => $ticketSendedArea,
        ));
    }

    /**
     * @Route("/admin/new_ticket_product_recepted/{peticion}", name="new_ticket_product_recepted" )
     */
    public function newTicketProductRecepted(Peticion $peticion) {
        $id = $peticion->getConfiguration()->getId();
        if (!$this->verify_if_me_have_admin_role_with_configuration($id)) {
            $this->setFlash(
                    'sonata_flash_error', 'No tiene permisos suficientes para realizar la acción ejecutada.'
            );
            return $this->redirectToRoute('show_peticion_panel', array("peticion" => $peticion->getId()));
        }
        $request = $this->getRequest();
        $admin_pool = $this->get('sonata.admin.pool');
        $em = $this->getDoctrine()->getManager();
        $productRecepted = new ProductRecepted();
        $productRecepted->setCreatedBy($this->getUser());
        $form = $this->createForm(new ProductReceptedType(), $productRecepted);
        //$ticket = $message->getTicket();
        if ($request->getMethod() == 'POST') {
            $form->bind($request);
            if ($form->isValid()) {
                $configuraion = $peticion->getConfiguration();
//                $configId = $configuraion->getId();
//                $timezone = $em->getRepository('WhatsappBundle:Configuration')->getTimezoneFromConfiguration($configId);
//                $timezone = $timezone[0]["timeZone"];
                $productRecepted->setConfiguration($configuraion);
                $productRecepted->setPeticion($peticion);
                $em->persist($productRecepted);
                $em->flush();
                //TODO enviar notificacion por correo.
                $this->addFlash('sonata_flash_success', 'Registrado la recepción del producto.');
                return $this->redirectToRoute('show_peticion_panel', array("peticion" => $peticion->getId(), "active-tab"=> "productrecepted"));
            }
        }
        return $this->render('WhatsappBundle:SHOP:new_ticket_product_recepted.html.twig', array(
                    'form' => $form->createView(),
                    'admin_pool' => $admin_pool,
                    'peticion' => $peticion,
        ));
    }

    /**
     * @Route("/admin/edit_ticket_product_recepted/{productRecepted}", name="edit_ticket_product_recepted" )
     */
    public function editTicketProductRecepted(ProductRecepted $productRecepted) {
        $peticion = $productRecepted->getPeticion();
        $id = $productRecepted->getConfiguration()->getId();
        if (!$this->verify_if_me_have_admin_role_with_configuration($id)) {
            $this->setFlash(
                    'sonata_flash_error', 'No tiene permisos suficientes para realizar la acción ejecutada.'
            );
            return $this->redirectToRoute('show_peticion_panel', array("peticion" => $peticion->getId()));
        }
        $request = $this->getRequest();
        $admin_pool = $this->get('sonata.admin.pool');
        $em = $this->getDoctrine()->getManager();
//        $ticketSendedArea = new TicketSendedArea();
        $form = $this->createForm(new ProductReceptedType(), $productRecepted);
        //$ticket = $message->getTicket();
        if ($request->getMethod() == 'POST') {
            $form->bind($request);
            if ($form->isValid()) {
                $configuraion = $peticion->getConfiguration();
//                $configId = $configuraion->getId();
//                $timezone = $em->getRepository('WhatsappBundle:Configuration')->getTimezoneFromConfiguration($configId);
//                $timezone = $timezone[0]["timeZone"];
                $productRecepted->setConfiguration($configuraion);
                $productRecepted->setUpdatedBy($this->getUser());
                $productRecepted->setPeticion($peticion);
                $em->persist($productRecepted);
                $em->flush();
                //TODO enviar notificacion por correo.
                $this->addFlash('sonata_flash_success', 'Mensaje enviado al area.');
                return $this->redirectToRoute('show_peticion_panel', array("peticion" => $peticion->getId()));
            }
        }
        return $this->render('WhatsappBundle:SHOP:edit_ticket_product_recepted.html.twig', array(
                    'form' => $form->createView(),
                    'admin_pool' => $admin_pool,
                    'peticion' => $peticion,
                    'productRecepted' => $productRecepted,
        ));
    }

    /**
     * @Route("/admin/show_peticion_panel/delete_ticket_product_recepted/{productRecepted}", name="delete_ticket_product_recepted" )
     */
    public function deleteTicketProductReceptedAction(ProductRecepted $productRecepted) {
        $request = $this->getRequest();
        $peticion = $productRecepted->getPeticion();
        $admin_pool = $this->get('sonata.admin.pool');
//        $alertEmailList = $em->getRepository('WhatsappBundle:UserCompany')->findByConfigurationByUser($alertEmail, $user);
        $em = $this->getDoctrine()->getManager();
        $configuration = $productRecepted->getConfiguration()->getId();
        if (!$this->verify_if_me_have_admin_role_with_configuration($configuration)) {
            $this->setFlash(
                    'sonata_flash_error', 'No tiene permisos suficientes para realizar la acción ejecutada.'
            );
            return $this->redirectToRoute('my_companies', array("action" => "list"));
        }
        
        $form = $this->createForm(new DeleteUserCompanyType(), $productRecepted);

        if ($request->getMethod() == 'POST') {
            $form->bind($request);
            if ($form->isValid()) {
                $em->remove($productRecepted);
                $em->flush();
                $this->setFlash(
                        'sonata_flash_success', 'Elemento eliminado correctamente.'
                );

                return $this->redirectToRoute('show_peticion_panel', array("peticion" => $peticion->getId()));
            }
        }
        return $this->render('WhatsappBundle:SHOP:delete_ticket_product_recepted.html.twig', array(
                    'admin_pool' => $admin_pool,
                    'form' => $form->createView(),
                    'productRecepted' => $productRecepted,
        ));
    }

    /**
     * @Route("/admin/new_ticket_product_sended/{peticion}", name="new_ticket_product_sended" )
     */
    public function newTicketProductSendedAction(Peticion $peticion) {
        $id = $peticion->getConfiguration()->getId();
        if (!$this->verify_if_me_have_admin_role_with_configuration($id)) {
            $this->setFlash(
                    'sonata_flash_error', 'No tiene permisos suficientes para realizar la acción ejecutada.'
            );
            return $this->redirectToRoute('show_peticion_panel', array("peticion" => $peticion->getId()));
        }
        $request = $this->getRequest();
        $admin_pool = $this->get('sonata.admin.pool');
        $em = $this->getDoctrine()->getManager();
        $productSended = new ProductSended();
        $productSended->setCreatedBy($this->getUser());
        $form = $this->createForm(new ProductSendedType(), $productSended);
        //$ticket = $message->getTicket();
        if ($request->getMethod() == 'POST') {
            $form->bind($request);
            if ($form->isValid()) {
                $configuraion = $peticion->getConfiguration();
//                $configId = $configuraion->getId();
//                $timezone = $em->getRepository('WhatsappBundle:Configuration')->getTimezoneFromConfiguration($configId);
//                $timezone = $timezone[0]["timeZone"];
                $productSended->setConfiguration($configuraion);
                $productSended->setPeticion($peticion);
                $em->persist($productSended);
                $em->flush();
                //TODO enviar notificacion por correo.
                $this->addFlash('sonata_flash_success', 'Registrado la recepción del producto.');
                return $this->redirectToRoute('show_peticion_panel', array("peticion" => $peticion->getId(), "active-tab"=> "productsended"));
            }
        }
        return $this->render('WhatsappBundle:SHOP:new_ticket_product_sended.html.twig', array(
                    'form' => $form->createView(),
                    'admin_pool' => $admin_pool,
                    'peticion' => $peticion,
        ));
    }

    /**
     * @Route("/admin/edit_ticket_product_sended/{productSended}", name="edit_ticket_product_sended" )
     */
    public function editTicketProductSended(ProductSended $productSended) {
        $peticion = $productSended->getPeticion();
        $id = $productSended->getConfiguration()->getId();
        if (!$this->verify_if_me_have_admin_role_with_configuration($id)) {
            $this->setFlash(
                    'sonata_flash_error', 'No tiene permisos suficientes para realizar la acción ejecutada.'
            );
            return $this->redirectToRoute('show_peticion_panel', array("peticion" => $peticion->getId()));
        }
        $request = $this->getRequest();
        $admin_pool = $this->get('sonata.admin.pool');
        $em = $this->getDoctrine()->getManager();
//        $ticketSendedArea = new TicketSendedArea();
        $form = $this->createForm(new ProductSendedType(), $productSended);
        //$ticket = $message->getTicket();
        if ($request->getMethod() == 'POST') {
            $form->bind($request);
            if ($form->isValid()) {
                $configuraion = $peticion->getConfiguration();
//                $configId = $configuraion->getId();
//                $timezone = $em->getRepository('WhatsappBundle:Configuration')->getTimezoneFromConfiguration($configId);
//                $timezone = $timezone[0]["timeZone"];
                $productSended->setConfiguration($configuraion);
                $productSended->setUpdatedBy($this->getUser());
                $productSended->setPeticion($peticion);
                $em->persist($productSended);
                $em->flush();
                //TODO enviar notificacion por correo.
                $this->addFlash('sonata_flash_success', 'Mensaje enviado al area.');
                return $this->redirectToRoute('show_peticion_panel', array("peticion" => $peticion->getId()));
            }
        }
        return $this->render('WhatsappBundle:SHOP:edit_ticket_product_sended.html.twig', array(
                    'form' => $form->createView(),
                    'admin_pool' => $admin_pool,
                    'peticion' => $peticion,
                    'productSended' => $productSended,
        ));
    }

    /**
     * @Route("/admin/show_peticion_panel/delete_ticket_product_sended/{productSended}", name="delete_ticket_product_sended" )
     */
    public function deleteTicketProductSendedAction(ProductSended $productSended) {
        $request = $this->getRequest();
        $peticion = $productSended->getPeticion();
        $admin_pool = $this->get('sonata.admin.pool');
//        $alertEmailList = $em->getRepository('WhatsappBundle:UserCompany')->findByConfigurationByUser($alertEmail, $user);
        $em = $this->getDoctrine()->getManager();
        $configuration = $productSended->getConfiguration()->getId();
        if (!$this->verify_if_me_have_admin_role_with_configuration($configuration)) {
            $this->setFlash(
                    'sonata_flash_error', 'No tiene permisos suficientes para realizar la acción ejecutada.'
            );
            return $this->redirectToRoute('my_companies', array("action" => "list"));
        }

        $form = $this->createForm(new DeleteUserCompanyType(), $productSended);

        if ($request->getMethod() == 'POST') {
            $form->bind($request);
            if ($form->isValid()) {
                $em->remove($productSended);
                $em->flush();
                $this->setFlash(
                        'sonata_flash_success', 'Elemento eliminado correctamente.'
                );

                return $this->redirectToRoute('show_peticion_panel', array("peticion" => $peticion->getId()));
            }
        }
        return $this->render('WhatsappBundle:SHOP:delete_ticket_product_sended.html.twig', array(
                    'admin_pool' => $admin_pool,
                    'form' => $form->createView(),
                    'productSended' => $productSended,
        ));
    }

    /**
     * @Route("/admin/new_oblea_sended/{peticion}", name="new_oblea_sended" )
     */
    public function newObleaSendedAction(Peticion $peticion) {
        $id = $peticion->getConfiguration()->getId();
        if (!$this->verify_if_me_have_admin_role_with_configuration($id)) {
            $this->setFlash(
                    'sonata_flash_error', 'No tiene permisos suficientes para realizar la acción ejecutada.'
            );
            return $this->redirectToRoute('show_peticion_panel', array("peticion" => $peticion->getId()));
        }
        $request = $this->getRequest();
        $admin_pool = $this->get('sonata.admin.pool');
        $em = $this->getDoctrine()->getManager();
        $obleaEnvio = new ObleaEnvio();
        $obleaEnvio->setCreatedBy($this->getUser());
        $obleaEnvio->setCreatedAt(new \DateTime("now"));
        $obleaEnvio->setName($this->getParameter('oblea_name'));
        $obleaEnvio->setImageurl($this->getParameter('oblea_image_url'));
        $obleaEnvio->setRemitente($this->getParameter('oblea_envio_remitente'));
        $obleaEnvio->setRemitenteContact($this->getParameter('oblea_envio_remitente_contacto'));
        $obleaEnvio->setNoReclamo($peticion->getNroReclamo());
        $obleaEnvio->setEntregaEnDomicilio($peticion->getWhatsappGroup()->getDomicilio());
        $obleaEnvio->setLocalidad($peticion->getWhatsappGroup()->getLocalidad());
        $obleaEnvio->setProvincia($peticion->getWhatsappGroup()->getProvincia());
        $obleaEnvio->setCp($peticion->getWhatsappGroup()->getCodigoPostal());
        $obleaEnvio->setDestinatario($peticion->getWhatsappGroup()->getName());
        $obleaEnvio->setObservaciones($peticion->getTimeDisponibility());
        if ($peticion->getProduct() != null)
            $obleaEnvio->setProduct($peticion->getProduct()->getName());
        $form = $this->createForm(new ObleaEnvioType(), $obleaEnvio);
        //$ticket = $message->getTicket();
        if ($request->getMethod() == 'POST') {
            $form->bind($request);
            if ($form->isValid()) {
                $configuraion = $peticion->getConfiguration();
//                $configId = $configuraion->getId();
//                $timezone = $em->getRepository('WhatsappBundle:Configuration')->getTimezoneFromConfiguration($configId);
//                $timezone = $timezone[0]["timeZone"];
                $obleaEnvio->setConfiguration($configuraion);
                $obleaEnvio->setPeticion($peticion);
                $em->persist($obleaEnvio);
                $em->flush();
                //TODO enviar notificacion por correo.
                $this->addFlash('sonata_flash_success', 'Oblea de envío creada de forma correcta.');
                return $this->redirectToRoute('show_peticion_panel', array("peticion" => $peticion->getId(), "active-tab"=> "obleas"));
            }
        }
        return $this->render('WhatsappBundle:SHOP:new_oblea_sended.html.twig', array(
                    'form' => $form->createView(),
                    'admin_pool' => $admin_pool,
                    'peticion' => $peticion,
        ));
    }

    /**
     * @Route("/admin/edit_ticket_oblea_envio/{obleaEnvio}", name="edit_ticket_oblea_envio" )
     */
    public function editTicketObleaSended(ObleaEnvio $obleaEnvio) {
        $peticion = $obleaEnvio->getPeticion();
        $id = $obleaEnvio->getConfiguration()->getId();
        if (!$this->verify_if_me_have_admin_role_with_configuration($id)) {
            $this->setFlash(
                    'sonata_flash_error', 'No tiene permisos suficientes para realizar la acción ejecutada.'
            );
            return $this->redirectToRoute('show_peticion_panel', array("peticion" => $peticion->getId()));
        }
        $request = $this->getRequest();
        $admin_pool = $this->get('sonata.admin.pool');
        $em = $this->getDoctrine()->getManager();
//        $ticketSendedArea = new TicketSendedArea();
        $form = $this->createForm(new ObleaEnvioType(), $obleaEnvio);
        //$ticket = $message->getTicket();
        if ($request->getMethod() == 'POST') {
            $form->bind($request);
            if ($form->isValid()) {
                $configuraion = $peticion->getConfiguration();
//                $configId = $configuraion->getId();
//                $timezone = $em->getRepository('WhatsappBundle:Configuration')->getTimezoneFromConfiguration($configId);
//                $timezone = $timezone[0]["timeZone"];
//                $productSended->setConfiguration($configuraion);
                $obleaEnvio->setUpdatedBy($this->getUser());
                $obleaEnvio->setUpdatedAt(new \DateTime("now"));
//                $productSended->setPeticion($peticion);
                $em->persist($obleaEnvio);
                $em->flush();
                //TODO enviar notificacion por correo.
                $this->addFlash('sonata_flash_success', 'Datos de oblea de envío actualizados.');
                return $this->redirectToRoute('show_peticion_panel', array("peticion" => $peticion->getId()));
            }
        }
        return $this->render('WhatsappBundle:SHOP:edit_ticket_oblea_envio.html.twig', array(
                    'form' => $form->createView(),
                    'admin_pool' => $admin_pool,
                    'peticion' => $peticion,
                    'obleaEnvio' => $obleaEnvio,
        ));
    }

    /**
     * @Route("/admin/show_peticion_panel/delete_ticket_oblea_envio/{obleaEnvio}", name="delete_ticket_oblea_envio" )
     */
    public function deleteTicketObleaEnvioAction(ObleaEnvio $obleaEnvio) {
        $request = $this->getRequest();
        $peticion = $obleaEnvio->getPeticion();
        $admin_pool = $this->get('sonata.admin.pool');
//        $alertEmailList = $em->getRepository('WhatsappBundle:UserCompany')->findByConfigurationByUser($alertEmail, $user);
        $em = $this->getDoctrine()->getManager();
        $configuration = $obleaEnvio->getConfiguration()->getId();
        if (!$this->verify_if_me_have_admin_role_with_configuration($configuration)) {
            $this->setFlash(
                    'sonata_flash_error', 'No tiene permisos suficientes para realizar la acción ejecutada.'
            );
            return $this->redirectToRoute('my_companies', array("action" => "list"));
        }

        $form = $this->createForm(new DeleteUserCompanyType(), $obleaEnvio);

        if ($request->getMethod() == 'POST') {
            $form->bind($request);
            if ($form->isValid()) {
                $em->remove($obleaEnvio);
                $em->flush();
                $this->setFlash(
                        'sonata_flash_success', 'Elemento eliminado correctamente.'
                );

                return $this->redirectToRoute('show_peticion_panel', array("peticion" => $peticion->getId()));
            }
        }
        return $this->render('WhatsappBundle:SHOP:delete_ticket_oblea_envio.html.twig', array(
                    'admin_pool' => $admin_pool,
                    'form' => $form->createView(),
                    'obleaEnvio' => $obleaEnvio,
        ));
    }

    /**
     * @Route("/admin/show_peticion_panel/ticket_oblea_envio_print_preview/{obleaEnvio}", name="ticket_oblea_envio_print_preview" )
     */
    public function ticketObleaEnvioPrintPreviewAction(ObleaEnvio $obleaEnvio) {
        $request = $this->getRequest();
        $peticion = $obleaEnvio->getPeticion();
        $admin_pool = $this->get('sonata.admin.pool');
//        $alertEmailList = $em->getRepository('WhatsappBundle:UserCompany')->findByConfigurationByUser($alertEmail, $user);
        $em = $this->getDoctrine()->getManager();
        $configuration = $obleaEnvio->getConfiguration()->getId();
        if (!$this->verify_if_me_have_admin_role_with_configuration($configuration)) {
            $this->setFlash(
                    'sonata_flash_error', 'No tiene permisos suficientes para realizar la acción ejecutada.'
            );
            return $this->redirectToRoute('my_companies', array("action" => "list"));
        }


        return $this->render('WhatsappBundle:SHOP:ticket_oblea_envio_print_preview.html.twig', array(
                    'obleaEnvio' => $obleaEnvio,
        ));
    }

    /**
     * @Route("/admin/show_peticion_panel/ticket_oblea_envio_print/{obleaEnvio}", name="ticket_oblea_envio_print" )
     */
    public function ticketObleaEnvioPrintAction(ObleaEnvio $obleaEnvio) {
        $request = $this->getRequest();
        $peticion = $obleaEnvio->getPeticion();
        $admin_pool = $this->get('sonata.admin.pool');
//        $alertEmailList = $em->getRepository('WhatsappBundle:UserCompany')->findByConfigurationByUser($alertEmail, $user);
        $em = $this->getDoctrine()->getManager();
        $configuration = $obleaEnvio->getConfiguration()->getId();
        if (!$this->verify_if_me_have_admin_role_with_configuration($configuration)) {
            $this->setFlash(
                    'sonata_flash_error', 'No tiene permisos suficientes para realizar la acción ejecutada.'
            );
            return $this->redirectToRoute('my_companies', array("action" => "list"));
        }
        $html = $this->renderView('WhatsappBundle:SHOP:ticket_oblea_envio_print_preview.html.twig', array(
            'obleaEnvio' => $obleaEnvio,
        ));

        return new PdfResponse(
                $this->get('knp_snappy.pdf')->getOutputFromHtml($html), 'file.pdf'
        );
    }

    /**
     * @Route("/admin/new_oblea_retiro/{peticion}", name="new_oblea_retiro" )
     */
    public function newObleaRetiroAction(Peticion $peticion) {
        $id = $peticion->getConfiguration()->getId();
        if (!$this->verify_if_me_have_admin_role_with_configuration($id)) {
            $this->setFlash(
                    'sonata_flash_error', 'No tiene permisos suficientes para realizar la acción ejecutada.'
            );
            return $this->redirectToRoute('show_peticion_panel', array("peticion" => $peticion->getId()));
        }
        $request = $this->getRequest();
        $admin_pool = $this->get('sonata.admin.pool');
        $em = $this->getDoctrine()->getManager();
        $obleaRetiro = new ObleaRetiro();
        $obleaRetiro->setCreatedBy($this->getUser());
        $obleaRetiro->setCreatedAt(new \DateTime("now"));
        $obleaRetiro->setName($this->getParameter('oblea_name'));
        $obleaRetiro->setImageurl($this->getParameter('oblea_image_url'));
        $obleaRetiro->setNoReclamo($peticion->getNroReclamo());
        $obleaRetiro->setEntregaEnDomicilio($this->getParameter('oblea_retiro_entrega_domicilio'));
        $obleaRetiro->setDestinatarioNames($this->getParameter('oblea_retiro_entrega_destinatario_nombre'));
        $obleaRetiro->setDestinatarioEmails($this->getParameter('oblea_retiro_entrega_destinatario_correo'));
        $obleaRetiro->setDestinatarioPhones($this->getParameter('oblea_retiro_entrega_destinatario_phone'));
        $form = $this->createForm(new ObleaRetiroType(), $obleaRetiro);
        //$ticket = $message->getTicket();
        if ($request->getMethod() == 'POST') {
            $form->bind($request);
            if ($form->isValid()) {
                $configuraion = $peticion->getConfiguration();
                $obleaRetiro->setConfiguration($configuraion);
                $obleaRetiro->setPeticion($peticion);
                $em->persist($obleaRetiro);
                $em->flush();
                $this->addFlash('sonata_flash_success', 'Oblea de retiro creada de forma correcta.');
                return $this->redirectToRoute('show_peticion_panel', array("peticion" => $peticion->getId(), "active-tab"=> "obleas"));
            }
        }
        return $this->render('WhatsappBundle:SHOP:new_oblea_retiro.html.twig', array(
                    'form' => $form->createView(),
                    'admin_pool' => $admin_pool,
                    'peticion' => $peticion,
        ));
    }

    /**
     * @Route("/admin/edit_ticket_oblea_retiro/{obleaRetiro}", name="edit_ticket_oblea_retiro" )
     */
    public function editTicketObleaRetiro(ObleaRetiro $obleaRetiro) {
        $peticion = $obleaRetiro->getPeticion();
        $id = $obleaRetiro->getConfiguration()->getId();
        if (!$this->verify_if_me_have_admin_role_with_configuration($id)) {
            $this->setFlash(
                    'sonata_flash_error', 'No tiene permisos suficientes para realizar la acción ejecutada.'
            );
            return $this->redirectToRoute('show_peticion_panel', array("peticion" => $peticion->getId()));
        }
        $request = $this->getRequest();
        $admin_pool = $this->get('sonata.admin.pool');
        $em = $this->getDoctrine()->getManager();
//        $ticketSendedArea = new TicketSendedArea();
        $form = $this->createForm(new ObleaRetiroType(), $obleaRetiro);
        //$ticket = $message->getTicket();
        if ($request->getMethod() == 'POST') {
            $form->bind($request);
            if ($form->isValid()) {
                $configuraion = $peticion->getConfiguration();
//                $configId = $configuraion->getId();
//                $timezone = $em->getRepository('WhatsappBundle:Configuration')->getTimezoneFromConfiguration($configId);
//                $timezone = $timezone[0]["timeZone"];
//                $productSended->setConfiguration($configuraion);
                $obleaRetiro->setUpdatedBy($this->getUser());
                $obleaRetiro->setUpdatedAt(new \DateTime("now"));
//                $productSended->setPeticion($peticion);
                $em->persist($obleaRetiro);
                $em->flush();
                //TODO enviar notificacion por correo.
                $this->addFlash('sonata_flash_success', 'Datos de oblea de retiro actualizados.');
                return $this->redirectToRoute('show_peticion_panel', array("peticion" => $peticion->getId()));
            }
        }
        return $this->render('WhatsappBundle:SHOP:edit_ticket_oblea_retiro.html.twig', array(
                    'form' => $form->createView(),
                    'admin_pool' => $admin_pool,
                    'peticion' => $peticion,
                    'obleaRetiro' => $obleaRetiro,
        ));
    }

    /**
     * @Route("/admin/show_peticion_panel/delete_ticket_oblea_retiro/{obleaRetiro}", name="delete_ticket_oblea_retiro" )
     */
    public function deleteTicketObleaRetiroAction(ObleaRetiro $obleaRetiro) {
        $request = $this->getRequest();
        $peticion = $obleaRetiro->getPeticion();
        $admin_pool = $this->get('sonata.admin.pool');
//        $alertEmailList = $em->getRepository('WhatsappBundle:UserCompany')->findByConfigurationByUser($alertEmail, $user);
        $em = $this->getDoctrine()->getManager();
        $configuration = $obleaRetiro->getConfiguration()->getId();
        if (!$this->verify_if_me_have_admin_role_with_configuration($configuration)) {
            $this->setFlash(
                    'sonata_flash_error', 'No tiene permisos suficientes para realizar la acción ejecutada.'
            );
            return $this->redirectToRoute('my_companies', array("action" => "list"));
        }

        $form = $this->createForm(new DeleteUserCompanyType(), $obleaRetiro);

        if ($request->getMethod() == 'POST') {
            $form->bind($request);
            if ($form->isValid()) {
                $em->remove($obleaRetiro);
                $em->flush();
                $this->setFlash(
                        'sonata_flash_success', 'Elemento eliminado correctamente.'
                );

                return $this->redirectToRoute('show_peticion_panel', array("peticion" => $peticion->getId()));
            }
        }
        return $this->render('WhatsappBundle:SHOP:delete_ticket_oblea_retiro.html.twig', array(
                    'admin_pool' => $admin_pool,
                    'form' => $form->createView(),
                    'obleaRetiro' => $obleaRetiro,
        ));
    }

    /**
     * @Route("/admin/show_peticion_panel/ticket_oblea_retiro_print_preview/{obleaRetiro}", name="ticket_oblea_retiro_print_preview" )
     */
    public function ticketObleaRetiroPrintPreviewAction(ObleaRetiro $obleaRetiro) {
        $request = $this->getRequest();
        $peticion = $obleaRetiro->getPeticion();
        $admin_pool = $this->get('sonata.admin.pool');
//        $alertEmailList = $em->getRepository('WhatsappBundle:UserCompany')->findByConfigurationByUser($alertEmail, $user);
        $em = $this->getDoctrine()->getManager();
        $configuration = $obleaRetiro->getConfiguration()->getId();
        if (!$this->verify_if_me_have_admin_role_with_configuration($configuration)) {
            $this->setFlash(
                    'sonata_flash_error', 'No tiene permisos suficientes para realizar la acción ejecutada.'
            );
            return $this->redirectToRoute('my_companies', array("action" => "list"));
        }


        return $this->render('WhatsappBundle:SHOP:ticket_oblea_retiro_print_preview.html.twig', array(
                    'obleaRetiro' => $obleaRetiro,
        ));
    }

    /**
     * @Route("/admin/show_peticion_panel/ticket_oblea_retiro_print/{obleaRetiro}", name="ticket_oblea_retiro_print" )
     */
    public function ticketObleaRetiroPrintAction(ObleaRetiro $obleaRetiro) {
        $request = $this->getRequest();
        $peticion = $obleaRetiro->getPeticion();
        $admin_pool = $this->get('sonata.admin.pool');
//        $alertEmailList = $em->getRepository('WhatsappBundle:UserCompany')->findByConfigurationByUser($alertEmail, $user);
        $em = $this->getDoctrine()->getManager();
        $configuration = $obleaRetiro->getConfiguration()->getId();
        if (!$this->verify_if_me_have_admin_role_with_configuration($configuration)) {
            $this->setFlash(
                    'sonata_flash_error', 'No tiene permisos suficientes para realizar la acción ejecutada.'
            );
            return $this->redirectToRoute('my_companies', array("action" => "list"));
        }
        $html = $this->renderView('WhatsappBundle:SHOP:ticket_oblea_retiro_print_preview.html.twig', array(
            'obleaRetiro' => $obleaRetiro,
        ));

        return new PdfResponse(
                $this->get('knp_snappy.pdf')->getOutputFromHtml($html), 'file.pdf'
        );
    }

    /**
     * @Route("/admin/send_mensaje_on_conversation/{conversation}", name="send_mensaje_on_conversation" )
     */
    public function sendMensajeOnConversationAction(Conversation $conversation) {
//        $id = $conversation->getConfiguration()->getId();
//        if (!$this->verify_if_me_have_admin_role_with_configuration($id)) {
//            $this->setFlash(
//                    'sonata_flash_error', 'No tiene permisos suficientes para realizar la acción ejecutada.'
//            );
//            return $this->redirectToRoute('show_peticion_panel', array("peticion"=>$peticion->getId()));
//        }
        $request = $this->getRequest();
        $admin_pool = $this->get('sonata.admin.pool');
        $em = $this->getDoctrine()->getManager();
        $messageSended = new MessageSended();
        $messageSended->setUser($this->getUser());
        $messageSended->setCreatedAt(new \DateTime("now"));

        $messageSended->setConversation($conversation);
        $form = $this->createForm(new MensajeToConversationType(), $messageSended);
        //$ticket = $message->getTicket();
        if ($request->getMethod() == 'POST') {
            $form->bind($request);
            if ($form->isValid()) {
//                $configuraion = $peticion->getConfiguration();
//                $messageSended->setConfiguration($configuraion);
                $em->persist($messageSended);
                $em->flush();
                $api = $this->get("whatsapp.sacspro.whatsappapi");
                if($messageSended->getConversation()->getWhatsappGroup()->getChatId() == null){
                    $this->setFlash(
                    'sonata_flash_error', 'El contacto no tiene el tributo chatId configurado. No se puede enviar el mensaje.'
                    );
                    return $this->redirectToRoute('admin_whatsapp_conversation_list');
                }
                //crear el message como si hubiera entrado
                $message = new Message();
                $strcontactname = null;
                if($conversation->getWhatsappGroup() != null){
                    $whatsappGroup = $conversation->getWhatsappGroup();
                    $strcontactname = $whatsappGroup->getWhatsappNick();
                }
                $message->setStrcontactname("Admin");
                $message->setDtmmessage(new \DateTime());
                $message->setStrmenssagetext($messageSended->getMessage());
                $message->setEnabled(true);
                $message->setApimessageid(generateRandomUnique());
                $message->setMessagetype("chat");
                $message->setSenderName("Admin");
                $message->setFromMe(true);
                $message->setAuthor("Admin");
                $message->setChatId($messageSended->getConversation()->getWhatsappGroup()->getChatId());
                $message->setMessageNumber(1);
                $message->setUrlMedia(null);
                $message->setChatName($strcontactname);
                $message->setProcesed(false);
                
                $em->persist($message);
                $em->flush();
                
                $api->sendMessageChatid($messageSended->getConversation()->getWhatsappGroup()->getChatId(), $messageSended->getMessage());
                $this->addFlash('sonata_flash_success', 'Mensaje enviado de forma correcta.');
                return $this->redirectToRoute('admin_whatsapp_conversation_list');
            }
        }
        return $this->render('WhatsappBundle:SHOP:send_mensaje_on_conversation.html.twig', array(
                    'form' => $form->createView(),
                    'admin_pool' => $admin_pool,
                    'conversation' => $conversation,
        ));
    }
    public function generateRandomUnique(){
        return uniqid();
    }

    /**
     * @Route("/admin/chat/{conversation}", name="chat_conversation" )
     */
    public function chatConversationAction(Conversation $conversation) {
//        $id = $conversation->getConfiguration()->getId();
//        if (!$this->verify_if_me_have_admin_role_with_configuration($id)) {
//            $this->setFlash(
//                    'sonata_flash_error', 'No tiene permisos suficientes para realizar la acción ejecutada.'
//            );
//            return $this->redirectToRoute('show_peticion_panel', array("peticion"=>$peticion->getId()));
//        }
//        $request = $this->getRequest();
//        $admin_pool = $this->get('sonata.admin.pool');
//        $em = $this->getDoctrine()->getManager();
//        $messageSended = new MessageSended();
//        $messageSended->setUser($this->getUser());
//        $messageSended->setCreatedAt(new \DateTime("now"));
//        $messageSended->setConversation($conversation);
//        $form = $this->createForm(new MensajeToConversationType(), $messageSended);
        //$ticket = $message->getTicket();
        $em = $this->getDoctrine()->getManager();
        $messages = $em->getRepository('WhatsappBundle:Message')->findLast15ByWhatsappGroup($conversation->getWhatsappGroup());
//        dump($messages);die;
        $conversation->setUnreadMessage(false);
        $em = $this->getDoctrine()->getManager();
        $em->persist($conversation);
        $em->flush();
        usort($messages, array($this, "customCompareMessagesByDate"));
        $whatsappGroup = $conversation->getWhatsappGroup();
//        dump($messages);die;
        return $this->render('WhatsappBundle:SHOP:chat_conversation.html.twig', array(
//                    'form' => $form->createView(),
//                    'admin_pool' => $admin_pool,
//                    'conversation' => $conversation,
                    'whatsappGroup' => $whatsappGroup,
                    'messages' => $messages,
        ));
    }

    /**
     * @Route("/admin/chat_whatsapp_group/{whatsappGroup}", name="chat_whatsapp_group" )
     */
    public function chatWhatsappGroupAction(WhatsappGroup $whatsappGroup) {
        $em = $this->getDoctrine()->getManager();
        $messages = $em->getRepository('WhatsappBundle:Message')->findLast15ByWhatsappGroup($whatsappGroup);
        usort($messages, array($this, "customCompareMessagesByDate"));
        return $this->render('WhatsappBundle:SHOP:chat_conversation.html.twig', array(
//                    'form' => $form->createView(),
//                    'admin_pool' => $admin_pool,
//                    'conversation' => $conversation,
                    'whatsappGroup' => $whatsappGroup,
                    'messages' => $messages,
        ));
    }

    /**
     * @Route("/admin/chat_whatsapp_group_ajax_send_sticker/{whatsappGroup}", name="chat_whatsapp_group_ajax_send_sticker" )
     */
    public function chatWhatsappGroupAjaxStickerAction(WhatsappGroup $whatsappGroup) {
        $em = $this->getDoctrine()->getManager();
        $request = $this->getRequest();
        $url = $request->request->get('image');
        $image = file_get_contents($url);
        if ($image !== false){
            $base64 = 'data:image/png;base64,'.base64_encode($image);

        }
        $api = $this->get("whatsapp.sacspro.whatsappapi");
//        dump($base64);
//        $api->sendMultimediaChatid("50762993038@c.us", "",$base64);
        //crear el message como si hubiera entrado
        $message = new Message();
        $strcontactname = $whatsappGroup->getWhatsappNick();
        
        $message->setStrcontactname("Admin");
        $message->setDtmmessage(new \DateTime());
        //$message->setStrmenssagetext($messageSended->getMessage());
        $message->setStrmenssagetext(null);
        $message->setEnabled(true);
        $message->setApimessageid(uniqid());
        $message->setMessagetype("image");
        $message->setSenderName("Admin");
        $message->setFromMe(true);
        $message->setAuthor("Admin");
        $message->setChatId($whatsappGroup->getChatId());
        $message->setMessageNumber(1);
        $message->setUrlMedia($url);
        $message->setChatName($strcontactname);
        $message->setProcesed(false);

        $em->persist($message);
        $em->flush();
        
        $api->sendMultimediaChatid($whatsappGroup->getChatId(), "",$base64);
        return new JsonResponse(array('status' => "ok", "response" => "ok"));
    }
    
    /**
     * @Route("/admin/chat_conversation_message_part/{message}", name="chat_conversation_message_part" )
     */
    public function chatConversationPartAction(Message $message) {

        return $this->render('WhatsappBundle:SHOP:chat_conversation_part.html.twig', array(
                    'message' => $message,
        ));
    }

    /**
     * @Route("/admin/publish_message_from_web_to_conversation/{whatsappGroup}", name="publish_message_from_web_to_conversation" )
     */
    public function publishMessageFromWebToConversationAction(WhatsappGroup $whatsappGroup) {
//        //Send mensaje to contact
        $request = $this->getRequest();
//        $chatId = $conversation->getWhatsappGroup()->getChatId();
        if ($request->getMethod() == 'POST') {
            $message = $request->request->get('message');
            $em = $this->getDoctrine()->getManager();
            $messageSended = new MessageSended();
            $messageSended->setUser($this->getUser());
            $messageSended->setCreatedAt(new \DateTime("now"));
//            $messageSended->setConversation($conversation);
            $messageSended->setWhatsappGroup($whatsappGroup);
            $messageSended->setMessage($message);
            $em->persist($messageSended);
            $em->flush();
            $api = $this->get("whatsapp.sacspro.whatsappapi");
            $api->sendMessageChatid($whatsappGroup->getChatId(), $messageSended->getMessage());
            
            return new JsonResponse(array('status' => "ok", "response" => "ok"));
//            $pusher = $this->get('gos_web_socket.wamp.pusher');
//            $pusher->push(array("id" => uniqid(), "strmenssagetext" => $message, "from_me" => false), "acme_topic", array('id' => $conversation->getId()), array());
        }
        
//        $em = $this->getDoctrine()->getManager();
//        $messages = $em->getRepository('WhatsappBundle:Message')->findLast15ByConversation($conversation);
//        dump($messages);die;

        return new JsonResponse(array('status' => "ok", "response" => "ok"));
    }

    /**
     * @Route("/admin/send_link_change_password/{user}", name="send_link_change_password" )
     */
    public function sendLinkChangePasswordAction(User $user) {
        
//        //Send mensaje to contact
        $request = $this->getRequest();
//      $username = $this->container->get('request')->request->get('username');

        /** @var $user UserInterface */
//        $user = $this->container->get('fos_user.user_manager')->findUserByUsernameOrEmail($user->getUsername());

        if (null === $user) {
            return $this->container->get('templating')->renderResponse('FOSUserBundle:Resetting:request.html.twig', array('invalid_username' => $username));
        }

//        if ($user->isPasswordRequestNonExpired($this->container->getParameter('fos_user.resetting.token_ttl'))) {
//            return $this->container->get('templating')->renderResponse('FOSUserBundle:Resetting:passwordAlreadyRequested.html.twig');
//        }

        if (null === $user->getConfirmationToken()) {
            /** @var $tokenGenerator \FOS\UserBundle\Util\TokenGeneratorInterface */
            $tokenGenerator = $this->container->get('fos_user.util.token_generator');
            $user->setConfirmationToken($tokenGenerator->generateToken());
        }

        $this->container->get('session')->set('fos_user_send_resetting_email/email', $this->getObfuscatedEmail($user));
        $this->container->get('fos_user.mailer')->sendResettingEmailMessage($user);
        $user->setPasswordRequestedAt(new \DateTime());
        $this->container->get('fos_user.user_manager')->updateUser($user);
        $this->setFlash(
                'sonata_flash_success', 'Se le envió un correo al usuario para que cambie su contraseña.'
        );
        return $this->redirectToRoute('admin_sonata_user_user_list');
    }

//    
//    /**
//     * @Route("/api/add_company_objetive/{conversation}", name="api_add_company_objective")
//     * 
//     */
//    public function createCompanyObjectiveAction(Request $request, Conversation $conversation) {
//       
//        if ($request->getMethod() == 'POST') {
//            $em = $this->getDoctrine()->getManager();
//            $title = $request->request->get('title');
//            // data is an array with "name", "email", and "message" keys
////          $title = $data["title"];
//            
//            $position = $em->getRepository('AppBundle:Objective')->countByPeriod($period);
//            $objective = new Objective();
//            $objective->setPeriod($period);
//            $objective->setCreateBy($this->getUser());
//            $objective->setProgress(0);
//            $objective->setTitle($title);
//            $objective->setCreateAt(new \DateTime("now"));
//            $objective->setPosition($position);
//            
//            $em->persist($objective);
//            $em->flush();
//        }
//        
//        
//        $response = $this->renderView('@App/Rest/objetive_company.html.twig', array(
//                    'objective' => $objective,
//                    'company' => $period->getCompany(),                    
//        ));
//        return new JsonResponse(array('status' => "ok", "response" => $response));
//    }

    /**
     * @Route("/admin/chat", name="chat" )
     */
    public function chatAction() {

        $admin_pool = $this->get('sonata.admin.pool');
        return $this->render('WhatsappBundle:SHOP:chat.html.twig', array(
                    'admin_pool' => $admin_pool,
        ));
    }
    
    /**
     * @Route("/admin/show_peticion_panel/ticket_oblea_retiro_export_xls/{obleaRetiro}", name="ticket_oblea_retiro_export_xls" )
     */
    public function ticketObleaRetiroExportXlsAction(ObleaRetiro $obleaRetiro) {
        $translator = $this->get('translator');

        $filename = "oblea-retiro".uniqid();

        $filename = $filename . ".xls";
        $this->file = fopen($filename, 'w', false);
        fwrite($this->file, '<html><head><meta http-equiv="Content-Type" content="text/html; charset=utf-8" /><meta name=ProgId content=Excel.Sheet><meta name=Generator content="https://github.com/sonata-project/exporter"></head><body><table>');
        fwrite($this->file, '<tr>');
        fwrite($this->file, sprintf('<th>%s</th>', "Fecha"));
//        fwrite($this->file, sprintf('<th>%s</th>', "Nombre"));
        fwrite($this->file, sprintf('<th>%s</th>', "N° Reclamo"));
        fwrite($this->file, sprintf('<th>%s</th>', "Bulto"));
        fwrite($this->file, sprintf('<th>%s</th>', "Peso"));
        fwrite($this->file, sprintf('<th>%s</th>', "Nombres de destinatario"));
        fwrite($this->file, sprintf('<th>%s</th>', "Emails de destinatario"));
        fwrite($this->file, sprintf('<th>%s</th>', "Teléfonos de destinatario"));
        fwrite($this->file, '</tr>');
//        $index = 0;
            fwrite($this->file, '<tr>');
            fwrite($this->file, sprintf('<td>%s</td>', $obleaRetiro->getPeticion()->getCreatedAt()->format("d-m-Y")));
//            fwrite($this->file, sprintf('<td>%s</td>', $obleaRetiro->getName()));
            fwrite($this->file, sprintf('<td>%s</td>', $obleaRetiro->getNoReclamo()));
            fwrite($this->file, sprintf('<td>%s</td>', $obleaRetiro->getBulto()));
            fwrite($this->file, sprintf('<td>%s</td>', $obleaRetiro->getPeso()));
            fwrite($this->file, sprintf('<td>%s</td>', $obleaRetiro->getDestinatarioNames()));
            fwrite($this->file, sprintf('<td>%s</td>', $obleaRetiro->getDestinatarioEmails()));
            fwrite($this->file, sprintf('<td>%s</td>', $obleaRetiro->getDestinatarioPhones()));
//            $index = $index + 1;
            fwrite($this->file, '</tr>');

        fwrite($this->file, '</table></body></html>');
        fclose($this->file);


        $response = new BinaryFileResponse($filename);
        $response->deleteFileAfterSend(true);
        $response->headers->set('Content-Type', 'application/vnd.ms-excel');
        $response->setContentDisposition(
                ResponseHeaderBag::DISPOSITION_ATTACHMENT, $filename
        );
        return $response;
    }
    
    /**
     * @Route("/admin/show_peticion_panel/ticket_oblea_envio_export_xls/{obleaEnvio}", name="ticket_oblea_envio_export_xls" )
     */
    public function ticketObleaEnvioExportXlsAction(ObleaEnvio $obleaEnvio) {
        $translator = $this->get('translator');

        $filename = "oblea-envio".uniqid();

        $filename = $filename . ".xls";
        $this->file = fopen($filename, 'w', false);
        fwrite($this->file, '<html><head><meta http-equiv="Content-Type" content="text/html; charset=utf-8" /><meta name=ProgId content=Excel.Sheet><meta name=Generator content="https://github.com/sonata-project/exporter"></head><body><table>');
        fwrite($this->file, '<tr>');
        fwrite($this->file, sprintf('<th>%s</th>', "Fecha"));
//        fwrite($this->file, sprintf('<th>%s</th>', "Nombre"));
        fwrite($this->file, sprintf('<th>%s</th>', "N° Reclamo"));
        fwrite($this->file, sprintf('<th>%s</th>', "Bulto"));
        fwrite($this->file, sprintf('<th>%s</th>', "Paquetes a Retirar"));
        fwrite($this->file, sprintf('<th>%s</th>', "Peso"));
        fwrite($this->file, sprintf('<th>%s</th>', "Entrega en domicilio"));
        fwrite($this->file, sprintf('<th>%s</th>', "Localidad"));
        fwrite($this->file, sprintf('<th>%s</th>', "Provincia"));
        fwrite($this->file, sprintf('<th>%s</th>', "CP"));
        fwrite($this->file, sprintf('<th>%s</th>', "Destinatario"));
        fwrite($this->file, sprintf('<th>%s</th>', "Observaciones"));
        fwrite($this->file, sprintf('<th>%s</th>', "Producto"));
        fwrite($this->file, sprintf('<th>%s</th>', "Remitente"));
        fwrite($this->file, sprintf('<th>%s</th>', "Remitente Contact"));
        fwrite($this->file, '</tr>');
//        $index = 0;
            fwrite($this->file, '<tr>');
            fwrite($this->file, sprintf('<td>%s</td>', $obleaEnvio->getPeticion()->getCreatedAt()->format("d-m-Y")));
//            fwrite($this->file, sprintf('<td>%s</td>', $obleaEnvio->getName()));
            fwrite($this->file, sprintf('<td>%s</td>', $obleaEnvio->getNoReclamo()));
            fwrite($this->file, sprintf('<td>%s</td>', $obleaEnvio->getBulto()));
            fwrite($this->file, sprintf('<td>%s</td>', $obleaEnvio->getPaquetesRetirar()));
            fwrite($this->file, sprintf('<td>%s</td>', $obleaEnvio->getPeso()));
            fwrite($this->file, sprintf('<td>%s</td>', $obleaEnvio->getEntregaEnDomicilio()));
            fwrite($this->file, sprintf('<td>%s</td>', $obleaEnvio->getLocalidad()));
            fwrite($this->file, sprintf('<td>%s</td>', $obleaEnvio->getProvincia()));
            fwrite($this->file, sprintf('<td>%s</td>', $obleaEnvio->getCp()));
            fwrite($this->file, sprintf('<td>%s</td>', $obleaEnvio->getDestinatario()));
            fwrite($this->file, sprintf('<td>%s</td>', $obleaEnvio->getObservaciones()));
            fwrite($this->file, sprintf('<td>%s</td>', $obleaEnvio->getProduct()));
            fwrite($this->file, sprintf('<td>%s</td>', $obleaEnvio->getRemitente()));
            fwrite($this->file, sprintf('<td>%s</td>', $obleaEnvio->getRemitenteContact()));
//            $index = $index + 1;
            fwrite($this->file, '</tr>');

        fwrite($this->file, '</table></body></html>');
        fclose($this->file);


        $response = new BinaryFileResponse($filename);
        $response->deleteFileAfterSend(true);
        $response->headers->set('Content-Type', 'application/vnd.ms-excel');
        $response->setContentDisposition(
                ResponseHeaderBag::DISPOSITION_ATTACHMENT, $filename
        );
        return $response;
    }

//    
//    /**
//     * @Route("/admin/add_message_to_ticket/{ticket}", name="add_message_to_ticket" )
//     */
//    public function addMessgeToTicketAction(Ticket $ticket) {
//        $id = $ticket->getConfiguration()->getId();
//        if (!$this->verify_if_me_have_admin_role_with_configuration($id)) {
//            $this->setFlash(
//                    'sonata_flash_error', 'No tiene permisos suficientes para realizar la acción ejecutada.'
//            );
//            return $this->redirectToRoute('admin_whatsapp_ticket_list');
//        }
//        $request = $this->getRequest();
//        $this->get('session')->set("menu", "add-mensaje-to-ticket");
//        $admin_pool = $this->get('sonata.admin.pool');
//        $message = new Message();
//        $message->setTicket($ticket);
//        $message->setWhatsappGroup($ticket->getWhatsappGroup());
//        $message->setDtmmessage(new \DateTime());
//        $configurations = array();
//        $configurations[] = $id;
//       
//        $options["configuration"] = $configurations;
//        $options["timezone"] = $ticket->getConfiguration()->getTimeZone();
//        
//        $form = $this->createForm(new MessageToTicketFormType(), $message, $options);
//        if ($request->getMethod() == 'POST') {
//            $form->bind($request);
//            if ($form->isValid()) {
//                $message->setConfiguration($ticket->getConfiguration());
//                $message->setTicket($ticket);
//                $message->setWhatsappGroup($ticket->getWhatsappGroup());
//                $em = $this->getDoctrine()->getManager();
//                $em->persist($message);
//                $em->flush();
//                $this->addFlash('sonata_flash_success', 'Mensaje creado.');
//                $message = new Message();
//                $message->setTicket($ticket);
//                $message->setWhatsappGroup($ticket->getWhatsappGroup());
//                $message->setDtmmessage(new \DateTime());
//                $form = $this->createForm(new MessageToTicketFormType(), $message, $options);
//            } else {
//                $this->setFlash(
//                        'sonata_flash_error', 'El mensaje no ha sido creado.'
//                );
//            }
//        }
//
//        return $this->render('WhatsappBundle:ADMIN:add_message_to_ticket.html.twig', array(
//                    'form' => $form->createView(),
//                    'admin_pool' => $admin_pool,
//                    'ticket' => $ticket,
//        ));
//    }
//
//    /**
//     * @Route("/admin/divide_ticket_by_message_click/{message}", name="divide_ticket_by_message_click" )
//     */
//    public function divideTicketByMessageClickAction(Message $message) {
//        $id = $message->getConfiguration()->getId();
//        if (!$this->verify_if_me_have_admin_role_with_configuration($id)) {
//            $this->setFlash(
//                    'sonata_flash_error', 'No tiene permisos suficientes para realizar la acción ejecutada.'
//            );
//            return $this->redirectToRoute('admin_whatsapp_message_list');
//        }
//        $request = $this->getRequest();
//        $admin_pool = $this->get('sonata.admin.pool');
//        $em = $this->getDoctrine()->getManager();
//        $message = $em->getRepository('WhatsappBundle:Message')->find($message->getId());
//        $configuraion = $message->getConfiguration();
//        $configId = $configuraion->getId();
//        $timezone = $em->getRepository('WhatsappBundle:Configuration')->getTimezoneFromConfiguration($configId);
//        $timezone = $timezone[0]["timeZone"];
//        $form = $this->createForm(new ConfirmType(), $message);
//        //$ticket = $message->getTicket();
//        if ($request->getMethod() == 'POST') {
//            $form->bind($request);
//            if ($form->isValid()) {
//                $ticket = $message->getTicket();
//
//                $messagesAfterThisMessage = $em->getRepository('WhatsappBundle:Message')->findPosThisIdDate($message->getDtmmessage(), $ticket);
//                array_unshift($messagesAfterThisMessage, $message);
//                //        dump($messagesAfterThisMessage[0]->getDtmmessage()->format("Y-m-d H:i:s"));
//                $ticketNew = new Ticket();
//                $ticketNew->setStartDate($message->getDtmmessage());
//                $ticketNew->setEndDate($messagesAfterThisMessage[count($messagesAfterThisMessage) - 1]->getDtmmessage());
//                $ticketNew->setMessages($messagesAfterThisMessage);
//                $ticketNew->setWhatsappGroup($ticket->getWhatsappGroup());
//                $ticketNew->setFirstanswer(true);
//
//                $ticketNew->setTicketended($ticket->getTicketended());
//                $ticketNew->setConfiguration($ticket->getConfiguration());
//                $startDate = $message->getDtmmessage();
//                $userTimezone = new \DateTimeZone($ticket->getConfiguration()->getTimeZone());
//                $startDate->setTimezone($userTimezone);
//                $name = $ticket->getWhatsappGroup()->getName() . " > " . $startDate->format("Y-m-d H:i:s");
//                $ticketNew->setName($name);
//                $ticketNew->recalculateResolutionByEndDate($messagesAfterThisMessage[count($messagesAfterThisMessage) - 1]->getDtmmessage());
//                $em->persist($ticketNew);
//                $em->flush();
//                //        $message->setTicket($ticketNew);
//                //        $em->persist($message);
//                foreach ($messagesAfterThisMessage as $message) {
//                    $message->setTicket($ticketNew);
//                    $ticket->removeMessage($message);
//                    $em->persist($message);
//                }
//
//                $ticketNew->setFirstanswer(true);
//                $ticket->setTicketended(true);
//                $em->flush();
//                $ticket->recalculateResolutionTates($timezone);
//                $em->persist($ticket);
//                $em->flush();
//                $alerts = $em->getRepository('WhatsappBundle:Alert')->findByTicketIsEnabled($ticket);
//                foreach ($alerts as $alert) {
//                    if ($ticket->getTicketended()) {
//                        $alert->setOpen(false);
//                        $em->persist($alert);
//                    }
//                }
//                $em->flush();
//                $this->addFlash('sonata_flash_success', 'Petición dividida correctamente.');
//                return $this->redirectToRoute('admin_whatsapp_ticket_list');
//            }
//        }
//        return $this->render('WhatsappBundle:MESSAGE:divide_ticket_confirm.html.twig', array(
//                    'form' => $form->createView(),
//                    'admin_pool' => $admin_pool,
//                    'message' => $message,
//        ));
//    }
//
//    /**
//     * @Route("/admin/new_ticket_by_message_click/{message}", name="new_ticket_by_message_click" )
//     */
//    public function newTicketByMessageClickAction(Message $message) {
//        $id = $message->getConfiguration()->getId();
//        if (!$this->verify_if_me_have_admin_role_with_configuration($id)) {
//            $this->setFlash(
//                    'sonata_flash_error', 'No tiene permisos suficientes para realizar la acción ejecutada.'
//            );
//            return $this->redirectToRoute('admin_whatsapp_message_list');
//        }
//        $request = $this->getRequest();
//        $admin_pool = $this->get('sonata.admin.pool');
//        $em = $this->getDoctrine()->getManager();
//        $message = $em->getRepository('WhatsappBundle:Message')->find($message->getId());
//        $form = $this->createForm(new ConfirmType(), $message);
//        //$ticket = $message->getTicket();
//        if ($request->getMethod() == 'POST') {
//            $form->bind($request);
//            if ($form->isValid()) {
//                $configuraion = $message->getConfiguration();
//                $configId = $configuraion->getId();
//                $timezone = $em->getRepository('WhatsappBundle:Configuration')->getTimezoneFromConfiguration($configId);
//                $timezone = $timezone[0]["timeZone"];
//                
//                $ticket = $message->getTicket();
//
////                $messagesAfterThisMessage = $em->getRepository('WhatsappBundle:Message')->findPosThisIdDate($message->getDtmmessage(), $ticket);
////                array_unshift($messagesAfterThisMessage, $message);
//                //        dump($messagesAfterThisMessage[0]->getDtmmessage()->format("Y-m-d H:i:s"));
//                $ticketNew = new Ticket();
//                $ticketNew->setStartDate($message->getDtmmessage());
////                $ticketNew->setEndDate($messagesAfterThisMessage[count($messagesAfterThisMessage) - 1]->getDtmmessage());
//                $ticketNew->setEndDate($message->getDtmmessage());
////                $ticketNew->setMessages($messagesAfterThisMessage);
//                $ticketNew->addMessages($message);
//                $ticketNew->setWhatsappGroup($ticket->getWhatsappGroup());
//                $ticketNew->setConfiguration($ticket->getConfiguration());
//                $ticketNew->setFirstanswer(true);
//
//                $ticketNew->setTicketended($ticket->getTicketended());
//                $startDate = $message->getDtmmessage();
//                $userTimezone = new \DateTimeZone($ticket->getConfiguration()->getTimeZone());
//                $startDate->setTimezone($userTimezone);
//                $name = $ticket->getWhatsappGroup()->getName() . " > " . $startDate->format("Y-m-d H:i:s");
//                $ticketNew->setName($name);
////                $ticketNew->recalculateResolutionByEndDate($messagesAfterThisMessage[count($messagesAfterThisMessage) - 1]->getDtmmessage());
//                $em->persist($ticketNew);
//                $em->flush();
//                $message->setTicket($ticketNew);
//                $em->persist($message);
////                foreach ($messagesAfterThisMessage as $message) {
////                    $message->setTicket($ticketNew);
////                    $ticket->removeMessage($message);
////                    $em->persist($message);
////                }
//
////                $ticketNew->setFirstanswer(true);
//                $ticket->setTicketended(true);
//                $em->flush();
//                $ticket->recalculateResolutionTates($timezone);
//                $em->persist($ticket);
//                $em->flush();
//                $alerts = $em->getRepository('WhatsappBundle:Alert')->findByTicketIsEnabled($ticket);
//                foreach ($alerts as $alert) {
//                    if ($ticket->getTicketended()) {
//                        $alert->setOpen(false);
//                        $em->persist($alert);
//                    }
//                }
//                $em->flush();
//                $this->addFlash('sonata_flash_success', 'Mensaje movido a una nueva Petición correctamente.');
//                return $this->redirectToRoute('admin_whatsapp_ticket_list');
//            }
//        }
//        return $this->render('WhatsappBundle:MESSAGE:new_ticket_by_message_click_confirm.html.twig', array(
//                    'form' => $form->createView(),
//                    'admin_pool' => $admin_pool,
//                    'message' => $message,
//        ));
//    }
//
//    /**
//     * @Route("/admin/unlink_message_ticket/{message}", name="unlink_message_ticket" )
//     */
//    public function unlinkMessageTicketAction(Message $message) {
//        $id = $message->getConfiguration()->getId();
//        if (!$this->verify_if_me_have_admin_role_with_configuration($id)) {
//            $this->setFlash(
//                    'sonata_flash_error', 'No tiene permisos suficientes para realizar la acción ejecutada.'
//            );
//            return $this->redirectToRoute('admin_whatsapp_message_list');
//        }
//        $request = $this->getRequest();
//        $admin_pool = $this->get('sonata.admin.pool');
//        $em = $this->getDoctrine()->getManager();
//        $message = $em->getRepository('WhatsappBundle:Message')->find($message->getId());
//        $form = $this->createForm(new ConfirmType(), $message);
//        //$ticket = $message->getTicket();
//        if ($request->getMethod() == 'POST') {
//            $form->bind($request);
//            if ($form->isValid()) {
//                $configuraion = $message->getConfiguration();
//                $configId = $configuraion->getId();
//                $timezone = $em->getRepository('WhatsappBundle:Configuration')->getTimezoneFromConfiguration($configId);
//                $timezone = $timezone[0]["timeZone"];
//                
//                $ticket = $message->getTicket();                
//                $message->setTicket(null);
//                $em->persist($message);
//                $em->flush();
//                $ticket->recalculateResolutionTates($timezone);
//                $em->persist($ticket);
//                $em->flush();
//                $this->addFlash('sonata_flash_success', 'El mensaje ha dejado de estar asociado a la petición.');
//                return $this->redirectToRoute('admin_whatsapp_message_list');
//            }
//        }
//        return $this->render('WhatsappBundle:MESSAGE:unblink_message_ticket_confirm.html.twig', array(
//                    'form' => $form->createView(),
//                    'admin_pool' => $admin_pool,
//                    'message' => $message,
//        ));
//    }
//    
//    /**
//     * 
//     * @Route("/admin/manual_settrue_phone_conected", name="manual_settrue_phone_conected" )
//     */
//    public function manualSetTruePhoneConectedAction() {
//        $em = $this->getDoctrine()->getManager();
//        $configuration = $em->getRepository('WhatsappBundle:GeneralConfiguration')->find(1);
//        $configuration->setStatusPhoneConected(true);
//        $em->persit($configuration);
//        $em->flush();
//        return $this->redirectToRoute('report_peticion');
//    }
//
//    /**
//     * 
//     * @Route("/admin/change_messages_to_ticket", name="change_messages_to_ticket" )
//     */
//    public function changeMessagesToTicketAction() {
//        $task = new MessagesChangeTicket();
//        $form = $this->createForm(new MessageChangeTicketType(), $task);
//        $request = $this->getRequest();
//        $form->handleRequest($request);
//
//        if ($form->isSubmitted() && $form->isValid()) {
//            $em = $this->getDoctrine()->getManager();
//            $messages = $task->getMessages();
//            $newTicket = $task->getTicket();
//            $configuraion = $newTicket->getConfiguration();
//            $configId = $configuraion->getId();
//            $timezone = $em->getRepository('WhatsappBundle:Configuration')->getTimezoneFromConfiguration($configId);
//            $timezone = $timezone[0]["timeZone"];
//            
//            foreach ($messages as $value) {
//                $messaje = $em->getRepository('WhatsappBundle:Message')->find($value);
//                $ticket = $messaje->getTicket();
//                if ($ticket)
//                    $ticket->removeMessage($messaje);
//                $newTicket->addMessages($messaje);
//                if ($ticket)
//                    $ticket->recalculateResolutionTates($timezone);
//                $newTicket->recalculateResolutionTates($timezone);
//                if ($ticket)
//                    $ticket->findAndRecalculeFirstAnswer();
//                $newTicket->findAndRecalculeFirstAnswer();
//                $messaje->setTicket($newTicket);
//                if ($ticket) {
//                    $em->persist($ticket);
//                    if (count($ticket->getMessages()) == 0)
//                        $em->remove($ticket);
//                }
//                $em->persist($newTicket);
//                $em->persist($messaje);
//            }
//            $em->flush();
//            $this->setFlash(
//                    'sonata_flash_success', 'Mensajes asignados a la peticion seleccionada.'
//            );
//        }
//        return $this->redirectToRoute('admin_whatsapp_ticket_list');
//    }
//
//    /**
//     * 
//     * @Route("/admin/change_ticket_to_group", name="change_ticket_to_group" )
//     */
//    public function changeTicketToGroupAction() {
//        $task = new TicketChangeGroup();
//        $form = $this->createForm(new TicketChangeGroupType(), $task);
//        $request = $this->getRequest();
//        $form->handleRequest($request);
//
//        if ($form->isSubmitted() && $form->isValid()) {
//            $em = $this->getDoctrine()->getManager();
//            $peticiones = $task->getPeticiones();
//            $newGroup = $task->getWhatsappGroup();
//            foreach ($peticiones as $value) {
//                $ticket = $em->getRepository('WhatsappBundle:Ticket')->find($value);
//                $messages = $em->getRepository('WhatsappBundle:Message')->findByTicket($ticket);
//                foreach ($messages as $value) {
//                    $value->setWhatsappGroup($newGroup);
//                    $em->persist($ticket);
//                }
//                $em->flush();
//                $group = $ticket->getWhatsappGroup();
//                $group->removeTicket($ticket);
//                $newGroup->addTickets($ticket);
//                $ticket->setWhatsappGroup($newGroup);
//                $ticket->setName($newGroup->getName() . " > " . strval($ticket->getStartDate()->format('Y-m-d H:i:s')));
//                $em->persist($ticket);
//                $em->persist($group);
//                $em->persist($newGroup);
//            }
//            $em->flush();
//            $this->setFlash(
//                    'sonata_flash_success', 'Peticiones asignadas al grupo seleccionado.'
//            );
//        }
//        return $this->redirectToRoute('admin_whatsapp_ticket_list');
//    }
//
//    /**
//     * 
//     * @Route("/admin/close_pending_tickets", name="close_pending_tickets" )
//     */
//    public function closePendingTicketsAction() {
//        $wt = $this->get('whatsapp.sacspro.phonestatus');
//        $wt->closePendingTickets();
//        $this->setFlash(
//                'sonata_flash_success', 'Cambios pendientes realizados correctamente.'
//        );
//        return $this->redirectToRoute('admin_whatsapp_ticket_list');
//    }
//
//    /**
//     * @Route("/admin/my_companies/{action}/{id}", name="my_companies", defaults={"action" = null, "id" = null} )
//     */
//    public function MyCompaniesAction($action = null, Configuration $id = null) {
//        $request = $this->getRequest();
//        $admin_pool = $this->get('sonata.admin.pool');
//        $em = $this->getDoctrine()->getManager();
//        $user = $this->getUser();
//        if ($action == "list") {
//
//            if ($this->is_super())
//                $configurations = $em->getRepository('WhatsappBundle:Configuration')->findAll();
//            else {
//                $configurations = array();
//                $configurationsUsers = $user->getConfigurations();
//                foreach ($configurationsUsers as $key => $value) {
//                    $configurations[] = $value->getConfiguration();
//                }
//            }
//
//
//            return $this->render('WhatsappBundle:ADMIN:my_companies.html.twig', array(
//                        'admin_pool' => $admin_pool,
//                        'configurations' => $configurations,
//                        'user' => $user,
//            ));
//        }
//
//        if ($action == "create") {
//            if (!$this->is_super()) {
//                $user = $this->getUser();
//                if (!$user->getAprovedplan()) {
//                    $this->setFlash(
//                            'sonata_flash_error', 'Necesita adquirir un plan para agregar sus empresas.'
//                    );
//                    return $this->redirectToRoute('my_companies', array("action" => "list"));
//                }
//            }
//            $configuration = new Configuration();
//            $form = $this->createForm(new ConfigurationType(), $configuration);
//            if ($request->getMethod() == 'POST') {
//                $form->bind($request);
//                if ($form->isValid()) {
//                    $user = $this->getUser();
//                    $userCompany = new UserCompany();
//                    $userCompany->setRol("Administrador");
//
//                    $userCompany->setUser($user);
//
//                    $configuration->addUser($userCompany);
//                    $configuration->setOwner($user);
////                    $prefix=$this->slugify($configuration->getCompany());
////                    $prefix = substr($prefix, 0, 7);
////                    $prefix = $prefix."-";
////                    $configuration->setPrefix($prefix);
//
//                    $em->persist($configuration);
//                    $userCompany->setConfiguration($configuration);
//                    $em->persist($userCompany);
//                    $em->flush();
//                    $configuration->setPrefix("-Tre".$configuration->getId());
//                    $em->persist($configuration);
//                    $em->flush();
//                    $this->setFlash(
//                            'sonata_flash_success', 'La empresa se ha creado correctamente.'
//                    );
//                    return $this->redirectToRoute('my_companies', array("action" => "list"));
//                } else {
//                    $this->setFlash(
//                            'sonata_flash_error', 'La empresa no pudo crearse.'
//                    );
//                }
//            }
//
//            return $this->render('WhatsappBundle:ADMIN:my_company_create.html.twig', array(
//                        'form' => $form->createView(),
//                        'admin_pool' => $admin_pool,
//            ));
//        }
//        if ($action == "manage") {
//            $configuration = $id;
//            $user = $this->getUser();
//            $em = $this->getDoctrine()->getManager();
//            $myRole = "Usuario";
//            $userCompanyList = $em->getRepository('WhatsappBundle:UserCompany')->findByConfigurationByUser($configuration, $user);
//            if (count($userCompanyList) == 0 and ! $this->is_super())
//                return $this->redirectToRoute('my_companies', array("action" => "list"));
//            foreach ($userCompanyList as $value) {
//                if ($value->getRol() == "Administrador") {
//                    $myRole = "Administrador";
//                    break;
//                }
//            }
//
////            dump($configuration);die;
////            $users = $em->getRepository('ApplicationSonataUserBundle:User')->findPosThisIdDate($message->getDtmmessage(), $ticket);
//            return $this->render('WhatsappBundle:ADMIN:my_company_conf.html.twig', array(
//                        'admin_pool' => $admin_pool,
//                        'configuration' => $configuration,
//                        'myRole' => $myRole,
//                        'authenticateUser' => $user,
//            ));
//        }
//        if ($action == "delete") {
//            $configuration = $id;
//            if ($configuration->getOwner()->getUsername() == $user->getUsername()) {
//                $em->remove($id);
//                $em->flush();
//                return $this->redirectToRoute('my_companies', array("action" => "list"));
//            } else {
//                $this->setFlash(
//                        'sonata_flash_error', 'solo el dueño de una empresa puede eliminar la empresa.'
//                );
//                return $this->redirectToRoute('my_companies', array("action" => "list"));
//            }
//        }
//        return $this->render('WhatsappBundle:ADMIN:my_company_conf.html.twig', array(
//                    'admin_pool' => $admin_pool,
//                    'configuration' => null,
//        ));
//    }
//
//    /**
//     * @Route("/admin/my_compani/{id}/add_user", name="my_companies_add_user" )
//     */
//    public function MyCompanieAddUserAction($id) {
//        $request = $this->getRequest();
//        $admin_pool = $this->get('sonata.admin.pool');
//        $em = $this->getDoctrine()->getManager();
//        
//        if (!$this->verify_if_me_is_owner_configuration($id)) {
//            $this->setFlash(
//                    'sonata_flash_error', 'No tiene permisos suficientes para realizar la acción ejecutada.'
//            );
//            return $this->redirectToRoute('my_companies', array("action" => "manage", "id" => $id));
//        }
//
//        $ids = array();
//
//        $userCompanyList = $em->getRepository('WhatsappBundle:UserCompany')->findByConfiguration($id);
//        foreach ($userCompanyList as $userCompany) {
//            $ids[] = $userCompany->getUser()->getId();
//        }
//
//        $userCompany = new UserCompany();
//        $configuration = $em->getRepository('WhatsappBundle:Configuration')->find($id);
//        $userCompany->setConfiguration($configuration);
//        $options["ids"] = $ids;
//
//        $form = $this->createForm(new UserCompanyType(), $userCompany, $options);
//
//        if ($request->getMethod() == 'POST') {
//            $form->bind($request);
//            if ($form->isValid()) {
//                $em->persist($userCompany);
//
//                $em->flush();
//                $this->setFlash(
//                        'sonata_flash_success', 'Usuario agregado correctamente.'
//                );
//                return $this->redirectToRoute('my_companies', array("action" => "manage", "id" => $id));
//            } else {
//                $this->setFlash(
//                        'sonata_flash_error', 'El usuario no pudo crearse.'
//                );
//            }
//        }
//
//        return $this->render('WhatsappBundle:ADMIN:my_companies_add_user.html.twig', array(
//                    'form' => $form->createView(),
//                    'id' => $id,
//                    'admin_pool' => $admin_pool,
//        ));
//    }
//
//    /**
//     * @Route("/admin/my_compani/{id}/create_user", name="my_companies_create_user" )
//     */
//    public function MyCompanieCreateUserAction($id) {
//        $request = $this->getRequest();
//        $admin_pool = $this->get('sonata.admin.pool');
//        $em = $this->getDoctrine()->getManager();
//        
//        if (!$this->verify_if_me_is_owner_configuration($id)) {
//            $this->setFlash(
//                    'sonata_flash_error', 'No tiene permisos suficientes para realizar la acción ejecutada.'
//            );
//            return $this->redirectToRoute('my_companies', array("action" => "manage", "id" => $id));
//        }
//
//
//        $user = new User();
//
//
//
//        $form = $this->createForm(new UserType(), $user);
//
//        if ($request->getMethod() == 'POST') {
//            $form->bind($request);
//            if ($form->isValid()) {
//                $data = $form->getData();
//                $manipulator = $this->get('fos_user.util.user_manipulator');
//                $usermio = $manipulator->create($user->getUsername(), $user->getPlainPassword(), $user->getEmail(), true, false);
//                $userCompany = new UserCompany();
//                $configuration = $em->getRepository('WhatsappBundle:Configuration')->find($id);
//                $groups = $em->getRepository('ApplicationSonataUserBundle:Group')->findByName("Usuarios");
//                $usermio->addGroup($groups[0]);
//                $userCompany->setRol($form->get("rol")->getData());
//                $userCompany->setConfiguration($configuration);
//                $userCompany->setUser($usermio);
//
//                $em->persist($userCompany);
//                $em->persist($usermio);
//                $em->flush();
//                $this->setFlash(
//                        'sonata_flash_success', 'Usuario agregado correctamente.'
//                );
//                return $this->redirectToRoute('my_companies', array("action" => "manage", "id" => $id));
//            } else {
//                $this->setFlash(
//                        'sonata_flash_error', 'El usuario no pudo crearse.'
//                );
//            }
//        }
//
//        return $this->render('WhatsappBundle:ADMIN:my_companies_create_user.html.twig', array(
//                    'form' => $form->createView(),
//                    'id' => $id,
//                    'admin_pool' => $admin_pool,
//        ));
//    }
//
//    /**
//     * @Route("/admin/my_compani/{id}/add_email", name="my_companies_add_email" )
//     */
//    public function MyCompanieAddEmailAction($id) {
//        $request = $this->getRequest();
//        $admin_pool = $this->get('sonata.admin.pool');
//        $em = $this->getDoctrine()->getManager();
//        if (!$this->verify_if_me_have_admin_role_with_configuration($id)) {
//            $this->setFlash(
//                    'sonata_flash_error', 'No tiene permisos suficientes para realizar la acción ejecutada.'
//            );
//            return $this->redirectToRoute('my_companies', array("action" => "manage", "id" => $id));
//        }
//
//        $ids = array();
//
////        $userCompanyList = $em->getRepository('WhatsappBundle:UserCompany')->findByConfiguration($id);
////        foreach ($userCompanyList as $userCompany) {
////            $ids[] = $userCompany->getUser()->getId();
////        }
//        $alertEmail = new ConfigurationAlertEmail();
//        $configuration = $em->getRepository('WhatsappBundle:Configuration')->find($id);
//        $alertEmail->setConfiguration($configuration);
//        $form = $this->createForm(new AlertEmailType(), $alertEmail);
//
//        if ($request->getMethod() == 'POST') {
//            $form->bind($request);
//            if ($form->isValid()) {
//
//                $alertEmail->setConfiguration($configuration);
//                $em->persist($alertEmail);
//
//
//                $em->flush();
//                $configuration->addConfigurationAlertEmail($alertEmail);
//                $em->persist($configuration);
//                $this->setFlash(
//                        'sonata_flash_success', 'Correo agregado correctamente.'
//                );
//                return $this->redirectToRoute('my_companies', array("action" => "manage", "id" => $id));
//            } else {
//                $this->setFlash(
//                        'sonata_flash_error', 'El correo no pudo insertarse.'
//                );
//            }
//        }
//
//        return $this->render('WhatsappBundle:ADMIN:my_companies_add_email.html.twig', array(
//                    'form' => $form->createView(),
//                    'id' => $id,
//                    'admin_pool' => $admin_pool,
//        ));
//    }
//
//    /**
//     * @Route("/admin/my_compani/{id}/add_phone", name="my_companies_add_phone" )
//     */
//    public function MyCompanieAddPhoneAction($id) {
//        $request = $this->getRequest();
//        $admin_pool = $this->get('sonata.admin.pool');
//        $em = $this->getDoctrine()->getManager();
//        if (!$this->verify_if_me_have_admin_role_with_configuration($id)) {
//            $this->setFlash(
//                    'sonata_flash_error', 'No tiene permisos suficientes para realizar la acción ejecutada.'
//            );
//            return $this->redirectToRoute('my_companies', array("action" => "manage", "id" => $id));
//        }
//
//        $ids = array();
//
////        $userCompanyList = $em->getRepository('WhatsappBundle:UserCompany')->findByConfiguration($id);
////        foreach ($userCompanyList as $userCompany) {
////            $ids[] = $userCompany->getUser()->getId();
////        }
//        $alertPhone = new ConfigurationAlertPhone();
//        $configuration = $em->getRepository('WhatsappBundle:Configuration')->find($id);
//        $alertPhone->setConfiguration($configuration);
//        $form = $this->createForm(new AlertPhoneType(), $alertPhone);
//
//        if ($request->getMethod() == 'POST') {
//            $form->bind($request);
//            if ($form->isValid()) {
//
//                $alertPhone->setConfiguration($configuration);
//                $em->persist($alertPhone);
//                $em->flush();
//                $configuration->addConfigurationAlertPhone($alertPhone);
//                $em->persist($configuration);
//                $this->setFlash(
//                        'sonata_flash_success', 'Correo agregado correctamente.'
//                );
//                return $this->redirectToRoute('my_companies', array("action" => "manage", "id" => $id));
//            } else {
//                $this->setFlash(
//                        'sonata_flash_error', 'El correo no pudo insertarse.'
//                );
//            }
//        }
//
//        return $this->render('WhatsappBundle:ADMIN:my_companies_add_phone.html.twig', array(
//                    'form' => $form->createView(),
//                    'id' => $id,
//                    'admin_pool' => $admin_pool,
//        ));
//    }
//
//    /**
//     * @Route("/admin/my_compani/{configuration}/edit/{id}", name="my_companies_edit_user_company" )
//     */
//    public function MyCompanieEditUserCompanyAction($configuration, $id) {
//        $request = $this->getRequest();
//        $admin_pool = $this->get('sonata.admin.pool');
//        if (!$this->verify_if_me_is_owner_configuration($configuration)) {
//            $this->setFlash(
//                    'sonata_flash_error', 'No tiene permisos suficientes para realizar la acción ejecutada.'
//            );
//            return $this->redirectToRoute('my_companies', array("action" => "list"));
//        }
//        $em = $this->getDoctrine()->getManager();
//
//
//
//
//        $ids = array();
//
//        $userCompanyList = $em->getRepository('WhatsappBundle:UserCompany')->findByConfiguration($configuration);
////        foreach ($userCompanyList as $userCompanyThis) {
////            dump($userCompanyThis->getUser()->getId());
////            dump($userCompany->getUser()->getId());
////            
////            if($userCompanyThis->getUser()->getId() != $userCompany->getUser()->getId())
////                $ids[] = $userCompany->getUser()->getId();
////        }
//        $options["ids"] = $ids;
//        $userCompany = $em->getRepository('WhatsappBundle:UserCompany')->find($id);
//        if ($userCompany->getUser() == $userCompany->getConfiguration()->getOwner()) {
//            $this->setFlash(
//                    'sonata_flash_error', 'No puede administrar las configuraciones del dueño de la empresa.'
//            );
//            return $this->redirectToRoute('my_companies', array("action" => "manage", "id" => $configuration));
//        }
//        $form = $this->createForm(new UserCompanyEditType(), $userCompany, $options);
//        if ($request->getMethod() == 'POST') {
//            $form->bind($request);
//            if ($form->isValid()) {
//
//                $em->persist($userCompany);
//                $em->flush();
//                $this->setFlash(
//                        'sonata_flash_success', 'Datos actualizados.'
//                );
//                return $this->redirectToRoute('my_companies', array("action" => "manage", "id" => $configuration));
//            }
//        }
//        return $this->render('WhatsappBundle:ADMIN:my_companies_edit_user.html.twig', array(
//                    'form' => $form->createView(),
//                    'id' => $id,
//                    'configuration' => $configuration,
//                    'userCompany' => $userCompany,
//                    'admin_pool' => $admin_pool,
//        ));
//    }
//
//    /**
//     * @Route("/admin/my_compani/{configuration}/editemail/{id}", name="my_companies_edit_email_company" )
//     */
//    public function MyCompanieEditEmailAction($configuration, $id) {
//        $request = $this->getRequest();
//        $admin_pool = $this->get('sonata.admin.pool');
//        if (!$this->verify_if_me_have_admin_role_with_configuration($configuration)) {
//            $this->setFlash(
//                    'sonata_flash_error', 'No tiene permisos suficientes para realizar la acción ejecutada.'
//            );
//            return $this->redirectToRoute('my_companies', array("action" => "list"));
//        }
//        $em = $this->getDoctrine()->getManager();
//        $ids = array();
//
//        $userCompanyList = $em->getRepository('WhatsappBundle:UserCompany')->findByConfiguration($configuration);
////        foreach ($userCompanyList as $userCompanyThis) {
////            dump($userCompanyThis->getUser()->getId());
////            dump($userCompany->getUser()->getId());
////            
////            if($userCompanyThis->getUser()->getId() != $userCompany->getUser()->getId())
////                $ids[] = $userCompany->getUser()->getId();
////        }
//        $userCompany = $em->getRepository('WhatsappBundle:ConfigurationAlertEmail')->find($id);
//        $form = $this->createForm(new AlertEmailType(), $userCompany);
//        if ($request->getMethod() == 'POST') {
//            $form->bind($request);
//            if ($form->isValid()) {
//                $em->persist($userCompany);
//                $em->flush();
//                $this->setFlash(
//                        'sonata_flash_success', 'Datos actualizados.'
//                );
//                return $this->redirectToRoute('my_companies', array("action" => "manage", "id" => $configuration));
//            }
//        }
//        return $this->render('WhatsappBundle:ADMIN:my_companies_edit_email.html.twig', array(
//                    'form' => $form->createView(),
//                    'id' => $id,
//                    'configuration' => $configuration,
//                    'userCompany' => $userCompany,
//                    'admin_pool' => $admin_pool,
//        ));
//    }
//
//    /**
//     * @Route("/admin/my_compani/{configuration}/editphone/{id}", name="my_companies_edit_phone_company" )
//     */
//    public function MyCompanieEditPhoneAction($configuration, $id) {
//        $request = $this->getRequest();
//        $admin_pool = $this->get('sonata.admin.pool');
//        if (!$this->verify_if_me_have_admin_role_with_configuration($configuration)) {
//            $this->setFlash(
//                    'sonata_flash_error', 'No tiene permisos suficientes para realizar la acción ejecutada.'
//            );
//            return $this->redirectToRoute('my_companies', array("action" => "list"));
//        }
//        $em = $this->getDoctrine()->getManager();
//        $ids = array();
//
//        $userCompanyList = $em->getRepository('WhatsappBundle:UserCompany')->findByConfiguration($configuration);
////        foreach ($userCompanyList as $userCompanyThis) {
////            dump($userCompanyThis->getUser()->getId());
////            dump($userCompany->getUser()->getId());
////            
////            if($userCompanyThis->getUser()->getId() != $userCompany->getUser()->getId())
////                $ids[] = $userCompany->getUser()->getId();
////        }
//        $userCompany = $em->getRepository('WhatsappBundle:ConfigurationAlertPhone')->find($id);
//        $form = $this->createForm(new AlertPhoneType(), $userCompany);
//        if ($request->getMethod() == 'POST') {
//            $form->bind($request);
//            if ($form->isValid()) {
//                $em->persist($userCompany);
//                $em->flush();
//                $this->setFlash(
//                        'sonata_flash_success', 'Datos actualizados.'
//                );
//                return $this->redirectToRoute('my_companies', array("action" => "manage", "id" => $configuration));
//            }
//        }
//        return $this->render('WhatsappBundle:ADMIN:my_companies_edit_phone.html.twig', array(
//                    'form' => $form->createView(),
//                    'id' => $id,
//                    'configuration' => $configuration,
//                    'userCompany' => $userCompany,
//                    'admin_pool' => $admin_pool,
//        ));
//    }
//    
//    /**
//     * @Route("/admin/my_compani/{configuration}/confirmgroup/{id}", name="my_companies_confirm_group_company" )
//     */
//    public function MyCompanieConfirmGroupAction($configuration, $id) {
//        $request = $this->getRequest();
//        $admin_pool = $this->get('sonata.admin.pool');
//        if (!$this->verify_if_me_have_admin_role_with_configuration($configuration)) {
//            $this->setFlash(
//                    'sonata_flash_error', 'No tiene permisos suficientes para realizar la acción ejecutada.'
//            );
//            return $this->redirectToRoute('my_companies', array("action" => "list"));
//        }
//        $em = $this->getDoctrine()->getManager();        
//        $whatsappGroup = $em->getRepository('WhatsappBundle:WhatsappGroup')->find($id);
//        $whatsappGroup->setCompanyConfirmed(true);
//        $em->persist($whatsappGroup);
//        $em->flush();
//        //Buscar todos los mensajes del grupo y ponerle esta compañia
//        $messages = $whatsappGroup->getMessages();
//        foreach ($messages as $message) {
//            $message->setConfiguration($whatsappGroup->getConfiguration());
//            $em->persist($message);
//        }
//        $em->flush();
//        return $this->redirectToRoute('my_companies', array("action" => "manage", "id" => $configuration));
//    }
//    
//    /**
//     * @Route("/admin/my_compani/{configuration}/cancelgroup/{id}", name="my_companies_cancel_group_company" )
//     */
//    public function MyCompanieCancelGroupAction($configuration, $id) {
//        $request = $this->getRequest();
//        $admin_pool = $this->get('sonata.admin.pool');
//        if (!$this->verify_if_me_have_admin_role_with_configuration($configuration)) {
//            $this->setFlash(
//                    'sonata_flash_error', 'No tiene permisos suficientes para realizar la acción ejecutada.'
//            );
//            return $this->redirectToRoute('my_companies', array("action" => "list"));
//        }
//        $em = $this->getDoctrine()->getManager();
//        $whatsappGroup = $em->getRepository('WhatsappBundle:WhatsappGroup')->find($id);
//        $whatsappGroup->setConfiguration(null);
//        $em->persist($whatsappGroup);
//        $em->flush();
//        return $this->redirectToRoute('my_companies', array("action" => "manage", "id" => $configuration));
//    }
//
//    /**
//     * @Route("/admin/my_compani/{id}/edit", name="my_companies_edit_general_data" )
//     */
//    public function MyCompanieEditGeneralDataAction($id) {
//        $request = $this->getRequest();
//        $admin_pool = $this->get('sonata.admin.pool');
//        if (!$this->verify_if_me_have_admin_role_with_configuration($id)) {
//            $this->setFlash(
//                    'sonata_flash_error', 'No tiene permisos suficientes para realizar la acción ejecutada.'
//            );
//            return $this->redirectToRoute('my_companies', array("action" => "manage", "id" => $id));
//        }
//        $em = $this->getDoctrine()->getManager();
//        $oldConfiguration = $em->getRepository('WhatsappBundle:Configuration')->find($id);
//        $oldPrefix = $oldConfiguration->getPrefix();
//        $configuration = $em->getRepository('WhatsappBundle:Configuration')->find($id);
//        $form = $this->createForm(new ConfigurationType(), $configuration);
//        if ($request->getMethod() == 'POST') {
//            $form->bind($request);
//            if ($form->isValid()) {
//                //cambiar nombres de grupos si se cambio el prefijo
//                if ($oldPrefix != $configuration->getPrefix()) {
//                    $groups = $em->getRepository('WhatsappBundle:WhatsappGroup')->findByConfiguration($id);
//                    foreach ($groups as $group) {
//                        $groupName = $group->getName();
//                        $groupName = preg_replace("/^" . $oldPrefix . "/", $configuration->getPrefix(), $groupName);
//                        $group->setName($groupName);
//                        $em->persist($group);
//                    }
//                    if (count($groups) > 0) {
//                        $this->setFlash(
//                                'sonata_flash_info', 'Se han cambiado de forma automática los nombres de los grupos. Por favor verifique que coincidan con los nombres de su Whatsapp.'
//                        );
//                    }
//                    $em->persist($configuration);
//                }
//                $em->flush();
//                $this->setFlash(
//                        'sonata_flash_success', 'Datos actualizados.'
//                );
//                return $this->redirectToRoute('my_companies', array("action" => "manage", "id" => $id));
//            }
//        }
//
//
//        return $this->render('WhatsappBundle:ADMIN:my_company_edit_general_data.html.twig', array(
//                    'admin_pool' => $admin_pool,
//                    'form' => $form->createView(),
//                    'configuration' => $configuration,
//        ));
//    }
//
////    
////     /**
////     * @Route("/admin/my_companies/create", name="my_companies_edit" )
////     */
////    public function MyCompanieEditAction(Configuration $id)
////    {
////        $request = $this->getRequest();
////        $admin_pool = $this->get('sonata.admin.pool');
////        
////
////        return $this->render('WhatsappBundle:ADMIN:my_companies.html.twig', array(
////                    'admin_pool' => $admin_pool,
////        ));
////    }
//
//    /**
//     * @Route("/admin/my_companies/delete_user_from_company_id/{configuration}/{userCompany}", name="delete_user_from_company_id" )
//     */
//    public function deleteUserFromCompanyIdAction(Configuration $configuration, UserCompany $userCompany) {
//        $request = $this->getRequest();
//        $admin_pool = $this->get('sonata.admin.pool');
////        $userCompanyList = $em->getRepository('WhatsappBundle:UserCompany')->findByConfigurationByUser($userCompany, $user);
//        $em = $this->getDoctrine()->getManager();
//        if (!$this->verify_if_me_is_owner_configuration($configuration)) {
//            $this->setFlash(
//                    'sonata_flash_error', 'No tiene permisos suficientes para realizar la acción ejecutada.'
//            );
//            return $this->redirectToRoute('my_companies', array("action" => "list"));
//        }
//
//        $form = $this->createForm(new DeleteUserCompanyType(), $userCompany);
//
//        if ($request->getMethod() == 'POST') {
//            $form->bind($request);
//            if ($form->isValid()) {
//                $user = $this->getUser();
//                if ($user != $userCompany->getUser() && $userCompany->getUser() != $userCompany->getConfiguration()->getOwner()) {
//                    $em->remove($userCompany);
//                    $em->flush();
//                    $this->setFlash(
//                            'sonata_flash_success', 'Elemento eliminado correctamente.'
//                    );
//                } else {
//                    $this->setFlash(
//                            'sonata_flash_error', 'No puede borrar el usuario.'
//                    );
//                }
//                return $this->redirectToRoute('my_companies', array("action" => "manage", "id" => $configuration->getId()));
//            }
//        }
//        return $this->render('WhatsappBundle:ADMIN:delete_user_from_company_id_confirm.html.twig', array(
//                    'admin_pool' => $admin_pool,
//                    'form' => $form->createView(),
//                    'configuration' => $configuration->getId(),
//                    'userCompany' => $userCompany->getId(),
//        ));
//    }
//
//    /**
//     * @Route("/admin/my_companies/delete_email_from_company_id/{configuration}/{alertEmail}", name="delete_email_from_company_id" )
//     */
//    public function deleteEmailFromCompanyIdAction(Configuration $configuration, ConfigurationAlertEmail $alertEmail) {
//        $request = $this->getRequest();
//        $admin_pool = $this->get('sonata.admin.pool');
////        $alertEmailList = $em->getRepository('WhatsappBundle:UserCompany')->findByConfigurationByUser($alertEmail, $user);
//        $em = $this->getDoctrine()->getManager();
//        if (!$this->verify_if_me_have_admin_role_with_configuration($configuration)) {
//            $this->setFlash(
//                    'sonata_flash_error', 'No tiene permisos suficientes para realizar la acción ejecutada.'
//            );
//            return $this->redirectToRoute('my_companies', array("action" => "list"));
//        }
//
//        $form = $this->createForm(new DeleteUserCompanyType(), $alertEmail);
//
//        if ($request->getMethod() == 'POST') {
//            $form->bind($request);
//            if ($form->isValid()) {
//                $em->remove($alertEmail);
//                $em->flush();
//                $this->setFlash(
//                        'sonata_flash_success', 'Elemento eliminado correctamente.'
//                );
//
//                return $this->redirectToRoute('my_companies', array("action" => "manage", "id" => $configuration->getId()));
//            }
//        }
//        return $this->render('WhatsappBundle:ADMIN:delete_email_from_company_id_confirm.html.twig', array(
//                    'admin_pool' => $admin_pool,
//                    'form' => $form->createView(),
//                    'configuration' => $configuration->getId(),
//                    'alertEmail' => $alertEmail->getId(),
//        ));
//    }
//
//    /**
//     * @Route("/admin/my_companies/delete_phone_from_company_id/{configuration}/{alertPhone}", name="delete_phone_from_company_id" )
//     */
//    public function deletePhoneFromCompanyIdAction(Configuration $configuration, ConfigurationAlertPhone $alertPhone) {
//        $request = $this->getRequest();
//        $admin_pool = $this->get('sonata.admin.pool');
////        $alertEmailList = $em->getRepository('WhatsappBundle:UserCompany')->findByConfigurationByUser($alertEmail, $user);
//        $em = $this->getDoctrine()->getManager();
//        if (!$this->verify_if_me_have_admin_role_with_configuration($configuration)) {
//            $this->setFlash(
//                    'sonata_flash_error', 'No tiene permisos suficientes para realizar la acción ejecutada.'
//            );
//            return $this->redirectToRoute('my_companies', array("action" => "list"));
//        }
//
//        $form = $this->createForm(new DeleteUserCompanyType(), $alertPhone);
//
//        if ($request->getMethod() == 'POST') {
//            $form->bind($request);
//            if ($form->isValid()) {
//                $em->remove($alertPhone);
//                $em->flush();
//                $this->setFlash(
//                        'sonata_flash_success', 'Elemento eliminado correctamente.'
//                );
//
//                return $this->redirectToRoute('my_companies', array("action" => "manage", "id" => $configuration->getId()));
//            }
//        }
//        return $this->render('WhatsappBundle:ADMIN:delete_phone_from_company_id_confirm.html.twig', array(
//                    'admin_pool' => $admin_pool,
//                    'form' => $form->createView(),
//                    'configuration' => $configuration->getId(),
//                    'alertPhone' => $alertPhone->getId(),
//        ));
//    }
//
//
//
//    private function slugify($text) {
//        // replace non letter or digits by -
//        $text = preg_replace('~[^\pL\d]+~u', '-', $text);
//
//        // transliterate
//        $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);
//
//        // remove unwanted characters
//        $text = preg_replace('~[^-\w]+~', '', $text);
//
//        // trim
////      $text = trim($text, '-');
//        // remove duplicate -
//        $text = preg_replace('~-+~', '-', $text);
//
//        // lowercase
////      $text = strtolower($text);
//
//        if (empty($text)) {
//            return 'n-a';
//        }
//
//        return $text;
//    }
//
//    /**
//     * @Route("/admin/companie/delete_company/{configuration}", name="delete_company" )
//     */
//    public function deleteCompanyAction(Configuration $configuration) {
//        $request = $this->getRequest();
//        $admin_pool = $this->get('sonata.admin.pool');
//        $em = $this->getDoctrine()->getManager();
//        if (!$this->verify_if_me_have_admin_role_with_configuration($configuration)) {
//            $this->setFlash(
//                    'sonata_flash_error', 'No tiene permisos suficientes para realizar la acción ejecutada.'
//            );
//            return $this->redirectToRoute('my_companies', array("action" => "list"));
//        }
//
//        $form = $this->createForm(new DeleteUserCompanyType(), $configuration);
//
//        if ($request->getMethod() == 'POST') {
//            $form->bind($request);
//            if ($form->isValid()) {
//                $user = $this->getUser();
//                if ($configuration->getOwner()) {
//                    if ($configuration->getOwner()->getUsername() == $user->getUsername()) {
//
//                        //Borrar todos las configuraciones de la empresa
//                        //
//                        $alerts = $em->getRepository('WhatsappBundle:Alert')->findByConfiguration($configuration);
//                        if (count($alerts) > 0) {
//                            foreach ($alerts as $value) {
//                                $em->remove($value);
//                                $em->flush();
//                            }
//                        }
//                        $configurationAlertEmail = $em->getRepository('WhatsappBundle:ConfigurationAlertEmail')->findByConfiguration($configuration);
//                        if (count($configurationAlertEmail) > 0) {
//                            foreach ($configurationAlertEmail as $value) {
//                                $em->remove($value);
//                                $em->flush();
//                            }
//                        }
//                        $configurationAlertPhone = $em->getRepository('WhatsappBundle:ConfigurationAlertPhone')->findByConfiguration($configuration);
//                        if (count($configurationAlertPhone) > 0) {
//                            foreach ($configurationAlertPhone as $value) {
//                                $em->remove($value);
//                                $em->flush();
//                            }
//                        }
//                        $firstAnswerKeyword = $em->getRepository('WhatsappBundle:FirstAnswerKeyword')->findByConfiguration($configuration);
//                        if (count($firstAnswerKeyword) > 0) {
//                            foreach ($firstAnswerKeyword as $value) {
//                                $em->remove($value);
//                                $em->flush();
//                            }
//                        }
//                        $firstNoFollowKeyword = $em->getRepository('WhatsappBundle:FirstNoFollowKeyword')->findByConfiguration($configuration);
//                        if (count($firstNoFollowKeyword) > 0) {
//                            foreach ($firstNoFollowKeyword as $value) {
//                                $em->remove($value);
//                                $em->flush();
//                            }
//                        }
//                        $lastAnswerKeyword = $em->getRepository('WhatsappBundle:LastAnswerKeyword')->findByConfiguration($configuration);
//                        if (count($lastAnswerKeyword) > 0) {
//                            foreach ($lastAnswerKeyword as $value) {
//                                $em->remove($value);
//                                $em->flush();
//                            }
//                        }
//                        $ticket = $em->getRepository('WhatsappBundle:Ticket')->findByConfiguration($configuration);
//                        if (count($ticket) > 0) {
//                            foreach ($ticket as $value) {
//                                $em->remove($value);
//                                $em->flush();
//                            }
//                        }
//                        $message = $em->getRepository('WhatsappBundle:Message')->findByConfiguration($configuration);
//                        if (count($message) > 0) {
//                            foreach ($message as $value) {
//                                $em->remove($value);
//                                $em->flush();
//                            }
//                        }
//                        $whatsappGroup = $em->getRepository('WhatsappBundle:WhatsappGroup')->findByConfiguration($configuration);
//                        if (count($whatsappGroup) > 0) {
//                            foreach ($whatsappGroup as $value) {
//                                $em->remove($value);
//                                $em->flush();
//                            }
//                        }
//                        
//                        
//                        $solutionType = $em->getRepository('WhatsappBundle:SolutionType')->findByConfiguration($configuration);
//                        if (count($solutionType) > 0) {
//                            foreach ($solutionType as $value) {
//                                $em->remove($value);
//                                $em->flush();
//                            }
//                        }
//                        $supportMember = $em->getRepository('WhatsappBundle:SupportMember')->findByConfiguration($configuration);
//                        if (count($supportMember) > 0) {
//                            foreach ($supportMember as $value) {
//                                $em->remove($value);
//                                $em->flush();
//                            }
//                        }
//                        
//                        $ticketType = $em->getRepository('WhatsappBundle:TicketType')->findByConfiguration($configuration);
//                        if (count($ticketType) > 0) {
//                            foreach ($ticketType as $value) {
//                                $em->remove($value);
//                                $em->flush();
//                            }
//                        }
//                        $userCompany = $em->getRepository('WhatsappBundle:UserCompany')->findByConfiguration($configuration);
//                        if (count($userCompany) > 0) {
//                            foreach ($userCompany as $value) {
//                                $em->remove($value);
//                                $em->flush();
//                            }
//                        }
//                        $validationKeyword = $em->getRepository('WhatsappBundle:ValidationKeyword')->findByConfiguration($configuration);
//                        if (count($validationKeyword) > 0) {
//                            foreach ($validationKeyword as $value) {
//                                $em->remove($value);
//                                $em->flush();
//                            }
//                        }
//                        
//                        $em->remove($configuration);
//                        $em->flush();
//                        $this->setFlash(
//                                'sonata_flash_success', 'Elemento eliminado correctamente.'
//                        );
//                        return $this->redirectToRoute('my_companies', array("action" => "list"));
//                    } else {
//                        $this->setFlash(
//                                'sonata_flash_error', 'Solo el dueño de una empresa puede eliminar la empresa.'
//                        );
//                        return $this->redirectToRoute('my_companies', array("action" => "list"));
//                    }
//                } else {
//                    $this->setFlash(
//                            'sonata_flash_error', 'Solo el dueño de una empresa puede eliminar la empresa.'
//                    );
//                    return $this->redirectToRoute('my_companies', array("action" => "list"));
//                }
//
//                return $this->redirectToRoute('my_companies', array("action" => "manage", "id" => $configuration->getId()));
//            }
//        }
//        return $this->render('WhatsappBundle:ADMIN:delete_company_id_confirm.html.twig', array(
//                    'admin_pool' => $admin_pool,
//                    'form' => $form->createView(),
//                    'configuration' => $configuration->getId(),
//                    'company' => $configuration->getCompany(),
//        ));
//    }
//    
//    
//    /**
//     * @Route("/admin/client_to_support/{clientMember}", name="client_to_support" )
//     */
//    public function clientToSupportClickAction(ClientMember $clientMember) {
//        $id = $clientMember->getConfiguration()->getId();
//        if (!$this->verify_if_me_have_admin_role_with_configuration($id)) {
//            $this->setFlash(
//                    'sonata_flash_error', 'No tiene permisos suficientes para realizar la acción ejecutada.'
//            );
//            return $this->redirectToRoute('admin_whatsapp_clientMember_list');
//        }
//        $request = $this->getRequest();
//        $admin_pool = $this->get('sonata.admin.pool');
//        $em = $this->getDoctrine()->getManager();
//        $supportMember = new SupportMember();
//        $supportMemberArray = $em->getRepository('WhatsappBundle:SupportMember')->findByAuthor($clientMember->getAuthor());
//        if(count($supportMemberArray) > 0){
//            $supportMember = $supportMemberArray[0];
//        }
//        $supportMember->setAuthor($clientMember->getAuthor());
//        $supportMember->setConfiguration($clientMember->getConfiguration());
//        $supportMember->setName($clientMember->getName());
//        $supportMember->setPhoneNumber($clientMember->getPhoneNumber());
//        $supportMember->setWhatsappNick($clientMember->getWhatsappNick());
//        $em->persist($supportMember);
//        $em->flush();
//                
//        $messages = $em->getRepository('WhatsappBundle:Message')->findByClientMember($clientMember->getId());
//        foreach ($messages  as $message) {
//            $message->setClientMember(null);
//            $message->setSupportMember($supportMember);
//            $em->persist($message);
//        }
//        $em->flush();
//        $em->remove($clientMember);
//        $em->flush();
//        $this->addFlash('sonata_flash_success', 'Se ha convertido el cliente en miembro de soporte');
//        
//        return $this->redirectToRoute('admin_whatsapp_supportmember_list');
//          
//    }
//    /**
//     * @Route("/admin/support_to_client/{supportMember}", name="support_to_client" )
//     */
//    public function supportToClientClickAction(SupportMember $supportMember) {
//        $id = $supportMember->getConfiguration()->getId();
//        if (!$this->verify_if_me_have_admin_role_with_configuration($id)) {
//            $this->setFlash(
//                    'sonata_flash_error', 'No tiene permisos suficientes para realizar la acción ejecutada.'
//            );
//            return $this->redirectToRoute('admin_whatsapp_clientMember_list');
//        }
//        $request = $this->getRequest();
//        $admin_pool = $this->get('sonata.admin.pool');
//        $em = $this->getDoctrine()->getManager();
//        $clientMember = new ClientMember();
//        $clientMemberArray = $em->getRepository('WhatsappBundle:ClientMember')->findByAuthor($supportMember->getAuthor());
//        if(count($clientMemberArray) > 0){
//            $clientMember = $clientMemberArray[0];
//        }
//        $clientMember->setAuthor($supportMember->getAuthor());
//        $clientMember->setConfiguration($supportMember->getConfiguration());
//        $clientMember->setName($supportMember->getName());
//        $clientMember->setPhoneNumber($supportMember->getPhoneNumber());
//        $clientMember->setWhatsappNick($supportMember->getWhatsappNick());
//        $em->persist($clientMember);
//        $em->flush();
//                
//        $messages = $em->getRepository('WhatsappBundle:Message')->findBySupportMember($supportMember->getId());
//        foreach ($messages  as $message) {
//            $message->setSupportMember(null);
//            $message->setClientMember($clientMember);
//            $em->persist($message);
//        }
//        $em->flush();
//        $em->remove($supportMember);
//        $em->flush();
//        $this->addFlash('sonata_flash_success', 'Se ha convertido el miembro de soporte en cliente');
//        
//        return $this->redirectToRoute('admin_whatsapp_clientmember_list');
//          
//    }
//    
//    /**
//     * @Route("/admin/my_companies/delete_group_from_company_id/{configuration}/{id}", name="delete_group_from_company_id" )
//     */
//    public function deleteGroupFromCompanyIdAction(Configuration $configuration, WhatsappGroup $group) {
//        $request = $this->getRequest();
//        $admin_pool = $this->get('sonata.admin.pool');
////        $userCompanyList = $em->getRepository('WhatsappBundle:UserCompany')->findByConfigurationByUser($userCompany, $user);
//        $em = $this->getDoctrine()->getManager();
//        if (!$this->verify_if_me_is_owner_configuration($configuration)) {
//            $this->setFlash(
//                    'sonata_flash_error', 'No tiene permisos suficientes para realizar la acción ejecutada.'
//            );
//            return $this->redirectToRoute('my_companies', array("action" => "list"));
//        }
//
//        $form = $this->createForm(new DeleteUserCompanyType(), $group);
//
//        if ($request->getMethod() == 'POST') {
//            $form->bind($request);
//            if ($form->isValid()) {
//                $user = $this->getUser();
//                $userCompany = null;
//                $userCompanyList = $em->getRepository('WhatsappBundle:UserCompany')->findByConfigurationByUser($configuration, $user);
//                if(count($userCompanyList) > 0){
//                    $userCompany = $userCompanyList[0];
//                }
//                if ($userCompany->getUser() == $userCompany->getConfiguration()->getOwner()) {
//                   $group->setCompanyConfirmed(false);
//                   $group->setConfiguration(null);
//                   $em->persist($group);
//                   $em->flush();
////                     $em->remove($userCompany);
////                    $em->flush();
//                    
//                    $this->setFlash(
//                            'sonata_flash_success', 'Elemento eliminado correctamente.'
//                    );
//                } else {
//                    $this->setFlash(
//                            'sonata_flash_error', 'No tiene permisos para borrar el grupo.'
//                    );
//                }
//                return $this->redirectToRoute('my_companies', array("action" => "manage", "id" => $configuration->getId()));
//            }
//        }
//        return $this->render('WhatsappBundle:ADMIN:delete_group_from_company_id_confirm.html.twig', array(
//                    'admin_pool' => $admin_pool,
//                    'form' => $form->createView(),
//                    'configuration' => $configuration->getId(),
//                    'group' => $group->getId(),
//        ));
//    }

    private function is_super() {
        if ($this->get('security.context')->isGranted('ROLE_SUPER_ADMIN')) {
            return true;
        }
        return false;
    }

    private function verify_if_me_have_admin_role_with_configuration($configuration) {
        $user = $this->getUser();
        if ($this->is_super())
            return true;
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

    private function verify_if_me_is_owner_configuration($configuration) {
        $user = $this->getUser();
        if ($this->is_super())
            return true;
        $em = $this->getDoctrine()->getManager();
        $configuration = $em->getRepository('WhatsappBundle:Configuration')->find($configuration);
        if ($configuration->getOwner()->getId() == $user->getId())
            return true;
        return false;
    }

    private function slugify($text) {
        // replace non letter or digits by -
        $text = preg_replace('~[^\pL\d]+~u', '-', $text);

        // transliterate
        $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);

        // remove unwanted characters
        $text = preg_replace('~[^-\w]+~', '', $text);

        // trim
//      $text = trim($text, '-');
        // remove duplicate -
        $text = preg_replace('~-+~', '-', $text);

        // lowercase
//      $text = strtolower($text);

        if (empty($text)) {
            return 'n-a';
        }

        return $text;
    }

    /**
     * @param string $action
     * @param string $value
     */
    protected function setFlash($action, $value) {
        $this->get('session')->getFlashBag()->set($action, $value);
    }
    
     public function customCompareMessagesByDate($a, $b) {

        if ($a->getDtmmessage() == $b->getDtmmessage()) {
            return 0;
        }
        return ($a->getDtmmessage() > $b->getDtmmessage()) ? 1 : -1;
    }
    
    protected function getObfuscatedEmail($user)
    {
        $email = $user->getEmail();
        if (false !== $pos = strpos($email, '@')) {
            $email = '...' . substr($email, $pos);
        }

        return $email;
    }
    

}
