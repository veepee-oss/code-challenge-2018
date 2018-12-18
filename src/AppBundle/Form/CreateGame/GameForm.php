<?php

namespace AppBundle\Form\CreateGame;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Form type: GameForm
 *
 * @package AppBundle\Form\CreateGame
 */
class GameForm extends AbstractType
{
    const TYPE_GAME_DATA = 'game_data';
    const TYPE_PLAYERS   = 'players';

    /**
     * Configures the options for this type.
     *
     * @param OptionsResolver $resolver The resolver for the options
     * @return void
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setRequired(array(
            'action',
            'form_type'
        ));

        $resolver->setAllowedTypes('action', 'string');
        $resolver->setAllowedTypes('form_type', 'string');

        $resolver->setAllowedValues('form_type', array(
            static::TYPE_GAME_DATA,
            static::TYPE_PLAYERS
        ));

        $resolver->setDefaults(array(
            'csrf_protection' => false,
            'data_class' => GameEntity::class,
            'action' => null
        ));
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

        switch ($options['form_type']) {
            case static::TYPE_GAME_DATA:
                $this->buildGameDataForm($builder, $options);
                break;

            case static::TYPE_PLAYERS:
                $this->buildPlayersForm($builder, $options);
                break;

            default:
                break;
        }
    }

    /**
     * Builds the game data form
     *
     * @param FormBuilderInterface $builder The form builder
     * @param array                $options The options
     * @return void
     */
    protected function buildGameDataForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('height', IntegerType::class, array(
            'label' => 'app.createpage.form.height'
        ));

        $builder->add('width', IntegerType::class, array(
            'label' => 'app.createpage.form.width'
        ));

        $builder->add('playerNum', IntegerType::class, array(
            'label' => 'app.createpage.form.player-num'
        ));

        $builder->add('minGhosts', IntegerType::class, array(
            'label' => 'app.createpage.form.min-ghosts'
        ));

        $builder->add('ghostRate', IntegerType::class, array(
            'label' => 'app.createpage.form.ghost-rate'
        ));

        $builder->add('limit', IntegerType::class, array(
            'label' => 'app.createpage.form.limit'
        ));

        $builder->add('name', TextType::class, array(
            'label' => 'app.createpage.form.name-optional',
            'required' => false
        ));

        $builder->add('save', SubmitType::class, array(
            'label' => 'app.createpage.form.next'
        ));
    }

    /**
     * Builds the players form
     *
     * @param FormBuilderInterface $builder The form builder
     * @param array                $options The options
     * @return void
     */
    protected function buildPlayersForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('height', HiddenType::class);
        $builder->add('width', hiddenType::class);
        $builder->add('playerNum', hiddenType::class);
        $builder->add('minGhosts', hiddenType::class);
        $builder->add('ghostRate', hiddenType::class);
        $builder->add('limit', hiddenType::class);
        $builder->add('name', hiddenType::class);

        $builder->add('players', CollectionType::class, array(
            'entry_type' => '\AppBundle\Form\CreateGame\PlayerForm',
            'allow_add' => true
        ));

        $builder->add('save', SubmitType::class, array(
            'label' => 'app.createpage.form.create'
        ));
    }
}
