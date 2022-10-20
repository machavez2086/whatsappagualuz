<?php

namespace WhatsappBundle\Tests\Controller;

//use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

use WhatsappBundle\Common\AbstractTestCase;

class ApiControllerTest extends AbstractTestCase
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
    public function testOpenTickets()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/api/getopenstickets');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }
    public function testGetTicketsById()
    {
        $client = static::createClient();
        $em = $client->getContainer()->get('doctrine')->getManager(); 
        $tickets = $em->getRepository('WhatsappBundle:Ticket')->findAll();

        $crawler = $client->request('GET', '/api/getticketbyid/'.$tickets[0]->getId());
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $crawler = $client->request('GET', '/api/getticketbyid/1');
        $this->assertEquals(404, $client->getResponse()->getStatusCode());
    }
    
    public function testGetTickets()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/api/gettickets/1');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }
    public function testGetTicketsAllFilters()
    {
        $client = static::createClient();
//        $crawler = $client->request('GET', '/api/gettickets/1?filter[id][type]=&filter[id][value]=&filter[name][type]=&filter[name][value]=a&filter[startDate][type]=between&filter[startDate][value][start]=01-09-2018+00%3A00%3A00&filter[startDate][value][end]=04-11-2018+21%3A59%3A00&filter[weekday][type]=&filter[weekday][value]=&filter[whatsappGroup][type]=&filter[whatsappGroup][value]=&filter[solvedBySupportMember][type]=&filter[solvedBySupportMember][value]=&filter[ticketType][type]=&filter[ticketType][value]=&filter[solutionType][type]=&filter[solutionType][value]=&filter[endDate][type]=&filter[endDate][value]=&filter[startTime][type]=&filter[startTime][value][start]=13%3A38%3A16&filter[startTime][value][end]=23%3A59&filter[firstanswer][type]=&filter[firstanswer][value]=&filter[nofollow][type]=&filter[nofollow][value]=&filter[ticketended][type]=&filter[ticketended][value]=&filter[minutesAnswerTime][type]=&filter[minutesAnswerTime][value]=&filter[minutesSolutionTime][type]=&filter[minutesSolutionTime][value]=&_page=1&_sort_by=id&_sort_order=DESC&_per_page=32');
        $crawler = $client->request('GET', '/api/gettickets/1?filter_id_type=eq&filter_id_value=361&filter_name_type=eq&filter_name_value=a&filter_startDate_type=between&filter_startDate_value_start=01-09-2018+00%3A00%3A00&filter_startDate_value_end=&filter_weekday_type=eq&filter_weekday_value=Jueves&filter_whatsappGroup_type=eq&filter_whatsappGroup_value=12&filter_solvedBySupportMember_type=eq&filter_solvedBySupportMember_value=8&filter_ticketType_type=eq&filter_ticketType_value=1&filter_solutionType_type=eq&filter_solutionType_value=1&filter_endDate_type=eq&filter_endDate_value=01-09-2018+00%3A00%3A00&filter_startTime_type=between&filter_startTime_value_start=13%3A38%3A16&filter_startTime_value_end=23%3A59&filter_firstanswer_type=eq&filter_firstanswer_value=true&filter_nofollow_type=eq&filter_nofollow_value=false&filter_ticketended_type=eq&filter_ticketended_value=true&filter_minutesAnswerTime_type=eq&filter_minutesAnswerTime_value=21&filter_minutesSolutionTime_type=between&filter_minutesSolutionTime_value_start=12&filter_minutesSolutionTime_value_end=20&_out_of_range=true&filter__page=1&filter__sort_by=id&filter__sort_order=DESC&filter__per_page=32');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }
    public function testGetTicketsBadFilters()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/api/gettickets/1?filter_id_type=esadq&filter_id_value');
        $this->assertEquals(400, $client->getResponse()->getStatusCode());
    }
    public function testGetTicketsGoodFiltersId()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/api/gettickets/1?filter_id_type=eq&filter_id_value=');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        
        $crawler = $client->request('GET', '/api/gettickets/1?filter_id_value=2');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }
    public function testGetTicketsBadFiltersId()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/api/gettickets/1?filter_id_type=esadq&filter_id_value=');
        $this->assertEquals(400, $client->getResponse()->getStatusCode());
        $crawler = $client->request('GET', '/api/gettickets/1?filter_id_value=sdf');
        $this->assertEquals(400, $client->getResponse()->getStatusCode());
    }
    
    public function testGetTicketsGoodFiltersName()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/api/gettickets/1?filter_name_type=eq');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $crawler = $client->request('GET', '/api/gettickets/1?filter_name_value=Pepe');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }
    public function testGetTicketsBadFiltersName()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/api/gettickets/1?filter_name_type=between&filter_name_value=');
        $this->assertEquals(400, $client->getResponse()->getStatusCode());
    }
    public function testGetTicketsGoodFiltersWeekday()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/api/gettickets/1?filter_weekday_type=eq');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $crawler = $client->request('GET', '/api/gettickets/1?filter_weekday_value=Lunes');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }
    public function testGetTicketsBadFiltersWeekday()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/api/gettickets/1?filter_weekday_type=gt&filter_name_value=');
        $this->assertEquals(400, $client->getResponse()->getStatusCode());
        $crawler = $client->request('GET', '/api/gettickets/1?filter_weekday_value=luna');
        $this->assertEquals(400, $client->getResponse()->getStatusCode());
    }
    public function testGetTicketsGoodFiltersWhatsappGroup()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/api/gettickets/1?filter_whatsappGroup_type=eq');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $crawler = $client->request('GET', '/api/gettickets/1?filter_whatsappGroup_value=1');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }
    public function testGetTicketsBadFiltersWhatsappGroup()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/api/gettickets/1?filter_whatsappGroup_type=gt');
        $this->assertEquals(400, $client->getResponse()->getStatusCode());
        $crawler = $client->request('GET', '/api/gettickets/1?filter_whatsappGroup_value=grupo');
        $this->assertEquals(400, $client->getResponse()->getStatusCode());
    }
    public function testGetTicketsGoodFiltersSolvedBySupportMember()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/api/gettickets/1?filter_solvedBySupportMember_type=eq');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $crawler = $client->request('GET', '/api/gettickets/1?filter_solvedBySupportMember_value=1');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }
    public function testGetTicketsBadFiltersSolvedBySupportMember()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/api/gettickets/1?filter_solvedBySupportMember_type=gt');
        $this->assertEquals(400, $client->getResponse()->getStatusCode());
        $crawler = $client->request('GET', '/api/gettickets/1?filter_solvedBySupportMember_value=invalid');
        $this->assertEquals(400, $client->getResponse()->getStatusCode());
    }
    public function testGetTicketsGoodFiltersTicketType()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/api/gettickets/1?filter_ticketType_type=eq');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $crawler = $client->request('GET', '/api/gettickets/1?filter_ticketType_value=1');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }
    public function testGetTicketsBadFiltersTicketType()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/api/gettickets/1?filter_ticketType_type=gt');
        $this->assertEquals(400, $client->getResponse()->getStatusCode());
        $crawler = $client->request('GET', '/api/gettickets/1?filter_ticketType_value=invalid');
        $this->assertEquals(400, $client->getResponse()->getStatusCode());
    }
    public function testGetTicketsGoodFiltersSolutionType()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/api/gettickets/1?filter_solutionType_type=eq');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $crawler = $client->request('GET', '/api/gettickets/1?filter_solutionType_value=1');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }
    public function testGetTicketsBadFiltersSolutionType()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/api/gettickets/1?filter_solutionType_type=gt');
        $this->assertEquals(400, $client->getResponse()->getStatusCode());
        $crawler = $client->request('GET', '/api/gettickets/1?filter_solutionType_value=invalid');
        $this->assertEquals(400, $client->getResponse()->getStatusCode());
    }
    public function testGetTicketsGoodFiltersFirstanswer()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/api/gettickets/1?filter_firstanswer_type=eq');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $crawler = $client->request('GET', '/api/gettickets/1?filter_firstanswer_value=true');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }
    public function testGetTicketsBadFiltersFirstanswer()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/api/gettickets/1?filter_firstanswer_type=gt');
        $this->assertEquals(400, $client->getResponse()->getStatusCode());
        $crawler = $client->request('GET', '/api/gettickets/1?filter_firstanswer_value=invalid');
        $this->assertEquals(400, $client->getResponse()->getStatusCode());
    }
    public function testGetTicketsGoodFiltersTicketended()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/api/gettickets/1?filter_ticketended_type=eq');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $crawler = $client->request('GET', '/api/gettickets/1?filter_ticketended_value=true');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }
    public function testGetTicketsBadFiltersTicketended()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/api/gettickets/1?filter_ticketended_type=gt');
        $this->assertEquals(400, $client->getResponse()->getStatusCode());
        $crawler = $client->request('GET', '/api/gettickets/1?filter_ticketended_value=invalid');
        $this->assertEquals(400, $client->getResponse()->getStatusCode());
    }
    public function testGetTicketsGoodFiltersSendalert()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/api/gettickets/1?filter_sendalert_type=eq');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $crawler = $client->request('GET', '/api/gettickets/1?filter_sendalert_value=true');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }
    public function testGetTicketsBadFiltersSendalert()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/api/gettickets/1?filter_sendalert_type=gt');
        $this->assertEquals(400, $client->getResponse()->getStatusCode());
        $crawler = $client->request('GET', '/api/gettickets/1?filter_sendalert_value=invalid');
        $this->assertEquals(400, $client->getResponse()->getStatusCode());
    }
    public function testGetTicketsGoodFiltersSendalertSolution()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/api/gettickets/1?filter_sendalertSolution_type=eq');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $crawler = $client->request('GET', '/api/gettickets/1?filter_sendalertSolution_value=true');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }
    public function testGetTicketsBadFiltersSendalertSolution()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/api/gettickets/1?filter_sendalertSolution_type=gt');
        $this->assertEquals(400, $client->getResponse()->getStatusCode());
        $crawler = $client->request('GET', '/api/gettickets/1?filter_sendalertSolution_value=invalid');
        $this->assertEquals(400, $client->getResponse()->getStatusCode());
    }
    public function testGetTicketsGoodFiltersNofollow()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/api/gettickets/1?filter_nofollow_type=eq');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $crawler = $client->request('GET', '/api/gettickets/1?filter_nofollow_value=true');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }
    public function testGetTicketsBadFiltersNofollow()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/api/gettickets/1?filter_nofollow_type=gt');
        $this->assertEquals(400, $client->getResponse()->getStatusCode());
        $crawler = $client->request('GET', '/api/gettickets/1?filter_nofollow_value=invalid');
        $this->assertEquals(400, $client->getResponse()->getStatusCode());
    }
    public function testGetTicketsGoodFiltersIsValidated()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/api/gettickets/1?filter_isValidated_type=eq');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $crawler = $client->request('GET', '/api/gettickets/1?filter_isValidated_value=true');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }
    public function testGetTicketsBadFiltersIsValidated()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/api/gettickets/1?filter_isValidated_type=gt');
        $this->assertEquals(400, $client->getResponse()->getStatusCode());
        $crawler = $client->request('GET', '/api/gettickets/1?filter_isValidated_value=invalid');
        $this->assertEquals(400, $client->getResponse()->getStatusCode());
    }
    public function testGetTicketsGoodFiltersMinutesAnswerTime()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/api/gettickets/1?filter_minutesAnswerTime_type=eq');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $crawler = $client->request('GET', '/api/gettickets/1?filter_minutesAnswerTime_value=1');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $crawler = $client->request('GET', '/api/gettickets/1?filter_minutesAnswerTime_type=between&filter_minutesAnswerTime_value_start=1&filter_minutesAnswerTime_value_end=50');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $crawler = $client->request('GET', '/api/gettickets/1?filter_minutesAnswerTime_type=between&filter_minutesAnswerTime_value_start=1&filter_minutesAnswerTime_value_end=');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $crawler = $client->request('GET', '/api/gettickets/1?filter_minutesAnswerTime_type=between&filter_minutesAnswerTime_value_start=&filter_minutesAnswerTime_value_end=50');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $crawler = $client->request('GET', '/api/gettickets/1?filter_minutesAnswerTime_type=notbetween&filter_minutesAnswerTime_value_start=&filter_minutesAnswerTime_value_end=50');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }
    public function testGetTicketsBadFiltersMinutesAnswerTime()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/api/gettickets/1?filter_minutesAnswerTime_type=gtd');
        $this->assertEquals(400, $client->getResponse()->getStatusCode());
        $crawler = $client->request('GET', '/api/gettickets/1?filter_minutesAnswerTime_value=invalid');
        $this->assertEquals(400, $client->getResponse()->getStatusCode());
        $crawler = $client->request('GET', '/api/gettickets/1?filter_minutesAnswerTime_type=between&filter_minutesAnswerTime_value_start=sdf&filter_minutesAnswerTime_value_end=50');
        $this->assertEquals(400, $client->getResponse()->getStatusCode());
        $crawler = $client->request('GET', '/api/gettickets/1?filter_minutesAnswerTime_type=between&filter_minutesAnswerTime_value_start=1&filter_minutesAnswerTime_value_end=sdf');
        $this->assertEquals(400, $client->getResponse()->getStatusCode());
        $crawler = $client->request('GET', '/api/gettickets/1?filter_minutesAnswerTime_type=between&filter_minutesAnswerTime_value_start=dsf&filter_minutesAnswerTime_value_end=sdf');
        $this->assertEquals(400, $client->getResponse()->getStatusCode());
        $crawler = $client->request('GET', '/api/gettickets/1?filter_minutesAnswerTime_type=notbetween&filter_minutesAnswerTime_value_start=dsf&filter_minutesAnswerTime_value_end=sdf');
        $this->assertEquals(400, $client->getResponse()->getStatusCode());
    }
    public function testGetTicketsGoodFiltersMinutesSolutionTime()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/api/gettickets/1?filter_minutesSolutionTime_type=eq');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $crawler = $client->request('GET', '/api/gettickets/1?filter_minutesSolutionTime_value=1');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $crawler = $client->request('GET', '/api/gettickets/1?filter_minutesSolutionTime_type=between&filter_minutesSolutionTime_value_start=1&filter_minutesSolutionTime_value_end=50');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $crawler = $client->request('GET', '/api/gettickets/1?filter_minutesSolutionTime_type=between&filter_minutesSolutionTime_value_start=1&filter_minutesSolutionTime_value_end=');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $crawler = $client->request('GET', '/api/gettickets/1?filter_minutesSolutionTime_type=between&filter_minutesSolutionTime_value_start=&filter_minutesSolutionTime_value_end=50');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $crawler = $client->request('GET', '/api/gettickets/1?filter_minutesSolutionTime_type=notbetween&filter_minutesSolutionTime_value_start=&filter_minutesSolutionTime_value_end=50');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }
    public function testGetTicketsBadFiltersMinutesSolutionTime()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/api/gettickets/1?filter_minutesSolutionTime_type=gtd');
        $this->assertEquals(400, $client->getResponse()->getStatusCode());
        $crawler = $client->request('GET', '/api/gettickets/1?filter_minutesSolutionTime_value=invalid');
        $this->assertEquals(400, $client->getResponse()->getStatusCode());
        $crawler = $client->request('GET', '/api/gettickets/1?filter_minutesSolutionTime_type=between&filter_minutesSolutionTime_value_start=sdf&filter_minutesSolutionTime_value_end=50');
        $this->assertEquals(400, $client->getResponse()->getStatusCode());
        $crawler = $client->request('GET', '/api/gettickets/1?filter_minutesSolutionTime_type=between&filter_minutesSolutionTime_value_start=1&filter_minutesSolutionTime_value_end=sdf');
        $this->assertEquals(400, $client->getResponse()->getStatusCode());
        $crawler = $client->request('GET', '/api/gettickets/1?filter_minutesSolutionTime_type=between&filter_minutesSolutionTime_value_start=dsf&filter_minutesSolutionTime_value_end=sdf');
        $this->assertEquals(400, $client->getResponse()->getStatusCode());
        $crawler = $client->request('GET', '/api/gettickets/1?filter_minutesSolutionTime_type=notbetween&filter_minutesSolutionTime_value_start=dsf&filter_minutesSolutionTime_value_end=sdf');
        $this->assertEquals(400, $client->getResponse()->getStatusCode());
    }
    public function testGetTicketsGoodFiltersMinutesDevTime()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/api/gettickets/1?filter_minutesDevTime_type=eq');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $crawler = $client->request('GET', '/api/gettickets/1?filter_minutesDevTime_value=1');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $crawler = $client->request('GET', '/api/gettickets/1?filter_minutesDevTime_type=between&filter_minutesDevTime_value_start=1&filter_minutesDevTime_value_end=50');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $crawler = $client->request('GET', '/api/gettickets/1?filter_minutesDevTime_type=between&filter_minutesDevTime_value_start=1&filter_minutesDevTime_value_end=');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $crawler = $client->request('GET', '/api/gettickets/1?filter_minutesDevTime_type=between&filter_minutesDevTime_value_start=&filter_minutesDevTime_value_end=50');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $crawler = $client->request('GET', '/api/gettickets/1?filter_minutesDevTime_type=notbetween&filter_minutesDevTime_value_start=&filter_minutesDevTime_value_end=50');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }
    public function testGetTicketsBadFiltersMinutesDevTime()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/api/gettickets/1?filter_minutesDevTime_type=gtd');
        $this->assertEquals(400, $client->getResponse()->getStatusCode());
        $crawler = $client->request('GET', '/api/gettickets/1?filter_minutesDevTime_value=invalid');
        $this->assertEquals(400, $client->getResponse()->getStatusCode());
        $crawler = $client->request('GET', '/api/gettickets/1?filter_minutesDevTime_type=between&filter_minutesDevTime_value_start=sdf&filter_minutesDevTime_value_end=50');
        $this->assertEquals(400, $client->getResponse()->getStatusCode());
        $crawler = $client->request('GET', '/api/gettickets/1?filter_minutesDevTime_type=between&filter_minutesDevTime_value_start=1&filter_minutesDevTime_value_end=sdf');
        $this->assertEquals(400, $client->getResponse()->getStatusCode());
        $crawler = $client->request('GET', '/api/gettickets/1?filter_minutesDevTime_type=between&filter_minutesDevTime_value_start=dsf&filter_minutesDevTime_value_end=sdf');
        $this->assertEquals(400, $client->getResponse()->getStatusCode());
        $crawler = $client->request('GET', '/api/gettickets/1?filter_minutesDevTime_type=notbetween&filter_minutesDevTime_value_start=dsf&filter_minutesDevTime_value_end=sdf');
        $this->assertEquals(400, $client->getResponse()->getStatusCode());
    }
    public function testGetTicketsGoodFiltersMinutesValidationWaitTime()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/api/gettickets/1?filter_minutesValidationWaitTime_type=eq');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $crawler = $client->request('GET', '/api/gettickets/1?filter_minutesValidationWaitTime_value=1');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $crawler = $client->request('GET', '/api/gettickets/1?filter_minutesValidationWaitTime_type=between&filter_minutesValidationWaitTime_value_start=1&filter_minutesValidationWaitTime_value_end=50');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $crawler = $client->request('GET', '/api/gettickets/1?filter_minutesValidationWaitTime_type=between&filter_minutesValidationWaitTime_value_start=1&filter_minutesValidationWaitTime_value_end=');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $crawler = $client->request('GET', '/api/gettickets/1?filter_minutesValidationWaitTime_type=between&filter_minutesValidationWaitTime_value_start=&filter_minutesValidationWaitTime_value_end=50');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $crawler = $client->request('GET', '/api/gettickets/1?filter_minutesValidationWaitTime_type=notbetween&filter_minutesValidationWaitTime_value_start=&filter_minutesValidationWaitTime_value_end=50');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }
    public function testGetTicketsBadFiltersMinutesValidationWaitTime()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/api/gettickets/1?filter_minutesValidationWaitTime_type=gtd');
        $this->assertEquals(400, $client->getResponse()->getStatusCode());
        $crawler = $client->request('GET', '/api/gettickets/1?filter_minutesValidationWaitTime_value=invalid');
        $this->assertEquals(400, $client->getResponse()->getStatusCode());
        $crawler = $client->request('GET', '/api/gettickets/1?filter_minutesValidationWaitTime_type=between&filter_minutesValidationWaitTime_value_start=sdf&filter_minutesValidationWaitTime_value_end=50');
        $this->assertEquals(400, $client->getResponse()->getStatusCode());
        $crawler = $client->request('GET', '/api/gettickets/1?filter_minutesValidationWaitTime_type=between&filter_minutesValidationWaitTime_value_start=1&filter_minutesValidationWaitTime_value_end=sdf');
        $this->assertEquals(400, $client->getResponse()->getStatusCode());
        $crawler = $client->request('GET', '/api/gettickets/1?filter_minutesValidationWaitTime_type=between&filter_minutesValidationWaitTime_value_start=dsf&filter_minutesValidationWaitTime_value_end=sdf');
        $this->assertEquals(400, $client->getResponse()->getStatusCode());
        $crawler = $client->request('GET', '/api/gettickets/1?filter_minutesValidationWaitTime_type=notbetween&filter_minutesValidationWaitTime_value_start=dsf&filter_minutesValidationWaitTime_value_end=sdf');
        $this->assertEquals(400, $client->getResponse()->getStatusCode());
    }
    public function testGetTicketsGoodFiltersStartDate()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/api/gettickets/1?filter_startDate_type=eq');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $crawler = $client->request('GET', '/api/gettickets/1?filter_startDate_value=01-09-2018+00%3A00%3A00');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $crawler = $client->request('GET', '/api/gettickets/1?filter_startDate_type=between&filter_startDate_value_start=01-09-2018+00%3A00%3A00&filter_startDate_value_end=01-11-2018 00:00:00');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $crawler = $client->request('GET', '/api/gettickets/1?filter_startDate_type=between&filter_startDate_value_start=01-09-2018+00%3A00%3A00&filter_startDate_value_end=');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $crawler = $client->request('GET', '/api/gettickets/1?filter_startDate_type=between&filter_startDate_value_start=&filter_startDate_value_end=01-09-2018+00%3A00%3A00');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $crawler = $client->request('GET', '/api/gettickets/1?filter_startDate_type=notbetween&filter_startDate_value_start=01-09-2018+00%3A00%3A00&filter_startDate_value_end=01-11-2018+00%3A00%3A00');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }
    public function testGetTicketsBadFiltersStartDate()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/api/gettickets/1?filter_startDate_type=gtdasd');
        $this->assertEquals(400, $client->getResponse()->getStatusCode());
        $crawler = $client->request('GET', '/api/gettickets/1?filter_startDate_type=gt&filter_startDate_value=adfdsf');
        $this->assertEquals(400, $client->getResponse()->getStatusCode());
        $crawler = $client->request('GET', '/api/gettickets/1?filter_startDate_type=between&filter_startDate_value_start=sdf&filter_startDate_value_end=50');
        $this->assertEquals(400, $client->getResponse()->getStatusCode());
        $crawler = $client->request('GET', '/api/gettickets/1?filter_startDate_type=between&filter_startDate_value_start=11111111111&filter_startDate_value_end=sdf');
        $this->assertEquals(400, $client->getResponse()->getStatusCode());
        $crawler = $client->request('GET', '/api/gettickets/1?filter_startDate_type=between&filter_startDate_value_start=dsf&filter_startDate_value_end=sdf');
        $this->assertEquals(400, $client->getResponse()->getStatusCode());
        $crawler = $client->request('GET', '/api/gettickets/1?filter_startDate_type=notbetween&filter_startDate_value_start=dsf&filter_startDate_value_end=sdf');
        $this->assertEquals(400, $client->getResponse()->getStatusCode());
    }
    public function testGetTicketsGoodFiltersEndDate()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/api/gettickets/1?filter_endDate_type=eq');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $crawler = $client->request('GET', '/api/gettickets/1?filter_endDate_value=01-09-2018+00%3A00%3A00');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $crawler = $client->request('GET', '/api/gettickets/1?filter_endDate_type=between&filter_endDate_value_start=01-09-2018+00%3A00%3A00&filter_endDate_value_end=01-11-2018 00:00:00');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $crawler = $client->request('GET', '/api/gettickets/1?filter_endDate_type=between&filter_endDate_value_start=01-09-2018+00%3A00%3A00&filter_endDate_value_end=');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $crawler = $client->request('GET', '/api/gettickets/1?filter_endDate_type=between&filter_endDate_value_start=&filter_endDate_value_end=01-09-2018+00%3A00%3A00');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $crawler = $client->request('GET', '/api/gettickets/1?filter_endDate_type=notbetween&filter_endDate_value_start=01-09-2018+00%3A00%3A00&filter_endDate_value_end=01-11-2018+00%3A00%3A00');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }
    public function testGetTicketsBadFiltersEndDate()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/api/gettickets/1?filter_endDate_type=gtdasd');
        $this->assertEquals(400, $client->getResponse()->getStatusCode());
        $crawler = $client->request('GET', '/api/gettickets/1?filter_endDate_type=gt&filter_endDate_value=adfdsf');
