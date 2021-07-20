<?
/*
***********************************************************
**********# Name          : SA   #**********
**********# Company       : SILICON ARMADA                 #**********
**********# Copyright (c) WWW.SILICONARMADA.COM 2016     #**********
***********************************************************
*/
session_cache_limiter('private_no_expire');
include_once("include_files.php");
ini_set('max_execution_time','0');
include_once(PATH_TO_MAIN_PHYSICAL_LANGUAGE.$language.'/'.FILENAME_JOB_SEARCH);
$template->set_filenames(array('job_search' => 'job_search.htm','job_search_result'=>'job_search_result1.htm'));
include_once(FILENAME_BODY);
$jscript_file=PATH_TO_LANGUAGE.$language."/jscript/".'jobs_search.js';
$state_error=false;
//print_r($_GET);
$action="search";
// initialize
if(tep_not_null($_GET['keyword']) )
{
 $keyword=tep_db_prepare_input($_GET['keyword']);
}
if(tep_not_null($_GET['location']))
{
 $location=tep_db_prepare_input($_GET['location']);
}
$p=$_SESSION["p"];
if(tep_not_null($_GET['p']))
{
    $p=tep_db_prepare_input($_GET['p']);
}
$c=$_SESSION["c"];
if(tep_not_null($_GET['c']))
{
    $c=tep_db_prepare_input($_GET['c']);
}
$g=$_SESSION["g"];
if(tep_not_null($_GET['g']))
{
    $p=tep_db_prepare_input($_GET['g']);
}
$r1=$_SESSION["r1"];
if(tep_not_null($_GET['r1']))
{
    $r1=tep_db_prepare_input($_GET['r1']);
}
$r2=$_SESSION["r2"];
if(tep_not_null($_GET['r2']))
{
    $r2=tep_db_prepare_input($_GET['r2']);
}
// search
    $search = array ("'[\s]+'");
    $replace = array (" ");
    $keyword = preg_replace($search, $replace, $keyword);
    $keyword = mysql_real_escape_string($keyword);
    $keyword = trim($keyword, " ");

    $location = preg_replace($search, $replace, $location);
    $location = mysql_real_escape_string($location);
    $location = trim($location, " ");
   // keyword ends //////
   //   location starts //////
   //   location ends //////
   // inserted date end //
   // company starts //

