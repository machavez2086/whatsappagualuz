<?php
/**
 * This file is part of the <name> project.
 *
 * (c) <yourname> <youremail>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace WhatsappBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

class MessageChangeTicketType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('ticket', 'entity', array( 'required' => true, 'label' =>"Petición", 'class'   => 'WhatsappBundle:Ticket',))                
            ->add('messages', null, array(
            ))                
//            ->add('messages', 'collection', array(
//                'type'   => 'hidden',
////                 'options' => array('data' => 'Default Value'),
//            ))                
        ;
    }

    public function getName()
    {
        return 'fecharangetype';
    }
}