//        print_r($client->getResponse());die;
        $this->assertEquals(400, $client->getResponse()->getStatusCode());
        $crawler = $client->request('GET', '/api/gettickets/1?filter_endDate_type=between&filter_endDate_value_start=sdf&filter_endDate_value_end=50');
        $this->assertEquals(400, $client->getResponse()->getStatusCode());
        $crawler = $client->request('GET', '/api/gettickets/1?filter_endDate_type=between&filter_endDate_value_start=11111111111&filter_endDate_value_end=sdf');
        $this->assertEquals(400, $client->getResponse()->getStatusCode());
        $crawler = $client->request('GET', '/api/gettickets/1?filter_endDate_type=between&filter_endDate_value_start=dsf&filter_endDate_value_end=sdf');
        $this->assertEquals(400, $client->getResponse()->getStatusCode());
        $crawler = $client->request('GET', '/api/gettickets/1?filter_endDate_type=notbetween&filter_endDate_value_start=dsf&filter_endDate_value_end=sdf');
        $this->assertEquals(400, $client->getResponse()->getStatusCode());
    }
    public function testGetTicketsGoodStartTime()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/api/gettickets/1?filter_startTime_type=eq');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $crawler = $client->request('GET', '/api/gettickets/1?filter_startTime_value=00%3A00%3A00');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $crawler = $client->request('GET', '/api/gettickets/1?filter_startTime_type=between&filter_startTime_value_start=00:20:00&filter_startTime_value_end=10:20:00');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $crawler = $client->request('GET', '/api/gettickets/1?filter_startTime_type=between&filter_startTime_value_start=10:20:00&filter_startTime_value_end=');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $crawler = $client->request('GET', '/api/gettickets/1?filter_startTime_type=between&filter_startTime_value_start=&filter_startTime_value_end=10:20:00');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $crawler = $client->request('GET', '/api/gettickets/1?filter_startTime_type=notbetween&filter_startTime_value_start=00:00:00&filter_startTime_value_end=10:20:00');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }
    public function testGetTicketsBadFiltersStartTime()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/api/gettickets/1?filter_startTime_type=gtdasd');
        $this->assertEquals(400, $client->getResponse()->getStatusCode());
        $crawler = $client->request('GET', '/api/gettickets/1?filter_startTime_type=gt&filter_startTime_value=adfdsf');
        $this->assertEquals(400, $client->getResponse()->getStatusCode());
        $crawler = $client->request('GET', '/api/gettickets/1?filter_startTime_type=between&filter_startTime_value_start=sdf&filter_startTime_value_end=50');
        $this->assertEquals(400, $client->getResponse()->getStatusCode());
        $crawler = $client->request('GET', '/api/gettickets/1?filter_startTime_type=between&filter_startTime_value_start=11111111111&filter_startTime_value_end=sdf');
        $this->assertEquals(400, $client->getResponse()->getStatusCode());
        $crawler = $client->request('GET', '/api/gettickets/1?filter_startTime_type=between&filter_startTime_value_start=dsf&filter_startTime_value_end=sdf');
        $this->assertEquals(400, $client->getResponse()->getStatusCode());
        $crawler = $client->request('GET', '/api/gettickets/1?filter_startTime_type=notbetween&filter_startTime_value_start=dsf&filter_startTime_value_end=sdf');
        $this->assertEquals(400, $client->getResponse()->getStatusCode());
    }
    
    public function testGetTicketsTypeEq()
    {
        $client = static::createClient();
//        $crawler = $client->request('GET', '/api/gettickets/1?filter[id][type]=&filter[id][value]=&filter[name][type]=&filter[name][value]=a&filter[startDate][type]=between&filter[startDate][value][start]=01-09-2018+00%3A00%3A00&filter[startDate][value][end]=04-11-2018+21%3A59%3A00&filter[weekday][type]=&filter[weekday][value]=&filter[whatsappGroup][type]=&filter[whatsappGroup][value]=&filter[solvedBySupportMember][type]=&filter[solvedBySupportMember][value]=&filter[ticketType][type]=&filter[ticketType][value]=&filter[solutionType][type]=&filter[solutionType][value]=&filter[endDate][type]=&filter[endDate][value]=&filter[startTime][type]=&filter[startTime][value][start]=13%3A38%3A16&filter[startTime][value][end]=23%3A59&filter[firstanswer][type]=&filter[firstanswer][value]=&filter[nofollow][type]=&filter[nofollow][value]=&filter[ticketended][type]=&filter[ticketended][value]=&filter[minutesAnswerTime][type]=&filter[minutesAnswerTime][value]=&filter[minutesSolutionTime][type]=&filter[minutesSolutionTime][value]=&_page=1&_sort_by=id&_sort_order=DESC&_per_page=32');
        $crawler = $client->request('GET', '/api/gettickets/1?filter_id_type=eq&filter_id_value=361&filter_name_type=eq&filter_name_value=a&filter_startDate_type=between&filter_startDate_value_start=01-09-2018+00%3A00%3A00&filter_startDate_value_end=&filter_weekday_type=eq&filter_weekday_value=Jueves&filter_whatsappGroup_type=eq&filter_whatsappGroup_value=12&filter_solvedBySupportMember_type=eq&filter_solvedBySupportMember_value=8&filter_ticketType_type=eq&filter_ticketType_value=1&filter_solutionType_type=eq&filter_solutionType_value=1&filter_endDate_type=eq&filter_endDate_value=01-09-2018+00%3A00%3A00&filter_startTime_type=between&filter_startTime_value_start=13%3A38%3A16&filter_startTime_value_end=23%3A59&filter_firstanswer_type=eq&filter_firstanswer_value=true&filter_nofollow_type=eq&filter_nofollow_value=false&filter_ticketended_type=eq&filter_ticketended_value=true&filter_minutesAnswerTime_type=eq&filter_minutesAnswerTime_value=21&filter_minutesSolutionTime_type=between&filter_minutesSolutionTime_value_start=12&filter_minutesSolutionTime_value_end=20&_out_of_range=true&filter__page=1&filter__sort_by=id&filter__sort_order=DESC&filter__per_page=32');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }
   
}
