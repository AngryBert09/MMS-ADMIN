<?php

namespace App\Http\Controllers;

use Webklex\IMAP\Facades\Client;
use Illuminate\Http\Request;
use App\Jobs\FetchEmailsJob;
use Illuminate\Support\Facades\Cache;

use Illuminate\Support\Facades\Mail;

class EmailController extends Controller
{


    public function fetchEmails()
    {
        // Dispatch async job to fetch emails
        FetchEmailsJob::dispatch();

        // Return cached emails (or empty if first time)
        $emails = Cache::get('inbox_emails', []);

        return view('admin.applications.inbox', compact('emails'));
    }



    public function show($uid)
    {
        return Cache::remember("email_$uid", 60, function () use ($uid) {
            $client = Client::account('default');
            if (!$client->isConnected()) {
                $client->connect();
            }

            $folder = $client->getFolder('INBOX');
            $email = $folder->query()->whereUid($uid)->setFetchFlags(false)->setFetchBody(false)->get()->first();

            if (!$email) {
                return response()->json(['error' => 'Email not found'], 404);
            }

            return [
                'subject' => $email->subject,
                'from' => $email->from[0]->mail,
                'body' => strip_tags($email->getHTMLBody() ?: $email->getTextBody()),
            ];
        });
    }


    public function sendEmail(Request $request)
    {
        Mail::raw($request->message, function ($message) use ($request) {
            $message->to($request->to)
                ->subject($request->subject);
        });

        return back()->with('success', 'Email sent successfully!');
    }
}
