<?php

namespace App\Form;

use App\Entity\Teams;
use App\Entity\Tournaments;
use App\Services\TeamsShuffleService;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Event\PreSubmitEvent;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Count;
use Symfony\Component\String\Slugger\AsciiSlugger;

class TournamentsType extends AbstractType
{

    private TeamsShuffleService $shuffler;
    public function __construct(TeamsShuffleService $teamsShuffleService)
    {
        $this->shuffler = $teamsShuffleService;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('Name', TextType::class, [
                'label'=> 'Tournament name'
            ])

            ->addEventListener(FormEvents::PRE_SUBMIT, function (PreSubmitEvent $event): void {
                $data= $event->getData();
                $teams = $data['Teams'] ?? null;
                $data['brackets'] =  $this->shuffler->shuffle($teams);

                $slugger = new AsciiSlugger();
                $data['slug'] = $slugger->slug($data['Name']);

                $event->setData($data);
            })->addEventListener(
                FormEvents::PRE_SET_DATA,
                function (FormEvent $event) use ($options): void {
                    $form = $event->getForm();
                    if($options['Teams'] && count($options['Teams']) >= 2) {
                        $form->add('Teams', EntityType::class, [
                            'label' => 'Teams',
                            'class' => Teams::class,
                            'choice_label' => 'Name',
                            'multiple' => true,
                            'expanded' => true,
                            'constraints' => [new Count(['min'=>2])]
                        ]);
                    }

                }
            );
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Tournaments::class,
            'Teams' => [],
            'create_bracket' => true,
            'allow_extra_fields' => true
        ]);

    }

}
