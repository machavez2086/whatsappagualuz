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
use WhatsappBundle\Repository\WhatsappGroupRepository;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TicketChangeGroupOneType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        
        $builder
            ->add('whatsappGroup', 'entity', array( 'required' => true, 'label' =>"Grupo", 'class'   => 'WhatsappBundle:WhatsappGroup',
            'query_builder' => function (WhatsappGroupRepository $er)  use ( $options ) {
                    return $er->createQueryBuilder('u')
                            ->andWhere('u.id != :id')
                            ->andWhere('u.configuration in (:configurations)')
                            ->setParameter("id", $options['id'])
                            ->setParameter("configurations", $options['configuration']);
                },              
                
                ))                
//            ->add('messages', null, array(
//            ))                
            ->add('peticiones', 'collection', array(
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
            'id' => null,
            'configuration' => null,
        ));
    }
}