<?php

/*
 * This file is part of the Sonata Project package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace WhatsappBundle\Form;

use Sonata\UserBundle\Model\UserInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Sonata\UserBundle\Form\Type\ProfileType;

class ProfileType extends ProfileType
{
    /**
     * @var string
     */
    private $class;


    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        // NEXT_MAJOR: Keep FQCN when bumping Symfony requirement to 2.8+.
         parent::buildForm($builder, $options);

         if (method_exists('Symfony\Component\Form\AbstractType', 'getBlockPrefix')) {
            $userGenderType = 'Sonata\UserBundle\Form\Type\UserGenderListType';
            $birthdayType = 'Symfony\Component\Form\Extension\Core\Type\BirthdayType';
            $urlType = 'Symfony\Component\Form\Extension\Core\Type\UrlType';
            $textareaType = 'Symfony\Component\Form\Extension\Core\Type\TextareaType';
            $localeType = 'Symfony\Component\Form\Extension\Core\Type\LocaleType';
            $timezoneType = 'Symfony\Component\Form\Extension\Core\Type\TimezoneType';
        } else {
            $userGenderType = 'sonata_user_gender';
            $birthdayType = 'birthday';
            $urlType = 'url';
            $textareaType = 'text';
            $localeType = 'locale';
            $timezoneType = 'timezone';
        }
        $builder
            ->add('gender', $userGenderType, [
                'label' => 'form.label_gender',
                'required' => true,
                'translation_domain' => 'SonataUserBundle',
                'choices' => [
                     'gender_female' => UserInterface::GENDER_FEMALE ,
                     'gender_male' => UserInterface::GENDER_MALE,
                ],
            ]);
    }

   
  

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'sonata_user_profile1';
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->getBlockPrefix();
    }
}
