<?php

    echo $HTML->title_panel([
        'heading' => $Lang->get($heading1),
    ], $CurrentUser);

    if ($message) echo $message;    
        
    echo $HTML->heading2('Member details');
    
    echo $Form->form_start(false);
        //echo $Form->text_field('memberEmail', 'Email', isset($details['memberEmail'])?$details['memberEmail']:false, 'l');


        echo $Form->fields_from_template($Template, $details, array(), false);


        if (!is_object($Member)) {
            echo $Form->text_field('memberPassword', 'Password', isset($details['memberPassword'])?$details['memberPassword']:false, 'l');
        }

        /*
        echo $Form->select_field('memberStatus', 'Status', array(
                array('label'=>$Lang->get('Pending'), 'value'=>'pending'),
                array('label'=>$Lang->get('Active'), 'value'=>'active'),
                array('label'=>$Lang->get('Inactive'), 'value'=>'inactive'),

            ), isset($details['memberStatus'])?$details['memberStatus']:'active');
    */

        $state = '0';
        if (isset($details['memberStatus']) && $details['memberStatus']=='pending') {
            $state = '1';
        }

        echo $Form->hint('Will only be sent to active members');
        echo $Form->checkbox_field('send_email', 'Send welcome email', '1', $state);


        if (is_object($Member)) {


        echo $HTML->heading2('Tags');
?>
    <div class="form-inner">
        <table class="tags">
            <thead>
                <tr>
                    <th class="action">Enabled</th>
                    <th>Tag</th>
                    <th>Expires</th>
                </tr>
            </thead>
            <tbody>
        <?php
            if (PerchUtil::count($tags)) {
                foreach($tags as $Tag) {
                    echo '<tr>';
                        echo '<td class="action">'.$Form->checkbox('tag-'.$Tag->id(), '1', '1').'</td>';
                        echo '<td>'.PerchUtil::html($Tag->tag()).'</td>';
                        echo '<td>'.PerchUtil::html($Tag->tagExpires() ? date('d M Y', strtotime($Tag->tagExpires())) : '-').'</td>';
                    echo '</tr>';
                }
            }

            echo '<tr>';
                echo '<td class="action">'.$Form->label('new-tag', PerchLang::get('New')).'</td>';
                echo '<td>'.$Form->text('new-tag', false).'</td>';
                echo '<td>'.$Form->checkbox('new-expire', '1', '0').' '.$Form->datepicker('new-expires', false).'</td>';
            echo '</tr>';

        ?>

            </tbody>
        </table>
    </div>

<?php

        echo $HTML->heading2('Documents');
?>

  <div class="form-inner">
        <table class="tags">
            <thead>
                <tr>
                    <th class="action">ID</th>
                    <th>Document Name</th>
                      <th>Document Type</th>
                    <th>Upload Date</th>
                     <th>Archived</th>
                     <th>Status</th>
                </tr>
            </thead>
            <tbody>
        <?php
            if (PerchUtil::count($documents)) {
            	$target_dir ="/perch/addons/apps/perch_members/documents/";


                foreach($documents as $Document) {
                    echo '<tr>';
                        echo '<td class="action">'.PerchUtil::html($Document->documentID()).'</td>';

                        echo '<td><a target="_blank" href="'.$target_dir.$Document->documentName().'">'.PerchUtil::html($Document->documentName()).'</a></td>';
                     echo '<td class="action">'.PerchUtil::html($Document->documentType()).'</td>';

                        echo '<td>'.PerchUtil::html($Document->documenUploadDate() ? date('d M Y', strtotime($Document->documenUploadDate())) : '-').'</td>';
                        echo '<td>'.PerchUtil::html($Document->documentDeleted() ? 'Archived' : 'Available').'</td>';

                          echo '<td>';

                         echo '<span>';
                         echo '<select id="'.PerchUtil::html($Document->documentID()).'" name="docstatus">';
                       if($Document->documentStatus()=="pending"){
                        echo '<option selected="selected" value="pending">Pending</option>';
                         }else{
                          echo '<option  value="pending">Pending</option>';
                         }
                          if($Document->documentStatus()=="accepted"){
                                 echo '<option  selected="selected" value="accepted">Accepted</option>';
                          }else{
                                echo '<option value="accepted">Accepted</option>';
                           }
                             if($Document->documentStatus()=="declined"){

                         echo '<option  selected="selected" value="declined">Declined</option>';
                          }else{
                         echo '<option value="declined">Declined</option>';
                           }
                                if($Document->documentStatus()=="rerequest"){
                        echo '<option selected="selected" value="rerequest">Rerequest</option>';
                        }else{
                           echo '<option  value="rerequest">Rerequest</option>';
                          }
                        echo '</select>';
                       /*  echo "<select id='".PerchUtil::html($Document->documentID())."' name='docstatus' >
                         <option "; if($Document->documentStatus()=="pending"){echo " selected ";} echo"value='pending'>Pending</option><option value='accepted'>Accepted</option><option value='declined'>Declined</option><option value='rerequest'>Rerequest</option></select>";
echo '<span id="result-select'.PerchUtil::html($Document->documentID()).'" class="result"></span>';*/

                    echo '</td></tr>';



                }

            }

           echo '<tr>';
                echo '<td class="action">'.$Form->label('new-document', PerchLang::get('New')).'</td>';
                  //   echo '<td>'.$Form->text('new-document', false).'</td>';
                echo '<td>'.$Form->multifile_field('new-document', false).'</td>';
               // echo '<td>'.$Form->checkbox('new-expire', '1', '0').' '.$Form->datepicker('new-expires', false).'</td>';
            echo '</tr>';

        ?>

            </tbody>
        </table>
    </div>

     <?php
       //Questionnaire
             echo $HTML->heading2('Orders');
             if( $Customer){
                     echo'<div class="form-inner">
                          <table class="tags">
                              <thead>
                                  <tr>

                                      <th>Order</th>
                                      <th>Date</th>
 <th>Total</th>
                                  </tr>
                              </thead>
                              <tbody>';
               $orders = $Orders->findAll_for_customer($Customer);
                if (PerchUtil::count($orders)) {
                 foreach($orders as $Order) {
                        echo '<tr><td><a target="_blank" href="/perch/addons/apps/perch_shop_orders/order/?id='.$Order->orderID().'" >'.$Order->orderInvoiceNumber().'</a></td><td>'.$Order->orderCreated().'</td><td>'.$Order->orderTotal().'</td></tr>';
                }
             }else{
               echo '<tr><td>No Orders</td></tr>';
             }
          }


     ?>
      </tbody>
             </table>
         </div>
     <?php
       //Questionnaire
             echo $HTML->heading2('Questionnaire');
     ?>

       <div class="form-inner">
             <table class="tags">
                 <thead>
                     <tr>

                         <th>Question</th>
                         <th>Answer</th>

                     </tr>
                 </thead>
                 <tbody>
             <?php

            $questions=$Questionnaires->get_questions();
            $answer_indicates_allergies = static function ($answerText) {
                $normalized = strtolower(trim((string)$answerText));

                return $normalized !== '' && strpos($normalized, 'yes') === 0;
            };
            $should_skip_question = static function($slug, $answers_by_slug) use ($answer_indicates_allergies) {
                if ($slug === 'allergy_details') {
                    if (!isset($answers_by_slug['allergies'])) {
                        return false;
                    }

                    $allergy_answer = $answers_by_slug['allergies']->answer_text();

                    if (!$answer_indicates_allergies($allergy_answer)) {
                        return true;
                    }
                }

                return false;
            };
             $bmi_edit_controls_needed = false;

                if (PerchUtil::count($questionnaire)) {
                    $answers_by_slug = [];

                    foreach ($questionnaire as $Questionnaire) {
                        $slug = $Questionnaire->question_slug();
                        if (array_key_exists($slug, $questions) && !isset($answers_by_slug[$slug])) {
                            $answers_by_slug[$slug] = $Questionnaire;
                        }
                    }

                    if (PerchUtil::count($answers_by_slug)) {
                        $historyPrinted = false;
                        foreach ($questions as $slug => $question_label) {
                            if (!isset($answers_by_slug[$slug])) {
                                continue;
                            }
                            if ($should_skip_question($slug, $answers_by_slug)) {
                                continue;
                            }
                            $Questionnaire = $answers_by_slug[$slug];
                            if (!$historyPrinted) {
                                echo '<tr><td colspan="2"><a class="button button button-simple" target="_blank" href="https://'.$_SERVER['HTTP_HOST'].'/perch/addons/apps/perch_members/questionnaire_logs?userId='.$Questionnaire->uuid().'">History</a></td></tr>';
                                $historyPrinted = true;
                            }
                            echo '<tr>';
                            echo '<td class="action">'.PerchUtil::html($question_label).'</td>';
                            echo '<td>';

                                $answerText = (string)$Questionnaire->answer_text();
                                $output = PerchUtil::html($answerText);

                                echo $output;

                            echo '</td>';
                            echo '</tr>';
                        }
                    }
                }



             ?>

                 </tbody>
             </table>
         </div>


     <?php
       //Questionnaire
             echo $HTML->heading2('Questionnaire for Re-order');
     ?>

       <div class="form-inner">
             <table class="tags">
                 <thead>
                     <tr>

                         <th>Question</th>
                         <th>Answer</th>

                     </tr>
                 </thead>
                 <tbody>
             <?php

             $questions=$Questionnaires->get_questions("re-order");

                if (PerchUtil::count($questionnaire_reorder)) {
                    $answers_by_slug = [];

                    foreach ($questionnaire_reorder as $Questionnaire) {
                        $slug = $Questionnaire->question_slug();
                        if (array_key_exists($slug, $questions) && !isset($answers_by_slug[$slug])) {
                            $answers_by_slug[$slug] = $Questionnaire;
                        }
                    }

                    if (PerchUtil::count($answers_by_slug)) {
                        $historyPrinted = false;
                        foreach ($questions as $slug => $question_label) {
                            if (!isset($answers_by_slug[$slug])) {
                                continue;
                            }
                            if ($should_skip_question($slug, $answers_by_slug)) {
                                continue;
                            }
                            $Questionnaire = $answers_by_slug[$slug];
                            if (!$historyPrinted) {
                                echo '<tr><td colspan="2"><a class="button button button-simple" target="_blank" href="https://getweightloss.co.uk/perch/addons/apps/perch_members/questionnaire_logs?userId='.$Questionnaire->uuid().'&type=re-order">History</a></td></tr>';
                                $historyPrinted = true;
                            }
                            echo '<tr>';
                            echo '<td class="action">'.PerchUtil::html($question_label).'</td>';
                            echo '<td>';

                                echo PerchUtil::html($Questionnaire->answer_text());

                            echo '</td>';
                            echo '</tr>';
                        }
                    }
                }



             ?>

                 </tbody>
             </table>
         </div>




    <?php echo $HTML->heading2('Notes'); ?>

<?php
    if (!isset($note_pharmacy_statuses) || !is_array($note_pharmacy_statuses)) {
        $note_pharmacy_statuses = [];

        if (isset($API) && is_object($API) && isset($Member) && is_object($Member)) {
            $NotePharmacyStatuses = new PerchMembers_NotePharmacyStatuses($API);
            $note_pharmacy_statuses = $NotePharmacyStatuses->get_indexed_for_member($Member->id());

            if (!is_array($note_pharmacy_statuses)) {
                $note_pharmacy_statuses = [];
            }
        }
    }
?>

           <div class="form-inner">
                  <table class="notes">
                      <thead>
                          <tr>

                              <th>Note</th>
                              <th>Date</th>
                                <th>Added by</th>
                                <th>Pharmacy status</th>
                                <th class="action">Action</th>
                          </tr>
                      </thead>
                      <tbody>
                  <?php
                      if (PerchUtil::count($notes)) {
                          foreach($notes as $Note) {
                              $noteID = (int) $Note->id();
                              $pharmacyStatus = isset($note_pharmacy_statuses[$noteID]) ? $note_pharmacy_statuses[$noteID] : false;
                              $statusLabel = '';
                              $statusClassSuffix = 'sent';
                              $statusMessage = '';
                              $statusSentAt = '';

                              if ($pharmacyStatus instanceof PerchMembers_NotePharmacyStatus) {
                                  $statusLabel = trim((string) $pharmacyStatus->status());
                                  if ($statusLabel === '') {
                                      $statusLabel = 'Sent';
                                  }

                                  $statusClassSuffix = strtolower(preg_replace('/[^a-z0-9]+/', '-', $statusLabel));
                                  $statusClassSuffix = trim($statusClassSuffix, '-');
                                  if ($statusClassSuffix === '') {
                                      $statusClassSuffix = 'sent';
                                  }

                                  $statusMessage = trim((string) $pharmacyStatus->message());

                                  $sentAt = $pharmacyStatus->sentAt();
                                  if ($sentAt) {
                                      $timestamp = strtotime($sentAt);
                                      if ($timestamp) {
                                          $statusSentAt = date('d M Y H:i', $timestamp);
                                      }
                                  }
                              }

                              echo '<tr>';

                                  echo '<td>'.PerchUtil::html($Note->note_text()).'</td>';
                                  echo '<td>'.PerchUtil::html($Note->noteDate() ? date('d M Y', strtotime($Note->noteDate())) : '-').'</td>';
                                    echo '<td>'.PerchUtil::html($Note->addedBy()).'</td>';
                                    echo '<td>';
                                    if ($pharmacyStatus instanceof PerchMembers_NotePharmacyStatus) {
                                        echo '<span class="pharmacy-status pharmacy-status-'.$statusClassSuffix.'">Status: '.PerchUtil::html($statusLabel).'</span>';
                                        if ($statusSentAt !== '') {
                                            echo '<div class="meta">Sent '.PerchUtil::html($statusSentAt).'</div>';
                                        }
                                        if ($statusMessage !== '') {
                                            echo '<div class="meta">'.PerchUtil::html($statusMessage).'</div>';
                                        }
                                    } else {
                                        echo '-';
                                    }
                                    echo '</td>';
                                    echo '<td class="action">';
                                    if (is_object($Member) && !($pharmacyStatus instanceof PerchMembers_NotePharmacyStatus)) {
                                        echo '<button type="submit" style="background-color:#199d19" class="button button-simple" name="send_note_to_pharmacy" value="'.(int)$Note->id().'">Send to pharmacy</button>';
                                    }
                                    echo '</td>';
                              echo '</tr>';
                          }
                      }

                      echo '<tr>';
                          echo '<td colspan="5" class="action">'.$Form->label('new-note', PerchLang::get('New'));
                          echo $Form->text('new-note', false).'</td>';

                      echo '</tr>';

                  ?>

                      </tbody>
                  </table>
              </div>
<?php

          echo $HTML->heading2('Notifications');

          ?>

           <div class="form-inner">
                  <table class="notes">
                      <thead>
                          <tr>
                              <th>Title</th>
                              <th>Message</th>
                              <th>Date</th>
                          </tr>
                      </thead>
                      <tbody>
                  <?php
                      if (PerchUtil::count($notifications)) {
                          foreach($notifications as $Notification) {
                              echo '<tr>';
                                  echo '<td>'.PerchUtil::html($Notification->notificationTitle()).'</td>';
                                  echo '<td>'.PerchUtil::html($Notification->notificationMessage()).'</td>';
                                  echo '<td>'.PerchUtil::html($Notification->notificationDate() ? date('d m Y', strtotime($Notification->notificationDate())) : '-').'</td>';
                              echo '</tr>';
                          }
                      }

                      echo '<tr>';
                          echo '<td>'.$Form->text('new-notification-title', false).'</td>';
                          echo '<td>'.$Form->text('new-notification-message', false).'</td>';
                          echo '<td></td>';
                      echo '</tr>';

                  ?>

                      </tbody>
                  </table>
              </div>

<?php
        }// is object Member

        echo $Form->submit_field('btnSubmit', 'Save', $API->app_path());
    
    echo $Form->form_end();
