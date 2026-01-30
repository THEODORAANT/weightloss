<?php

class PerchMembers_Documents extends PerchAPI_Factory
{
    protected $table     = 'members_documents';
	protected $pk        = 'documentID';
	protected $singular_classname = 'PerchMembers_Document';

	protected $default_sort_column = 'date';
	public $static_fields = array('documentID', 'memberID', 'documentName', 'documentType','documentStatus', 'documenUploadDate');
    public     $default_fields = '
    				<perch:members type="file" id="documentFile" required="true" label="Email" listing="true" order="98" />
                    <perch:members type="date" id="documenUploadDate" required="true" label="Joined" listing="true" format="d F Y" order="99" />
                    ';

   	public function get_document($memberID,$documentID)
       {
           $sql = 'SELECT d.*
                   FROM  '.PERCH_DB_PREFIX.'members_documents d
                   WHERE d.documentID='.$this->db->pdb((int)$documentID).' AND d.memberID='.$this->db->pdb((int)$memberID).'
                   LIMIT 1';

           return $this->db->get_row($sql);
       }

        public function update_document_status($documentID, $status, $note = '')
       {
       $Document = $this->find((int)$documentID);
       if (!$Document) {
           return false;
       }

       $previousStatus = $Document->documentStatus();

       $updatedata['documentStatus']=$status;
       $r = $this->db->update($this->table, $updatedata, $this->pk, $documentID );

       if ($r && $status === 'rerequest' && $previousStatus !== 'rerequest') {
           $this->send_document_rerequest_email($Document, $note);
       }

       return $r;
       }

        public function delete_document($documentID)
        {
       $Document = $this->find((int)$documentID);

       if (!$Document) {
           return [
               'success' => false,
               'message' => 'Document not found.',
           ];
       }

       $file_name = trim((string) $Document->documentName());
       $file_deleted = true;

       if ($file_name !== '') {
           $file_path = PerchUtil::file_path(__DIR__.'/documents/'.$file_name);

           if ($file_path && file_exists($file_path) && is_file($file_path)) {
               if (!@unlink($file_path)) {
                   $file_deleted = false;
               }
           }
       }

       if ($Document->delete()) {
           $message = $file_deleted
               ? 'Document deleted.'
               : 'Document deleted but the file could not be removed from the server.';

           return [
               'success' => true,
               'message' => $message,
           ];
       }

       return [
           'success' => false,
           'message' => 'Unable to delete document.',
       ];
        }

       protected function send_document_rerequest_email(PerchMembers_Document $Document, $note = '')
       {
       if (!$this->api) {
           return;
       }

       $Members = new PerchMembers_Members($this->api);
       $Member  = $Members->find((int)$Document->memberID());

       if (!$Member) {
           return;
       }

       $memberEmail = trim((string)$Member->memberEmail());
       if ($memberEmail === '' || !PerchUtil::is_valid_email($memberEmail)) {
           return;
       }

       $properties = PerchUtil::json_safe_decode($Member->memberProperties(), true);
       if (!is_array($properties)) {
           $properties = [];
       }

       $firstName = isset($properties['first_name']) ? trim((string)$properties['first_name']) : '';
       $lastName  = isset($properties['last_name']) ? trim((string)$properties['last_name']) : '';

       $loginPage = '';
       $Settings = $this->api->get('Settings');
       if ($Settings) {
           $loginSetting = $Settings->get('perch_members_login_page');
           if ($loginSetting) {
               $loginPage = str_replace('{returnURL}', '', (string)$loginSetting->val());
           }
       }

       $uploadDate = $Document->documenUploadDate();
       $formattedUploadDate = '';
       if ($uploadDate) {
           $timestamp = strtotime($uploadDate);
           if ($timestamp) {
               $formattedUploadDate = date('d M Y', $timestamp);
           }
       }

       $emailData = [
           'first_name'            => $firstName,
           'last_name'             => $lastName,
           'memberEmail'           => $memberEmail,
           'document_name'         => $Document->documentName(),
           'document_type'         => $Document->documentType(),
           'document_upload_date'  => $formattedUploadDate,
           'login_page'            => $loginPage,
       ];

       if (trim((string)$note) !== '') {
           $emailData['document_rejection_reason'] = $note;
       }

       $Email = $this->api->get('Email');
       $Email->set_template('members/emails/document_rerequest_notification.html');
       $Email->set_bulk($emailData);
       $Email->subject('Action required: document needs updating');
       $Email->senderName(PERCH_EMAIL_FROM_NAME);
       $Email->senderEmail(PERCH_EMAIL_FROM);
       $Email->recipientEmail($memberEmail);

       if (!$Email->send()) {
           PerchUtil::debug('Failed to send document re-request email for member '.$Member->id().': '.$Email->errors, 'error');
       }
       }
    public function delete_passed_files(){
        $sql = 'SELECT d.*
                    FROM  '.PERCH_DB_PREFIX.'members_documents d
                    WHERE DATEDIFF( CURDATE(),  d.documenUploadDate ) > 15 AND documentDeleted="0"
                    ORDER BY d.documenUploadDate';

        $rows = $this->db->get_rows($sql);

        $target_dir = __DIR__."/documents/";
        if (PerchUtil::count($rows)) {
          foreach($rows as $row) {
          $filename= $target_dir.$row['documentName'];
          if (!unlink($filename)) {
               $log  ="$filename cannot be deleted due to an error";
           }
           else {
                $log  ="$filename has been deleted";
            }
              $log  .= implode("-",$row);

               $doc = array(
                  'documentDeleted'=>'1'
                );
                $updatedata=$row;
                $pk =$row['documentID'];
                //echo  $pk;
                $updatedata[ 'documentDeleted']='1';
                $r = $this->db->update($this->table, $updatedata, $this->pk, $pk );
               // print_r( $r);


            $log_filename = $_SERVER['DOCUMENT_ROOT']."/logs";
            if (!file_exists($log_filename))
            {
                // create directory/folder uploads.
               mkdir($log_filename, 0777, true);
             }
              $log_file_data = $log_filename.'/log_' . date('d-M-Y') . '.log';
               file_put_contents( $log_file_data , $log, FILE_APPEND);
           }
        }
        return true;
    }

