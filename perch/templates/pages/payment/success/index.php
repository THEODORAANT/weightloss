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

    perch_layout('client/header', [
        'page_title' => perch_page_title(true),
    ]);
?>
<main class="client-documents-main">
  <section class="client-documents py-5">
    <div class="container client-documents__container">
      <div class="client-documents__intro text-center mb-5">
        <?php if ($order_complete) {
            perch_shop_empty_cart();
        ?>
          <span class="client-documents__eyebrow">Payment complete</span>
          <h1 class="client-documents__heading fw-bolder mb-3">Complete your consultation</h1>
          <p class="client-documents__lead mb-0">Upload your identification documents and videos below to finalise your order. Review the quick guide before submitting to ensure everything is captured correctly.</p>
        <?php } else { ?>
          <span class="client-documents__eyebrow">Documents</span>
          <h1 class="client-documents__heading fw-bolder mb-3">Your documents</h1>
          <p class="client-documents__lead mb-0">Upload any missing files and check the progress of your verification documents.</p>
        <?php } ?>
      </div>

      <div class="client-documents__content">
        <div class="upload-helper card-shadow client-documents__helper">
            <div class="helper-header">
                <div>
                    <h2 class="helper-title">Need a hand recording your video?</h2>
                    <p class="helper-text">Follow the short video guide or download the instructions to get everything right the first time.</p>
                </div>
                <!--<button class="btn btn-outline-primary helper-button" type="button" onclick="openPopup()">Open full guide</button>-->
            </div>
            <div class="helper-content">
                <div class="video-wrapper" oncontextmenu="return false;">
                    <video id="helperVideo" preload="none" controls playsinline controlsList="nodownload">
                        <source src="/instructions.mp4" type="video/mp4">
                        Your browser does not support the video tag.
                    </video>
                </div>
                <ul class="helper-steps">
                    <li><strong>Step 1:</strong>Find a quiet, well lit space with your scales close by.</li>
                    <li><strong>Step 2:</strong>Place your mobile/tablet at a distance so that when you step away we can see you head-to-toe.</li>
                    <li><strong>Step 3:</strong>Whilst your device is recording, pick it up and step on the scales.Point the camera at the display showing your weight. Stop recording.</li>
                    <li><strong>Step 4:</strong>If the video shows you head-to-toe; clearly shows you stepping onto the scales and the weight, then upload.</li>
                </ul>
            </div>
        </div>

        <div id="guideModal" class="modal" role="dialog" aria-modal="true">
            <button class="close" type="button" aria-label="Close" onclick="closePopup()">&times;</button>
            <div class="modal-dialog" onclick="event.stopPropagation()">
                <iframe class="modal-content" id="pdfFrame" src="" data-src="/instructions.mp4" title="Video instructions"></iframe>
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
echo '<div class="documents-grid client-documents__documents">';
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
            <?php
                $documentTitle = htmlspecialchars($documentTypeLabel, ENT_QUOTES, 'UTF-8');
                $rawStatus = isset($y["status"]) ? (string)$y["status"] : '';
                $statusClass = strtolower(preg_replace('/[^a-z0-9\-]+/i', '-', $rawStatus));
                $statusLabel = htmlspecialchars(strtoupper($rawStatus), ENT_QUOTES, 'UTF-8');
            ?>
            <div>
                <span class="document-badge">Document type</span>
                <p class="document-title"><?php echo $documentTitle; ?></p>
            </div>
            <span class="status-badge status-badge--<?php echo $statusClass; ?>"><?php echo $statusLabel; ?></span>
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
                    <?php
                        $safeHost = htmlspecialchars($_SERVER['HTTP_HOST'], ENT_QUOTES, 'UTF-8');
                        $encodedName = rawurlencode($y['name']);
                    ?>
                    <img class="preview-media" src="https://<?php echo $safeHost; ?>/perch/addons/apps/perch_members/documents/<?php echo $encodedName; ?>" alt="Image Preview">
                <?php } else if (isVideoFile($y["name"])) {
                    $fileUrl = 'https://' . $_SERVER['HTTP_HOST'] . '/perch/addons/apps/perch_members/documents/' . rawurlencode($y['name']);
                    $fileExtension = pathinfo($y["name"], PATHINFO_EXTENSION);
                    $safeUrl = htmlspecialchars($fileUrl, ENT_QUOTES, 'UTF-8');
                    $safeExtension = htmlspecialchars($fileExtension, ENT_QUOTES, 'UTF-8');
                    echo '<video class="preview-media" controls>
                            <source src="' . $safeUrl . '" type="video/' . $safeExtension . '">
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
 <!-- <div class="bottom_btn mt-5">
        <a class="btn btn-primary next_btn mt-4 mb-3 next-btn" href="/client">Next <i class="fa-solid fa-arrow-right"></i></a>
   </div>-->
<?php
}


    $showUploadForm = !$docs || !$hasIdDocuments;
    ?>
    <div class="upload-grid client-documents__uploads">
        <?php if ($showUploadForm) { ?>
            <div class="card-shadow upload-card">
                <?php perch_member_form('upload.html'); ?>
            </div>
        <?php } ?>
        <div class="card-shadow upload-card">
            <?php perch_member_form('upload-proof.html'); ?>
        </div>
    </div>
 <!--   <div class="upload-next client-documents__next text-center">
        <a class="btn btn-primary next_btn mt-4 mb-3 next-btn" href="/client">Next <i class="fa-solid fa-arrow-right"></i></a>
    </div>-->
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
        img.className = 'preview-media-item';
        img.alt = 'Image Preview';
        container.appendChild(img);
      };
      reader.readAsDataURL(file);
    }

    function createVideoPreview(file, container) {
      const url = URL.createObjectURL(file);
      const video = document.createElement('video');
      video.controls = true;
      video.className = 'preview-media-item';
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

    const uploadLoading = document.getElementById('uploadLoading');

    document.querySelectorAll('.upload-form').forEach(function (form) {
      const submitButton = form.querySelector('input[name="upload"]');

      if (!submitButton) {
        return;
      }

      const originalLabel = submitButton.value;

      function resetButton() {
        submitButton.disabled = false;
        submitButton.value = originalLabel;
        if (uploadLoading) {
          uploadLoading.hidden = true;
        }
      }

      form.addEventListener('submit', function (event) {
        const hasFiles = Array.from(
          form.querySelectorAll('input[type="file"]')
        ).some(function (input) {
          return input.files && input.files.length > 0;
        });

        if (!hasFiles) {
          resetButton();
          return;
        }

        submitButton.disabled = true;
        submitButton.value = 'Uploading…';
        if (uploadLoading) {
          uploadLoading.hidden = false;
        }

        window.setTimeout(function () {
          if (event.defaultPrevented) {
            resetButton();
          }
        }, 0);
      });

      form.addEventListener(
        'invalid',
        function () {
          resetButton();
        },
        true
      );

      form.addEventListener('reset', resetButton);
    });

    const helperVideo = document.getElementById('helperVideo');
    if (helperVideo) {
      helperVideo.autoplay = false;
      helperVideo.pause();
      helperVideo.addEventListener('loadeddata', function () {
        helperVideo.currentTime = 0;
      });
    }

    const guideModal = document.getElementById('guideModal');
    const guideFrame = document.getElementById('pdfFrame');

    function openPopup() {
      if (guideModal) {
        if (guideFrame && !guideFrame.src) {
          const source = guideFrame.getAttribute('data-src');
          if (source) {
            guideFrame.src = source;
          }
        }
        guideModal.style.display = 'block';
      }
    }

    function closePopup() {
      if (guideModal) {
        guideModal.style.display = 'none';
      }
      if (guideFrame && guideFrame.src) {
        guideFrame.src = '';
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
        .client-documents-main {
            background-color: #f6f8fb;
            min-height: 100vh;
        }

        .client-documents__container {
            max-width: 1100px;
        }

        .client-documents__intro {
            max-width: 720px;
            margin: 0 auto 3rem;
            text-align: center;
        }

        .client-documents__eyebrow {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 0.35rem 0.85rem;
            border-radius: 999px;
            background: rgba(99, 102, 241, 0.12);
            color: #4338ca;
            font-weight: 600;
            font-size: 0.75rem;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            margin-bottom: 1rem;
        }

        .client-documents__heading {
            color: #172346;
            font-size: clamp(1.9rem, 1.2vw + 1.8rem, 2.85rem);
        }

        .client-documents__lead {
            color: #4d5b75;
            font-size: 1rem;
            max-width: 640px;
            margin: 0 auto;
        }

        .client-documents__content {
            display: flex;
            flex-direction: column;
            gap: 32px;
        }

        .client-documents__helper {
            margin-bottom: 0;
        }

        .client-documents__documents {
            margin-bottom: 8px;
        }

        .client-documents__uploads {
            margin-top: 8px;
        }

        .client-documents__next .btn {
            border-radius: 999px;
            padding: 0.75rem 1.75rem;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
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

        .upload-grid {
            display: grid;
            gap: 24px;
            margin-top: 24px;
            margin-bottom: 12px;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        }

        .upload-card {
            padding: 24px;
            display: flex;
            flex-direction: column;
            height: 100%;
        }

        .upload-card form {
            width: 100%;
        }

        .upload-form {
            display: flex;
            flex-direction: column;
            gap: 20px;
            height: 100%;
        }

        .upload-form__fields {
            display: grid;
            gap: 16px;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        }

        .upload-box {
            display: flex;
            flex-direction: column;
            gap: 12px;
            height: 100%;
        }

        .upload-box .preview {
            flex-grow: 1;
            border: 1px dashed #d3d9e6;
            border-radius: 12px;
            background-color: #f9fbff;
            padding: 16px;
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            justify-content: center;
            gap: 12px;
            overflow: hidden;
        }

        .preview-media-item {
            max-width: 100%;
            height: auto;
            border-radius: 8px;
            display: block;
        }

        .preview-media-item:not(video) {
            width: 100%;
        }

        video.preview-media-item {
            width: 100%;
            height: auto;
        }

        .upload-helper-text {
            margin-bottom: 0;
            color: #4d5b75;
            font-size: 0.9rem;
        }

        .upload-submit {
            align-self: flex-start;
            margin-top: auto;
        }

        .upload-next {
            margin-top: 12px;
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
            .client-documents__intro {
                margin-bottom: 2.25rem;
            }

            .client-documents__content {
                gap: 24px;
            }

            .client-documents__next .btn {
                width: 100%;
            }

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
