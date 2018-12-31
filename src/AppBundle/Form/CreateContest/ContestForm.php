<?php

namespace AppBundle\Form\CreateContest;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Form type: ContestForm
 *
 * @package AppBundle\Form\CreateContest
 */
class ContestForm extends AbstractType
{
    const MODE_CREATE = 0;
    const MODE_EDIT = 1;

    /**
     * Configures the options for this type.
     *
     * @param OptionsResolver $resolver The resolver for the options
     * @return void
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setRequired([
            'action',
            'mode'
        ]);

        $resolver->setAllowedTypes('action', 'string');
        $resolver->setAllowedTypes('mode', 'int');

        $resolver->setAllowedValues('mode', [ self::MODE_CREATE, self::MODE_EDIT ]);

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
     * @param FormBuilderInterface $builder The form builder
     * @param array                $options The options
     * @return void
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->setAction($options['action']);

        if ($options['mode'] == self::MODE_CREATE) {
            $builder->add('name', TextType::class, [
                'label'         => 'app.contest-edit.form.name',
                'required'      => true
            ]);
        } else {
            $builder->add('name', TextType::class, [
                'label'         => 'app.contest-edit.form.name',
                'disabled'      => true
            ]);
        }

        $builder->add('description', TextareaType::class, [
            'label'         => 'app.contest-edit.form.description',
            'required'      => false,
            'attr'          => [
                'rows'          => 5
            ]
        ]);

        $builder->add('regex', TextType::class, [
            'label'         => 'app.contest-edit.form.regex',
            'required'      => false
        ]);

        $builder->add('startDate', DateTimeType::class, [
            'label'         => 'app.contest-edit.form.start-date',
            'date_widget'   => 'single_text',
            'time_widget'   => 'single_text',
            'required'      => true
        ]);

        $builder->add('endDate', DateTimeType::class, [
            'label'         => 'app.contest-edit.form.end-date',
            'date_widget'   => 'single_text',
            'time_widget'   => 'single_text',
            'required'      => true
        ]);

        $builder->add('contestDate', DateTimeType::class, [
            'label'         => 'app.contest-edit.form.contest-date',
            'date_widget'   => 'single_text',
            'time_widget'   => 'single_text',
            'required'      => false
        ]);

        $builder->add('maxCompetitors', IntegerType::class, [
            'label'         => 'app.contest-edit.form.max-competitors',
            'required'      => false
        ]);

        if ($options['mode'] == self::MODE_CREATE) {
            $builder->add('submit', SubmitType::class, array(
                'label' => 'app.contest-edit.form.create'
            ));
        } else {
            $builder->add('submit', SubmitType::class, array(
                'label' => 'app.contest-edit.form.save'
            ));
        }
    }

    /**
     * Builds the form view.
     *
     * This method is called for each type in the hierarchy starting from the
     * top most type. Type extensions can further modify the view.
     *
     * A view of a form is built before the views of the child forms are built.
     * This means that you cannot access child views in this method. If you need
     * to do so, move your logic to {@link finishView()} instead.
     *
     * @param FormView      $view    The view
     * @param FormInterface $form    The form
     * @param array         $options The options
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars += [
            'mode' => $options['mode']
        ];

        parent::buildView($view, $form, $options);
    }
}
