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

class ObleaRetiroType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', null, array( 'required' => true, 'label' =>"Titulo",))
            ->add('noReclamo', null, array( 'required' => true, 'label' =>"Numero de reclamo",))
            ->add('bulto', null, array( 'required' => false, 'label' =>"Bulto",))
            ->add('peso', null, array( 'required' => true, 'label' =>"Peso",))
            ->add('entregaEnDomicilio', null, array( 'required' => true, 'label' =>"Entrega en domicilio",))
            ->add('destinatarioNames', null, array( 'required' => true, 'label' =>"Destinatario nombres",))
            ->add('destinatarioEmails', null, array( 'required' => true, 'label' =>"Destinatario correos",))
            ->add('destinatarioPhones', null, array( 'required' => true, 'label' =>"Destinatario teléfonos",))
            ->add('cantPaquetesRetirar', null, array('label' => 'Cantidad paquetes retiro'))
        ;
    }

    public function getName()
    {
        return 'obleaRetiroType';
    }
    
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'ids' => null,
        ));
    }
}