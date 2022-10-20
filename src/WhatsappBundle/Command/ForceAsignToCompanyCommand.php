<?php

/*
 * This file is part of the Sonata Project package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace WhatsappBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Application\Sonata\ClassificationBundle\Entity\Category;
use ORION\Bundle\DirectoryBundle\Entity\Domain;
use Symfony\Component\HttpFoundation\Request;

class ForceAsignToCompanyCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    public function configure()
    {
        //mongoexport --db cuba --collection AbstractFather --out sample.json --jsonArray
        $this->setName('sacspro:group:force');
        $this->setDescription('Cierra las peticiones que hace más de una hora no han sido cerradas');
    }

    /**
     * {@inheritdoc}
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
//        $wt = $this->getContainer()->get('whatsapp.sacspro.phonestatus');
//        $wt->closePendingTickets();
        $wt = $this->getContainer()->get('whatsapp.sacspro.phonestatus');
//        $wt->forceChangeGroupConfiguration();
        $wt->convertTicketTypeToMany();
    }
    

    
}
