<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <title>Client</title>
        <link rel="shortcut icon" href="/asset/favicon.png" type="image/x-icon">
        <link rel="preconnect" href="https://fonts.googleapis.com" />
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin /><link
          href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;700&family=Poppins:wght@700;800&display=swap"rel="stylesheet"/>
        <link rel="stylesheet" href="/css/bootstrap.min.css" />
        <link rel="stylesheet" href="/css/custom-font.css" />
        <link href="https://cdnjs.cloudflare.com/ajax/libs/flag-icon-css/3.5.0/css/flag-icon.min.css" rel="stylesheet" />
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
        <link rel="stylesheet" href="/dist/style.css">
        <link rel="stylesheet" href="/css/style.css">
        <link rel="stylesheet" href="/css/style-2.css">
        <link rel="stylesheet" href="/css/due.css">
      </head>
<body>


    <!-- ==================================================================coding Start====================================================================================================== -->

        <!--//////////////////==============Header section section START=========///////////////-->
        <div class="getweightloss_header">
            <div class="main_code px-5">
                <div class="back_btn">
                    <a href="/home"><i class="fa-solid fa-angle-left text-light"></i> Back</a>
                </div>
                <div class="getweightloss_logo">
                    <img style="height: 300px; width: 300px;" src="/asset/logo-final.png" alt="logo">
                </div>
            </div>
        </div>
        <!--//////////////////==============Header section section END=========///////////////-->
   <style>


        .subheader {
          background-color: #fff;
          border-bottom: 1px solid #ddd;
        }

        .welcome-msg {
          padding: 12px 20px;
          font-size: 16px;
          color: #333;
          border-bottom: 1px solid #eee;
        }

        .tabs {
          display: flex;
          padding: 0 20px;
          background-color: #f9f9f9;
        }

        .tab {
          padding: 12px 16px;
          margin-right: 10px;
          text-decoration: none;
          color: #555;
          border-bottom: 3px solid transparent;
          transition: all 0.2s ease;
          font-weight: 500;
        }

        .tab:hover {
          color: #000;
          border-color: #007bff;
        }

        .tab.active {
          color: #007bff;
          border-color: #007bff;
          background-color: #fff;
        }
        .unread-dot {
          display: inline-block;
          width: 8px;
          height: 8px;
          background: #dc3545;
          border-radius: 50%;
          margin-left: 4px;
        }
      </style>
         <?php if (perch_member_logged_in()) { ?>
   <div class="subheader">

     <div class="welcome-msg">
       Hello, <strong><?php echo perch_member_get('first_name'); ?></strong>
     </div>
    <?php $currentUrl =  $_SERVER['REQUEST_URI'];

     $parts = explode('/', $currentUrl);
     $lastPart = end($parts);
     // echo  $lastPart;
      $profile_tab="";
      $orders_tab="";
      $reorder_tab="";
       $documents_tab="";
        $affiliate_tab="";
        $notifications_tab="";
        $chat_tab="";
        $chat_unread=false;
        $unread_count=0;
        $member_notifications = perch_member_notifications();
        if ($member_notifications) {
            foreach ($member_notifications as $n) {
                if (!$n['read']) $unread_count++;
            }
        }
        try {
            $chatRepo = new PerchMembers_ChatRepository();
            if ($chatRepo->tables_ready()) {
                $chat_unread = $chatRepo->member_has_unread(perch_member_get('memberID'));
            }
        } catch (Throwable $chatException) {
            $chat_unread = false;
        }
  if($lastPart=="client"){
  $profile_tab="active";
  }else if( $lastPart=="orders" ){
   $orders_tab="active";
  }else if( $lastPart=="re-order"){
      $reorder_tab="active";
     }else if($lastPart=="success" ){
           $documents_tab="active";
           }else if($lastPart=="affiliate-dashboard" ){

           $affiliate_tab="active";
           }else if($lastPart=="notifications" ){
            $notifications_tab="active";
           }else if($lastPart=="chat" ){
            $chat_tab="active";
           }
      ?>
     <div class="tabs">
       <a href="/client" class="tab <?php echo $profile_tab; ?>">Profile</a>
                     <a href="/payment/success" class="tab <?php echo $documents_tab; ?>">Documents</a>

       <a href="/client/orders" class="tab <?php echo $orders_tab; ?>">Orders</a>
       <a href="/client/notifications" class="tab <?php echo $notifications_tab; ?>">Notifications<?php if($unread_count){?><span class="unread-dot"></span><?php } ?></a>
       <a href="/client/chat" class="tab <?php echo $chat_tab; ?>">Chat<?php if($chat_unread){?><span class="unread-dot"></span><?php } ?></a>
       <a href="/client/affiliate-dashboard" class="tab <?php echo $affiliate_tab; ?>">Affiliate</a>
       <a href="/order/re-order" class="tab <?php echo $reorder_tab; ?>">Order</a>
       <a href="/client/logout" class="tab ">Logout</a>
     </div>


   </div>
<?php  } ?>
