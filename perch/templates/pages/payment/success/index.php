<?php
if (session_status() === PHP_SESSION_NONE) session_start();
ob_start();

// Clear package identifiers stored in the session
unset(
    $_SESSION['package_billing_type'],
    $_SESSION['perch_shop_package_id'],
    $_SESSION['questionnaire'],
    $_SESSION['questionnaire-reorder']
);

// Expire related cookies so they do not persist after completion
setcookie('package_billing_type', '', time()-3600, '/');
setcookie('perch_shop_package_id', '', time()-3600, '/');
setcookie('questionnaire', '', time()-3600, '/');
setcookie('questionnaire_reorder', '', time()-3600, '/');
setcookie('draft_package_item', '', time()-3600, '/');

$order_complete = perch_shop_order_successful();

if (!$order_complete) {
    $order_complete = perch_shop_active_order_has_status('pending');

}

     perch_layout('product/header', [
          'page_title' => perch_page_title(true),
      ]);
?>
  <style>
        .subheader {
          background-color: #fff;
          border-bottom: 1px solid #e5e5e5;
        }

        .welcome-msg {
          padding: 12px 20px;
          font-size: 16px;
          color: #333;
          border-bottom: 1px solid #f1f1f1;
        }

        .tabs {
          display: flex;
          flex-wrap: wrap;
          gap: 6px;
          padding: 12px 20px 0;
          background-color: #f7f9fc;
        }

        .tab {
          padding: 10px 16px;
          border-radius: 6px 6px 0 0;
          text-decoration: none;
          color: #4a4a4a;
          border: 1px solid transparent;
          border-bottom: 3px solid transparent;
          transition: all 0.2s ease;
          font-weight: 500;
        }

        .tab:hover {
          color: #0a0a0a;
          border-color: #d6e4ff;
          background-color: #eef4ff;
        }

        .tab.active {
          color: #0b4db3;
          border-color: #0b4db3;
          background-color: #fff;
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
     $spilit_parts=explode("?", $lastPart);

    //  echo  $lastPart;
    $profile_tab="";
        $orders_tab="";
        $reorder_tab="";
         $documents_tab="";
          $affiliate_tab="";
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
             }
        ?>
       <div class="tabs">
         <a href="/client" class="tab <?php echo $profile_tab; ?>">Profile</a>
                       <a href="/payment/success" class="tab <?php echo $documents_tab; ?>">Documents</a>

         <a href="/client/orders" class="tab <?php echo $orders_tab; ?>">Orders</a>
         <a href="/client/affiliate-dashboard" class="tab <?php echo $affiliate_tab; ?>">Affiliate</a>
         <a href="/order/re-order" class="tab <?php echo $reorder_tab; ?>">Order</a>
         <a href="/client/logout" class="tab ">Logout</a>
       </div>


   </div>
<?php  } ?>
  <section class="success-section py-5">
    <div class="container all_content">
    <?php if ($order_complete) {
    perch_shop_empty_cart();
    ?>
        <div class="page-heading text-center mb-5">
            <h1 class="fw-bolder mb-3">Complete your consultation</h1>
            <p class="lead mb-0">Upload your identification documents and videos below to finalise your order. Review the quick guide before submitting to ensure everything is captured correctly.</p>
        </div>
 <?php }else { ?>
        <div class="page-heading text-center mb-5">
            <h1 class="fw-bolder mb-3">Your documents</h1>
            <p class="lead mb-0">Upload any missing files and check the progress of your verification documents.</p>
        </div>

   <?php } ?>
        <div class="upload-helper card-shadow mb-5">
            <div class="helper-header">
                <div>
                    <h2 class="helper-title">Need a hand recording your video?</h2>
                    <p class="helper-text">Follow the short video guide or download the instructions to get everything right the first time.</p>
                </div>
                <button class="btn btn-outline-primary helper-button" type="button" onclick="openPopup()">Open full guide</button>
            </div>
            <div class="helper-content">
                <div class="video-wrapper" oncontextmenu="return false;">
                    <video id="myVideo" preload="metadata" controls playsinline>
                        <source src="/instructions.mp4" type="video/mp4">
                        Your browser does not support the video tag.
                    </video>
                </div>
                <ul class="helper-steps">
                    <li><strong>Step 1:</strong> Find a quiet, well lit space where we can clearly see your face.</li>
                    <li><strong>Step 2:</strong> Hold your photo ID next to your face and clearly read the statement shown on screen.</li>
                    <li><strong>Step 3:</strong> Upload the video and a clear photo of your ID using the cards below.</li>
                </ul>
            </div>
        </div>

    <div id="guideModal" class="modal" role="dialog" aria-modal="true">
        <button class="close" type="button" aria-label="Close" onclick="closePopup()">&times;</button>
        <div class="modal-dialog" onclick="event.stopPropagation()">
            <iframe class="modal-content" id="pdfFrame" src="/instructions.mp4" title="Video instructions"></iframe>
        </div>
    </div>

    <div id="uploadLoading" class="text-center mb-4" hidden>
        <img src="/asset/loading.gif" alt="Loading..." width="80" height="80">
        <p class="small text-muted mt-2">Uploading your files… please keep this page open.</p>
    </div>

