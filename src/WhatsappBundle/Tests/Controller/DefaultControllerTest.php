<?php


namespace WhatsappBundle\Tests\Controller;

//use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use WhatsappBundle\Common\AbstractTestCase;
use Symfony\Component\BrowserKit\Cookie;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use WhatsappBundle\Controller\DefaultController;


class DefaultControllerTest extends AbstractTestCase
{
    /**
     * fixtures to load before each test
     */
    protected $fixtures = array(
        'WhatsappBundle\DataFixtures\ORM\LoadUserData',
        'WhatsappBundle\DataFixtures\ORM\LoadConfigurationData',
        'WhatsappBundle\DataFixtures\ORM\LoadTicketsData',
        
    );

    public function setUp()
    {
        parent::setUp();

    }
     
    public function testDashboard()
    {
        $this->logIn();
        $client = $this->client;
        $client->request('GET', '/frontend/dashboard');
//        dump($client->getResponse());die;
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        
    }
     
    public function testReportPeticion403()
    {
        $client = $this->client;
        $client->request('GET', '/frontend/report_peticion');
        $this->assertEquals(302, $client->getResponse()->getStatusCode());
    }
    public function testReportPeticion()
    {
        $this->logIn();
        $client = $this->client;
        $client->request('GET', '/frontend/report_peticion');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }
    public function testReportByDateRange()
    {
        $this->logIn();
        $client = $this->client;
        $client->request('GET', '/frontend/report_by_date_range');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }
    
    public function testReportByDateRange403()
    {
        $client = $this->client;
        $client->request('GET', '/frontend/report_by_date_range');
        $this->assertEquals(302, $client->getResponse()->getStatusCode());
    }
    
    private function logIn()
    {
        $session = $this->client->getContainer()->get('session');

        $firewallName = 'admin';
        // if you don't define multiple connected firewalls, the context defaults to the firewall name
        // See https://symfony.com/doc/current/reference/configuration/security.html#firewall-context
        $firewallContext = 'user';

        // you may need to use a different token class depending on your application.
        // for example, when using Guard authentication you must instantiate PostAuthenticationGuardToken
        $em = $this->client->getContainer()->get('doctrine')->getManager(); 
        $user = $em->getRepository('ApplicationSonataUserBundle:User')->findOneByUsername('admin');
        $token = new UsernamePasswordToken($user, $user->getPassword(), $firewallName, $user->getRoles());
        self::$kernel->getContainer()->get('security.context')->setToken($token);

        $session = $this->client->getContainer()->get('session');
        $session->set('_security_' . $firewallContext, serialize($token));
        $session->save();
        $cookie = new Cookie($session->getName(), $session->getId());
        $this->client->getCookieJar()->set($cookie);

    }
    
     public function testcustomCompare()
    {
        $controller = new DefaultController();
        $value = $controller->customCompare(array("as", 3, 3, 5), array("as", 3, 3, 5));
        $this->assertEquals(0,$value);
        $value = $controller->customCompare(array("as", 3, 3, 5), array("as", 3, 3, 6));
        $this->assertEquals(1,$value);
        $value = $controller->customCompare(array("as", 3, 3, 6), array("as", 3, 3, 5));
        $this->assertEquals(-1,$value);
    }
    
     public function testgetDayOfWeek()
    {
        $controller = new DefaultController();
        $value = $controller->customCompare(array("as", 3, 3, 5), array("as", 3, 3, 5));
        $this->assertEquals(0,$value);
        $value = $controller->customCompare(array("as", 3, 3, 5), array("as", 3, 3, 6));
        $this->assertEquals(1,$value);
        $value = $controller->customCompare(array("as", 3, 3, 6), array("as", 3, 3, 5));
        $this->assertEquals(-1,$value);
    }
   
}
