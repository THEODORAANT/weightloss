<?php

class PerchMembers_NotePharmacyStatus extends PerchAPI_Base
{
    protected $table = 'members_note_pharmacy_statuses';
    protected $pk    = 'statusID';

    public function memberID()
    {
        return isset($this->details['memberID']) ? (int) $this->details['memberID'] : null;
    }

    public function noteID()
    {
        return isset($this->details['noteID']) ? (int) $this->details['noteID'] : null;
    }

    public function status()
    {
        return isset($this->details['status']) ? $this->details['status'] : '';
    }

    public function message()
    {
        return isset($this->details['message']) ? $this->details['message'] : '';
    }

    public function sentAt()
    {
        return isset($this->details['sentAt']) ? $this->details['sentAt'] : null;
    }
}