<?php
function isImageFile($filename) {
    $imageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp', 'svg'];
    $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
    return in_array($ext, $imageExtensions);
}

function isVideoFile($filename) {
    $videoExtensions = ['mp4', 'webm', 'ogg', 'mov', 'avi', 'mkv'];
    $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
    return in_array($ext, $videoExtensions);
}

  // your 'success' and 'failure' URLs
if (perch_member_logged_in()) {
//perch_shop_orders();
//perch_shop_empty_cart();
$docs=perch_member_documents();
$hasIdDocuments = false;
if($docs){
echo '<div class="documents-grid">';
foreach ($docs as $x => $y) {

    if($y["type"]=="id-documents") {
        $hasIdDocuments = true;
    }

    $documentTypeLabel = '';
    if($y["type"]=="id-documents"){ $documentTypeLabel = "ID and video documents";}
    else if($y["type"]=="order-proof"){ $documentTypeLabel = "Proof of Purchase"; }

 ?>

   <div class="document-card card-shadow" data-save="8">
         <div class="document-card__header">
            <div>
                <span class="document-badge">Document type</span>
                <p class="document-title"><?php echo $documentTypeLabel; ?></p>
            </div>
            <span class="status-badge status-badge--<?php echo strtolower($y["status"]); ?>"><?php  echo strtoupper($y["status"]); ?></span>
         </div>

         <?php if($y["status"]=="rerequest") { ?>
            <div class="document-card__body">
                <p class="mb-3">We still need a replacement file for this document. Upload it below.</p>
                <?php
                if (isImageFile($y["name"])) {
                    perch_member_form('upload-image.html');
                } else if (isVideoFile($y["name"])) {
                    perch_member_form('upload-video.html');
                }
                ?>
            </div>
         <?php } ?>

         <?php if ($y["status"]!="rerequest") { ?>
             <div class="document-card__preview">
                <?php if (isImageFile($y["name"])) { ?>
                    <img class="preview-media" src="https://<?php echo $_SERVER['HTTP_HOST'];?>/perch/addons/apps/perch_members/documents/<?php echo $y['name']; ?>" alt="Image Preview">
                <?php } else if (isVideoFile($y["name"])) {
                    $fileUrl="https://".$_SERVER['HTTP_HOST']."/perch/addons/apps/perch_members/documents/".$y['name'];
                    echo '<video class="preview-media" controls>
                            <source src="' . htmlspecialchars($fileUrl) . '" type="video/' . pathinfo($y["name"], PATHINFO_EXTENSION) . '">
                            Your browser does not support the video tag.
                          </video>';
                } ?>
             </div>
         <?php } ?>
    </div>

<?php
 }
echo '</div>';
  ?>
  <div class="bottom_btn mt-5">
        <a class="btn btn-primary next_btn mt-4 mb-3 next-btn" href="/client">Next <i class="fa-solid fa-arrow-right"></i></a>
   </div>
<?php
}

if(!$docs || !$hasIdDocuments){
?>
    <div class="card-shadow form-card mb-5">
        <?php perch_member_form('upload.html'); ?>
    </div>
<?php
}
?>
    <div class="card-shadow form-card">
        <?php perch_member_form('upload-proof.html'); ?>
    </div>
    </div>
  </section>

  <!-- Loading GIF
  <img id="loading" src="loading.gif" style="display: none;" alt="Loading..." width="100">

  <div class="preview">
    <img id="imgPreview" style="display: none;" alt="Image Preview">
    <video id="videoPreview" style="display: none;" controls></video>
  </div>
-->
  <script>
    function createImagePreview(file, container) {
      const reader = new FileReader();
      reader.onload = function (e) {
        const img = document.createElement('img');
        img.src = e.target.result;
        img.width = 320;
        img.height = 240;
        img.className = 'me-3 mb-3';
        img.alt = 'Image Preview';
        container.appendChild(img);
      };
      reader.readAsDataURL(file);
    }

    function createVideoPreview(file, container) {
      const url = URL.createObjectURL(file);
      const video = document.createElement('video');
      video.width = 320;
      video.height = 240;
      video.controls = true;
      video.className = 'me-3 mb-3';
      video.src = url;
      video.onloadeddata = function () {
        URL.revokeObjectURL(url);
      };
      container.appendChild(video);
    }

    document.querySelectorAll('.document-image-input').forEach(function (input) {
      input.addEventListener('change', function () {
        const wrapper = this.closest('.document-card') || this.closest('.plan');
        const container = wrapper ? wrapper.querySelector('.image-preview-container') : null;
        if (!container) {
          return;
        }
        container.innerHTML = '';
        Array.from(this.files || []).forEach(function (file) {
          if (file && file.type && file.type.startsWith('image/')) {
            createImagePreview(file, container);
          }
        });
      });
    });

    document.querySelectorAll('.document-video-input').forEach(function (input) {
      input.addEventListener('change', function () {
        const wrapper = this.closest('.document-card') || this.closest('.plan');
        const container = wrapper ? wrapper.querySelector('.video-preview-container') : null;
        if (!container) {
          return;
        }
        container.innerHTML = '';
        Array.from(this.files || []).forEach(function (file) {
          if (file && file.type && file.type.startsWith('video/')) {
            createVideoPreview(file, container);
          }
        });
      });
    });

    document.querySelectorAll('input[name="upload"]').forEach(function (button) {
      button.addEventListener('click', function () {
        const loading = document.getElementById('uploadLoading');
        if (loading) {
          loading.hidden = false;
        }
        this.value = 'Uploading…';
      });
    });

    const guideModal = document.getElementById('guideModal');

    function openPopup() {
      if (guideModal) {
        guideModal.style.display = 'block';
      }
    }

    function closePopup() {
      if (guideModal) {
        guideModal.style.display = 'none';
      }
    }

    if (guideModal) {
      guideModal.addEventListener('click', function (event) {
        if (event.target === guideModal) {
          closePopup();
        }
      });
    }

    document.addEventListener('keydown', function (event) {
      if (event.key === 'Escape') {
        closePopup();
      }
    });

    window.openPopup = openPopup;
    window.closePopup = closePopup;
  </script>

  <?php
 }
