<?php

class PerchMembers_Notifications extends PerchAPI_Factory
{
    protected $table               = 'members_notifications';
    protected $pk                  = 'notificationID';
    protected $singular_classname  = 'PerchMembers_Notification';
    protected $default_sort_column = 'notificationDate';
    public $static_fields = ['notificationID','memberID','notificationTitle','notificationMessage','notificationDate','notificationRead'];

    public function get_for_member($memberID)
    {
        $sql = 'SELECT n.*
                FROM '.PERCH_DB_PREFIX.'members_notifications n
                WHERE n.memberID='.$this->db->pdb((int)$memberID).'
                ORDER BY n.notificationDate DESC';

        return $this->return_instances($this->db->get_rows($sql));
    }
}