    public function get_for_member($memberID)
    {
        $sql = 'SELECT d.*
                FROM  '.PERCH_DB_PREFIX.'members_documents d
                WHERE d.memberID='.$this->db->pdb((int)$memberID).'
                ORDER BY d.documenUploadDate DESC';

        return $this->return_instances($this->db->get_rows($sql));
    }

        public function get_pending_for_members($memberIDs)
    {
        if (!PerchUtil::count($memberIDs)) {
            return [];
        }

        $ids = array_map('intval', $memberIDs);
        $id_list = $this->db->implode_for_sql_in($ids, true);
        $pending = $this->db->pdb('pending');

        $sql = 'SELECT d.*
                FROM  '.PERCH_DB_PREFIX.'members_documents d
                WHERE d.memberID IN ('.$id_list.')
                  AND (d.documentStatus IS NULL OR d.documentStatus='.$pending.')
                  AND COALESCE(d.documentDeleted, \'0\') != \'1\'
                ORDER BY d.memberID, d.documenUploadDate DESC';

        $rows = $this->db->get_rows($sql);

        if (!PerchUtil::count($rows)) {
            return [];
        }

        $documents = $this->return_instances($rows);
        $grouped = [];

        foreach ($documents as $Document) {
            $grouped[$Document->memberID()][] = $Document;
        }

        return $grouped;
    }

        public function count_by_status($status='pending')
    {
        $status_sql = $this->db->pdb($status);

        $sql = 'SELECT COUNT(*)
                FROM '.$this->table.'
                WHERE (documentStatus IS NULL OR documentStatus='.$status_sql.')
                  AND COALESCE(documentDeleted, \'0\') != \'1\'';

        return (int)$this->db->get_value($sql);
    }

        public function upload($file,$memberID,$documentType='documents')
        {

        $target_dir = __DIR__."/documents/";
      	$filef =  $file['name'];
      	$path = pathinfo($filef);
      	$filename = $path['filename'];
      	$ext = $path['extension'];
      	$temp_name = $file['tmp_name'];
      	$path_filename_ext = $target_dir.$filename.".".$ext;
      	 $newName = time() . '_' . uniqid() . '.' . $ext;
        $destination = $target_dir . $newName;

      // Check if file already exists
      if (file_exists($path_filename_ext)) {
       echo "Sorry, file already exists.";
       }else{
       move_uploaded_file($temp_name,$destination);
       //echo "Congratulations! File Uploaded Successfully.";
       //echo  $HTML->success_message('Congratulations! File Uploaded Successfully');
       }
		// Tag wasn't found, so create a new one and return it.
        //'documentID', 'memberID', 'documentName', 'documenUploadDate');
		$data = array();
		$data['memberID'] =$memberID;
		$data['documentType']=$documentType;
		$data['documentName'] =$newName;
		$data['documenUploadDate'] = date('Y-m-d H:i:s');
        $this->create($data);
		}


}
