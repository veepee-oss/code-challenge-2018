<?php

namespace AppBundle\Form\EditPlayer;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Form type: PlayerForm
 *
 * @package AppBundle\Form\EditPlayer
 */
class PlayerForm extends AbstractType
{
    /**
     * Configures the options for this type.
     *
     * @param OptionsResolver $resolver The resolver for the options
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => PlayerEntity::class
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
        $builder->add('name', TextType::class, array(
            'label' => 'app.player-edit.form.name'
        ));

        $builder->add('email', EmailType::class, array(
            'label' => 'app.player-edit.form.email'
        ));

        $builder->add('url', UrlType::class, array(
            'label' => 'app.player-edit.form.url'
        ));

        $builder->add('save', SubmitType::class, array(
            'label' => 'app.player-edit.form.submit'
        ));
    }
}