if($_GET['r1'] == 'Employer' and $_GET['r2'] == 'Agency'){//all
    $ct="";
    $_SESSION["r1"]="Employer";
    $_SESSION["r2"]="Agency";
    $chk_ct='<div class="recruiter_type_chk_search">
                    <p><input tabindex="7" id="employers" type="checkbox" name="r1" value="Employer" checked><label for="employers">Employers</label></p>
                    <p><input tabindex="8" id="agencies" type="checkbox" name="r2" value="Agency" checked><label for="agencies">Agencies</label></p>
                </div>
                            ';
}
else if($_GET['r1'] != 'Employer' and $_GET['r2'] != 'Agency'){//none
    $ct="";
    $_SESSION["r1"]="";
    $_SESSION["r2"]="";
    $chk_ct='<div class="recruiter_type_chk_search">
                    <p><input tabindex="7" id="employers" type="checkbox" name="r1" value="Employer"><label for="employers">Employers</label></p>
                    <p><input tabindex="8" id="agencies" type="checkbox" name="r2" value="Agency"><label for="agencies">Agencies</label></p>
                </div>
                            ';
}
elseif($_GET['r1'] == 'Employer' and $_GET['r2'] != 'Agency'){//employer
    $_SESSION["r1"]="Employer";
    $_SESSION["r2"]="";
    $ct=" and job_recruiter_type ='Employer'";
    $chk_ct='<div class="recruiter_type_chk_search">
                    <p><input tabindex="7" id="employers" type="checkbox" name="r1" value="Employer" checked><label for="employers">Employers</label></p>
                    <p><input tabindex="8" id="agencies" type="checkbox" name="r2" value="Agency"><label for="agencies">Agencies</label></p>
                </div>
                            ';
}
elseif($_GET['r2'] == 'Agency' and $_GET['r1'] != 'Employer')//agency
{
    $_SESSION["r2"]="Agency";
    $_SESSION["r1"]="";
    $ct=" and job_recruiter_type ='Agency'";
    $chk_ct='<div class="recruiter_type_chk_search">
                    <p><input tabindex="7" id="employers" type="checkbox" name="r1" value="Employer"><label for="employers">Employers</label></p>
                    <p><input tabindex="8" id="agencies" type="checkbox" name="r2" value="Agency" checked><label for="agencies">Agencies</label></p>
                </div>
                            ';
}
$jtIndeed="";
if($_GET["p"] == "t" and $_GET['c'] == "t" and $_GET['g'] == "t"){//all
    $jt="";
    $_SESSION["c"]="t";
    $_SESSION["g"]="t";
    $_SESSION["p"]="t";
    $chk_jt='<div class="job_type_chk_search"><h2><p><input id="permanent" type="checkbox" name="p" value="t"  checked/><label for="permanent">Permanent</label></p></h2>
                        <h2><p><input id="contract" type="checkbox" name="c" value="t"   checked/><label for="contract">Contract</label></p></h2>
                        <h2><p><input id="graduate" type="checkbox" name="g" value="t"   checked/><label for="graduate">Graduate / Internships</label></p></h2></div>';
}elseif($_GET['p'] != "t" and $_GET['c'] != "t" and $_GET['g'] != "t"){//none
    $jt="";
    $_SESSION["c"]="";
    $_SESSION["g"]="";
    $_SESSION["p"]="";
    $chk_jt='<div class="job_type_chk_search"><h2><p><input id="permanent" type="checkbox" name="p" value="t"  /><label for="permanent">Permanent</label></p></h2>
                        <h2><p><input id="contract" type="checkbox" name="c" value="t"  /><label for="contract">Contract</label></p></h2>
                        <h2><p><input id="graduate" type="checkbox" name="g" value="t"  /><label for="graduate">Graduate / Internships</label></p></h2></div>';
}elseif($_GET['p'] == "t" and $_GET['c'] == "t" and $_GET['g'] != "t"){//except grad
    $jt=" and (job_type =3 or job_type =4 ) ";
    $_SESSION["c"]="t";
    $_SESSION["g"]="";
    $_SESSION["p"]="t";
    $chk_jt='<div class="job_type_chk_search"><h2><p><input id="permanent" type="checkbox" name="p" value="t"   checked/><label for="permanent">Permanent</label></p></h2>
                        <h2><p><input id="contract" type="checkbox" name="c" value="t"   checked/><label for="contract">Contract</label></p></h2>
                        <h2><p><input id="graduate" type="checkbox" name="g" value="t"  /><label for="graduate">Graduate / Internships</label></p></h2></div>';
}elseif($_GET['p'] == "t" and $_GET['c'] != "t" and $_GET['g'] == "t"){//except contract
    $jt=" and ( job_type =6 or job_type =4 ) ";
    $_SESSION["c"]="";
    $_SESSION["g"]="t";
    $_SESSION["p"]="t";
    $chk_jt='<div class="job_type_chk_search"><h2><p><input id="permanent" type="checkbox" name="p" value="t"  checked /><label for="permanent">Permanent</label></p></h2>
                        <h2><p><input id="contract" type="checkbox" name="c" value="t"  /><label for="contract">Contract</label></p></h2>
                        <h2><p><input id="graduate" type="checkbox" name="g" value="t"  checked /><label for="graduate">Graduate / Internships</label></p></h2></div>';
}
elseif($_GET['p'] != "t" and $_GET['c'] == "t" and $_GET['g'] == "t"){//except permanent
    $jt=" and (job_type =6 or job_type =3 ) ";
    $_SESSION["c"]="t";
    $_SESSION["g"]="t";
    $_SESSION["p"]="";
    $chk_jt='<div class="job_type_chk_search"><h2><p><input id="permanent" type="checkbox" name="p" value="t"  /><label for="permanent">Permanent</label></p></h2>
                        <h2><p><input id="contract" type="checkbox" name="c" value="t"  checked /><label for="contract">Contract</label></p></h2>
                        <h2><p><input id="graduate" type="checkbox" name="g" value="t"   checked/><label for="graduate">Graduate / Internships</label></p></h2></div>';
}
elseif($_GET['p'] != "t" and $_GET['c'] != "t" and $_GET['g'] == "t"){//grad
    $jtIndeed="internship";
    $jt=" and (job_type =6) ";
    $_SESSION["c"]="";
    $_SESSION["g"]="t";
    $_SESSION["p"]="";
    $chk_jt='<div class="job_type_chk_search"><h2><p><input id="permanent" type="checkbox" name="p" value="t"  /><label for="permanent">Permanent</label></p></h2>
                        <h2><p><input id="contract" type="checkbox" name="c" value="t"  /><label for="contract">Contract</label></p></h2>
                        <h2><p><input id="graduate" type="checkbox" name="g" value="t"  checked /><label for="graduate">Graduate / Internships</label></p></h2></div>';
}
elseif($_GET['p'] == "t" and $_GET['c'] != "t" and $_GET['g'] != "t"){//permanent
    $jtIndeed="fulltime";
    $jt=" and ( job_type =4) ";
    $_SESSION["c"]="";
    $_SESSION["g"]="";
    $_SESSION["p"]="t";
    $chk_jt='<div class="job_type_chk_search"><h2><p><input id="permanent" type="checkbox" name="p" value="t"  checked /><label for="permanent">Permanent</label></p></h2>
                        <h2><p><input id="contract" type="checkbox" name="c" value="t"  /><label for="contract">Contract</label></p></h2>
                        <h2><p><input id="graduate" type="checkbox" name="g" value="t"  /><label for="graduate">Graduate / Internships</label></p></h2></div>';
}
elseif($_GET['p'] != "t" and $_GET['c'] == "t" and $_GET['g'] != "t"){//contract
    $jtIndeed="contract";
    $jt=" and ( job_type =3 ) ";
    $_SESSION["c"]="t";
    $_SESSION["g"]="";
    $_SESSION["p"]="";
    $chk_jt='<div class="job_type_chk_search"><h2><p><input id="permanent" type="checkbox" name="p" value="t"  /><label for="permanent">Permanent</label></p></h2>
                        <h2><p><input id="contract" type="checkbox" name="c" value="t"  checked /><label for="contract">Contract</label></p></h2>
                        <h2><p><input id="graduate" type="checkbox" name="g" value="t"  /><label for="graduate">Graduate / Internships</label></p></h2></div>';
}

