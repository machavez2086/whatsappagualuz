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
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\OptionsResolver\OptionsResolver;


class UserType extends AbstractType {

    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
                ->add('username', null, array('label' => 'Usuario', 'required' => true,
                ))
                ->add('email', null, array('label' => 'Correo', 'required' => true,'constraints' => new Email()
                ))
                ->add('plainPassword', 'password', array('required' => true, 'label' => "Contraseña",
                ))
                ->add('rol', 'choice', array( "mapped" => false,'required' => true, 'label' => "Rol", 'choices' => array("Administrador" => "Administrador", "Usuario" => "Usuario" )
                ))

        ;
    }

    public function getName() {
        return 'usertype';
    }
    
    
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'validation_groups' => array('Registration'),
            'allow_extra_fields' => true
        ));
    }

}
