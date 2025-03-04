<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class FetchAndStoreResigned extends Command
{
    protected $signature = 'fetch:resigned'; // Command name
    protected $description = 'Fetch resigned employees and store them in notifications';

    public function handle()
    {
        // Fetch data from the external API
        $response = Http::get('https://hr1.gwamerchandise.com/api/resigned/');

        if ($response->failed()) {
            $this->error('Failed to fetch data');
            return;
        }

        $data = $response->json();

        foreach ($data as $item) {
            DB::table('notifications')->updateOrInsert(
                ['id' => $item['id']], // Prevent duplicates
                [
                    'type' => 'App\Notifications\UserTerminated',
                    'notifiable_type' => 'App\Models\User',
                    'notifiable_id' => 1, // Adjust based on your user model
                    'data' => json_encode([
                        'title' => $item['title'],
                        'details' => $item['details'],
                    ]),
                    'created_at' => Carbon::parse($item['created_at'])->format('Y-m-d H:i:s'),
                    'updated_at' => Carbon::parse($item['updated_at'])->format('Y-m-d H:i:s'),
                ]
            );
        }

        $this->info('Data fetched and stored successfully!');
    }
}
