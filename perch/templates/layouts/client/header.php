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
        <header class="client-topbar">
            <div class="client-topbar__inner">
                <a class="client-back" href="/home">
                    <span class="client-back__icon" aria-hidden="true"><i class="fa-solid fa-angle-left"></i></span>
                    <span class="client-back__label">Back</span>
                </a>
                <a class="client-brand" href="/client">
                    <img src="/asset/logo-final.png" alt="GetWeightLoss" />
                </a>
            </div>
        </header>
        <!--//////////////////==============Header section section END=========///////////////-->
   <style>


        .client-topbar {
          background: linear-gradient(90deg, #201c78 0%, #4133d4 100%);
          padding: 14px 0;
          box-shadow: 0 6px 16px rgba(29, 33, 67, 0.25);
          position: sticky;
          top: 0;
          z-index: 1030;
        }

        .client-topbar__inner {
          max-width: 1200px;
          margin: 0 auto;
          padding: 0 24px;
          display: flex;
          justify-content: space-between;
          align-items: center;
          gap: 24px;
        }

        .client-back {
          display: inline-flex;
          align-items: center;
          gap: 10px;
          color: #f0f4ff;
          text-decoration: none;
          font-weight: 600;
          letter-spacing: 0.02em;
          transition: transform 0.2s ease, color 0.2s ease;
        }

        .client-back__icon {
          display: inline-flex;
          align-items: center;
          justify-content: center;
          width: 36px;
          height: 36px;
          border-radius: 999px;
          background: rgba(255, 255, 255, 0.16);
        }

        .client-back:hover {
          color: #ffffff;
          transform: translateX(-2px);
        }

        .client-brand img {
          height: 70px;
          width: auto;
          display: block;
          filter: drop-shadow(0 8px 18px rgba(0, 0, 0, 0.15));
        }

        .client-nav {
          background-color: #ffffff;
          border-bottom: 1px solid rgba(15, 23, 42, 0.08);
          box-shadow: 0 12px 24px rgba(15, 23, 42, 0.04);
        }

        .client-nav__inner {
          max-width: 1200px;
          margin: 0 auto;
          padding: 18px 24px;
          display: flex;
          flex-wrap: wrap;
          justify-content: space-between;
          align-items: center;
          gap: 18px;
        }

        .client-greeting {
          font-size: 1rem;
          color: #1f2937;
          font-weight: 500;
        }

        .client-tabs {
          list-style: none;
          display: flex;
          flex-wrap: wrap;
          gap: 10px;
          margin: 0;
          padding: 0;
        }

        .client-tab-link {
          display: inline-flex;
          align-items: center;
          gap: 8px;
          padding: 10px 18px;
          border-radius: 999px;
          text-decoration: none;
          color: #374151;
          font-weight: 500;
          background-color: rgba(67, 56, 202, 0.06);
          border: 1px solid transparent;
          transition: all 0.2s ease;
        }

        .client-tab-link:hover {
          color: #1f2937;
          background-color: rgba(67, 56, 202, 0.15);
          border-color: rgba(79, 70, 229, 0.25);
          box-shadow: 0 6px 12px rgba(37, 99, 235, 0.12);
        }

        .client-tab-link.is-active {
          background: linear-gradient(90deg, #4338ca 0%, #6366f1 100%);
          color: #ffffff;
          border-color: transparent;
          box-shadow: 0 10px 20px rgba(79, 70, 229, 0.25);
        }

        .unread-dot {
          display: inline-flex;
          width: 9px;
          height: 9px;
          border-radius: 50%;
          background: #ef4444;
        }

        @media (max-width: 768px) {
          .client-topbar__inner {
            flex-direction: column;
            align-items: flex-start;
          }

          .client-brand img {
            height: 56px;
          }

          .client-nav__inner {
            padding: 16px;
          }

          .client-greeting {
            width: 100%;
          }

          .client-tabs {
            width: 100%;
          }

          .client-tab-link {
            flex: 1 1 calc(50% - 10px);
            justify-content: center;
          }
        }
      </style>
         <?php if (perch_member_logged_in()) { ?>
   <nav class="client-nav">

     <div class="client-nav__inner">
       <div class="client-greeting">
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
     <ul class="client-tabs">
       <li><a href="/client" class="client-tab-link <?php echo $profile_tab === 'active' ? 'is-active' : ''; ?>">Profile</a></li>
                     <li><a href="/payment/success" class="client-tab-link <?php echo $documents_tab === 'active' ? 'is-active' : ''; ?>">Documents</a></li>

       <li><a href="/client/orders" class="client-tab-link <?php echo $orders_tab === 'active' ? 'is-active' : ''; ?>">Orders</a></li>
       <li><a href="/client/notifications" class="client-tab-link <?php echo $notifications_tab === 'active' ? 'is-active' : ''; ?>">Notifications<?php if($unread_count){?><span class="unread-dot"></span><?php } ?></a></li>
       <li><a href="/client/chat" class="client-tab-link <?php echo $chat_tab === 'active' ? 'is-active' : ''; ?>">Chat<?php if($chat_unread){?><span class="unread-dot"></span><?php } ?></a></li>
       <li><a href="/client/affiliate-dashboard" class="client-tab-link <?php echo $affiliate_tab === 'active' ? 'is-active' : ''; ?>">Affiliate</a></li>
       <li><a href="/order/re-order" class="client-tab-link <?php echo $reorder_tab === 'active' ? 'is-active' : ''; ?>">Order</a></li>
       <li><a href="/client/logout" class="client-tab-link">Logout</a></li>
     </ul>


   </div>
   </nav>
<?php  } ?>
