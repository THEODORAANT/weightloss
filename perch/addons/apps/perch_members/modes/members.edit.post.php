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
                        echo '<td>'.PerchUtil::html($Tag->tagExpires() ? date('d b Y', strtotime($Tag->tagExpires())) : '-').'</td>';
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
     <?php /*
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
                            $Questionnaire = $answers_by_slug[$slug];
                            if (!$historyPrinted) {
                                echo '<tr><td colspan="2"><a class="button button button-simple" target="_blank" href="https://'.$_SERVER['HTTP_HOST'].'/perch/addons/apps/perch_members/questionnaire_logs?userId='.$Questionnaire->uuid().'">History</a></td></tr>';
                                $historyPrinted = true;
                            }
                            echo '<tr>';
                            echo '<td class="action">'.PerchUtil::html($question_label).'</td>';
                            echo '<td>';
                            if ($slug === 'bmi') {
                                $bmiValue = $Questionnaire->answer_text();
                                if ($bmiValue === null || $bmiValue === '') {
                                    $entryDetails = $Questionnaire->to_array();
                                    if (is_array($entryDetails) && isset($entryDetails['answer']) && $entryDetails['answer'] !== '') {
                                        $bmiValue = $entryDetails['answer'];
                                    }
                                }

                                $trimmedBmiValue = trim((string) $bmiValue);
                                $inputID = 'bmi-input-'.$Questionnaire->id();
                                $inputName = 'questionnaire_bmi['.$Questionnaire->id().']';

                                $numericBmiValue = '';
                                if ($trimmedBmiValue !== '' && preg_match('/-?\d+(?:\.\d+)?/', $trimmedBmiValue, $matches)) {
                                    $numericBmiValue = $matches[0];
                                }

                                $baseAttributes = 'step="0.1" min="0"';

                                if ($trimmedBmiValue === '') {
                                    echo $Form->text($inputName, $numericBmiValue, 'input-simple', false, 'number', $baseAttributes);
                                } else {
                                    echo '<div class="bmi-field">';
                                    echo '<span class="js-bmi-display">'.PerchUtil::html($trimmedBmiValue).'</span>';
                                    echo ' <button type="button" class="button button-simple js-bmi-edit" data-input-id="'.$inputID.'">Edit</button>';
                                    echo '</div>';
                                    echo $Form->text($inputName, $numericBmiValue, 'input-simple', false, 'number', $baseAttributes.' id="'.$inputID.'" style="display:none;"');
                                    $bmi_edit_controls_needed = true;
                                }
                            } else {
                                echo PerchUtil::html($Questionnaire->answer_text());
                            }
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
                            $Questionnaire = $answers_by_slug[$slug];
                            if (!$historyPrinted) {
                                echo '<tr><td colspan="2"><a class="button button button-simple" target="_blank" href="https://getweightloss.co.uk/perch/addons/apps/perch_members/questionnaire_logs?userId='.$Questionnaire->uuid().'&type=re-order">History</a></td></tr>';
                                $historyPrinted = true;
                            }
                            echo '<tr>';
                            echo '<td class="action">'.PerchUtil::html($question_label).'</td>';
                            echo '<td>';
                            if ($slug === 'bmi') {
                                $bmiValue = $Questionnaire->answer_text();
                                if ($bmiValue === null || $bmiValue === '') {
                                    $entryDetails = $Questionnaire->to_array();
                                    if (is_array($entryDetails) && isset($entryDetails['answer']) && $entryDetails['answer'] !== '') {
                                        $bmiValue = $entryDetails['answer'];
                                    }
                                }

                                $trimmedBmiValue = trim((string) $bmiValue);
                                $inputID = 'bmi-input-'.$Questionnaire->id();
                                $inputName = 'questionnaire_bmi['.$Questionnaire->id().']';

                                $numericBmiValue = '';
                                if ($trimmedBmiValue !== '' && preg_match('/-?\d+(?:\.\d+)?/', $trimmedBmiValue, $matches)) {
                                    $numericBmiValue = $matches[0];
                                }

                                $baseAttributes = 'step="0.1" min="0"';

                                if ($trimmedBmiValue === '') {
                                    echo $Form->text($inputName, $numericBmiValue, 'input-simple', false, 'number', $baseAttributes);
                                } else {
                                    echo '<div class="bmi-field">';
                                    echo '<span class="js-bmi-display">'.PerchUtil::html($trimmedBmiValue).'</span>';
                                    echo ' <button type="button" class="button button-simple js-bmi-edit" data-input-id="'.$inputID.'">Edit</button>';
                                    echo '</div>';
                                    echo $Form->text($inputName, $numericBmiValue, 'input-simple', false, 'number', $baseAttributes.' id="'.$inputID.'" style="display:none;"');
                                    $bmi_edit_controls_needed = true;
                                }
                            } else {
                                echo PerchUtil::html($Questionnaire->answer_text());
                            }
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
        if (!empty($bmi_edit_controls_needed)) {
            echo '<script>(function(){var buttons=document.querySelectorAll(".js-bmi-edit");if(!buttons.length){return;}Array.prototype.forEach.call(buttons,function(button){button.addEventListener("click",function(){var inputId=button.getAttribute("data-input-id");if(!inputId){return;}var input=document.getElementById(inputId);if(!input){return;}var container=button.parentNode;while(container&&!(container.className&&container.className.indexOf("bmi-field")!==-1)){container=container.parentNode;}if(container){var display=container.querySelector(".js-bmi-display");if(display){display.style.display="none";}}button.style.display="none";input.style.display="";input.focus();});});})();</script>';
        }
*/
    ?>


    <?php echo $HTML->heading2('Notes'); ?>

           <div class="form-inner">
                  <table class="notes">
                      <thead>
                          <tr>

                              <th>Note</th>
                              <th>Date</th>
                                <th>Added by</th>
                          </tr>
                      </thead>
                      <tbody>
                  <?php
                      if (PerchUtil::count($notes)) {
                          foreach($notes as $Note) {
                              echo '<tr>';

                                  echo '<td>'.PerchUtil::html($Note->note()).'</td>';
                                  echo '<td>'.PerchUtil::html($Note->noteDate() ? date('d b Y', strtotime($Note->noteDate())) : '-').'</td>';
                                    echo '<td>'.PerchUtil::html($Note->addedBy()).'</td>';
                              echo '</tr>';
                          }
                      }

                      echo '<tr>';
                          echo '<td colspan="3" class="action">'.$Form->label('new-note', PerchLang::get('New'));
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
