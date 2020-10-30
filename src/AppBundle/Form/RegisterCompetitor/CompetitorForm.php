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
            'mode'       => 'register'
        ]);

        $resolver->setAllowedTypes('action', 'string');
        $resolver->setAllowedTypes('mode', 'string');
        $resolver->setAllowedValues('mode', [ 'register', 'validate', 'admin' ]);

        $resolver->setDefaults([
            'validation_groups' => function (FormInterface $form) {
                return $form->getConfig()->getOption('mode');
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

        switch ($options['mode']) {

            case 'register':
                $this->addContest($builder, true)
                    ->addEmail($builder, false)
                    ->addUrl($builder, true)
                    ->addSubmit($builder, 'register');
                break;

            case 'validate':
                $this->addContest($builder, false)
                    ->addEmail($builder, true)
                    ->addName($builder)
                    ->addUrl($builder, true)
                    ->addSubmit($builder, 'validate');
                break;

            case 'admin':
                $this->addContest($builder, false)
                    ->addEmail($builder, false)
                    ->addName($builder)
                    ->addUrl($builder, false)
                    ->addValidated($builder)
                    ->addSubmit($builder, 'admin');
                break;

            default:
                break;
        }
    }

    private function addContest(FormBuilderInterface $builder, bool $entityType): CompetitorForm
    {
        if ($entityType) {
            $builder->add('contest', EntityType::class, [
                'label' => 'app.register-competitor.form.contest',
                'class' => 'AppBundle:Contest',
                'choice_label' => 'name',
                'query_builder' => function (ContestRepository $repo) {
                    return $repo->getFindOpenedContestsQueryBuilder();
                },
                'required' => true
            ]);
        } else {
            $builder->add('visibleContest', TextType::class, [
                'label' => 'app.register-competitor.form.contest',
                'mapped' => false,
                'disabled' => true
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

        return $this;
    }

    private function addEmail(FormBuilderInterface $builder, bool $disabled): CompetitorForm
    {
        $builder->add('email', EmailType::class, [
            'label'         => 'app.register-competitor.form.email',
            'required'      => true,
            'disabled'      => $disabled
        ]);

        return $this;
    }

    private function addName(FormBuilderInterface $builder): CompetitorForm
    {
        $builder->add('name', TextType::class, [
            'label'         => 'app.register-competitor.form.name',
            'required'      => true
        ]);

        return $this;
    }

    private function addUrl(FormBuilderInterface $builder, bool $required): CompetitorForm
    {
        $label = $required
            ? 'app.register-competitor.form.url'
            : 'app.register-competitor.form.url-opt';

        $builder->add('url', UrlType::class, [
            'label'         => $label,
            'required'      => $required
        ]);

        return $this;
    }

    private function addValidated(FormBuilderInterface $builder): CompetitorForm
    {
        $builder->add('validated', CheckboxType::class, [
            'label' => 'app.register-competitor.form.validated',
            'required' => false
        ]);

        return $this;
    }

    private function addSubmit(FormBuilderInterface $builder, string $mode): CompetitorForm
    {
        switch ($mode) {
            default:
            case 'register':
                $label = 'app.register-competitor.form.submit.register';
                break;

            case 'validate':
                $label = 'app.register-competitor.form.submit.validate';
                break;

            case 'admin':
                $label = 'app.register-competitor.form.submit.save';
                break;
        }

        $builder->add('submit', SubmitType::class, array(
            'label'         => $label
        ));

        return $this;
    }
}
