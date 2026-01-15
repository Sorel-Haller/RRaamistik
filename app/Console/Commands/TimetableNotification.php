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

        $entries = collect();

        $date = Carbon::parse($dateVariable)->locale('et');
                dd($date->format('d.m.Y'), $date->dayName);

        $entries = collect(data_get($data, 'content', []))
            ->map(function ($item) {
                $date = Carbon::parse(data_get($item, 'from'));
                return [
                    'name' => data_get($item, 'studentGroupName', 'Unknown Group'),
                    'date' => $date->toDateString(),
                    'day' => $date->translatedFormat('l'),
                    'subject' => data_get($item, 'subject', 'Unknown Subject'),
                    'description' => data_get($item, 'description', ''),
                ];
            });

        dd($entries);
    } 
}