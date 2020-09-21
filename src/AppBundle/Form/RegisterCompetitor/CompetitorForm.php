<?php

namespace AppBundle\Form\RegisterCompetitor;

use AppBundle\Repository\ContestRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
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

        $resolver->setDefaults([
            'data_class' => CompetitorEntity::class,
            'action'     => null,
            'admin'      => false
        ]);

        $resolver->setAllowedTypes('action', 'string');
        $resolver->setAllowedTypes('admin', 'bool');

        $resolver->setDefaults([
            'validation_groups' => function (FormInterface $form) {
                $isAdmin = $form->getConfig()->getOption('admin');
                return ($isAdmin) ? ['admin'] : ['default'];
            }
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

        if (!$options['admin']) {
            $builder->add('contest', EntityType::class, [
                'label'         => 'app.register-competitor.form.contest',
                'class'         => 'AppBundle:Contest',
                'choice_label'  => 'name',
                'query_builder' => function (ContestRepository $repo) {
                    return $repo->getFindOpenedContestsQueryBuilder();
                },
                'required'      => true
            ]);
        } else {
            $builder->add('visibleContest', TextType::class, [
                'label'         => 'app.register-competitor.form.contest',
                'mapped'        => false,
                'disabled'      => true
            ]);

            $builder->addEventListener(FormEvents::POST_SET_DATA, function (FormEvent $event) {
                /** @var CompetitorEntity $competitor */
                $competitor = $event->getData();
                $form = $event->getForm();

                if ($competitor || $competitor->getContest()) {
                    $form->get('visibleContest')->setData($competitor->getContest()->getName());
                }
            });
        }

        $builder->add('email', EmailType::class, [
            'label'         => 'app.register-competitor.form.email',
            'required'      => true
        ]);

        if ($options['admin']) {
            $builder->add('name', TextType::class, [
                'label'         => 'app.register-competitor.form.name',
                'required'      => true
            ]);

            $builder->add('url', UrlType::class, [
                'label'         => 'app.register-competitor.form.url-opt',
                'required'      => false
            ]);

            $builder->add('validated', CheckboxType::class, [
                'label' => 'app.register-competitor.form.validated',
                'required' => false
            ]);
        } else {
            $builder->add('url', UrlType::class, [
                'label'         => 'app.register-competitor.form.url',
                'required'      => true
            ]);
        }

        $builder->add('submit', SubmitType::class, array(
            'label'         => 'app.register-competitor.form.submit'
        ));
    }
}
