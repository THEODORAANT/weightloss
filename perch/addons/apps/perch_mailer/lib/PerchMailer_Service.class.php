<?php

class PerchMailer_Service
{
    private $api;
    private $settings;

    public function __construct(PerchAPI $API)
    {
        $this->api = $API;
        $this->settings = $API->get('Settings');
    }

    public function send_trigger($trigger_slug, $memberID = null, array $vars = [], $contactEmail = null)
    {
        $Triggers = new PerchMailer_Triggers($this->api);
        $Trigger = $Triggers->find_by_slug($trigger_slug);

        if (!$Trigger || !$Trigger->triggerActive()) {
            return false;
        }

        $Template = $Trigger->template();
        if (!$Template) {
            return false;
        }

        $Contacts = new PerchMailer_Contacts($this->api);
        $Contact = $this->resolve_contact($Contacts, $memberID, $contactEmail, $vars);

        if (!$Contact) {
            return false;
        }

        $email_vars = array_merge($Contact->to_template_array(), $vars);

        $subject = $Template->render_subject($email_vars);
        if ($subject === '') {
            $subject = $Template->templateTitle();
        }

        $html_body = $Template->render_html_body($email_vars);
        $plain_body = $Template->render_plain_body($email_vars);

        if ($html_body === '' && $plain_body !== '') {
            $html_body = nl2br($plain_body);
        }

        $from_name = trim($Template->templateFromName()) ?: $this->settings->get('perch_mailer_from_name')->val();
        $from_email = trim($Template->templateFromEmail()) ?: $this->settings->get('perch_mailer_from_email')->val();

        if (!$from_email && defined('PERCH_EMAIL_FROM')) {
            $from_email = PERCH_EMAIL_FROM;
        }

        if ($from_email === '') {
            return false;
        }

        $Email = new PerchMailer_Email('email/mailer.html', 'perch_mailer');
        $Email->template_method('dollar');
        $Email->set_bulk([
            'subject' => $subject,
            'body'    => $html_body,
        ]);

        if ($plain_body) {
            $Email->set('plaintext', $plain_body);
        }

        $Email->subject($subject);
        $Email->body($html_body);
        $Email->senderName($from_name);
        $Email->senderEmail($from_email);
        $Email->recipientEmail($Contact->contactEmail());
        $Email->recipientName($Contact->full_name());

        return $Email->send();
    }

    private function resolve_contact(PerchMailer_Contacts $Contacts, $memberID = null, $contactEmail = null, array $vars = [])
    {
        if ($memberID) {
            $Contact = $Contacts->find_by_member_id($memberID);
            if ($Contact) {
                return $Contact;
            }

            $Member = $this->fetch_member($memberID);
            if ($Member) {
                $Contacts->sync_member_contact($Member);
                $Contact = $Contacts->find_by_member_id($memberID);
                if ($Contact) {
                    return $Contact;
                }
            }
        }

        if ($contactEmail) {
            $Contact = $Contacts->find_by_email($contactEmail);
            if ($Contact) {
                return $Contact;
            }

            $Contact = $this->create_contact_from_email($Contacts, $contactEmail, $vars);
            if ($Contact) {
                return $Contact;
            }
        }

        return false;
    }

    private function fetch_member($memberID)
    {
        if (!class_exists('PerchMembers_Members')) {
            $member_path = PERCH_PATH . '/addons/apps/perch_members/PerchMembers_Members.class.php';
            if (file_exists($member_path)) {
                include_once $member_path;
            }
        }

        if (!class_exists('PerchMembers_Members')) {
            return false;
        }

        $members_api = new PerchAPI(1.0, 'perch_members');
        $Members = new PerchMembers_Members($members_api);

        return $Members->find((int) $memberID);
    }

    private function create_contact_from_email(PerchMailer_Contacts $Contacts, $email, array $vars = [])
    {
        $email = trim($email);
        if ($email === '') {
            return false;
        }

        $data = [
            'contactEmail'   => $email,
            'contactStatus'  => 'active',
            'contactUpdated' => date('Y-m-d H:i:s'),
            'contactCreated' => date('Y-m-d H:i:s'),
        ];

        if (isset($vars['first_name'])) {
            $data['contactFirstName'] = $vars['first_name'];
        }

        if (isset($vars['last_name'])) {
            $data['contactLastName'] = $vars['last_name'];
        }

        return $Contacts->create($data);
    }
}
