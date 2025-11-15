<?php

declare(strict_types=1);

namespace Masilia\ConsentBundle\Form\Type;

use Masilia\ConsentBundle\Entity\Cookie;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class CookieType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'cookie.form.name',
                'attr' => [
                    'placeholder' => 'cookie.form.name_placeholder',
                    'class' => 'ibexa-input ibexa-input--text',
                ],
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => 'cookie.form.name_required',
                    ]),
                    new Assert\Length([
                        'max' => 100,
                        'maxMessage' => 'cookie.form.name_max_length',
                    ]),
                ],
                'translation_domain' => 'masilia_consent',
            ])
            ->add('purpose', TextareaType::class, [
                'label' => 'cookie.form.purpose',
                'attr' => [
                    'placeholder' => 'cookie.form.purpose_placeholder',
                    'class' => 'ibexa-input ibexa-input--textarea',
                    'rows' => 2,
                ],
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => 'cookie.form.purpose_required',
                    ]),
                ],
                'translation_domain' => 'masilia_consent',
            ])
            ->add('provider', TextType::class, [
                'label' => 'cookie.form.provider',
                'attr' => [
                    'placeholder' => 'cookie.form.provider_placeholder',
                    'class' => 'ibexa-input ibexa-input--text',
                ],
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => 'cookie.form.provider_required',
                    ]),
                    new Assert\Length([
                        'max' => 100,
                        'maxMessage' => 'cookie.form.provider_max_length',
                    ]),
                ],
                'translation_domain' => 'masilia_consent',
            ])
            ->add('expiry', TextType::class, [
                'label' => 'cookie.form.expiry',
                'attr' => [
                    'placeholder' => 'cookie.form.expiry_placeholder',
                    'class' => 'ibexa-input ibexa-input--text',
                ],
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => 'cookie.form.expiry_required',
                    ]),
                    new Assert\Length([
                        'max' => 50,
                        'maxMessage' => 'cookie.form.expiry_max_length',
                    ]),
                ],
                'help' => 'cookie.form.expiry_help',
                'translation_domain' => 'masilia_consent',
            ])
            ->add('scriptSrc', UrlType::class, [
                'label' => 'cookie.form.script_src',
                'required' => false,
                'attr' => [
                    'placeholder' => 'cookie.form.script_src_placeholder',
                    'class' => 'ibexa-input ibexa-input--text',
                ],
                'constraints' => [
                    new Assert\Url([
                        'message' => 'cookie.form.script_src_url',
                    ]),
                    new Assert\Length([
                        'max' => 500,
                        'maxMessage' => 'cookie.form.script_src_max_length',
                    ]),
                ],
                'help' => 'cookie.form.script_src_help',
                'translation_domain' => 'masilia_consent',
            ])
            ->add('scriptAsync', CheckboxType::class, [
                'label' => 'cookie.form.script_async',
                'required' => false,
                'attr' => [
                    'class' => 'ibexa-input ibexa-input--checkbox',
                ],
                'help' => 'cookie.form.script_async_help',
                'translation_domain' => 'masilia_consent',
            ])
            ->add('initCode', TextareaType::class, [
                'label' => 'cookie.form.init_code',
                'required' => false,
                'attr' => [
                    'placeholder' => 'cookie.form.init_code_placeholder',
                    'class' => 'ibexa-input ibexa-input--textarea',
                    'rows' => 5,
                ],
                'help' => 'cookie.form.init_code_help',
                'translation_domain' => 'masilia_consent',
            ])
            ->add('position', IntegerType::class, [
                'label' => 'cookie.form.position',
                'attr' => [
                    'placeholder' => 'cookie.form.position_placeholder',
                    'class' => 'ibexa-input ibexa-input--text',
                    'min' => 0,
                ],
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => 'cookie.form.position_required',
                    ]),
                    new Assert\PositiveOrZero([
                        'message' => 'cookie.form.position_positive',
                    ]),
                ],
                'translation_domain' => 'masilia_consent',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Cookie::class,
            'translation_domain' => 'masilia_consent',
        ]);
    }
}
