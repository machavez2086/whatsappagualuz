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
use Symfony\Component\OptionsResolver\OptionsResolver;

use WhatsappBundle\Repository\SupportMemberRepository;
use WhatsappBundle\Repository\ClientMemberRepository;

class MessageToTicketFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('strmenssagetext', null, array('required' => true, 'label' => "Mensaje"))
            ->add('dtmmessage', null, array('required' => true, 'label' => "Fecha", "with_seconds" => true))
//            ->add('supportMember', null, array('required' => false, 'label' => "Miembro de soporte"))
//            ->add('clientMember', null, array('required' => false, 'label' => "Cliente"))
//            ->add('mappedAuthorNick', null, array('required' => false, 'label' => "nick"))
            ->add('ticket', null, array('disabled' => true, 'label' => "Petición"))
            ->add('whatsappGroup', null, array('disabled' => true, 'label' => "Grupo"))
            ->add('supportMember', 'entity', array( 'required' => false, 'label' =>"Miembro de soporte", 'class'   => 'WhatsappBundle:SupportMember',
            'query_builder' => function (SupportMemberRepository $er)  use ( $options ) {
                    return $er->createQueryBuilder('u')
                            ->andWhere('u.configuration in (:configurations)')
                            ->setParameter("configurations", $options['configuration']);
                },              
                
                ))
            ->add('clientMember', 'entity', array( 'required' => false, 'label' => "Cliente", 'class'   => 'WhatsappBundle:ClientMember',
            'query_builder' => function (ClientMemberRepository $er)  use ( $options ) {
                    return $er->createQueryBuilder('u')
                            ->andWhere('u.configuration in (:configurations)')
                            ->setParameter("configurations", $options['configuration']);
                },              
                
                ))
            
        ;
    }

    public function getName()
    {
        return 'messageToTicket';
    }
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'configuration' => null,
            'timezone' => "America/Mexico_City",
        ));
    }
}