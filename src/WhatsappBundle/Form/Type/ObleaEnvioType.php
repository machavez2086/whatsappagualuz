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

class ObleaEnvioType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', null, array( 'required' => true, 'label' =>"Titulo",))
            ->add('noReclamo', null, array( 'required' => true, 'label' =>"Numero de reclamo",))
            ->add('bulto', null, array( 'required' => false, 'label' =>"Bulto",))
            ->add('paquetesRetirar', null, array( 'required' => true, 'label' =>"Paquetes de envío",))
            ->add('peso', null, array( 'required' => true, 'label' =>"Peso",))
            ->add('entregaEnDomicilio', null, array( 'required' => true, 'label' =>"Entrega en domicilio",))
            ->add('localidad', null, array( 'required' => true, 'label' =>"Localidad",))
            ->add('provincia', null, array( 'required' => true, 'label' =>"Provincia",))
            ->add('cp', null, array( 'required' => true, 'label' =>"CP",))
            ->add('destinatario', null, array( 'required' => true, 'label' =>"Destinatario",))
            ->add('observaciones', null, array( 'required' => true, 'label' =>"Observaciones",))
            ->add('product', null, array( 'required' => true, 'label' =>"Producto",))
            ->add('remitente', null, array( 'required' => true, 'label' =>"Remitente",))
            ->add('remitenteContact', null, array( 'required' => true, 'label' =>"Remitente contactos",))
                     
                        
        ;
    }

    public function getName()
    {
        return 'obleaEnvioType';
    }
    
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'ids' => null,
        ));
    }
}