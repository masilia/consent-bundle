<?php

declare(strict_types=1);

namespace Masilia\ConsentBundle\Form\Type;

use Masilia\ConsentBundle\Entity\CookieCategory;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class CategoryType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('identifier', TextType::class, [
                'label' => 'category.form.identifier',
                'attr' => [
                    'placeholder' => 'category.form.identifier_placeholder',
                    'class' => 'ibexa-input ibexa-input--text',
                ],
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => 'category.form.identifier_required',
                    ]),
                    new Assert\Length([
                        'max' => 50,
                        'maxMessage' => 'category.form.identifier_max_length',
                    ]),
                    new Assert\Regex([
                        'pattern' => '/^[a-z_]+$/',
                        'message' => 'category.form.identifier_format',
                    ]),
                ],
                'translation_domain' => 'masilia_consent',
            ])
            ->add('name', TextType::class, [
                'label' => 'category.form.name',
                'attr' => [
                    'placeholder' => 'category.form.name_placeholder',
                    'class' => 'ibexa-input ibexa-input--text',
                ],
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => 'category.form.name_required',
                    ]),
                    new Assert\Length([
                        'max' => 100,
                        'maxMessage' => 'category.form.name_max_length',
                    ]),
                ],
                'translation_domain' => 'masilia_consent',
            ])
            ->add('description', TextareaType::class, [
                'label' => 'category.form.description',
                'attr' => [
                    'placeholder' => 'category.form.description_placeholder',
                    'class' => 'ibexa-input ibexa-input--textarea',
                    'rows' => 3,
                ],
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => 'category.form.description_required',
                    ]),
                ],
                'translation_domain' => 'masilia_consent',
            ])
            ->add('required', CheckboxType::class, [
                'label' => 'category.form.required',
                'required' => false,
                'attr' => [
                    'class' => 'ibexa-input ibexa-input--checkbox',
                ],
                'help' => 'category.form.required_help',
                'translation_domain' => 'masilia_consent',
            ])
            ->add('defaultEnabled', CheckboxType::class, [
                'label' => 'category.form.default_enabled',
                'required' => false,
                'attr' => [
                    'class' => 'ibexa-input ibexa-input--checkbox',
                ],
                'help' => 'category.form.default_enabled_help',
                'translation_domain' => 'masilia_consent',
            ])
            ->add('position', IntegerType::class, [
                'label' => 'category.form.position',
                'attr' => [
                    'placeholder' => 'category.form.position_placeholder',
                    'class' => 'ibexa-input ibexa-input--text',
                    'min' => 0,
                ],
                'constraints' => [
                    new Assert\NotBlank([
                        'message' => 'category.form.position_required',
                    ]),
                    new Assert\PositiveOrZero([
                        'message' => 'category.form.position_positive',
                    ]),
                ],
                'translation_domain' => 'masilia_consent',
            ])
            ->add('cookies', CollectionType::class, [
                'entry_type' => CookieType::class,
                'entry_options' => [
                    'label' => false,
                ],
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'label' => 'category.form.cookies',
                'attr' => [
                    'class' => 'ibexa-collection',
                ],
                'translation_domain' => 'masilia_consent',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => CookieCategory::class,
            'translation_domain' => 'masilia_consent',
        ]);
    }
}
