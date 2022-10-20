<?php


namespace WhatsappBundle\Tests\Admin;

//use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use WhatsappBundle\Common\AbstractTestCase;
use Symfony\Component\BrowserKit\Cookie;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;



class SolutionTypeAdminTest extends AbstractTestCase
{
    /**
     * fixtures to load before each test
     */
    protected $fixtures = array(
        'WhatsappBundle\DataFixtures\ORM\LoadUserData'
        
    );
    

    public function setUp()
    {
        parent::setUp();
        $this->logIn();
    }
     
    public function testList()
    {
        
//        $client = static::createClient(array(), array(
//        'PHP_AUTH_USER' => 'admin',
//        'PHP_AUTH_PW'   => '1820.Tre$iteUbun',
//        ));
//        $client->request('GET', '/admin/whatsapp/ticket/list', array(), array(), array(
//        'PHP_AUTH_USER' => 'admin',
//        'PHP_AUTH_PW'   => '1820.Tre$iteUbun',
//        ));
            $this->logIn();
//        $client = static::createClient(array(), array(
//            'PHP_AUTH_USER' => 'admin',
//            'PHP_AUTH_PW'   => '1820.Tre$iteUbun',
//        ));
        $client = $this->client;
        $client->request('GET', '/admin/whatsapp/solutiontype/list');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        
    }
    
    public function testNew()
    {
        $this->logIn();
        $client = $this->client;
        $client->request('GET', '/admin/whatsapp/solutiontype/create');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        
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
   
   
}
