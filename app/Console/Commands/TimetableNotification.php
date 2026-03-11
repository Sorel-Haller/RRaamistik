<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use App\Mail\Timetable;

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
        $startDate = now()->startOfWeek();
        $endDate = now()->endOfWeek();

        $response = Http::get('https://tahveltp.edu.ee/hois_back/timetableevents/timetableSearch', [
            'from' => $startDate->toISOString(),
            'lang' => 'ET',
            'page' => 0,
            'schoolId' => 38,
            'size' => 50,
            'studentGroups' => '39248890-7051-4182-9a9a-8a82259b862b',
            'thru' => $endDate->toISOString(),
        ]);

        $data = $response->json();

        $timetableEvents = collect(data_get($data, 'content', []))
            ->sortBy(['date', 'timeStart'])
            ->groupBy(function ($event) {
                return Carbon::parse($event['date'])
                    ->locale('et_EE')
                    ->dayName;
            });

        Mail::to('test@test.ee')->send(new Timetable($timetableEvents, $startDate, $endDate));

        $this->info('Timetable email sent successfully!');
    }
}