<?php

namespace App\Console\Commands;

use Illuminate\Support\Facades\Http;
use Illuminate\Console\Command;
use Carbon\Carbon;
use Illuminate\Support\Collection;


class TimetableNotification extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:timetable-notification';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $response = Http::get('https://tahveltp.edu.ee/hois_back/timetableevents/timetableSearch', [
            'from' => now()->addWeek(1)->startOfWeek()->toIsoString(),
            'thru' => now()->addWeek(1)->endOfWeek()->toIsoString(),
            'lang' => 'ET',
            'page' => 0,
            'schoolId' => 38,
            'size' => 50,
            'studentGroups' => '39248890-7051-4182-9a9a-8a82259b862b',
        ]);

        $data = $response->json();
        $content = data_get($data, 'content', []);
        $entries = [];

        $demo = [
            'friday' => [
                0 => [ 'name' => '123', 'start' => '10:00', 'end' => '11:00', 'room' => 'A101' ],
            ]
        ];

        foreach ($content as $entry) {
            $date = Carbon::parse(data_get($entry, 'date'))->locale('et');
            $dayName = $date->dayName;

            $entries[$dayName][] = [
                'name' => data_get($entry, 'nameEt'),
                'start' => data_get($entry, 'timeStart'),
                'end' => data_get($entry, 'timeEnd'),
                'room' => data_get($entry, 'rooms.0.roomCode'),
            ];
        
        foreach ($content as $item) {
            $date = Carbon::parse(data_get($item, 'date'))->locale('et');
            $dayName = $date->dayName;

            if (! array_key_exists($dayName, $entries)) {
                $entries[$dayName] = [
                    'date' => $date->toDateString('d F Y'),
                    'day' => [$dayName],
                ];
            }

            $entries[$dayName]['entries'][] = [
                'name' => data_get($item, 'nameEt'),
                'start' => data_get($item, 'timeStart'),
                'end' => data_get($item, 'timeEnd'),
                'room' => data_get($item, 'rooms.0.roomCode'),
            ];
        }

        dd($entries);
        } 
    }   
}