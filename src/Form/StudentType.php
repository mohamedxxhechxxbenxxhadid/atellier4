<?php

namespace App\Form;

use App\Entity\Student;
use App\Entity\Classroom;
use App\Entity\Club;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Doctrine\ORM\EntityRepository;

class StudentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('Email')
            ->add('NSC')
            ->add('classrooms',EntityType::class, [
                'class' => Classroom::class,
                'choice_label' => 'name', // Replace 'name' with the actual property you want to display
                'multiple' => false, // Set to true if you want to allow selecting multiple classrooms
                'expanded' => false, // Set to true if you want checkboxes instead of a select dropdown
            ])
            ->add('age')
            ->add('name')
            ->add('clubs', EntityType::class, [
                'class' => Club::class,
                'expanded' => true,
                'multiple' => true,
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('s')
                        ->orderBy('s.REF', 'ASC'); // You can adjust this sorting logic
                },
            ]);
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Student::class,
        ]);
    }
}
