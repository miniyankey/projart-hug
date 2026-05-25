<?php

namespace App\Enums;

enum FormSubmissionType: string
{
    case Contact = 'contact';
    case TropheeParticipation = 'trophee_participation';
    case CollectRequest = 'collect_request';
}
