<?php

namespace AppBundle\Form\CreateContest;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Form type: ContestForm
 *
 * @package AppBundle\Form\CreateContest
 */
class ContestForm extends AbstractType
{
    /**
     * Configures the options for this type.
     *
     * @param OptionsResolver $resolver The resolver for the options
     * @return void
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setRequired([
            'action'
        ]);

        $resolver->setAllowedTypes('action', 'string');

        $resolver->setDefaults([
            'data_class' => ContestEntity::class,
            'action'     => null
        ]);
    }

    /**
     * Builds the form.
     *
     * This method is called for each type in the hierarchy starting from the
     * top most type. Type extensions can further modify the form.
     *
     * @see FormTypeExtensionInterface::buildForm()
     *
     * @param FormBuilderInterface $builder The form builder
     * @param array                $options The options
     * @return void
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->setAction($options['action']);

        $builder->add('name', TextType::class, [
            'label'         => 'app.contest-create.form.name',
            'required'      => true
        ]);

        $builder->add('description', TextareaType::class, [
            'label'         => 'app.contest-create.form.description',
            'required'      => false,
            'attr'          => [
                'rows'          => 5
            ]
        ]);

        $builder->add('regex', TextType::class, [
            'label'         => 'app.contest-create.form.regex',
            'required'      => false
        ]);

        $builder->add('startDate', DateTimeType::class, [
            'label'         => 'app.contest-create.form.start-date',
            'date_widget'   => 'single_text',
            'time_widget'   => 'single_text',
            'required'      => true
        ]);

        $builder->add('endDate', DateTimeType::class, [
            'label'         => 'app.contest-create.form.end-date',
            'date_widget'   => 'single_text',
            'time_widget'   => 'single_text',
            'required'      => true
        ]);

        $builder->add('contestDate', DateTimeType::class, [
            'label'         => 'app.contest-create.form.contest-date',
            'date_widget'   => 'single_text',
            'time_widget'   => 'single_text',
            'required'      => false
        ]);

        $builder->add('maxCompetitors', IntegerType::class, [
            'label'         => 'app.contest-create.form.max-competitors',
            'required'      => false
        ]);

        $builder->add('save', SubmitType::class, array(
            'label'         => 'app.contest-create.form.submit'
        ));
    }
}
