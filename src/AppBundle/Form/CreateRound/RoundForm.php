<?php

namespace AppBundle\Form\CreateRound;

use AppBundle\Domain\Entity\Contest\Round;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Exception\InvalidConfigurationException;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
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
            'action',
            'rounds'
        ]);

        $resolver->setDefaults([
            'data_class' => RoundEntity::class,
            'action'     => null
        ]);

        $resolver->setAllowedTypes('action', 'string');
        $resolver->setAllowedTypes('rounds', 'array');
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

        /** @var Round $rounds */
        $rounds = $options['rounds'];

        $choices = [];
        foreach ($rounds as $round) {
            if (!$round instanceof Round) {
                throw new InvalidConfigurationException('$round must be an instance of ' . Round::class);
            }
            $choices[$round->name()] = $round->uuid();
        }

        $builder->add('contest', HiddenType::class);

        $builder->add('name', TextType::class, [
            'label'         => 'app.round-create.form.name',
            'required'      => true
        ]);

        $builder->add('sourceRound', ChoiceType::class, [
            'label'         => 'app.round-create.form.source-round',
            'placeholder'   => 'app.round-create.form.no-source',
            'choices'       => $choices,
            'required'      => false
        ]);

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

        $builder->add('matchesPerPlayer', IntegerType::class, array(
            'label'         => 'app.round-create.form.num-matches',
            'required'      => true
        ));

        $builder->add('submit', SubmitType::class, array(
            'label'         => 'app.round-create.form.submit'
        ));
    }
}
