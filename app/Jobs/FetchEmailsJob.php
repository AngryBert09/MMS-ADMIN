<?php


namespace App\Jobs;

use Webklex\PHPIMAP\ClientManager;

use Webklex\IMAP\Facades\Client;

use Illuminate\Support\Facades\Cache;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class FetchEmailsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle()
    {
        $client = Client::account('default');
        $client->connect();
        $inbox = $client->getFolder('INBOX');

        // Fetch only metadata, not full body
        $emails = $inbox->query()
            ->unseen()
            ->setFetchFlags(false)
            ->setFetchBody(false)
            ->setFetchFrom(true) // Ensure 'from' field is fetched
            ->limit(20)
            ->get();


        // Cache the fetched emails for fast loading
        Cache::put('inbox_emails', $emails, now()->addMinutes(5));
    }
}
