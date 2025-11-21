<?php

declare(strict_types=1);

namespace Masilia\ConsentBundle\Form\Type;

use Masilia\ConsentBundle\Entity\CookiePolicy;
use Masilia\ConsentBundle\Service\SiteAccessProvider;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class PolicyType extends AbstractType
{
    public function __construct(
        private readonly SiteAccessProvider $siteAccessProvider
    ) {
    }

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
            ->add('siteAccess', ChoiceType::class, [
                'label' => 'policy.form.site_access',
                'choices' => $this->siteAccessProvider->getSiteAccessChoices(),
                'required' => false,
                'placeholder' => 'policy.form.site_access_placeholder',
                'attr' => [
                    'class' => 'ibexa-input ibexa-input--select',
                ],
                'help' => 'policy.form.site_access_help',
                'translation_domain' => 'masilia_consent',
            ])
            ->add('cookieName', TextType::class, [
                'label' => 'policy.form.cookie_name',
                'required' => true,
                'attr' => [
                    'class' => 'ibexa-input ibexa-input--text',
                    'placeholder' => 'policy.form.cookie_name_placeholder',
                ],
                'help' => 'policy.form.cookie_name_help',
                'constraints' => [
                    new Assert\NotBlank(['message' => 'policy.form.cookie_name_required']),
                    new Assert\Length([
                        'max' => 100,
                        'maxMessage' => 'policy.form.cookie_name_max_length',
                    ]),
                    new Assert\Regex([
                        'pattern' => '/^[a-z_]+$/',
                        'message' => 'policy.form.cookie_name_format',
                    ]),
                ],
                'translation_domain' => 'masilia_consent',
            ])
            ->add('cookieLifetime', IntegerType::class, [
                'label' => 'policy.form.cookie_lifetime',
                'required' => true,
                'attr' => [
                    'class' => 'ibexa-input ibexa-input--text',
                    'placeholder' => 'policy.form.cookie_lifetime_placeholder',
                ],
                'help' => 'policy.form.cookie_lifetime_help',
                'constraints' => [
                    new Assert\NotBlank(['message' => 'policy.form.cookie_lifetime_required']),
                    new Assert\Range([
                        'min' => 1,
                        'max' => 3650,
                        'notInRangeMessage' => 'policy.form.cookie_lifetime_range',
                    ]),
                ],
                'translation_domain' => 'masilia_consent',
            ])
            ->add('cookiePath', TextType::class, [
                'label' => 'policy.form.cookie_path',
                'required' => true,
                'attr' => [
                    'class' => 'ibexa-input ibexa-input--text',
                    'placeholder' => 'policy.form.cookie_path_placeholder',
                ],
                'help' => 'policy.form.cookie_path_help',
                'translation_domain' => 'masilia_consent',
            ])
            ->add('cookieDomain', TextType::class, [
                'label' => 'policy.form.cookie_domain',
                'required' => false,
                'attr' => [
                    'class' => 'ibexa-input ibexa-input--text',
                    'placeholder' => 'policy.form.cookie_domain_placeholder',
                ],
                'help' => 'policy.form.cookie_domain_help',
                'translation_domain' => 'masilia_consent',
            ])
            ->add('cookieSecure', CheckboxType::class, [
                'label' => 'policy.form.cookie_secure',
                'required' => false,
                'attr' => [
                    'class' => 'ibexa-input ibexa-input--checkbox',
                ],
                'help' => 'policy.form.cookie_secure_help',
                'translation_domain' => 'masilia_consent',
            ])
            ->add('cookieHttpOnly', CheckboxType::class, [
                'label' => 'policy.form.cookie_http_only',
                'required' => false,
                'attr' => [
                    'class' => 'ibexa-input ibexa-input--checkbox',
                ],
                'help' => 'policy.form.cookie_http_only_help',
                'translation_domain' => 'masilia_consent',
            ])
            ->add('cookieSameSite', ChoiceType::class, [
                'label' => 'policy.form.cookie_same_site',
                'choices' => [
                    'policy.form.cookie_same_site_lax' => 'lax',
                    'policy.form.cookie_same_site_strict' => 'strict',
                    'policy.form.cookie_same_site_none' => 'none',
                ],
                'required' => true,
                'attr' => [
                    'class' => 'ibexa-input ibexa-input--select',
                ],
                'help' => 'policy.form.cookie_same_site_help',
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
            ->add('save', SubmitType::class, [
                'label' => 'policy.form.save',
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
