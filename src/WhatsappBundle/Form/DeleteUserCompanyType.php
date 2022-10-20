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

class DeleteUserCompanyType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
//            ->add('id', "hidden", array('required' => true, 'label' =>"",))
//            ->add('minutesansweralert', null, array( 'required' => true, 'label' =>"Minutos para alerta de respuesta",))
//            ->add('minutesresolutionalert', null, array( 'required' => true, 'label' =>"Minutos para alerta de resolución",))
//            ->add('demo', null, array( 'required' => true, 'label' =>"Demo",))
                        
        ;
    }

    public function getName()
    {
        return 'deleteusercompanytype';
    }
    
    
}