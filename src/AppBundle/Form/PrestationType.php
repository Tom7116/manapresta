<?php

namespace AppBundle\Form;

use AppBundle\Entity\Employer;
use AppBundle\Entity\Prestation;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class PrestationType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $years = range(date('Y') + 1, date('Y') - 5);
        $user = $options['user'];
        $builder
            ->add('employer', EntityType::class, [
                'class' => Employer::class,
                'constraints' => new NotBlank(['message' => 'Champ obligatoire.']),
                'query_builder' => function (EntityRepository $er) use ($user) {
                    return $er->createQueryBuilder('u')
                        ->where('u.user = :user')
                        ->setParameter('user', $user);
                }
            ])
            ->add('place', TextType::class, [
                'constraints' => new NotBlank(['message' => 'Champ obligatoire.']),
            ])
            ->add('startTime', DateTimeType::class, [
                'constraints' => new NotBlank(['message' => 'Champ obligatoire.']),
                'years' => $years,
            ])
            ->add('endTime', DateTimeType::class, [
                'constraints' => new NotBlank(['message' => 'Champ obligatoire.']),
                'years' => $years,
            ])
            ->add('grossHonorary', NumberType::class, [
                'required' => false,
            ])
            ->add('netHonorary', NumberType::class, [
                'required' => false,
            ]);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Prestation::class,
        ]);
        $resolver->setRequired('user');
    }
}
