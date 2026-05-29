<?php

namespace App\Enums;

enum FormSubmissionType: string
{
    case Contact = 'contact';
    case CollectRequest = 'collect_request';
}
