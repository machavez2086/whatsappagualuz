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

class ConfigurationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('company', null, array( 'required' => true, 'label' =>"Nombre de la empresa",))
//            ->add('prefix', null, array( 'required' => true, 'label' =>"Prefijo de grupos",))
            ->add('timeZone', "timezone", array('required' => true,'label' => 'Zona Horaria'))
            ->add('isAlertMail', null, array( 'required' => false, 'label' =>"Envío de alerta por correo",))
            ->add('isAlertCall', null, array( 'required' => false, 'label' =>"Llamada de alerta",))
            ->add('isAlertSms', null, array( 'required' => false, 'label' =>"Mensaje al teléfono (SMS)",))
            
            ->add('minutesAnswerAlert', null, array( 'required' => true, 'label' =>"Minutos para alerta de respuesta",))
            ->add('minutesResolutionAlert', null, array( 'required' => true, 'label' =>"Minutos para alerta de resolución",))
            ->add('dayEndWeek', 'choice', array('required' => true, 'label' => 'Último día de la semana',
                'choices' =>array('monday' => 'Lunes' , 'tuesday' => 'Martes', 'wednesday' => 'Miércoles', 'thursday' => 'Jueves', 'friday' => 'Viernes', 'saturday' => 'Sábado', 'sunday' => 'Domingo')
                ))
            ->add('hourEndWeek', null, array('required' => true, 'label' => 'Hora del último día de la semana'))
//            ->add('demo', null, array( 'required' => true, 'label' =>"Demo",))
                        
        ;
    }

    public function getName()
    {
        return 'configurationtype';
    }
    
//    public function configureOptions(OptionsResolver $resolver)
//    {
//        $resolver->setDefaults(array(
//            'id' => null,
//        ));
//    }
}