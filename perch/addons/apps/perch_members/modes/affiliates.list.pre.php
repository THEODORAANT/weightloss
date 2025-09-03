<?php


    $HTML = $API->get('HTML');

    $Members = new PerchMembers_Members($API);
    $Affiliates = new PerchMembers_Affiliates($API);

    $Paging = $API->get('Paging');
    $Paging->set_per_page(40);



	$Lang = $API->get('Lang');

    $members = array();

    /****Filtering template****/
    $Template   = $API->get('Template');
    $Template->set('members/filter.html', 'members');

    $Form = $API->get('Form');
    $Form->handle_empty_block_generation($Template);

        if ($Form->submitted()) {

               $post = $_POST;


               //$postvars = array('memberEmail', 'memberStatus');

           	//$data = $Form->receive($postvars);

               $data = $Form->get_posted_content($Template, $Members, false, false);
               $filerdata= json_encode($data);
               // $details=$filerdata;

                $details=$data["memberProperties"];
                 $details =json_decode($details, TRUE);

               //print_r( $filerdata);
              $filter="memberProperties";
              $status = 'all';
               // PerchUtil::debug($data);
          }else{
             $details = array();

             $filter = '';
              $status = 'all';

          }
    //print_r( $details);

  $Form->set_required_fields_from_template($Template, $details);





    if (isset($_GET['affID']) && $_GET['affID'] != '') {
        $filter = 'affID';
        $affID = $_GET['affID'];
        $status = '';
    }




    if (isset($_GET['show-filter']) && $_GET['show-filter'] != '') {
        $status = '';
    }


 $sort="^affid";
if (isset($_GET['sort']) && $_GET['sort'] != '') {
        $sort = $_GET['sort'];

        }
    switch ($filter) {



        case 'affID':
            $members = $Affiliates->get_by_affID($affID);
            break;




        default:
            $members = $Affiliates->get_affiliates_listing($sort);


            break;
    }


