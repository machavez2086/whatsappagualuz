<?php
/**
 * This file is part of the <name> project.
 *
 * (c) <yourname> <youremail>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace WhatsappBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Application\Sonata\UserBundle\Repository\UserRepository;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TicketSendendAreaType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('ask', null, array( 'required' => true, 'label' =>"Pregunta",))
            ->add('area', null, array( 'required' => true, 'label' =>"Area",))
                     
                        
        ;
    }

    public function getName()
    {
        return 'TicketSendendAreaType';
    }
    
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'ids' => null,
        ));
    }
}