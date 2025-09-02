 <?php
 ?>

    <?php  // output the top of the page
    perch_layout('product/header', [
        'page_title' => perch_page_title(true),
    ]);
if (perch_member_logged_in()) {
perch_member_form('profile.html');
}
    ?>


        <?php
      perch_layout('getStarted/footer');?>
