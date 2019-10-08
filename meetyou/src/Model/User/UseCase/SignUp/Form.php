<?php
declare(strict_types=1);

namespace App\Model\User\UseCase\SignUp;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class Form extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $years = range(1902, date('Y', strtotime('15 years ago')));
        rsort($years);

        $builder
            ->add('email', EmailType::class)
            ->add('password', PasswordType::class)
            ->add('firstname', TextType::class)
            ->add('lastname', TextType::class)
            ->add('city', TextType::class)
            ->add('gender', ChoiceType::class, [
                'choices' => ['Male' => 'Male', 'Female' => 'Female'],
            ])
            ->add('birthday', DateType::class,
                ['years' => $years, 'input' => 'datetime_immutable'])
            ->add('interests', TextareaType::class)
            ->add('sign_up', SubmitType::class);
    }

}