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

class LoteType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
//            ->add('ask', null, array( 'required' => true, 'label' =>"Pregunta",))
//            ->add('area', null, array( 'required' => true, 'label' =>"Area",))
            ->add('loteNo', null, array('label' => 'No', 'attr' => array('class' => 'col-md-4')))
            ->add('loteHour', null, array('label' => 'Hora'))
            ->add('loteMaquina', null, array('label' => 'Máquina'))
                     
                        
        ;
    }

    public function getName()
    {
        return 'LoteType';
    }
    
    public function configureOptions(OptionsResolver $resolver)
    {
        
    }
}