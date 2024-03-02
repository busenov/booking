<?php
namespace booking\forms\AmoCRM;

use AmoCRM\Models\ContactModel;
use AmoCRM\Models\LeadModel;

class LeadForms implements LeadFormsInterface
{
    public ?int $contactId = null;

    public function getLead(): LeadModel
    {
        return new LeadModel();
    }
    public function getContact(): ContactModel
    {
        return new ContactModel();
    }
    public function setContactId(int $contactId):void
    {
        $this->contactId=$contactId;
    }

    public function getNotes()
    {
    }
}