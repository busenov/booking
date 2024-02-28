<?php


namespace booking\forms\AmoCRM;

use AmoCRM\Models\ContactModel;
use AmoCRM\Models\LeadModel;

interface LeadFormsInterface
{
    public function getLead(): LeadModel;
    public function getContact(): ContactModel;
    public function setContactId(int $contactId):void;
}