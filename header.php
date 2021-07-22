<?

 include_once(dirname(__FILE__).'/language/english.php');
	$jscript_file=PATH_TO_LANGUAGE.$language."/jscript/".'jobseeker_login.js';

if(check_login("admin"))
{
 if(isset($_GET['jID']))
 {
  $session_array=array("sess_recruiterid"=>$_GET['rID'],"sess_recruiterlogin"=>"y");
  unset_session_value($session_array);
  if($row=getAnyTableWhereData(JOBSEEKER_LOGIN_TABLE,"jobseeker_id='".(int)tep_db_input($_GET['jID'])."'",'jobseeker_id'))
  {
   $session_array=array("sess_jobseekerid"=>$_GET['jID'],"sess_jobseekerlogin"=>"y");
   set_session_value($session_array);
  }
  else
  {
   $messageStack->add_session(MESSAGE_JOBSEEKER_ERROR, 'error');
   tep_redirect(tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_JOBSEEKERS,'selected_box=jobseekers'));
  }
 }
 else if($_GET['add']=='jobseeker')
 {
  $session_array=array("sess_jobseekerid"=>$_GET['jID'],"sess_jobseekerlogin"=>"y");
  unset_session_value($session_array);
 }
 else if(isset($_GET['rID']))
 {
  $session_array=array("sess_jobseekerid"=>$_GET['jID'],"sess_jobseekerlogin"=>"y");
  unset_session_value($session_array);
  if($row=getAnyTableWhereData(RECRUITER_TABLE,"recruiter_id='".(int)tep_db_input($_GET['rID'])."'",'recruiter_id'))
  {
   $session_array=array("sess_recruiterid"=>$_GET['rID'],"sess_recruiterlogin"=>"y");
   set_session_value($session_array);
  }
  else
  {
   $messageStack->add_session(MESSAGE_RECRUITER_ERROR, 'error');
   tep_redirect(tep_href_link(PATH_TO_ADMIN.FILENAME_ADMIN1_RECRUITERS,'selected_box=recruiters'));
  }
 }
 else if($_GET['add']=='recruiter')
 {
  $session_array=array("sess_recruiterid"=>$_GET['rID'],"sess_recruiterlogin"=>"y");
  unset_session_value($session_array);
 }
}
//////////////////////////////
if(strtolower($_SERVER['PHP_SELF'])=="/".PATH_TO_MAIN.FILENAME_JOB_DETAILS)
{
$job_name=getAnyTableWhereData(JOB_TABLE," job_id ='".$_GET['query_string']."'","job_title,job_short_description");
$meta_title=$job_name['job_title']."/".SITE_TITLE;
$meta_description="<META NAME='Keywords' CONTENT='".$job_name['job_title']."'/>
<META NAME='Description' CONTENT='".strip_tags($job_name['job_short_description'], '<a><b><i><u><>')."'/>";
}
else
{
$meta_title   = $obj_title_metakeyword->title;
$meta_description = $obj_title_metakeyword->metakeywords;
}
///////////////////////////////

$add_script='';
//autologin(); ///auto login
if(strtolower($_SERVER['PHP_SELF'])=="/".PATH_TO_MAIN.FILENAME_JOBSEEKER_RESUME2)
{
 $add_script=' set_current_emp();';
}
$add_script_file='';
if(strtolower($_SERVER['PHP_SELF'])=="/".PATH_TO_MAIN.FILENAME_INDEX)
{
 $add_script_file.='<script type="text/JavaScript" src="'.tep_href_link("jscript/common.js").'"></script>';
	$add_script_file.='';
}
else
{
 $add_script_file='<script type="text/JavaScript" src="'.tep_href_link(PATH_TO_LANGUAGE.$language."/jscript/optionlist.js").'"></script>';
 $add_script.='initOptionLists();';
}
$abs=strstr($_SERVER['REQUEST_URI'],'?');
$path1=(($abs)?(stristr($_SERVER['REQUEST_URI'],'language=')?substr($_SERVER['REQUEST_URI'],0,-2):$_SERVER['REQUEST_URI'].'&language='):$_SERVER['REQUEST_URI'].'?language=');
if(strtolower($_SERVER['PHP_SELF'])=="/".PATH_TO_MAIN.FILENAME_RECRUITER_POST_JOB)
{
 $add_script.='show_hide();';
}
$TREF_email_address='Email address';
$TR_password       ='Password';

$JOBSEEKER_REGISTER_NOW  ='<a href="'.tep_href_link(FILENAME_JOBSEEKER_REGISTER1).'" class="jobsite2">Register Now</a>';
if(check_login("jobseeker"))
{
	$jobseeker_login_form1    ='';
$INFO_TEXT_H_J_EMAIL11    = '';
$INFO_TEXT_H_J_PASSWORD11 = '';

}
else
{
$jobseeker_login_form1    = tep_draw_form('jobseeker_login_form1', FILENAME_JOBSEEKER_LOGIN,'','post', 'onsubmit="return ValidateForm(this)"').tep_draw_hidden_field('action','check');
$INFO_TEXT_H_J_EMAIL11    = tep_draw_input_field('TREF_email_address', $TREF_email_address,'class="textfield_effect1 login_input" onfocus="document.jobseeker_login_form1.TREF_email_address.value=\'\'"',false);
$INFO_TEXT_H_J_PASSWORD11 = tep_draw_input_field('TR_password', $TR_password, 'class="textfield_effect1 login_input" onfocus="document.jobseeker_login_form1.TR_password.value=\'\'"',false, 'password');
$BUTTON111                = '<input type="submit" class="login_button" value="login" alt="login" />';
}

