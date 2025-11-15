<?php

declare(strict_types=1);

namespace Masilia\ConsentBundle\Form\Type;

use Masilia\ConsentBundle\Entity\ThirdPartyService;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class ThirdPartyServiceType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('identifier', TextType::class, [
                'label' => 'service.form.identifier',
                'attr' => [
                    'placeholder' => 'service.form.identifier_placeholder',
                    'class' => 'ibexa-input ibexa-input--text',
                ],
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => 'service.form.identifier_required',
                    ]),
                    new Assert\Length([
                        'max' => 50,
                        'maxMessage' => 'service.form.identifier_max_length',
                    ]),
                    new Assert\Regex([
                        'pattern' => '/^[a-z_]+$/',
                        'message' => 'service.form.identifier_format',
                    ]),
                ],
                'translation_domain' => 'masilia_consent',
            ])
            ->add('name', TextType::class, [
                'label' => 'service.form.name',
                'attr' => [
                    'placeholder' => 'service.form.name_placeholder',
                    'class' => 'ibexa-input ibexa-input--text',
                ],
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => 'service.form.name_required',
                    ]),
                    new Assert\Length([
                        'max' => 100,
                        'maxMessage' => 'service.form.name_max_length',
                    ]),
                ],
                'translation_domain' => 'masilia_consent',
            ])
            ->add('category', TextType::class, [
                'label' => 'service.form.category',
                'attr' => [
                    'placeholder' => 'service.form.category_placeholder',
                    'class' => 'ibexa-input ibexa-input--text',
                ],
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => 'service.form.category_required',
                    ]),
                    new Assert\Length([
                        'max' => 50,
                        'maxMessage' => 'service.form.category_max_length',
                    ]),
                ],
                'help' => 'service.form.category_help',
                'translation_domain' => 'masilia_consent',
            ])
            ->add('description', TextareaType::class, [
                'label' => 'service.form.description',
                'attr' => [
                    'placeholder' => 'service.form.description_placeholder',
                    'class' => 'ibexa-input ibexa-input--textarea',
                    'rows' => 3,
                ],
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => 'service.form.description_required',
                    ]),
                ],
                'translation_domain' => 'masilia_consent',
            ])
            ->add('privacyPolicyUrl', UrlType::class, [
                'label' => 'service.form.privacy_policy_url',
                'attr' => [
                    'placeholder' => 'service.form.privacy_policy_url_placeholder',
                    'class' => 'ibexa-input ibexa-input--text',
                ],
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => 'service.form.privacy_policy_url_required',
                    ]),
                    new Assert\Url([
                        'message' => 'service.form.privacy_policy_url_url',
                    ]),
                    new Assert\Length([
                        'max' => 500,
                        'maxMessage' => 'service.form.privacy_policy_url_max_length',
                    ]),
                ],
                'translation_domain' => 'masilia_consent',
            ])
            ->add('configKey', TextType::class, [
                'label' => 'service.form.config_key',
                'attr' => [
                    'placeholder' => 'service.form.config_key_placeholder',
                    'class' => 'ibexa-input ibexa-input--text',
                ],
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => 'service.form.config_key_required',
                    ]),
                    new Assert\Length([
                        'max' => 100,
                        'maxMessage' => 'service.form.config_key_max_length',
                    ]),
                ],
                'help' => 'service.form.config_key_help',
                'translation_domain' => 'masilia_consent',
            ])
            ->add('configValue', TextType::class, [
                'label' => 'service.form.config_value',
                'attr' => [
                    'placeholder' => 'service.form.config_value_placeholder',
                    'class' => 'ibexa-input ibexa-input--text',
                ],
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => 'service.form.config_value_required',
                    ]),
                    new Assert\Length([
                        'max' => 255,
                        'maxMessage' => 'service.form.config_value_max_length',
                    ]),
                ],
                'help' => 'service.form.config_value_help',
                'translation_domain' => 'masilia_consent',
            ])
            ->add('enabled', CheckboxType::class, [
                'label' => 'service.form.enabled',
                'required' => false,
                'attr' => [
                    'class' => 'ibexa-input ibexa-input--checkbox',
                ],
                'translation_domain' => 'masilia_consent',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ThirdPartyService::class,
            'translation_domain' => 'masilia_consent',
        ]);
    }
}
