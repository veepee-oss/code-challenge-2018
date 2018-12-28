<?php

namespace AppBundle\Form\RegisterCompetitor;

use AppBundle\Entity\Contest;
use AppBundle\Repository\ContestRepository;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Form type: CompetitorForm
 *
 * @package AppBundle\Form\RegisterCompetitor
 */
class CompetitorForm extends AbstractType
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
            'data_class' => CompetitorEntity::class,
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

        $builder->add('contest', EntityType::class, [
            'label'         => 'app.register-competitor.form.contest',
            'class'         => 'AppBundle:Contest',
            'choice_label'  => 'name',
            'query_builder' => function (ContestRepository $repo) {
                return $repo->getFindActiveContestsQueryBuilder();
            },
            'required'      => true
        ]);

        $builder->add('email', EmailType::class, [
            'label'         => 'app.register-competitor.form.email',
            'required'      => true
        ]);

        $builder->add('url', UrlType::class, [
            'label'         => 'app.register-competitor.form.url',
            'required'      => true
        ]);

        $builder->add('submit', SubmitType::class, array(
            'label'         => 'app.register-competitor.form.submit'
        ));
    }
}
