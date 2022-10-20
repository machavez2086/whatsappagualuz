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
use WhatsappBundle\Repository\TicketRepository;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MessageChangeTicketOneType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('ticket', 'entity', array( 'required' => true, 'label' =>"Petición", 'class'   => 'WhatsappBundle:Ticket',
            'query_builder' => function (TicketRepository $er)  use ( $options ) {
                    return $er->createQueryBuilder('u')
                            ->where('u.whatsappGroup = :group')
                            ->andWhere('u.id != :id')
                            ->andWhere('u.startDate >= :dateIni')
                            ->andWhere('u.startDate <= :datefin')
                            ->andWhere('u.configuration in (:configurations)')
                            ->setParameter("group", $options['group'])
                            ->setParameter("id", $options['id'])
                            ->setParameter("dateIni", $options['dateIni'])
                            ->setParameter("datefin", $options['datefin'])
                            ->setParameter("configurations", $options['configuration'])
                        ->orderBy('u.startDate', 'DESC');
                },              
                
                ))                
//            ->add('messages', null, array(
//            ))                
            ->add('messages', 'collection', array(
                'type'   => 'hidden',
//                 'options' => array('data' => 'Default Value'),
            ))                
        ;
    }

    public function getName()
    {
        return 'fecharangetype';
    }
    
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'group' => null,
            'id' => null,
            'dateIni' => null,
            'datefin' => null,
            'configuration' => null,
        ));
    }
}