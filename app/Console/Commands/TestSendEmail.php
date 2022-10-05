<?php

namespace App\Console\Commands;

use App\Mail\TestEmail;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class TestSendEmail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:send-email';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Use this command to test if email is sending / delivering, in production.';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {

        $testEmails = env('TEST_EMAIL_DESTINATION', null);

        if (is_null($testEmails)) {
            die(PHP_EOL . PHP_EOL .  'Must set TEST_EMAIL_DESTINATION env variable. (Emails comma separated)' . PHP_EOL . PHP_EOL);
        }

        Mail::to(
            $testEmails
        )->queue(new TestEmail());

        return Command::SUCCESS;
    }
}
