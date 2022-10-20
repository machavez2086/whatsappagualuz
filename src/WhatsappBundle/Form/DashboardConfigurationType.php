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
use Application\Sonata\UserBundle\Repository\UserRepository;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DashboardConfigurationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
//            ->add('user', null, array( 'required' => true, 'label' =>"Usuario",))
            ->add('configuration',  'choice', array('attr'=>(array('class'=>'mr-sm-2')),'label' => 'Empresa', 'choices' => $options["configurations"]))
                
        ;
    }

    public function getName()
    {
        return 'dashboardconfigurationtype';
    }
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'configurations' => null,
        ));
    }
    
}