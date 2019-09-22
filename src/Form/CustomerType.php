<?php

namespace App\Form;

use App\Entity\Customer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Date;
use Symfony\Component\Validator\Constraints\GreaterThan;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\LessThanOrEqual;

/**
 * Class CustomerType
 * @package App\Form
 */
class CustomerType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('firstName', TextType::class, [
                'constraints' => [
                    new Length(['min' => 2, 'max' => 32])
                ]
            ])
            ->add('lastName', TextType::class, [
                'constraints' => [
                    new Length(['min' => 2, 'max' => 32])
                ]
            ])
            // Only adult are authorized
            ->add('dateOfBirth', DateType::class, [
                'widget' => 'single_text',
                'format' => 'yyyy-MM-dd',
                'constraints' => [
                    new Date(),
                    new LessThanOrEqual('-18 years')
                ]
            ])
            ->add('products', CollectionType::class, [
                'entry_type' => ProductType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false
            ]);
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Customer::class,
            'csrf_protection' => false
        ]);
    }
}
