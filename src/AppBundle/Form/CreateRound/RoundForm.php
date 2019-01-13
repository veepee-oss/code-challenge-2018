<?php

namespace AppBundle\Form\CreateRound;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Form type: RoundForm
 *
 * @package AppBundle\Form\CreateRound
 */
class RoundForm extends AbstractType
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

        $resolver->setDefaults([
            'data_class' => RoundEntity::class,
            'action'     => null
        ]);

        $resolver->setAllowedTypes('action', 'string');
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

        $builder->add('contest', HiddenType::class);

        $builder->add('name', TextType::class, [
            'label'         => 'app.round-create.form.name',
            'required'      => true
        ]);

        // TODO add sourceRound

        $builder->add('height', IntegerType::class, array(
            'label'         => 'app.create-page.form.height',
            'required'      => true
        ));

        $builder->add('width', IntegerType::class, array(
            'label'         => 'app.create-page.form.width',
            'required'      => true
        ));

        $builder->add('minGhosts', IntegerType::class, array(
            'label'         => 'app.create-page.form.min-ghosts',
            'required'      => true
        ));

        $builder->add('ghostRate', IntegerType::class, array(
            'label'         => 'app.create-page.form.ghost-rate',
            'required'      => true
        ));

        $builder->add('limit', IntegerType::class, array(
            'label'         => 'app.create-page.form.limit',
            'required'      => true
        ));

        $builder->add('submit', SubmitType::class, array(
            'label'         => 'app.round-create.form.submit'
        ));
    }
}
