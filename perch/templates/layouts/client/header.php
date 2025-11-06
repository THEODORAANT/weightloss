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
                <!--
                <div class="back_btn">
                    <a href="/home"><i class="fa-solid fa-angle-left text-light"></i> Back</a>
                </div>
                -->
                <div class="getweightloss_logo">
                    <a href="/client" class="getweightloss_logo-link">
                        <img src="/asset/logo-final.png" alt="GetWeightLoss" />
                    </a>
                </div>
            </div>
        </div>
        <!--//////////////////==============Header section section END=========///////////////-->
   <style>


        .getweightloss_header {
          background: linear-gradient(90deg, #081c3b 0%, #11315a 50%, #081c3b 100%);
          padding: 18px 0;
          position: sticky;
          top: 0;
          z-index: 1030;
        }

        .getweightloss_header .main_code {
          display: flex;
          align-items: center;
          justify-content: flex-start;
          gap: 24px;
        }

        .getweightloss_logo img {
          max-height: 110px;
          width: auto;
          display: block;
        }

        .getweightloss_logo-link {
          display: inline-flex;
          align-items: center;
        }

        @media (max-width: 575.98px) {
          .getweightloss_header {
            padding: 14px 0;
          }

          .getweightloss_header .main_code {
            justify-content: center;
          }

          .getweightloss_logo img {
            max-height: 84px;
          }
        }

        .client-columns {
          display: grid;
          gap: 32px;
        }

        .client-columns > * {
          min-width: 0;
        }

        .client-columns__primary,
        .client-columns__secondary {
          display: flex;
          flex-direction: column;
          gap: 32px;
        }

        @media (min-width: 992px) {
          .client-columns {
            grid-template-columns: 1.35fr 1fr;
            align-items: stretch;
          }
        }

        .client-columns--support {
          gap: 36px;
        }

        @media (min-width: 1200px) {
          .client-columns--support {
            grid-template-columns: minmax(0, 1.65fr) minmax(0, 1fr);
          }
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

        .client-nav__brand {
          display: inline-flex;
          align-items: center;
          gap: 16px;
        }

        .client-nav__logo {
          display: inline-flex;
          align-items: center;
        }

        .client-nav__logo img {
          height: 54px;
          width: auto;
          display: block;
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

        .client-page {
          background: linear-gradient(180deg, #f8f9ff 0%, #ffffff 100%);
          padding: 64px 0 80px;
        }

        .client-hero {
          max-width: 720px;
          margin: 0 auto 48px;
          text-align: center;
        }

        .client-hero h1 {
          font-size: clamp(2rem, 3vw + 1.2rem, 2.75rem);
          font-weight: 700;
          color: #111827;
          margin-bottom: 16px;
        }

        .client-hero p {
          margin: 0 auto;
          color: #4b5563;
          font-size: 1.05rem;
          line-height: 1.7;
        }

        .client-card,
        .client-sidecard,
        .client-panel {
          background: #ffffff;
          border-radius: 24px;
          box-shadow: 0 24px 48px rgba(15, 23, 42, 0.08);
          border: 1px solid rgba(148, 163, 184, 0.18);
        }

        .client-card {
          padding: 32px 34px;
        }

        .client-sidecard,
        .client-panel {
          padding: 28px 30px;
        }

        .client-panel__body {
          display: flex;
          flex-direction: column;
          gap: 16px;
        }

        .client-card__title {
          font-size: 1.35rem;
          font-weight: 600;
          color: #111827;
          margin-bottom: 12px;
        }

        .client-card__intro {
          color: #4b5563;
          margin-bottom: 28px;
          line-height: 1.6;
        }

        .client-card__section + .client-card__section {
          margin-top: 32px;
          padding-top: 28px;
          border-top: 1px solid rgba(148, 163, 184, 0.18);
        }

        .client-sidecard__title {
          font-size: 1.25rem;
          font-weight: 600;
          color: #1f2937;
          margin-bottom: 10px;
        }

        .client-sidecard__intro {
          color: #4b5563;
          margin-bottom: 18px;
          line-height: 1.6;
        }

        .client-list {
          list-style: none;
          padding: 0;
          margin: 0;
          display: flex;
          flex-direction: column;
          gap: 16px;
        }

        .client-list__item {
          padding: 18px 20px;
          border-radius: 18px;
          background: rgba(99, 102, 241, 0.08);
          border: 1px solid rgba(99, 102, 241, 0.14);
        }

        .client-list__title {
          font-weight: 600;
          color: #312e81;
          margin-bottom: 8px;
        }

        .client-list__body {
          margin: 0;
          color: #4338ca;
          line-height: 1.5;
        }

        .client-table {
          width: 100%;
          border-collapse: separate;
          border-spacing: 0;
          border-radius: 18px;
          overflow: hidden;
          box-shadow: 0 14px 28px rgba(15, 23, 42, 0.08);
        }

        .client-table thead {
          background: linear-gradient(90deg, #4338ca 0%, #6366f1 100%);
          color: #ffffff;
        }

        .client-table th,
        .client-table td {
          padding: 16px 18px;
          border-bottom: 1px solid rgba(226, 232, 240, 0.5);
          text-align: left;
          font-size: 0.95rem;
        }

        .client-table tbody tr:nth-child(2n) {
          background: #f8fafc;
        }

        .client-pill {
          display: inline-flex;
          align-items: center;
          gap: 8px;
          padding: 6px 14px;
          border-radius: 999px;
          background: rgba(79, 70, 229, 0.12);
          color: #4338ca;
          font-weight: 500;
        }

        .client-empty {
          text-align: center;
          padding: 48px 30px;
          background: rgba(99, 102, 241, 0.06);
          border-radius: 24px;
          border: 1px dashed rgba(99, 102, 241, 0.3);
          color: #4b5563;
        }

        .client-empty h3 {
          font-weight: 600;
          color: #1f2937;
          margin-bottom: 12px;
        }

        .client-actions {
          display: flex;
          flex-wrap: wrap;
          gap: 12px;
          align-items: center;
        }

        .client-actions .btn,
        .client-actions button,
        .client-actions a {
          border-radius: 999px;
          font-weight: 600;
          letter-spacing: 0.01em;
        }

        @media (max-width: 992px) {
          .client-card,
          .client-sidecard,
          .client-panel {
            padding: 26px 24px;
            border-radius: 20px;
          }

          .client-nav__logo img {
            height: 48px;
          }
        }

        @media (max-width: 768px) {
          .client-nav__inner {
            padding: 16px;
          }

          .client-nav__brand {
            width: 100%;
            flex-direction: column;
            justify-content: center;
            text-align: center;
            gap: 12px;
          }

          .client-nav__logo img {
            height: 42px;
          }

          .client-greeting {
            width: auto;
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
       <div class="client-nav__brand">
         <a class="client-nav__logo" href="/client" aria-label="GetWeightLoss home">
           <img src="/asset/logo-final.png" alt="GetWeightLoss" />
         </a>
         <div class="client-greeting">
           Hello, <strong><?php echo perch_member_get('first_name'); ?></strong>
         </div>
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