$searchCombo= trim($keyword." ".$location," ");
$selectJob="SELECT * from jobs ";
$join=" as j left outer join recruiter as r on (j.recruiter_id=r.recruiter_id) left outer join recruiter_login as rl on (j.recruiter_id=rl.recruiter_id)";
if($keyword!="" and $location!=""){
    $query = "SELECT *, MATCH(job_title) AGAINST (\"$keyword\" IN BOOLEAN MODE) as rel1,MATCH(job_description) AGAINST (\"$keyword\" IN BOOLEAN MODE) as rel2 from jobs $join where MATCH(job_title, job_description) AGAINST (\"$keyword\" IN BOOLEAN MODE) and rl.recruiter_status='Yes' $jt $ct and job_location LIKE '%$location%'  order by job_featured='Yes' desc, (rel1*2)+(rel2)desc";
}
elseif($searchCombo==""){
    $query = "$selectJob $join where job_status='Yes' and rl.recruiter_status='Yes' $jt $ct order by job_featured='Yes' desc";//add more sorts, random
}
elseif($keyword!="" and $location!=""){
    $query = "$selectJob $join where job_location LIKE '%$location%' and job_title LIKE '%$keyword%' and rl.recruiter_status='Yes' $jt $ct and job_status='Yes' order by job_featured='Yes' desc";//add more sorts, random
}
elseif($keyword!=""){
    $query = "$selectJob $join where job_title LIKE '%$keyword%' or(r.recruiter_company_name LIKE '$keyword%') and rl.recruiter_status='Yes' $jt $ct and job_status='Yes' order by job_featured='Yes' desc";//add more sorts, random
}
elseif($location!=""){
    $query = "$selectJob $join where job_location LIKE '%$location%' and rl.recruiter_status='Yes' $jt $ct and job_status='Yes' order by job_featured='Yes' desc";//add more sorts, random
}
//echo $query;

      //ORDER the job list above - in future by views ascending ----------------------------------------------------------------^^^---------------------------------------
                  $starting=0;
                  $recpage = 16;
                  $obj = new pagination_class($query,$starting,$recpage,$keyword,$location);//$listQueryIDs
                  $result = $obj->result;

                  $x=tep_db_num_rows($result);
                  $content='';
                  $count=1;
                  $count1=1;

                  $i_page=0;
                  $res=mysql_query($query);

                  while($row=mysql_fetch_assoc($res)){
                          $i_page++;
                  }

         if(mysql_num_rows($result)!=0)
         {
          while($row = tep_db_fetch_array($result))
          {
           $ide=$row["job_id"];
           $recruiter_logo='';
           $company_logo=$row['recruiter_logo'];
           $title_format=encode_category($row['job_title']);
           $query_string=encode_string("job_id=".$ide."=job_id");
          if($row['recruiter_id']!= 826){
                          if(tep_not_null($company_logo) && is_file(PATH_TO_MAIN_PHYSICAL.PATH_TO_LOGO.$company_logo))
           $recruiter_logo=tep_image(FILENAME_IMAGE."?image_name=".PATH_TO_LOGO.$company_logo."&size=120");
          }
          else{
              $recruiter_logo= '<img src= '. $row['jobg8_logo_url'] .'>';
          }
                          $email_job    ='<a href="'.tep_href_link(FILENAME_TELL_TO_FRIEND,'query_string='.$query_string).'" title="'.tep_db_output(INFO_TEXT_EMAIL_THIS_JOB).'" target="_blank">'.INFO_TEXT_EMAIL_THIS_JOB.'</a>';
                          $apply_job    ='<a href="'.tep_href_link(FILENAME_APPLY_NOW,'query_string='.$query_string).'" title="'.tep_db_output(INFO_TEXT_APPLY_TO_THIS_JOB).'" target="_blank">'.INFO_TEXT_APPLY_TO_THIS_JOB.'</a>';
           if($row['job_featured']=='Yes')
                          {
                           $row_selected='jobSearchRowFea';
                           $featuredClass='';
                           $featured = "border-left:10px solid #0071bc;";

                          }
                          else
                          {
                          $featured = "";
                          $featuredClass ="";
                           $row_selected='jobSearchRow1';
                              $count++;
                          }

                // If the recruiter ID is 213 (indeeds), customize the title
                // link to go directly to the job and not the more details page.
                $job_title = tep_db_output($row['job_title']);
                $job_link = tep_href_link($ide.'/'.$title_format.'.html');

          if($row['jobg8_logo_url'] == null){
              $company_name = tep_db_output($row['recruiter_company_name']);
          }
          else{
                  $company_name = $row['jobg8_company'];
              }
              if($company_name == "None"){//jobs have this often
                          $company_name ="";
                      }

              //-----INSERT RECRUITER ID THAT HAS IFRAME BLOCKED TO USE NO IFRAME AND USE JUST OUR SITE INSTEAD-----
      //LIST OF COMPANIES HERE NOW IS:
              if($row["recruiter_id"] == 419 OR $row["recruiter_id"] == 273 OR $row["recruiter_id"] == 335 OR $row["recruiter_id"] == 349 OR $row["recruiter_id"] == 322 OR $row["recruiter_id"] == 62 OR $row["recruiter_id"] == 59 OR $row["recruiter_id"] == 86 OR $row["recruiter_id"] == 423 OR $row["recruiter_id"] == 264 OR $row["recruiter_id"] == 20 OR $row["recruiter_id"] == 66 OR $row["recruiter_id"] == 663 OR $row["recruiter_id"] == 417){
                  //new tab
                  $post_url_click = $row['url'];
                  $clickLink = '<a class="jobAnchor" target="_tab" href="'.$post_url_click.'">';
              }else{
                  //iframe
                  $clickLink = '<a class="jobAnchor" href="'.$job_link.'">';
              }

              if($row["job_type"]==4){
                  $job_type_word='<div class="job_type">Permanent</div>';
              }
              elseif($row["job_type"]==3){
                  $job_type_word='<div class="job_type">Contract</div>';
              }
              elseif($row["job_type"]==6){
                  $job_type_word='<div class="job_type">Graduate</div>';
              }
              else{
                  $job_type_word='';
              }

              if($row["level"] !="" and $row["level"] !=null) {
                  $job_level_word = '<div class="job_level">'.$row["level"].'</div>';
              }
              else{
                  $job_level_word='';
              }

              $skill1 = $row["key1"];
              $skill2 = $row["key2"];
              $skill3 = $row["key3"];

              if($skill1 != "" or $skill1 != NULL){
                  $tag1 = '<div class="skill">'.$skill1.'</div>';
              }
              else{
                  $tag1 = '';
              }
              if($skill2 != "" or $skill2 != NULL){
                  $tag2 = '<div class="skill">'.$skill2.'</div>';
              }
              else{
                  $tag2 = '';
              }
              if($skill3 != "" or $skill3 != NULL){
                  $tag3 = '<div class="skill">'.$skill3.'</div>';
              }
              else{
                  $tag3 = '';
              }

              $template->assign_block_vars('job_search_result',
                  array(
                      'recruiter_id' => $row["recruiter_id"],
                      'row_selected' => $row_selected,
                      'check_box' => (($row['post_url']=='Yes'  )?'':'<input type="checkbox" name="apply_job" value="'.$query_string.'">'),
                      'job_title' => $job_title,
                      'company_name' => $company_name,
                      'location' =>$row['job_location'],
                      'experience' =>tep_db_output(calculate_experience($row['min_experience'],$row['max_experience'])),
                      'salary' =>(tep_not_null($row['job_salary']))?tep_db_output($row['job_salary']):'',
                      'salary_class' =>(tep_not_null($row['job_salary']))?'':'result_hide',
                      'description' => nl2br(tep_db_output(strip_tags($row['job_short_description']))),
                      'apply_before' => tep_date_long($row['expired']),
                      'logo'      => $recruiter_logo,
                      'email_job' => $email_job,
                      'apply_job' => $apply_job,
                      'job_link' => $job_link,
                      'featured' => $featured,
                      'featuredClass' => $featuredClass,
                      'clickLink' => $clickLink,
                      'skill1' => $tag1,
                      'skill2' => $tag2,
                      'skill3' => $tag3,
                      'job_type_word' => $job_type_word,
                      'job_level_word' => $job_level_word,
                  )
              );
           /////////////////////////////////////////////////////////
           if($check_row=getAnytableWhereData(JOB_STATISTICS_TABLE,"job_id='".$ide."'",'viewed'))
           {
            $sql_data_array=array('job_id'=>$ide,
                                  'viewed'=>($check_row['viewed']+1)
                                  );
            tep_db_perform(JOB_STATISTICS_TABLE, $sql_data_array, 'update', "job_id='".$ide."'");
           }
           else
           {
            $sql_data_array=array('job_id'=>$ide,
                                  'viewed'=>1
                                  );
            tep_db_perform(JOB_STATISTICS_TABLE, $sql_data_array);
           }

           /////////////////////////////////////////////////////////end of while loop
          }
             if($keyword !== "" && $location !== ""){
                 $searchResultsFound = ''.$i_page.' results found for "'.$keyword.'" in "'.$location.'"';
             }
             elseif($keyword == "" && $location == ""){
                 $searchResultsFound = ''.$i_page.' results found ';
             }
             elseif($location !== "" && $location !== null){
                 $searchResultsFound = ''.$i_page.' results found in "'.$location.'"';
             }
             elseif($keyword !== "" && $keyword !== null){
                 $searchResultsFound = ''.$i_page.' results found for "'.$keyword.'"';
             }

                   $template->assign_vars(array('pages'=>$obj->anchors,'total_pages'=>$obj->total, 'total_items'=>$i_page, 'keyword'=>$keyword, 'location'=>$location));

          $plural=($x1=="1")?INFO_TEXT_JOB:INFO_TEXT_JOBS;
          $template->assign_vars(array('total'=>SITE_TITLE." ".INFO_TEXT_HAS_MATCHED." <font color='red'><b>$x1</b></font> ".$plural." ".INFO_TEXT_TO_YOUR_SEARCH_CRITERIA));
         }
         else//no results
         {
          $template->assign_vars(array('content_hide'=>'result_hide','total'=>SITE_TITLE." ".INFO_TEXT_HAS_NOT_MATCHED." <br><br>&nbsp;&nbsp;&nbsp;", 'total_items'=>$i_page));

         }

