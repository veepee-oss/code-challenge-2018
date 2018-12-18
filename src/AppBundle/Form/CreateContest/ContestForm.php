<?php

namespace AppBundle\Form\CreateContest;

use Doctrine\DBAL\Types\StringType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
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
    /** @var string Constants */
    private const OPTION_ACTION = 'action';
    private const OPTION_DATA_CLASS = 'data_class';
    private const OPTION_LABEL = 'label';
    private const OPTION_REQUIRED = 'required';
    private const OPTION_DATE_FORMAT = 'date_format';
    private const OPTION_DATE_WIDGET = 'date_widget';
    private const OPTION_TIME_WIDGET = 'time_widget';
    private const VALUE_SINGLE_TEXT = 'single_text';


    /**
     * Configures the options for this type.
     *
     * @param OptionsResolver $resolver The resolver for the options
     * @return void
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setRequired([
            self::OPTION_ACTION
        ]);

        $resolver->setAllowedTypes(self::OPTION_ACTION, 'string');

        $resolver->setDefaults([
            self::OPTION_DATA_CLASS => ContestEntity::class,
            self::OPTION_ACTION     => null
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

        $builder->add('name', StringType::class, [
            self::OPTION_LABEL          => 'app.createcontest.form.name',
            self::OPTION_REQUIRED       => true
        ]);

        $builder->add('description', TextType::class, [
            self::OPTION_LABEL          => 'app.createcontest.form.description',
            self::OPTION_REQUIRED       => false
        ]);

        $builder->add('regex', TextType::class, [
            self::OPTION_LABEL          => 'app.createcontest.form.regex',
            self::OPTION_REQUIRED       => false
        ]);

        $builder->add('startDate', DateTimeType::class, [
            self::OPTION_LABEL          => 'app.createcontest.form.start-date',
            self::OPTION_REQUIRED       => true,
            self::OPTION_DATE_FORMAT    => \IntlDateFormatter::MEDIUM,
            self::OPTION_DATE_WIDGET    => self::VALUE_SINGLE_TEXT,
            self::OPTION_TIME_WIDGET    => self::VALUE_SINGLE_TEXT
        ]);

        $builder->add('endDate', DateTimeType::class, [
            self::OPTION_LABEL          => 'app.createcontest.form.end-date',
            self::OPTION_REQUIRED       => true,
            self::OPTION_DATE_FORMAT    => \IntlDateFormatter::MEDIUM,
            self::OPTION_DATE_WIDGET    => self::VALUE_SINGLE_TEXT,
            self::OPTION_TIME_WIDGET    => self::VALUE_SINGLE_TEXT
        ]);

        $builder->add('contestDate', DateTimeType::class, [
            self::OPTION_LABEL          => 'app.createcontest.form.contest-date',
            self::OPTION_REQUIRED       => true,
            self::OPTION_DATE_FORMAT    => \IntlDateFormatter::MEDIUM,
            self::OPTION_DATE_WIDGET    => self::VALUE_SINGLE_TEXT,
            self::OPTION_TIME_WIDGET    => self::VALUE_SINGLE_TEXT
        ]);
    }
}
