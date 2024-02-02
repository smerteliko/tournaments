<?php

namespace App\Form;

use App\Entity\Matches;
use App\Entity\Teams;
use App\Entity\Tournaments;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Event\PreSubmitEvent;
use Symfony\Component\Form\Event\SubmitEvent;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Count;
use Symfony\Component\Validator\Constraints\Date;


class TournamentsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('Name', TextType::class, [
                'label'=> 'Tournament name'
            ])
            ->add('Teams', EntityType::class, [
                'label' => 'Teams',
                'class' => Teams::class,
                'choice_label' => 'Name',
                'multiple' => true,
                'expanded' => true,
                'constraints' => [new Count(['min'=>2])]
            ])
            ->addEventListener(FormEvents::PRE_SUBMIT, function (PreSubmitEvent $event): void {
                $data= $event->getData();
                $teamsCount = count($data['Teams']);
                if($teamsCount < 2) {
                    return;
                }
                $halfTour = $teamsCount;

                if($teamsCount === 2 ) {
                    $matchesPerRound = 1;
                    $halfTour = 1;
                } else if ( $teamsCount < 4 && $teamsCount  > 2) {
                    $matchesPerRound = (int)($teamsCount / 2);
                } else {
                    $matchesPerRound = 4;
                }

                $rounds = [];
                for ($round = 0; $round < $halfTour; $round++) {
                    $matches = [];
                    for ($match = 0; $match < $matchesPerRound; $match++) {
                        $team_1 = ($round + $match) % ($teamsCount - 1) ;

                        $team_2 = ($teamsCount - 1 - $match + $round) % ($teamsCount - 1) ;
                        if ($match === 0) {
                            $team_2 = $teamsCount-1;
                        }
                        $matches[] = [
                            'team_1' => $data['Teams'][$team_1],
                            'team_2' => $data['Teams'][$team_2],
                        ];
                    }
                    $rounds[] = ['matches'=>$matches, 'date'=>date('Y-m-d',strtotime("+".$round." day"))];
                }

                $data['brackets'] = $rounds;
                $event->setData($data);





            })
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Tournaments::class,
            'create_bracket' => true,
            'allow_extra_fields' => true
        ]);
    }
}
