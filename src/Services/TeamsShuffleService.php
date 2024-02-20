<?php

namespace App\Services;

class TeamsShuffleService
{
    public function shuffle(array|null $teams): array
    {
        if(is_null($teams)) {
            return [];
        }
        $teamsCount = count($teams);
        if($teamsCount < 2) {
            return [];
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
                    'team_1' => $teams[$team_1],
                    'team_2' => $teams[$team_2],
                ];
            }
            $rounds[] = ['matches'=>$matches, 'date'=>date('Y-m-d',strtotime("+".$round." day"))];
        }

        return $rounds;
    }
}