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

     perch_layout('product/header', [
          'page_title' => perch_page_title(true),
      ]);
?>
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
  <section class="shippin_section">
    <div class="container all_content mt-4">
    <?php if (perch_shop_order_successful()) {
    perch_shop_empty_cart();
    ?>
        <h2 class="text-center fw-bolder">Complete your consultation. <br/>Upload your identification document and video here.<br/>
        </h2>
        <h4>Please review the information document before uploading your video</h4>
 <?php }else { ?>
         <h2 class="text-center fw-bolder">	Your Documents</h2>

   <?php } ?>
        <div class="plans mt-4">
  <div class="plan"  data-save="8">
           <img id="loading" src="/asset/loading.gif" style="display: none;" alt="Loading..." width="100">

    <!-- Tooltip element -->
    <div class="tooltip-info" onclick="openPopup()">
     <p > How to record your video: </p>

<style>
  .video-container {
    position: relative;
    width: 320px;
    height: 240px;
    background: #000;
  }

  video {
    width: 100%;
    height: 100%;
    display: block;
  }

  .controls {
    position: absolute;
    bottom: 0;
    width: 100%;
    background: rgba(0,0,0,0.6);
    display: flex;
    justify-content: space-between;
    padding: 5px;
    box-sizing: border-box;
  }

  .controls button {
    background: none;
    border: none;
    color: white;
    font-size: 14px;
    cursor: pointer;
  }

  .controls input[type=range] {
    flex: 1;
    margin: 0 5px;
  }
</style>

<div class="video-container" oncontextmenu="return false;">
  <video id="myVideo" preload="metadata">
    <source src="/instructions.mp4" type="video/mp4">
    Your browser does not support the video tag.
  </video>
  <div class="controls">
    <button id="playPause">Play</button>
    <input type="range" id="seekBar" value="0" step="0.1">
    <button id="muteUnmute">Mute</button>
  </div>
</div>

<script>
  const video = document.getElementById('myVideo');
  const playPause = document.getElementById('playPause');
  const muteUnmute = document.getElementById('muteUnmute');
  const seekBar = document.getElementById('seekBar');




</script>

    <!--   <img width="20px;" height="20px;" src="/asset/info.png" />
        <span class="tooltiptext">Click to view image</span>-->
    </div>

    <!-- Modal/popup -->
    <div id="imageModal" class="modal" onclick="closePopup()">
        <span class="close">&times;</span>
            <iframe class="modal-content" id="pdfFrame" src="/instructions.mp4"></iframe>

          <!--  <img class="modal-content" id="popupImage" src="your-image.jpg" alt="Popup Image">-->
    </div>

    <script>
        function openPopup() {
            document.getElementById("imageModal").style.display = "block";
        }

        function closePopup() {
            document.getElementById("imageModal").style.display = "none";
        }
    </script>
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
if($docs){
//print_r($docs);
foreach ($docs as $x => $y) {

 ?>

   <div class="plan"  data-save="8">
         <div>

         <p class="price-section"><h5>Document Type:</h5> <span style="background-color: #0083cc;color: #fff;padding: 5px 10px; border-radius: 5px; font-size: 14px;">
      <?php  if($y["type"]=="id-documents"){ echo "ID and video documents";}else if($y["type"]=="order-proof"){echo "Proof of Purchase";} ?></span></p>

         <?php if($y["status"]=="rerequest") {
          echo '<p>You requested to upload ne documents:</p>';
          if (isImageFile($y["name"])) {
         perch_member_form('upload-image.html');
         }else if (isVideoFile($y["name"])) {
          perch_member_form('upload-video.html');
         }

         } ?>
         <p class="price-section"><h5>Document Status:</h5> <span style="background-color: #00ccbd;color: #fff;padding: 5px 10px; border-radius: 5px; font-size: 14px;"><?php  echo strtoupper($y["status"]); ?></span></p>
            </div>
             <div class="price-section">
             <span class="old-price"></span>
              <span class="price fw-bold">
<?php if (isImageFile($y["name"]) && $y["status"]!="rerequest") { ?>
 <img width="320" height="240"   src="https://<?php echo $_SERVER['HTTP_HOST'];?>/perch/addons/apps/perch_members/documents/<?php echo $y['name']; ?>" alt="Image Preview">
<?php
}else if (isVideoFile($y["name"]) && $y["status"]!="rerequest") {
$fileUrl="https://".$_SERVER['HTTP_HOST']."/perch/addons/apps/perch_members/documents/".$y['name'];
          echo '<video width="320" height="240" controls>
                  <source src="' . htmlspecialchars($fileUrl) . '" type="video/' . pathinfo($y["name"], PATHINFO_EXTENSION) . '">
                  Your browser does not support the video tag.
                </video>';
      } ?>
      </span>
                </div>
    </div>

<?php
 }?>
  <div class="bottom_btn mt-5">
        <button class="btn btn-primary next_btn mt-4 mb-3 next-btn"><a href="/client">Next <i class="fa-solid fa-arrow-right"></i></button>
   </div>
<?php
}else{
perch_member_form('upload.html');

}
perch_member_form('upload-proof.html');
  ?>

</div>    </div>       </section>

  <!-- Loading GIF
  <img id="loading" src="loading.gif" style="display: none;" alt="Loading..." width="100">

  <div class="preview">
    <img id="imgPreview" style="display: none;" alt="Image Preview">
    <video id="videoPreview" style="display: none;" controls></video>
  </div>
-->
  <script>
    // Preview Image
    //document.getElementById("image")
      document.getElementsByName("image")[0].addEventListener("change", function () {
      const file = this.files[0];
      if (file) {
        const reader = new FileReader();
        reader.onload = function (e) {
          const img = document.getElementById("imgPreview");
          img.src = e.target.result;
          img.style.display = "block";
        };
        reader.readAsDataURL(file);
      }
    });

    // Preview Video
   // document.getElementById("video")
    document.getElementsByName("video")[0].addEventListener("change", function () {
      const file = this.files[0];
      if (file) {
        const reader = new FileReader();
        reader.onload = function (e) {
          const video = document.getElementById("videoPreview");
          video.src = e.target.result;
          video.style.display = "block";
        };
        reader.readAsDataURL(file);
      }
    });

    // Show loading on form submit


 document.getElementsByName("upload")[0].addEventListener("click", function () {
 console.log("upload");
  document.getElementById("loading").style.display = "block";
         document.getElementsByName("upload")[0].value = "...";

   });
  </script>

  <?php
}
?>
</main>

  <style>
        /* Tooltip styling */
       .tooltip-info {
           /*  position: relative; */
            align: center;
            display: inline-block;
            cursor: pointer;
        }

        .tooltip-info .tooltiptext {
            visibility: hidden;
            width: 140px;

            text-align: center;
            padding: 5px 8px;
            border-radius: 6px;
            position: absolute;
            z-index: 1;
            bottom: 125%;
            left: 50%;
            transform: translateX(-50%);
            opacity: 0;
            transition: opacity 0.3s;
        }

       /* .tooltip-info:hover .tooltiptext {
            visibility: visible;
            opacity: 1;
        }
*/
        /* Popup modal styling */
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

        .modal-content {
            margin: 10% auto;
            display: block;
             width: 900px;
             max-width: 1000px;
             height: 900px;
        }

        .close {
            position: absolute;
            top: 20px;
            right: 40px;
            color: white;
            font-size: 40px;
            font-weight: bold;
            cursor: pointer;
        }
    </style>
    <?php
  perch_layout('getStarted/footer');?>