$uri = $_SERVER['REQUEST_URI'];
$needle = '/';
$afterFirstSlash = substr($uri, strpos($uri, $needle) + 1);
$number = substr($afterFirstSlash,0,1);
if(is_numeric($number)){
	$afterSecondSlash = substr($afterFirstSlash, strpos($afterFirstSlash, $needle) + 1);
	$letter = substr($afterSecondSlash,0,1);
	if(ctype_alpha($letter)){
		$socialMenuScript = '<script type="text/javascript" src="//s7.addthis.com/js/300/addthis_widget.js#pubid=ra-5516e5ee43eb6127" async="async"></script>';
	}
}


define('HEADER_HTML','<!DOCTYPE html>
<html>

<head>
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
<title>'.$meta_title.'</title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
'.$meta_description.'
<link rel="stylesheet" type="text/css" href="'.tep_href_link("themes/sample9/stylesheet.css").'" >
<link href="//netdna.bootstrapcdn.com/font-awesome/4.1.0/css/font-awesome.min.css" rel="stylesheet">


<link rel="stylesheet" type="text/css" href="'.tep_href_link("themes/sample9/jquery.cookiebar.css").'" >
		<script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jquery/1.8.3/jquery.min.js"></script>
		<script type="text/javascript" src="'.tep_href_link("themes/sample9/jquery.cookiebar.js").'"></script>



<link href="http://fonts.googleapis.com/css?family=Roboto:400,700" rel="stylesheet" type="text/css">
<link href="http://fonts.googleapis.com/css?family=Oswald:300" rel="stylesheet" type="text/css">

<script type="text/JavaScript" src="'.tep_href_link(PATH_TO_LANGUAGE.$language."/jscript/page.js").'"></script>



'.$add_script_file.'
<script type="text/JavaScript">



function body_load()
{
 '.$add_script.'

}

</script>

<script type="text/javascript" src="'.tep_href_link("themes/sample9/jquery-2.1.0.min.js").'"></script>

<script type="text/javascript">
$( document ).ready(function() {
  $(".responsive_menu .button").click(function(){
	if ($(this).closest(".responsive_menu").hasClass("active")) {
		$(this).closest(".responsive_menu").removeClass("active");
		$(this).closest(".responsive_menu").find(".menu").css({"display" : "none"});
		$(this).closest(".responsive_menu").find(".menu").slideUp(5);
	} else {
		$(this).closest(".responsive_menu").addClass("active");
		$(this).closest(".responsive_menu").find(".menu").slideDown(50);
	}
  })
});
</script>

<!-- Begin Cookie Consent plugin by Silktide - http://silktide.com/cookieconsent -->
<script type="text/javascript">
    window.cookieconsent_options = {"message":"This website uses cookies to ensure you get the best experience on Silicon Armada","dismiss":"Got it!","learnMore":"More info","link":"http://www.siliconarmada.com/privacy.php","theme":"dark-bottom"};
</script>

<script type="text/javascript" src="//s3.amazonaws.com/cc.silktide.com/cookieconsent.latest.min.js"></script>
<!-- End Cookie Consent plugin -->

</head>

<body>
<?php include_once("analyticstracking.php") ?>
<div class="container">
	<div class="header">
        <div class="centered">
            <ul class="nav">
                <li><a href="'.tep_href_link('').'">'.INFO_TEXT_F_HOME.'</a></li>
            	<li><a href="'.tep_href_link(FILENAME_ARTICLE).'">Blog</a></li>
                
            </ul>

			<div class="responsive_menu">
				<span class="button"></span>

				<div class="menu">
                	<ul class="nav">
                        <li><a href="'.tep_href_link('').'">'.INFO_TEXT_F_HOME.'</a></li>
						<li><a href="'.tep_href_link(FILENAME_ARTICLE).'">Blog</a></li>
                        <li>
                            <a href="http://www.linkedin.com/company/siliconarmada" target="_blank"><img src="/img/socialMediaIcons/linkedin.png" width="18" height="18" alt="" ></a>

                            <a href="http://facebook.com/siliconarmada" target="_blank"><img src="/img/socialMediaIcons/fb.png" width="18" height="18" alt="" ></a>
							<a href="https://plus.google.com/+SiliconArmadaSA" target="_blank"><img src="/img/socialMediaIcons/google_plus.png" width="18" height="18" alt="" ></a>
							<a href="https://www.pinterest.com/siliconarmada" target="_blank"><img src="/img/socialMediaIcons/pinterest.png" width="18" height="18" alt="" ></a>
							<a href="http://twitter.com/siliconarmada" target="_blank"><img src="/img/socialMediaIcons/twitter.png" width="18" height="18" alt="" ></a>
                            </li>
                    </ul>
                </div>



            </div>
<a href="'.tep_href_link('').'"><div id="magnifying-glass"><p></p></div></a>
            <div class="right_box">
                <a href="http://www.linkedin.com/company/siliconarmada" target="_blank"><img src="/img/socialMediaIcons/linkedin.png" width="20" height="20" alt="" ></a>

                <a href="http://facebook.com/siliconarmada" target="_blank"><img src="/img/socialMediaIcons/fb.png" width="20" height="20" alt="" ></a>
                <a href="https://plus.google.com/+SiliconArmadaSA" target="_blank"><img src="/img/socialMediaIcons/google_plus.png" width="20" height="20" alt="" ></a>
                <a href="https://www.pinterest.com/siliconarmada" target="_blank"><img src="/img/socialMediaIcons/pinterest.png" width="20" height="20" alt="" ></a>
                <a href="http://twitter.com/siliconarmada" target="_blank"><img src="/img/socialMediaIcons/twitter.png" width="20" height="20" alt="" ></a>
                </div>

        </div>
    </div>
');

?>
