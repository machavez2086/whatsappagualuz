<?php

namespace WhatsappBundle\Block\Service;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Sonata\BlockBundle\Model\BlockInterface;
use Sonata\BlockBundle\Block\BlockContextInterface;

use Sonata\AdminBundle\Form\FormMapper;
use Sonata\CoreBundle\Validator\ErrorElement;
use Sonata\BlockBundle\Block\BaseBlockService;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;

class RssBlockService extends BaseBlockService{
   
    /**
     * @var string
     */
    protected $em;
     /**
     * @param string          $name
     * @param EngineInterface $templating
     */
    public function __construct($name, EngineInterface $templating, $em, $securitytoken_storage)
    {
        $this->name = $name;
        $this->templating = $templating;
        $this->em = $em;
        $this->user = $securitytoken_storage->getToken()->getUser();
    }
    
public function configureSettings(OptionsResolver $resolver)
{
    $resolver->setDefaults(array(
        'url'      => false,
        'title'    => 'Mis empresas',
        'template' => 'WhatsappBundle:Block:block_core_rss.html.twig',
    ));
}
//
//public function buildEditForm(FormMapper $formMapper, BlockInterface $block)
//{
//    $formMapper
//        ->add('settings', 'sonata_type_immutable_array', array(
//            'keys' => array(
//                array('url', 'url', array('required' => false)),
//                array('title', 'text', array('required' => false)),
//            )
//        ))
//    ;
//}


public function validateBlock(ErrorElement $errorElement, BlockInterface $block)
{
    $errorElement
        ->with('settings.url')
            ->assertNotNull(array())
            ->assertNotBlank()
        ->end()
        ->with('settings.title')
            ->assertNotNull(array())
            ->assertNotBlank()
            ->assertMaxLength(array('limit' => 50))
        ->end()
    ;
}


public function execute(BlockContextInterface $blockContext, Response $response = null)
{
    // merge settings
    $settings = $blockContext->getSettings();
//    $feeds = false;
//    
//    if ($settings['url']) {
//        $options = array(
//            'http' => array(
//                'user_agent' => 'Sonata/RSS Reader',
//                'timeout' => 2,
//            )
//        );
//
//        // retrieve contents with a specific stream context to avoid php errors
//        $content = @file_get_contents($settings['url'], false, stream_context_create($options));
//
//        if ($content) {
//            // generate a simple xml element
//            try {
//                $feeds = new \SimpleXMLElement($content);
//                $feeds = $feeds->channel->item;
//            } catch (\Exception $e) {
//                // silently fail error
//            }
//        }
//    }
    $configurations = array();
    $em = $this->em;
    $user = $this->user;
    $userCompanyList = $em->getRepository('WhatsappBundle:UserCompany')->findByUser($user);
    $totaltickets = 0;
    $total = 0;
    $totalmessages = 0;
    $totalalerts = 0;
    foreach ($userCompanyList as $value) {
        $configurations[] = $value->getConfiguration()->getId();
        $total = $total+1;
        $totaltickets = $totaltickets+$em->getRepository('WhatsappBundle:Ticket')->ticketsCountByDates(new \DateTime("1970"),new \DateTime("tomorrow"), $value->getConfiguration()->getId());
        $totalmessages = $totalmessages + $em->getRepository('WhatsappBundle:Message')->messagesFilteredCount(1, 1, "id", "ASC", $value->getConfiguration()->getId(), array());
        $totalalerts = $totalalerts + $em->getRepository('WhatsappBundle:Alert')->countTotalAlertsAnswer($value->getConfiguration()->getId()) + $em->getRepository('WhatsappBundle:Alert')->countTotalAlertsSolution($value->getConfiguration()->getId());
    }
    

    return $this->renderResponse($blockContext->getTemplate(), array(
        'total'     => $total,
        'totaltickets'     => $totaltickets,
        'totalmessages'     => $totalmessages,
        'totalalerts'     => $totalalerts,
        'block'     => $blockContext->getBlock(),
        'settings'  => $settings
    ), $response);
}

}