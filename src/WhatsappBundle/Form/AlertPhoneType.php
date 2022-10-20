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

class AlertPhoneType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
//            ->add('user', null, array( 'required' => true, 'label' =>"Usuario",))
            ->add('phone', "text", array('label' => 'Teléfono', ))
        ;
    }

    public function getName()
    {
        return 'alertphonetype';
    }
    
}