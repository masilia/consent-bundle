<?php

declare(strict_types=1);

namespace Masilia\ConsentBundle\Form\Type;

use Masilia\ConsentBundle\Entity\ThirdPartyService;
use Masilia\ConsentBundle\Service\CookiePresetService;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class ThirdPartyServiceType extends AbstractType
{
    public function __construct(
        private readonly CookiePresetService $presetService
    ) {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $presets = $this->presetService->getPresets();
        $presetChoices = [];
        foreach ($presets as $key => $preset) {
            $presetChoices[$preset['name']] = $key;
        }

        $builder
            ->add('presetType', ChoiceType::class, [
                'label' => 'third_party_service.form.preset_type',
                'choices' => $presetChoices,
                'required' => false,
                'placeholder' => 'third_party_service.form.preset_type_placeholder',
                'attr' => [
                    'class' => 'ibexa-input ibexa-input--select',
                ],
                'help' => 'third_party_service.form.preset_type_help',
                'translation_domain' => 'masilia_consent',
            ])
            ->add('identifier', TextType::class, [
                'label' => 'third_party_service.form.identifier',
                'attr' => [
                    'placeholder' => 'third_party_service.form.identifier_placeholder',
                    'class' => 'ibexa-input ibexa-input--text',
                ],
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => 'third_party_service.form.identifier_required',
                    ]),
                    new Assert\Length([
                        'max' => 50,
                        'maxMessage' => 'third_party_service.form.identifier_max_length',
                    ])
                ],
                'translation_domain' => 'masilia_consent',
            ])
            ->add('name', TextType::class, [
                'label' => 'third_party_service.form.name',
                'attr' => [
                    'placeholder' => 'third_party_service.form.name_placeholder',
                    'class' => 'ibexa-input ibexa-input--text',
                ],
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => 'third_party_service.form.name_required',
                    ]),
                    new Assert\Length([
                        'max' => 100,
                        'maxMessage' => 'third_party_service.form.name_max_length',
                    ]),
                ],
                'translation_domain' => 'masilia_consent',
            ])
            ->add('category', TextType::class, [
                'label' => 'third_party_service.form.category',
                'attr' => [
                    'placeholder' => 'third_party_service.form.category_placeholder',
                    'class' => 'ibexa-input ibexa-input--text',
                ],
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => 'third_party_service.form.category_required',
                    ]),
                    new Assert\Length([
                        'max' => 50,
                        'maxMessage' => 'third_party_service.form.category_max_length',
                    ]),
                ],
                'help' => 'third_party_service.form.category_help',
                'translation_domain' => 'masilia_consent',
            ])
            ->add('description', TextareaType::class, [
                'label' => 'third_party_service.form.description',
                'attr' => [
                    'placeholder' => 'third_party_service.form.description_placeholder',
                    'class' => 'ibexa-input ibexa-input--textarea',
                    'rows' => 3,
                ],
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => 'third_party_service.form.description_required',
                    ]),
                ],
                'translation_domain' => 'masilia_consent',
            ])
            ->add('privacyPolicyUrl', UrlType::class, [
                'label' => 'third_party_service.form.privacy_policy_url',
                'attr' => [
                    'placeholder' => 'third_party_service.form.privacy_policy_url_placeholder',
                    'class' => 'ibexa-input ibexa-input--text',
                ],
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => 'third_party_service.form.privacy_policy_url_required',
                    ]),
                    new Assert\Url([
                        'message' => 'third_party_service.form.privacy_policy_url_format',
                    ]),
                    new Assert\Length([
                        'max' => 500,
                        'maxMessage' => 'third_party_service.form.privacy_policy_url_max_length',
                    ]),
                ],
                'translation_domain' => 'masilia_consent',
            ])
            ->add('configKey', TextType::class, [
                'label' => 'third_party_service.form.config_key',
                'attr' => [
                    'placeholder' => 'third_party_service.form.config_key_placeholder',
                    'class' => 'ibexa-input ibexa-input--text',
                ],
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => 'third_party_service.form.config_key_required',
                    ]),
                    new Assert\Length([
                        'max' => 100,
                        'maxMessage' => 'third_party_service.form.config_key_max_length',
                    ]),
                ],
                'help' => 'third_party_service.form.config_key_help',
                'translation_domain' => 'masilia_consent',
            ])
            ->add('configValue', TextType::class, [
                'label' => 'third_party_service.form.config_value',
                'attr' => [
                    'placeholder' => 'third_party_service.form.config_value_placeholder',
                    'class' => 'ibexa-input ibexa-input--text',
                ],
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => 'third_party_service.form.config_value_required',
                    ]),
                    new Assert\Length([
                        'max' => 255,
                        'maxMessage' => 'third_party_service.form.config_value_max_length',
                    ]),
                ],
                'help' => 'third_party_service.form.config_value_help',
                'translation_domain' => 'masilia_consent',
            ])
            ->add('enabled', CheckboxType::class, [
                'label' => 'third_party_service.form.enabled',
                'required' => false,
                'attr' => [
                    'class' => 'ibexa-input ibexa-input--checkbox',
                ],
                'help' => 'third_party_service.form.enabled_help',
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
