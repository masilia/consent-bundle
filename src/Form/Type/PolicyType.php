<?php

declare(strict_types=1);

namespace Masilia\ConsentBundle\Form\Type;

use Masilia\ConsentBundle\Entity\CookiePolicy;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class PolicyType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('version', TextType::class, [
                'label' => 'policy.form.version',
                'attr' => [
                    'placeholder' => 'policy.form.version_placeholder',
                    'class' => 'ibexa-input ibexa-input--text',
                ],
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => 'policy.form.version_required',
                    ]),
                    new Assert\Length([
                        'max' => 20,
                        'maxMessage' => 'policy.form.version_max_length',
                    ]),
                    new Assert\Regex([
                        'pattern' => '/^[0-9]+\.[0-9]+\.[0-9]+$/',
                        'message' => 'policy.form.version_format',
                    ]),
                ],
                'translation_domain' => 'masilia_consent',
            ])
            ->add('cookiePrefix', TextType::class, [
                'label' => 'policy.form.cookie_prefix',
                'attr' => [
                    'placeholder' => 'policy.form.cookie_prefix_placeholder',
                    'class' => 'ibexa-input ibexa-input--text',
                ],
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => 'policy.form.cookie_prefix_required',
                    ]),
                    new Assert\Length([
                        'max' => 50,
                        'maxMessage' => 'policy.form.cookie_prefix_max_length',
                    ]),
                    new Assert\Regex([
                        'pattern' => '/^[a-z_]+$/',
                        'message' => 'policy.form.cookie_prefix_format',
                    ]),
                ],
                'translation_domain' => 'masilia_consent',
            ])
            ->add('expirationDays', IntegerType::class, [
                'label' => 'policy.form.expiration_days',
                'attr' => [
                    'placeholder' => 'policy.form.expiration_days_placeholder',
                    'class' => 'ibexa-input ibexa-input--text',
                    'min' => 1,
                    'max' => 365,
                ],
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => 'policy.form.expiration_days_required',
                    ]),
                    new Assert\Range([
                        'min' => 1,
                        'max' => 365,
                        'notInRangeMessage' => 'policy.form.expiration_days_range',
                    ]),
                ],
                'translation_domain' => 'masilia_consent',
            ])
            ->add('isActive', CheckboxType::class, [
                'label' => 'policy.form.is_active',
                'required' => false,
                'attr' => [
                    'class' => 'ibexa-input ibexa-input--checkbox',
                ],
                'translation_domain' => 'masilia_consent',
            ])
            ->add('categories', CollectionType::class, [
                'entry_type' => CategoryType::class,
                'entry_options' => [
                    'label' => false,
                ],
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'label' => 'policy.form.categories',
                'attr' => [
                    'class' => 'ibexa-collection',
                ],
                'translation_domain' => 'masilia_consent',
            ])
            ->add('thirdPartyServices', CollectionType::class, [
                'entry_type' => ThirdPartyServiceType::class,
                'entry_options' => [
                    'label' => false,
                ],
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'label' => 'policy.form.third_party_services',
                'attr' => [
                    'class' => 'ibexa-collection',
                ],
                'translation_domain' => 'masilia_consent',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => CookiePolicy::class,
            'translation_domain' => 'masilia_consent',
        ]);
    }
}
