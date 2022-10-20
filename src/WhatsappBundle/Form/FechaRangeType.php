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

class FechaRangeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('fechainicial', 'datetime', array( 'required' => false, 'format' => 'yyyy-MM-dd HH:mm', 'read_only' => false, 'widget' => 'single_text', 'label' =>"Fecha inicial", 'model_timezone' => 'America/Mexico_City', 'view_timezone' => $options["usertimezone"]))
            ->add('fechafinal', 'datetime', array('required' => false, 'format' => 'yyyy-MM-dd HH:mm', 'read_only' => false,  'widget' => 'single_text', 'label' =>"Fecha final", 'model_timezone' => 'America/Mexico_City', 'view_timezone' => $options["usertimezone"]))                
        ;
    }

    public function getName()
    {
        return 'fecharangetype';
    }
     public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'usertimezone' => null,
        ));
    }
}