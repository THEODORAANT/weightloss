<?php

class PerchMailer_Contacts extends PerchMailer_Factory
{
    protected $table = 'mailer_contacts';
    protected $pk = 'contactID';
    protected $singular_classname = 'PerchMailer_Contact';

    protected $default_sort_column = 'contactLastName';
    protected $created_date_column = 'contactCreated';
    protected $updated_date_column = 'contactUpdated';

    public $static_fields = [
        'contactFirstName',
        'contactLastName',
        'contactEmail',
        'contactStatus',
        'memberID',
        'contactCreated',
        'contactUpdated',
    ];

    public function find_by_member_id($memberID)
    {
        return $this->get_one_by('memberID', (int) $memberID);
    }

    public function find_by_email($email)
    {
        return $this->get_one_by('contactEmail', $email);
    }

    public function sync_from_members()
    {
        $this->ensure_members_loaded();

        if (!class_exists('PerchMembers_Members')) {
            return 0;
        }

        $members_api = new PerchAPI(1.0, 'perch_members');
        $Members = new PerchMembers_Members($members_api);
        $members = $Members->all();
        $count = 0;

        if (PerchUtil::count($members)) {
            foreach ($members as $Member) {
                if ($this->sync_member_contact($Member)) {
                    $count++;
                }
            }
        }

        return $count;
    }

    public function sync_member_contact(PerchMembers_Member $Member)
    {
        $email = trim($Member->memberEmail());
        if ($email === '') {
            return false;
        }

        $properties = PerchUtil::json_safe_decode($Member->memberProperties(), true);
        if (!is_array($properties)) {
            $properties = [];
        }

        $data = [
            'contactFirstName'     => $properties['first_name'] ?? '',
            'contactLastName'      => $properties['last_name'] ?? '',
            'contactEmail'         => $email,
            'contactStatus'        => 'active',
            'memberID'             => $Member->id(),
            'contactDynamicFields' => PerchUtil::json_safe_encode($properties),
            'contactUpdated'       => date('Y-m-d H:i:s'),
        ];

        $Contact = $this->find_by_member_id($Member->id());
        if (!$Contact) {
            $Contact = $this->find_by_email($email);
        }

        if ($Contact) {
            return (bool) $Contact->update($data);
        }

        $data['contactCreated'] = date('Y-m-d H:i:s');
        return (bool) $this->create($data);
    }

    private function ensure_members_loaded()
    {
        if (!class_exists('PerchMembers_Member') || !class_exists('PerchMembers_Members')) {
            $member_path = PERCH_PATH . '/addons/apps/perch_members/PerchMembers_Members.class.php';
            if (file_exists($member_path)) {
                include_once $member_path;
            }
        }
    }
}
