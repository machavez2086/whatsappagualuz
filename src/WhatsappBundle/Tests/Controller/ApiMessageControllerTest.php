<?php

namespace WhatsappBundle\Tests\Controller;

//use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

use WhatsappBundle\Common\AbstractTestCase;

class ApiMessageControllerTest extends AbstractTestCase
{
     /**
     * fixtures to load before each test
     */
    protected $fixtures = array(
        'WhatsappBundle\DataFixtures\ORM\LoadTicketsData'
    );
//    public function setUp()
//    {
//        parent::setUp();
//        $this->logIn();
//        
//    }
    
    public function testGetMessages()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/api/getmessages');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }
    public function testGetMessagesAllFilters()
    {
        $client = static::createClient();
//        $crawler = $client->request('GET', '/api/getmessages?filter[id][type]=&filter[id][value]=&filter[name][type]=&filter[name][value]=a&filter[startDate][type]=between&filter[startDate][value][start]=01-09-2018+00%3A00%3A00&filter[startDate][value][end]=04-11-2018+21%3A59%3A00&filter[weekday][type]=&filter[weekday][value]=&filter[whatsappGroup][type]=&filter[whatsappGroup][value]=&filter[solvedBySupportMember][type]=&filter[solvedBySupportMember][value]=&filter[ticketType][type]=&filter[ticketType][value]=&filter[solutionType][type]=&filter[solutionType][value]=&filter[endDate][type]=&filter[endDate][value]=&filter[startTime][type]=&filter[startTime][value][start]=13%3A38%3A16&filter[startTime][value][end]=23%3A59&filter[firstanswer][type]=&filter[firstanswer][value]=&filter[nofollow][type]=&filter[nofollow][value]=&filter[ticketended][type]=&filter[ticketended][value]=&filter[minutesAnswerTime][type]=&filter[minutesAnswerTime][value]=&filter[minutesSolutionTime][type]=&filter[minutesSolutionTime][value]=&_page=1&_sort_by=id&_sort_order=DESC&_per_page=32');
        $crawler = $client->request('GET', '/api/getmessages?filter_id_type=eq&filter_id_value=361&filter_supportFirstAnswer_type=eq&filter_supportFirstAnswer_value=true&filter_isValidationKeyword_type=eq&filter_isValidationKeyword_value=true&filter_ticket_type=eq&filter_ticket_value=1&filter_whatsappGroup_type=eq&filter_whatsappGroup_value=1&filter_supportMember_type=eq&filter_supportMember_value=1&filter_clientMember_type=eq&filter_clientMember_value=1&filter_strmenssagetext_type=eq&filter_strmenssagetext_value=1&filter_dtmmessage_type=eq&filter_dtmmessage_value=01-09-2018+00%3A00%3A00&filter_dtmmessage_value_start=01-09-2018+00%3A00%3A00&filter_dtmmessage_value_end=01-09-2018+00%3A00%3A00&_page=1&_per_page=20&_sort_by=id&_sort_order=asc');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }
    public function testGetMessagesBadFilters()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/api/getmessages?filter_id_type=esadq&filter_id_value');
        $this->assertEquals(400, $client->getResponse()->getStatusCode());
    }
    public function testGetMessagesGoodFiltersId()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/api/getmessages?filter_id_type=eq&filter_id_value=');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        
        $crawler = $client->request('GET', '/api/getmessages?filter_id_value=2');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }
    public function testGetMessagesBadFiltersId()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/api/getmessages?filter_id_type=esadq&filter_id_value=');
        $this->assertEquals(400, $client->getResponse()->getStatusCode());
        $crawler = $client->request('GET', '/api/getmessages?filter_id_value=sdf');
        $this->assertEquals(400, $client->getResponse()->getStatusCode());
    }
    
    public function testGetMessagesGoodFiltersSupportFirstAnswer()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/api/getmessages?filter_supportFirstAnswer_type=eq');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $crawler = $client->request('GET', '/api/getmessages?filter_supportFirstAnswer_value=true');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }
    public function testGetMessagesBadFiltersSupportFirstAnswer()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/api/getmessages?filter_supportFirstAnswer_type=between&filter_supportFirstAnswer_value=');
        $this->assertEquals(400, $client->getResponse()->getStatusCode());
    }
    public function testGetMessagesGoodFiltersIsValidationKeyword()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/api/getmessages?filter_isValidationKeyword_type=eq');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $crawler = $client->request('GET', '/api/getmessages?filter_isValidationKeyword_value=false');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }
    public function testGetMessagesBadFiltersIsValidationKeyword()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/api/getmessages?filter_isValidationKeyword_type=gt&filter_name_value=');
        $this->assertEquals(400, $client->getResponse()->getStatusCode());
        $crawler = $client->request('GET', '/api/getmessages?filter_isValidationKeyword_value=luna');
        $this->assertEquals(400, $client->getResponse()->getStatusCode());
    }
    public function testGetMessagesGoodFiltersTicket()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/api/getmessages?filter_ticket_type=eq');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $crawler = $client->request('GET', '/api/getmessages?filter_ticket_value=1');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }
    public function testGetMessagesBadFiltersTicket()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/api/getmessages?filter_ticket_type=gt');
        $this->assertEquals(400, $client->getResponse()->getStatusCode());
        $crawler = $client->request('GET', '/api/getmessages?filter_ticket_value=grupo');
        $this->assertEquals(400, $client->getResponse()->getStatusCode());
    }
    public function testGetMessagesGoodFiltersWhatsappGroup()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/api/getmessages?filter_whatsappGroup_type=eq');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $crawler = $client->request('GET', '/api/getmessages?filter_whatsappGroup_value=1');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }
    public function testGetMessagesBadFiltersWhatsappGroup()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/api/getmessages?filter_whatsappGroup_type=gt');
        $this->assertEquals(400, $client->getResponse()->getStatusCode());
        $crawler = $client->request('GET', '/api/getmessages?filter_whatsappGroup_value=grupo');
        $this->assertEquals(400, $client->getResponse()->getStatusCode());
    }
    public function testGetMessagesGoodFiltersSupportMember()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/api/getmessages?filter_supportMember_type=eq');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $crawler = $client->request('GET', '/api/getmessages?filter_supportMember_value=1');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }
    public function testGetMessagesBadFiltersSupportMember()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/api/getmessages?filter_supportMember_type=gt');
        $this->assertEquals(400, $client->getResponse()->getStatusCode());
        $crawler = $client->request('GET', '/api/getmessages?filter_supportMember_value=grupo');
        $this->assertEquals(400, $client->getResponse()->getStatusCode());
    }
    public function testGetMessagesGoodFiltersClientMember()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/api/getmessages?filter_clientMember_type=eq');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $crawler = $client->request('GET', '/api/getmessages?filter_clientMember_value=1');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }
    public function testGetMessagesBadFiltersClientMember()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/api/getmessages?filter_clientMember_type=gt');
        $this->assertEquals(400, $client->getResponse()->getStatusCode());
        $crawler = $client->request('GET', '/api/getmessages?filter_clientMember_value=grupo');
        $this->assertEquals(400, $client->getResponse()->getStatusCode());
    }
    public function testGetMessagesGoodFiltersStrmenssagetext()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/api/getmessages?filter_strmenssagetext_type=like');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $crawler = $client->request('GET', '/api/getmessages?filter_strmenssagetext_value=prueba');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }
    public function testGetMessagesBadFiltersStrmenssagetext()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/api/getmessages?filter_strmenssagetext_type=gt');
        $this->assertEquals(400, $client->getResponse()->getStatusCode());
       
    }

    public function testGetMessagesGoodFiltersDtmmessage()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/api/getmessages?filter_dtmmessage_type=eq');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $crawler = $client->request('GET', '/api/getmessages?filter_dtmmessage_value=01-09-2018+00%3A00%3A00');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $crawler = $client->request('GET', '/api/getmessages?filter_dtmmessage_type=between&filter_dtmmessage_value_start=01-09-2018+00%3A00%3A00&filter_dtmmessage_value_end=01-11-2018 00:00:00');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $crawler = $client->request('GET', '/api/getmessages?filter_dtmmessage_type=between&filter_dtmmessage_value_start=01-09-2018+00%3A00%3A00&filter_dtmmessage_value_end=');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $crawler = $client->request('GET', '/api/getmessages?filter_dtmmessage_type=between&filter_dtmmessage_value_start=&filter_dtmmessage_value_end=01-09-2018+00%3A00%3A00');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $crawler = $client->request('GET', '/api/getmessages?filter_dtmmessage_type=notbetween&filter_dtmmessage_value_start=01-09-2018+00%3A00%3A00&filter_dtmmessage_value_end=01-11-2018+00%3A00%3A00');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }
    public function testGetMessagesBadFiltersDtmmessage()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/api/getmessages?filter_dtmmessage_type=gtdasd');
        $this->assertEquals(400, $client->getResponse()->getStatusCode());
        $crawler = $client->request('GET', '/api/getmessages?filter_dtmmessage_type=gt&filter_dtmmessage_value=adfdsf');
        $this->assertEquals(400, $client->getResponse()->getStatusCode());
        $crawler = $client->request('GET', '/api/getmessages?filter_dtmmessage_type=between&filter_dtmmessage_value_start=sdf&filter_dtmmessage_value_end=50');
        $this->assertEquals(400, $client->getResponse()->getStatusCode());
        $crawler = $client->request('GET', '/api/getmessages?filter_dtmmessage_type=between&filter_dtmmessage_value_start=11111111111&filter_dtmmessage_value_end=sdf');
        $this->assertEquals(400, $client->getResponse()->getStatusCode());
        $crawler = $client->request('GET', '/api/getmessages?filter_dtmmessage_type=between&filter_dtmmessage_value_start=dsf&filter_dtmmessage_value_end=sdf');
        $this->assertEquals(400, $client->getResponse()->getStatusCode());
        $crawler = $client->request('GET', '/api/getmessages?filter_dtmmessage_type=notbetween&filter_dtmmessage_value_start=dsf&filter_dtmmessage_value_end=sdf');
        $this->assertEquals(400, $client->getResponse()->getStatusCode());
    }

    public function testGetMessagesGoodFiltersPage()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/api/getmessages?_page=1');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }
    public function testGetMessagesBadFiltersPage()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/api/getmessages?_page=gt');
        $this->assertEquals(400, $client->getResponse()->getStatusCode());
    }
    public function testGetMessagesGoodFiltersPerPage()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/api/getmessages?_per_page=10');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }
    public function testGetMessagesBadFiltersPerPage()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/api/getmessages?_per_page=gt');
        $this->assertEquals(400, $client->getResponse()->getStatusCode());
    }
    public function testGetMessagesGoodFiltersSortBy()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/api/getmessages?_sort_by=supportFirstAnswer');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $crawler = $client->request('GET', '/api/getmessages?_sort_order=asc');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $crawler = $client->request('GET', '/api/getmessages?_sort_order=asc&_sort_by=supportFirstAnswer');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }
    public function testGetMessagesBadFiltersSortBy()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/api/getmessages?_sort_by=gt');
        $this->assertEquals(400, $client->getResponse()->getStatusCode());
        $crawler = $client->request('GET', '/api/getmessages?_sort_order=gt');
        $this->assertEquals(400, $client->getResponse()->getStatusCode());
    }
   
}
