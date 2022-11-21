<?php

namespace WhatsappBundle\Controller;

use FOS\RestBundle\Controller\Annotations\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Request;
use FOS\RestBundle\Controller\Annotations;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Routing\ClassResourceInterface;
use FOS\RestBundle\Controller\Annotations\RouteResource;
use Symfony\Component\HttpFoundation\JsonResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use FOS\RestBundle\Controller\Annotations\View;
use FOS\RestBundle\Controller\Annotations\Post;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use WhatsappBundle\Model\OpenTicketApiResponse;
use WhatsappBundle\Model\OpenTicketListResponse;
use WhatsappBundle\Model\TicketListResponse;
use WhatsappBundle\Model\TicketApiResponse;
use WhatsappBundle\Model\MessageApiResponse;
use WhatsappBundle\Model\MessageListResponse;
use WhatsappBundle\Model\CompanySummaryStatusApiResponse;
use FOS\RestBundle\Request\ParamFetcher;
use FOS\RestBundle\Controller\Annotations\RequestParam;
use FOS\RestBundle\Controller\Annotations\QueryParam;
use Symfony\Component\HttpKernel\Exception\HttpException;

class JwtApiController extends FOSRestController implements ClassResourceInterface {

    
    /**
     * @Route("/api/user-profile", name="jwtapiuserprofile")
     * @Method({"GET", "POST"})
     * 
     * @View(
     *  templateVar="",
     *  statusCode=null,
     *  serializerGroups={},
     *  populateDefaultVars=true,
     *  serializerEnableMaxDepthChecks=false
     * )
     *     
     * @ApiDoc(
     *  resource=true,
     *  description="Retorna perfil del usuario", resource=true,
     *  output={
     *   "class"   = "WhatsappBundle\Model\OpenTicketListResponse",
     *   "parsers" = {
     *       "Nelmio\ApiDocBundle\Parser\JmsMetadataParser",
     *       "Nelmio\ApiDocBundle\Parser\ValidationParser"
     *   }
     * },
     *  filters={

     *  },
     * statusCodes = {
     *     200 = "Returned when successful",
     *     500 = "Internal error"
     *   },
     * 
     * )
     */
    public function getProfileAction(Request $request) {
        $jwtManager = $this->get('lexik_jwt_authentication.jwt_manager');

        $tokenStorage = $this->get('security.token_storage');
        $token = $tokenStorage->getToken();

        $user = $token->getUser();
        
        
        $view = $this->view($user);
//                dump($view);die;
        return $view;
    }
    
    /**
     * @Route("/api/tickets-list", name="jwtapiticketslist")
     * @Method({"GET", "POST"})
     * 
     * @View(
     *  templateVar="",
     *  statusCode=null,
     *  serializerGroups={},
     *  populateDefaultVars=true,
     *  serializerEnableMaxDepthChecks=false
     * )
     *     
     * @ApiDoc(
     *  resource=true,
     *  description="Retorna perfil del usuario", resource=true,
     *  output={
     *   "class"   = "WhatsappBundle\Model\OpenTicketListResponse",
     *   "parsers" = {
     *       "Nelmio\ApiDocBundle\Parser\JmsMetadataParser",
     *       "Nelmio\ApiDocBundle\Parser\ValidationParser"
     *   }
     * },
     *  filters={

     *  },
     * statusCodes = {
     *     200 = "Returned when successful",
     *     500 = "Internal error"
     *   },
     * 
     * )
     */
    public function getTicketsListAction(Request $request) {
        $em = $this->getDoctrine()->getManager();
        $findAll = $em->getRepository('WhatsappBundle:Peticion')->findAll();
        $result = array();
        foreach ($findAll as $value) {
            $p = new \WhatsappBundle\Entity\Peticion();
            $p->setId($value->getId()); 
            $result[] = $p;
        }
        //$findAll = array();
        //$findAll[] = $result;
        
        $view = $this->view($result);
//                dump($view);die;
        return $view;
    }
    
    
    /**
     * @Route("/api/messages-list-by-peticion/{id}", name="jwtapiticketslist")
     * @Method({"GET", "POST"})
     * 
     * @View(
     *  templateVar="",
     *  statusCode=null,
     *  serializerGroups={},
     *  populateDefaultVars=true,
     *  serializerEnableMaxDepthChecks=false
     * )
     *     
     * @ApiDoc(
     *  resource=true,
     *  description="Retorna perfil del usuario", resource=true,
     *  output={
     *   "class"   = "WhatsappBundle\Model\OpenTicketListResponse",
     *   "parsers" = {
     *       "Nelmio\ApiDocBundle\Parser\JmsMetadataParser",
     *       "Nelmio\ApiDocBundle\Parser\ValidationParser"
     *   }
     * },
     *  filters={

     *  },
     * statusCodes = {
     *     200 = "Returned when successful",
     *     500 = "Internal error"
     *   },
     * 
     * )
     */
    public function getMessageByPeticionAction(Request $request, $id) {
        $em = $this->getDoctrine()->getManager();
        $findAll = $em->getRepository('WhatsappBundle:Message')->findByTicketOrderDt($id);
        $result = array();
        foreach ($findAll as $value) {
            $p = new \WhatsappBundle\Entity\Message();
            $p->setId($value->getId()); 
            $p->setStrmenssagetext($value->getStrmenssagetext()); 
            $result[] = $p;
        }
        //$findAll = array();
        //$findAll[] = $result;
        
        $view = $this->view($result);
//                dump($view);die;
        return $view;
    }
    

}
