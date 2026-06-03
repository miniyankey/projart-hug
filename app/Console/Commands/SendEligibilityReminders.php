<?php

namespace App\Console\Commands;

use App\Mail\EligibilityReminderMail;
use App\Models\EligibilityReminder;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class SendEligibilityReminders extends Command
{
    /**
     * @var string
     */
    protected $signature = 'app:send-eligibility-reminders';

    /**
     * @var string
     */
    protected $description = 'Envoie les rappels aux visiteurs redevenus éligibles au don du sang.';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $reminders = EligibilityReminder::query()
            ->whereNull('sent_at')
            ->whereDate('eligible_at', '<=', today())
            ->with('collect.company')
            ->get();

        foreach ($reminders as $reminder) {
            Mail::to($reminder->email)->send(new EligibilityReminderMail($reminder));
            $reminder->update(['sent_at' => now()]);
        }

        $this->info("Rappels d'éligibilité envoyés : {$reminders->count()}.");

        return self::SUCCESS;
    }
}
