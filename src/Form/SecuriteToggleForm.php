<?php

namespace App\Form;

use App\Entity\Securite;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SecuriteToggleForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('isEnabled', CheckboxType::class, [
                'label' => 'Activer la sécurité',
                'label_attr' => ['class' => 'fw-bold'],
                'required' => false,
                'value' => true,
            ])
            ->add('submit', SubmitType::class, [
                'attr' => ['class' => 'btn bg-primary text-white m-4'],
                'row_attr' => ['class' => 'text-center'],
                'label' => 'Valider l\'état'
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Securite::class,
        ]);
    }
}