?>
</main>

  <style>
        .success-section {
            background-color: #f6f8fb;
        }

        .card-shadow {
            background-color: #fff;
            border-radius: 16px;
            box-shadow: 0 18px 45px -25px rgba(15, 33, 61, 0.35);
            padding: 32px;
        }

        .plan {
            background-color: #fff;
            border-radius: 16px;
            box-shadow: 0 10px 30px -20px rgba(15, 33, 61, 0.4);
            padding: 24px;
            border: 1px solid rgba(15, 33, 61, 0.06);
        }

        .form-card form {
            margin-bottom: 0;
        }

        .form-card .plan {
            box-shadow: none;
            border: 1px dashed #d3d9e6;
            margin-bottom: 24px;
        }

        .form-card .plan:last-child {
            margin-bottom: 0;
        }

        .upload-helper {
            display: flex;
            flex-direction: column;
            gap: 24px;
        }

        .helper-header {
            display: flex;
            flex-direction: column;
            gap: 16px;
        }

        .helper-title {
            font-size: 22px;
            margin: 0;
        }

        .helper-text {
            margin-bottom: 0;
            color: #4d5b75;
        }

        .helper-button {
            align-self: flex-start;
        }

        .helper-content {
            display: flex;
            flex-direction: column;
            gap: 24px;
        }

        .video-wrapper {
            width: 100%;
            max-width: 460px;
            border-radius: 12px;
            overflow: hidden;
            background-color: #000;
        }

        .video-wrapper video {
            width: 100%;
            height: auto;
            display: block;
        }

        .helper-steps {
            margin: 0;
            padding-left: 20px;
            color: #32415b;
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .documents-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 24px;
        }

        .document-card {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        .document-card__header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 16px;
        }

        .document-badge {
            display: inline-block;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: #6c7a92;
        }

        .document-title {
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 0;
        }

        .status-badge {
            padding: 6px 12px;
            border-radius: 999px;
            font-size: 12px;
            font-weight: 600;
            letter-spacing: 0.08em;
            color: #fff;
            background-color: #4d5b75;
        }

        .status-badge--approved {
            background-color: #00a884;
        }

        .status-badge--pending {
            background-color: #f0a500;
        }

        .status-badge--rerequest {
            background-color: #d9423b;
        }

        .document-card__body {
            display: flex;
            flex-direction: column;
            gap: 16px;
        }

        .document-card__preview {
            border: 1px dashed #d3d9e6;
            border-radius: 12px;
            padding: 12px;
            display: flex;
            justify-content: center;
            background-color: #f9fbff;
        }

        .preview-media {
            max-width: 100%;
            border-radius: 8px;
        }

        .bottom_btn .btn {
            padding: 12px 24px;
            border-radius: 999px;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            text-decoration: none;
        }

        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0,0,0,0.6);
        }

        .modal-dialog {
            margin: 5% auto;
            width: min(900px, 90%);
            max-width: 100%;
        }

        .modal-content {
            display: block;
            width: 100%;
            height: min(700px, 90vh);
            border-radius: 12px;
        }

        .close {
            position: absolute;
            top: 20px;
            right: 40px;
            color: white;
            font-size: 40px;
            font-weight: bold;
            cursor: pointer;
            background: none;
            border: none;
        }

        @media (min-width: 768px) {
            .helper-content {
                flex-direction: row;
                align-items: center;
            }

            .helper-header {
                flex-direction: row;
                justify-content: space-between;
                align-items: center;
            }
        }

        @media (max-width: 767px) {
            .card-shadow {
                padding: 24px;
            }

            .document-card__header {
                flex-direction: column;
                align-items: flex-start;
            }

            .bottom_btn .btn {
                width: 100%;
                justify-content: center;
            }
        }
    </style>
    <?php
  perch_layout('getStarted/footer');?>