if($i_page == 0 && $keyword !== "" && $location !== ""){
    $searchResultsFound = ''.$i_page.' results found for "'.$keyword.'" in "'.$location.'"';
}
elseif($i_page == 0 && $keyword == "" && $location == ""){
    $searchResultsFound = ''.$i_page.' results found ';
}
elseif($i_page == 0 && $location !== ""){
    $searchResultsFound = ''.$i_page.' results found in "'.$location.'"';
}
elseif($i_page == 0 && $keyword !== ""){
    $searchResultsFound = ''.$i_page.' results found for "'.$keyword.'"';
}

$key1=(tep_not_null($keyword)?$key1=$keyword:'keyword');
 $loc1=(tep_not_null($location)?$loc1=$location:'location');
 $template->assign_vars(array( 'hidden_fields' => $hidden_fields,
  'HEADING_TITLE'          => HEADING_TITLE,
  'hidden_fields1'          => $hidden_fields1,
  'form'                   => tep_draw_form('page', FILENAME_JOB_SEARCH,($edit?'sID='.$save_search_id:''),'get'),
  'form1'                  => tep_draw_form('search1', FILENAME_JOB_SEARCH,'','get').tep_draw_hidden_field('action','search'),
  'button'                 => tep_image_submit(PATH_TO_BUTTON.'button_refine_search.jpg', IMAGE_SEARCH),
  'INFO_TEXT_KEYWORD'      => INFO_TEXT_KEYWORD,
  'INFO_TEXT_KEYWORD1'     => tep_draw_input_field('keyword', $key1,'style="font-size: 12px;color: #626262; width:120;"',false),
  'INFO_TEXT_LOCATION'     => INFO_TEXT_LOCATION,
  'INFO_TEXT_LOCATION1'    => tep_draw_input_field('location', $loc1 ,'style="font-size: 12px;color: #626262; width:120;"',false),
  'INFO_TEXT_APPLY_NOW'    => (($x>0)?INFO_TEXT_APPLY_NOW:''),
  'INFO_TEXT_APPLY_NOW1'   => (($x>0)?INFO_TEXT_APPLY_NOW1:''),
  'INFO_TEXT_APPLY_ARROW'  => (($x>0)?tep_image('img/job_search_arrow.gif',''):''),
  'INFO_TEXT_APPLY_BUTTON' => (($x>0)?(check_login("jobseeker")?tep_image_button(PATH_TO_BUTTON.'button_apply_selectedjob.gif', IMAGE_APPLY,'onclick="ckeck_application(\'\');" style="cursor:pointer;"'):tep_image_button(PATH_TO_BUTTON.'button_registered_user.png', IMAGE_APPLY,'onclick="ckeck_application(\'\');" style="cursor:pointer;"').' '.tep_image_button(PATH_TO_BUTTON.'button_new_user.png', IMAGE_APPLY,'onclick="ckeck_application(\'new\');" style="cursor:pointer;"')):''),
  'INFO_TEXT_LOCATION_NAME'=> INFO_TEXT_LOCATION_NAME,
  'INFO_TEXT_EXPERIENCE'   => INFO_TEXT_EXPERIENCE,
		'INFO_TEXT_SALARY'       => INFO_TEXT_SALARY,
  'INFO_TEXT_APPLY_BEFORE' => INFO_TEXT_APPLY_BEFORE,
  'save_search'            => tep_draw_form('save_search', FILENAME_JOB_SEARCH,($edit?'sID='.$save_search_id:''),'get','onsubmit="return ValidateForm(this)"').tep_draw_hidden_field('action1','save_search'),
  'INFO_TEXT_ALERT_TEXT'   => (($action1=='save_search')?'':"<a class='size14 gray lato no_underline' href='#' onclick='document.save_search.submit();'>".INFO_TEXT_ALERT_TEXT."</a>"),
  'INFO_TEXT_ALERT_IMAGE'  => (($action1=='save_search')?'':tep_image_submit('img/alert_icon.jpg','')),
  'JOB_SEARCH_LEFT'        => JOB_SEARCH_LEFT,
		'INFO_TEXT_COMPANY_NAME' => INFO_TEXT_COMPANY_NAME,
  'INFO_TEXT_JSCRIPT_FILE'  => $jscript_file,
  //'save_button'            => tep_image_submit(PATH_TO_BUTTON.'button_save.gif', IMAGE_SAVE).($action1=='save_search'?'&nbsp;'.'<a href="'.tep_href_link(FILENAME_JOBSEEKER_LIST_OF_SAVED_SEARCHES).'">'.tep_image(PATH_TO_BUTTON.'button_cancel.gif', IMAGE_CANCEL).'</a>':'').' <a href="'.tep_href_link(FILENAME_JOB_SEARCH).'">'.tep_image(PATH_TO_BUTTON.'button_back.gif', IMAGE_BACK).'</a>',
                      ));

