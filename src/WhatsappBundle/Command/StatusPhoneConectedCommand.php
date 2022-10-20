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
use Symfony\Component\Console\Output\OutputInterface;

class StatusPhoneConectedCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    public function configure()
    {
        //mongoexport --db cuba --collection AbstractFather --out sample.json --jsonArray
        $this->setName('sacspro:phone:status');
        $this->setDescription('Verifica si el móvil esta conectado');
    }

    /**
     * {@inheritdoc}
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        $wt = $this->getContainer()->get('whatsapp.sacspro.phonestatus');
        $wt->statusPhoneConected();
    }

    
}
