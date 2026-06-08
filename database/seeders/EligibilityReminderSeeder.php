<?php

namespace Database\Seeders;

use App\Models\Collect;
use App\Models\EligibilityReminder;
use Illuminate\Database\Seeder;

/**
 * Rappels d'éligibilité issus du jeu : un visiteur temporairement inéligible
 * (vaccin, voyage, tatouage...) laisse son email pour être prévenu dès qu'il
 * pourra donner. Quelques rappels sont encore en attente (sent_at null), d'autres
 * déjà envoyés, pour alimenter l'écran admin et la démo.
 */
class EligibilityReminderSeeder extends Seeder
{
    public function run(): void
    {
        // Rattache une partie des rappels à une collecte encore ouverte.
        $collectIds = Collect::ongoing()->pluck('id');

        $reminders = [
            // En attente : redeviendra éligible bientôt.
            ['email' => 'lucas.morel@example.com', 'locale' => 'fr', 'eligible_in_days' => 12, 'sent' => false],
            ['email' => 'emma.schneider@example.com', 'locale' => 'fr', 'eligible_in_days' => 28, 'sent' => false],
            ['email' => 'james.taylor@example.com', 'locale' => 'en', 'eligible_in_days' => 5, 'sent' => false],
            // Déjà envoyés : la date d'éligibilité est passée et le mail est parti.
            ['email' => 'chloe.dubois@example.com', 'locale' => 'fr', 'eligible_in_days' => -3, 'sent' => true],
            ['email' => 'noah.fischer@example.com', 'locale' => 'fr', 'eligible_in_days' => -14, 'sent' => true],
            ['email' => 'olivia.rossi@example.com', 'locale' => 'en', 'eligible_in_days' => -20, 'sent' => true],
        ];

        foreach ($reminders as $index => $data) {
            EligibilityReminder::create([
                'email' => $data['email'],
                'locale' => $data['locale'],
                // ~la moitié vient d'une collecte précise, le reste du jeu public.
                'collect_id' => $collectIds->isNotEmpty() && $index % 2 === 0
                    ? $collectIds[$index % $collectIds->count()]
                    : null,
                'eligible_at' => now()->addDays($data['eligible_in_days'])->toDateString(),
                'sent_at' => $data['sent'] ? now()->addDays($data['eligible_in_days']) : null,
            ]);
        }
    }
}