include_once("i-indeed.php");

$template->assign_vars(array(
    'indeed-api' => $indeedApiLogo,
    'indeed-job' => $job[0],
    'indeed-job1' => $job[1],
    'chk_ct' => $chk_ct,
 'chk_jt' => $chk_jt,
 'searchResultsFound'=>$searchResultsFound,
 'RIGHT_BOX_WIDTH' => RIGHT_BOX_WIDTH1,
 'RIGHT_HTML' => RIGHT_HTML,
 'update_message' => $messageStack->output()));

 $template->pparse('job_search_result');//IMPLODE the ids!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!

//only get what we need from jobs table

// weeding out results that are no longer valid
function jobs($ids, $jobType) {//put job ids in here, include and in $jobType
    return mysql_query("SELECT job_id,job_title,job_location,job_short_description,job_description,job_type,job_featured,url,jobg8_company,key1,key2,key3,level,job_recruiter_type FROM jobs WHERE $jobType job_status='Yes' job_id IN ($ids)");//change where and to recruiter type for all in selection

    //add any html thats needed in here to the job_search_result object

//iframe,jobg8, skills, levels, job type , company type

    //job type can narrow down the jobs too!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
}
function getCompanies($ids,$whereAnd) {//put recruiter ids in here, company TYPE narrow down, - logos, company names
    return mysql_query("SELECT recruiter_id,recruiter_company_name,recruiter_logo FROM recruiters WHERE $whereAnd recruiter_id IN ($ids)");

//do i need to get the info row by row here? i think so

    //now i will have all the recruiter stuff
}
//when we get those 2 tables info back we need to merge them into one i think

//most jobs are gone if text was entered into search, if no text blank search then
//we must search everything, so no need for full text - select all job ids with status yes - featured up top
?>