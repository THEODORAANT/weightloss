<?php
  if (!defined('PERCH_MEMBERS_SESSION_TIME')) define('PERCH_MEMBERS_SESSION_TIME', '5 DAYS');
    if (!defined('PERCH_MEMBERS_COOKIE'))       define('PERCH_MEMBERS_COOKIE', 'p_m');

    # include the API
    include('../../../../core/inc/api.php');

    $API  = new PerchAPI(1.0, 'perch_members');
    $Lang = $API->get('Lang');

    # include your class files
    include('../PerchMembers_Members.class.php');
    include('../PerchMembers_Member.class.php');
    include('../PerchMembers_Auth.class.php');

    # Set the page title
    $Perch->page_title = $Lang->get('Impersonate member');

    # Do anything you want to do before output is started
    include('../modes/members.impersonate.pre.php');

    # Top layout
    include(PERCH_CORE . '/inc/top.php');

    # Display your page
    include('../modes/members.impersonate.post.php');

    # Bottom layout
    include(PERCH_CORE . '/inc/btm.php');
?>

