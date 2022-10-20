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

class UserCompanyType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
//            ->add('user', null, array( 'required' => true, 'label' =>"Usuario",))
            ->add('rol', 'choice', array('label' => 'Rol', 
                'choices' =>array('Administrador' => 'Administrador', 'Usuario' => 'Usuario')))
                ->add('user', 'entity', array( 'required' => true, 'label' =>"Usuario", 'class'   => 'ApplicationSonataUserBundle:User',
            'query_builder' => function (UserRepository $er)  use ( $options ) {
                    return $er->createQueryBuilder('u')
                            ->andWhere('u.id not in (:ids)')
                            ->setParameter("ids", $options['ids']);
                },              
                
                ))                
                        
        ;
    }

    public function getName()
    {
        return 'usercompanytype';
    }
    
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'ids' => null,
        ));
    }
}