<?php

namespace App\Services;

use Brevo\Brevo;
use Brevo\Contacts\Requests\CreateContactRequest;
use Brevo\Contacts\Requests\RemoveContactFromListRequest;
use Brevo\Contacts\Types\RemoveContactFromListRequestBodyEmails;

class BrevoService
{
    private Brevo $client;

    private int $listId;

    public function __construct()
    {
        $this->client = new Brevo(apiKey: config('services.brevo.key'));
        $this->listId = config('services.brevo.list_id');
    }

    /**
     * Ajoute un contact à la liste newsletter Brevo.
     */
    public function subscribe(string $email, string $locale = 'fr'): void
    {
        $this->client->contacts->createContact(
            new CreateContactRequest([
                'email' => $email,
                'listIds' => [$this->listId],   // liste Brevo cible
                'updateEnabled' => true,         // si le contact existe déjà, on l'ajoute à la liste sans erreur
                'attributes' => [
                    'LOCALE' => $locale,         // permet de filtrer par langue lors d'une campagne
                ],
            ]),
        );
    }

    /**
     * Retire un contact de la liste newsletter Brevo.
     * Le contact n'est pas supprimé globalement : il peut toujours recevoir les mails transactionnels.
     */
    public function unsubscribe(string $email): void
    {
        $this->client->contacts->removeContactFromList(
            $this->listId,
            new RemoveContactFromListRequest([
                'body' => new RemoveContactFromListRequestBodyEmails([
                    'emails' => [$email],
                ]),
            ]),
        );
    }
}
