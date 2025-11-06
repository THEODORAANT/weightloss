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

if ($order_complete) {
    perch_shop_empty_cart();
}

$docs = [];
$hasIdDocuments = false;

if (perch_member_logged_in()) {
    $docs = perch_member_documents();
    if (is_array($docs)) {
        foreach ($docs as $doc) {
            if ($doc['type'] === 'id-documents') {
                $hasIdDocuments = true;
                break;
            }
        }
    }
}

$showUploadForm = empty($docs) || !$hasIdDocuments;
?>

<section class="client-page">
  <div class="container all_content">
    <div class="client-hero">
      <?php if ($order_complete) { ?>
        <h1>Complete your consultation</h1>
        <p>Upload your identification documents and videos below to finalise your order. Review the quick guide before submitting to ensure everything is captured correctly.</p>
      <?php } else { ?>
        <h1>Your documents</h1>
        <p>Upload any missing files and check the progress of your verification documents.</p>
      <?php } ?>
    </div>

    <style>
      .documents-grid {
        display: grid;
        gap: 24px;
      }

      @media (min-width: 768px) {
        .documents-grid {
          grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
        }
      }

      .document-card {
        background: #ffffff;
        border-radius: 22px;
        border: 1px solid rgba(148, 163, 184, 0.2);
        box-shadow: 0 18px 38px rgba(15, 23, 42, 0.08);
        padding: 22px 24px;
        display: flex;
        flex-direction: column;
        gap: 20px;
      }

      .document-card__header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 16px;
      }

      .document-badge {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 6px 12px;
        border-radius: 999px;
        background: rgba(67, 56, 202, 0.12);
        color: #4338ca;
        font-weight: 600;
        font-size: 0.8rem;
        text-transform: uppercase;
        letter-spacing: 0.05em;
      }

      .document-title {
        margin: 10px 0 0;
        font-size: 1.15rem;
        font-weight: 600;
        color: #111827;
      }

      .status-badge {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 6px 14px;
        border-radius: 999px;
        font-weight: 600;
        font-size: 0.85rem;
        text-transform: uppercase;
        letter-spacing: 0.04em;
      }

      .status-badge--approved {
        background: rgba(16, 185, 129, 0.12);
        color: #047857;
      }

      .status-badge--pending {
        background: rgba(59, 130, 246, 0.12);
        color: #1d4ed8;
      }

      .status-badge--rerequest {
        background: rgba(239, 68, 68, 0.12);
        color: #b91c1c;
      }

      .document-card__body {
        color: #4b5563;
        line-height: 1.6;
      }

      .document-card__preview {
        border-radius: 16px;
        overflow: hidden;
        background: #0f172a;
        display: flex;
        justify-content: center;
        align-items: center;
      }

      .document-card__preview .preview-media {
        width: 100%;
        height: auto;
        display: block;
      }

      .upload-grid {
        display: grid;
        gap: 20px;
      }

      @media (min-width: 768px) {
        .upload-grid {
          grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
        }
      }

      .client-panel--upload {
        background: rgba(99, 102, 241, 0.08);
        border-radius: 18px;
        border: 1px dashed rgba(99, 102, 241, 0.3);
        padding: 24px;
      }

      .helper-video {
        border-radius: 18px;
        overflow: hidden;
        box-shadow: 0 18px 38px rgba(15, 23, 42, 0.12);
        margin-bottom: 24px;
      }

      .helper-video video {
        width: 100%;
        display: block;
      }

      .helper-steps {
        list-style: none;
        padding: 0;
        margin: 0;
        display: flex;
        flex-direction: column;
        gap: 14px;
        color: #4338ca;
      }

      .helper-steps li strong {
        color: #312e81;
      }

      #uploadLoading {
        background: rgba(99, 102, 241, 0.08);
        border-radius: 18px;
        padding: 24px;
      }
    </style>

    <div id="uploadLoading" class="text-center mb-4" hidden>
      <img src="/asset/loading.gif" alt="Loading..." width="80" height="80">
      <p class="small text-muted mt-2">Uploading your files… please keep this page open.</p>
    </div>

    <div class="client-columns">
      <div class="client-columns__primary">
        <div class="client-card">
          <div class="client-card__section">
            <h2 class="client-card__title">Document status</h2>
            <p class="client-card__intro">Track what you have sent us so far and review any items that still need attention. We will update these statuses as soon as our team has reviewed your files.</p>
            <?php if ($docs) { ?>
              <div class="documents-grid">
                <?php
                foreach ($docs as $document) {
                    $documentTypeLabel = '';
                    if ($document['type'] === 'id-documents') {
                        $documentTypeLabel = 'ID and video documents';
                    } elseif ($document['type'] === 'order-proof') {
                        $documentTypeLabel = 'Proof of purchase';
                    } else {
                        $documentTypeLabel = ucfirst(str_replace('-', ' ', $document['type']));
                    }
                ?>
                  <div class="document-card" data-save="<?php echo (int) $document['save']; ?>">
                    <div class="document-card__header">
                      <div>
                        <span class="document-badge">Document type</span>
                        <p class="document-title"><?php echo $documentTypeLabel; ?></p>
                      </div>
                      <span class="status-badge status-badge--<?php echo strtolower($document['status']); ?>"><?php echo strtoupper($document['status']); ?></span>
                    </div>
                    <?php if ($document['status'] === 'rerequest') { ?>
                      <div class="document-card__body">
                        <p class="mb-3">We still need a replacement file for this document. Upload it below.</p>
                        <?php
                        if (isImageFile($document['name'])) {
                            perch_member_form('upload-image.html');
                        } elseif (isVideoFile($document['name'])) {
                            perch_member_form('upload-video.html');
                        }
                        ?>
                      </div>
                    <?php } else { ?>
                      <div class="document-card__preview">
                        <?php if (isImageFile($document['name'])) { ?>
                          <img class="preview-media" src="https://<?php echo $_SERVER['HTTP_HOST']; ?>/perch/addons/apps/perch_members/documents/<?php echo $document['name']; ?>" alt="Uploaded document preview">
                        <?php } elseif (isVideoFile($document['name'])) { ?>
                          <video class="preview-media" controls>
                            <source src="https://<?php echo $_SERVER['HTTP_HOST']; ?>/perch/addons/apps/perch_members/documents/<?php echo $document['name']; ?>" type="video/<?php echo pathinfo($document['name'], PATHINFO_EXTENSION); ?>">
                            Your browser does not support the video tag.
                          </video>
                        <?php } ?>
                      </div>
                    <?php } ?>
                  </div>
                <?php } ?>
              </div>
            <?php } else { ?>
              <div class="client-empty">
                <h3>No documents uploaded yet</h3>
                <p>Once you send us your files, we will show their progress here. Use the upload section below to get started.</p>
              </div>
            <?php } ?>
          </div>
        </div>

        <div class="client-card">
          <div class="client-card__section">
            <h2 class="client-card__title">Upload your documents</h2>
            <p class="client-card__intro">Upload fresh identification documents, consultation videos or proof of purchase files. You can submit multiple files and we will link them to your order automatically.</p>
            <div class="upload-grid">
              <?php if ($showUploadForm) { ?>
                <div class="client-panel client-panel--upload">
                  <?php perch_member_form('upload.html'); ?>
                </div>
              <?php } ?>
              <div class="client-panel client-panel--upload">
                <?php perch_member_form('upload-proof.html'); ?>
              </div>
            </div>
            <div class="client-actions justify-content-center mt-4">
              <a class="btn btn-primary next-btn" href="/client">Next <i class="fa-solid fa-arrow-right"></i></a>
            </div>
          </div>
        </div>
      </div>

      <div class="client-columns__secondary">
        <aside class="client-sidecard client-sidecard--helper">
          <h2 class="client-sidecard__title">Need a hand recording your video?</h2>
          <p class="client-sidecard__intro">Follow this quick walkthrough to make sure your video meets our verification requirements. Everything can be recorded using your phone.</p>
          <div class="helper-video" oncontextmenu="return false;">
            <video id="helperVideo" preload="none" controls playsinline controlsList="nodownload">
              <source src="/instructions.mp4" type="video/mp4">
              Your browser does not support the video tag.
            </video>
          </div>
          <ul class="helper-steps">
            <li><strong>Step 1:</strong> Find a quiet, well-lit space with your scales close by.</li>
            <li><strong>Step 2:</strong> Position your device so we can see you head-to-toe when you step back.</li>
            <li><strong>Step 3:</strong> Record yourself stepping onto the scales and show the weight clearly.</li>
            <li><strong>Step 4:</strong> Upload the video here once you are happy with the recording.</li>
          </ul>
        </aside>
      </div>
    </div>
  </div>
</section>

<?php
function isImageFile($filename) {
    $imageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp', 'svg'];
    $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
    return in_array($ext, $imageExtensions, true);
}

function isVideoFile($filename) {
    $videoExtensions = ['mp4', 'webm', 'ogg', 'mov', 'avi', 'mkv'];
    $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
    return in_array($ext, $videoExtensions, true);
}
?>

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

  const helperVideo = document.getElementById('helperVideo');
  if (helperVideo) {
    helperVideo.autoplay = false;
    helperVideo.pause();
    helperVideo.addEventListener('loadeddata', function () {
      helperVideo.currentTime = 0;
    });
  }
</script>

<?php perch_layout('getStarted/footer'); ?>
