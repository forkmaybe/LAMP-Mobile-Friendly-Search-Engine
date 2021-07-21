<?
/*
***********************************************************
**********# Name          : SA   #**********
**********# Company       : SILICON ARMADA                 #**********
**********# Copyright (c) WWW.SILICONARMADA.COM 2014     #**********
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
$action = (isset($_GET['action']) ? $_GET['action'] : '');
$action1 = (isset($_GET['action1']) ? $_GET['action1'] : '');
$sID = (isset($_GET['sID']) ?(int)$_GET['sID'] : '');
$edit=false;
$search_name='';
if(tep_not_null($sID))
{
 if(!$row_check=getAnyTableWhereData(SEARCH_JOB_RESULT_TABLE,"jobseeker_id='".$_SESSION['sess_jobseekerid']."' and id='".tep_db_input($sID)."'",'id,title_name'))
 {
  $messageStack->add_session(MESSAGE_ERROR_SAVED_SERCH_NOT_EXIST,'error');
  tep_redirect(FILENAME_JOBSEEKER_LIST_OF_SAVED_SEARCHES);
 }
 $search_name=$row_check['title_name'];
 $save_search_id=(int)$sID;
 $edit=true;
}
// initialize
if(tep_not_null($_GET['keyword']) && (($_GET['keyword']!='keyword') && ($_GET['keyword']!='Search by keywords') && ($_GET['keyword']!='mots-cl�s de recherche d\'emploi')) )
{
 $keyword=tep_db_prepare_input($_GET['keyword']);
}
if(tep_not_null($_GET['location']) && ($_GET['location']!='location'))
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

if(tep_not_null($_GET['job_post_day']))
{
 $job_post_day=tep_db_prepare_input($_GET['job_post_day']);
}
if(tep_not_null($_GET['inserted_date']))
{
 $inserted_date=tep_db_prepare_input($_GET['inserted_date']);
}
if(tep_not_null($_GET['word1']))
{
 $word1=tep_db_prepare_input($_GET['word1']);
}
if(tep_not_null($_GET['country']))
{
 $country=(int)tep_db_prepare_input($_GET['country']);
}
$zip_code       = tep_db_prepare_input($_GET['zip_code']);
$radius         = (int)tep_db_prepare_input($_GET['radius']);
$search_zip_code=1;
if(tep_not_null($zip_code))
$search_zip_code= 2;
if(tep_not_null($_GET['state']))
{
 if(is_array($_GET['state']))
  $state=implode(',',tep_db_prepare_input($_GET['state']));
 else
  $state=tep_db_prepare_input($_GET['state']);
 if($state[0]==',')
  $state=substr($state,1);
}
elseif(tep_not_null($_GET['state1']))
{
 $state=tep_db_prepare_input($_GET['state1']);
}
if(tep_not_null($_GET['job_category']))
{
 $job_category=$_GET['job_category'];
 $job_category1=implode(",",$job_category);
}
if(tep_not_null($_GET['experience']))
{
 $experience=$_GET['experience'];
}
if(tep_not_null($action1))
{
 switch($action1)
 {
  case 'save_search':
   if(!check_login("jobseeker"))
   {
    $_SESSION['REDIRECT_URL']=$_SERVER['REQUEST_URI'];
    $messageStack->add_session(LOGON_FIRST_MESSAGE, 'error');
    tep_redirect(FILENAME_JOBSEEKER_LOGIN);
   }
   $error=false;
   if(tep_not_null($_GET['TR_search_name']))
   {
    if($edit)
    {
     if($row_check=getAnyTableWhereData(SEARCH_JOB_RESULT_TABLE,"jobseeker_id='".$_SESSION['sess_jobseekerid']."' and title_name='".tep_db_input($_GET['TR_search_name'])."' and id!='".$save_search_id."'"))
     {
      $error=true;
      $messageStack->add(MESSAGE_ERROR_SAVED_SERCH_ALREADY_EXIST, 'error');
     }
     if(!$error)
     {
      $sql_data_array=array( 'updated'=>'now()',
                             'title_name '=>tep_db_prepare_input($_GET['TR_search_name']) ,
                             'keyword'=>$keyword,
                             'location'=>$location,
          'p'=>$p,
          'c'=>$c,
          'g'=>$g,
          'r1'=>$r1,
          'r2'=>$r2,
						                     	 'company'=>$company,
                             'word1'=>$word1,
                             'country'=>$country,
                             'state'=>$state,
                             'industry_sector'=>$job_category1,
                             'experience'=>$experience,
                             'zip_code'=>$zip_code,
                             'radius'=>$radius,
                             'jobseeker_id'=>$_SESSION['sess_jobseekerid'],
                             
                            );
      //print_r($sql_data_array); die();
      tep_db_perform(SEARCH_JOB_RESULT_TABLE, $sql_data_array,'update',"id='".$save_search_id."'");
      $messageStack->add_session(MESSAGE_SUCCESS_UPDATED, 'success');
      tep_redirect(FILENAME_JOBSEEKER_LIST_OF_SAVED_SEARCHES);
     }
    }
    else
    {
     if($row_check=getAnyTableWhereData(SEARCH_JOB_RESULT_TABLE,"jobseeker_id='".$_SESSION['sess_jobseekerid']."' and title_name='".tep_db_input($_GET['TR_search_name'])."'"))
     {
      $error=true;
      $messageStack->add(MESSAGE_ERROR_SAVED_SERCH_ALREADY_EXIST, 'error');
     }
     if(!$error)
     {
      $sql_data_array=array( 'inserted'=>'now()',
                             'title_name '=>tep_db_prepare_input($_GET['TR_search_name']) ,
                             'keyword'=>$keyword,
                             'location'=>$location,
                            'p'=>$p,
                            'c'=>$c,
                            'g'=>$g,
                            'r1'=>$r1,
                            'r2'=>$r2,
                             'company'=>$company,
                             'word1'=>$word1,
                             'country'=>$country,
                             'state'=>$state,
                             'industry_sector'=>$job_category1,
                             'experience'=>$experience,
                             'zip_code'=>$zip_code,
                             'radius'=>$radius,
                             'jobseeker_id'=>$_SESSION['sess_jobseekerid'],
          
                           );
      tep_db_perform(SEARCH_JOB_RESULT_TABLE, $sql_data_array);
      $messageStack->add_session(MESSAGE_SUCCESS_INSERTED, 'success');
      tep_redirect(FILENAME_JOBSEEKER_LIST_OF_SAVED_SEARCHES);
     }
    }
   }
   $template->assign_vars(array('INFO_TEXT_TITLE_NAME' =>"<br>".INFO_TEXT_TITLE_NAME,
                                'INFO_TEXT_TITLE_NAME1'=>tep_draw_input_field('TR_search_name', $search_name,'size="26" maxlength="32"',true)));
 }
}
// search
if(tep_not_null($action))
{
 switch($action)
 {
  case 'search':
   $hidden_fields1='';
   $action=tep_db_prepare_input($_GET['action']);
   $hidden_fields.=tep_draw_hidden_field('action',$action);
   $field=tep_db_prepare_input($_GET['field']);
   $order=tep_db_prepare_input($_GET['order']);
   $lower=(int)tep_db_prepare_input($_GET['lower']);
   $higher=(int)tep_db_prepare_input($_GET['higher']);
   $whereClause='';
   if ((preg_match("/http:\/\//i",$keyword)))
   $keyword='';
   if(tep_not_null($keyword)  && (($_GET['keyword']!='keyword') && ($_GET['keyword']!='job search keywords')) ) //   keyword starts //////
   {
    if($_SESSION['sess_jobsearch']!='y')
    tag_key_check($keyword);
    $_SESSION['sess_jobsearch']='y';

    $whereClause1='(';
    $hidden_fields1.=tep_draw_hidden_field('keyword',$keyword);
    $search = array ("'[\s]+'");
    $replace = array (" ");
    $keyword = preg_replace($search, $replace, $keyword);
    if($word1=='Yes')
    {
     $hidden_fields.=tep_draw_hidden_field('word1',$word1);
     $explode_string=explode(' ',$keyword);
		   $total_keys = count($explode_string);
					for($i=0;$i<$total_keys;$i++)
					{
		    if(strlen($explode_string[$i])< 3 or strtolower($explode_string[$i])=='and')
					 {
       unset($explode_string[$i]);
					 }
					}
					sort($explode_string);
     $whereClause1.='(';
		   $total_keys = count($explode_string);
     for($i=0;$i<$total_keys;$i++)
     {
      if($i>0)
      $whereClause1.='or ( ';
      $whereClause1.=" j.job_title like '%".tep_db_input($explode_string[$i])."%' or ";
      $whereClause1.=" j.job_state like '%".tep_db_input($explode_string[$i])."%' or ";
      $whereClause1.=" j.job_location like '%".tep_db_input($explode_string[$i])."%' or ";
      $whereClause1.=" j.job_short_description like '%".tep_db_input($explode_string[$i])."%' or ";
      $whereClause1.=" j.job_description like '%".tep_db_input($explode_string[$i])."%' or ";
      $whereClause1.=" r.recruiter_company_name like '%".tep_db_input($explode_string[$i])."%' or ";

      $temp_result=tep_db_query("select zone_id from " . ZONES_TABLE . " where (".TEXT_LANGUAGE."zone_name like '%" . tep_db_input($explode_string[$i]) . "%' or zone_code like '%" . tep_db_input($explode_string[$i]) . "%')");
      if(tep_db_num_rows($temp_result) > 0)
      {
       $whereClause1.=" (  ";
       while($temp_row = tep_db_fetch_array($temp_result))
       {
        $whereClause1.=" j.job_state_id ='".$temp_row['zone_id']."' or ";
       }
       $whereClause1=substr($whereClause1,0,-4);
       $whereClause1.=" ) or ";
       tep_db_free_result($temp_result);
      }
      $temp_result=tep_db_query("select id from ".COUNTRIES_TABLE." where ".TEXT_LANGUAGE."country_name like '%".tep_db_input($explode_string[$i])."%'");
      if(tep_db_num_rows($temp_result) > 0)
      {
       $whereClause1.=" (  ";
       while($temp_row = tep_db_fetch_array($temp_result))
       {
        $whereClause1.=" j.job_country_id ='".$temp_row['id']."' or ";
       }
       $whereClause1=substr($whereClause1,0,-4);
       $whereClause1.=" ) or ";
       tep_db_free_result($temp_result);
      }
      $whereClause1=substr($whereClause1,0,-4);
      $whereClause1.=" ) ";
     }
					if($total_keys<=0)
					$whereClause1='';
    }
    else
    {
     $whereClause1.=" j.job_title like '%".tep_db_input($keyword)."%' ";
     $whereClause1.=" or j.job_state like '%".tep_db_input($keyword)."%' ";
     $whereClause1.=" or j.job_location like '%".tep_db_input($keyword)."%' ";
     $whereClause1.=" or j.job_short_description like '%".tep_db_input($keyword)."%'";
     $whereClause1.=" or j.job_description like '%".tep_db_input($keyword)."%'";
     $whereClause1.=" or r.recruiter_company_name like '%".tep_db_input($keyword)."%'";

     $temp_result=tep_db_query("select zone_id from " . ZONES_TABLE . " where (".TEXT_LANGUAGE."zone_name like '%" . tep_db_input($keyword) . "%' or zone_code like '%" . tep_db_input($keyword) . "%')");
     if(tep_db_num_rows($temp_result) > 0)
     {
      $whereClause1.=" or (  ";
      while($temp_row = tep_db_fetch_array($temp_result))
      {
       $whereClause1.=" j.job_state_id ='".$temp_row['zone_id']."' or ";
      }
      $whereClause1=substr($whereClause1,0,-4);
      $whereClause1.=" ) ";
      tep_db_free_result($temp_result);
     }
     $temp_result=tep_db_query("select id from ".COUNTRIES_TABLE." where ".TEXT_LANGUAGE."country_name like '%".tep_db_input($keyword)."%'");
     if(tep_db_num_rows($temp_result) > 0)
     {
      $whereClause1.=" or (  ";
      while($temp_row = tep_db_fetch_array($temp_result))
      {
       $whereClause1.=" j.job_country_id ='".$temp_row['id']."' or ";
      }
      $whereClause1=substr($whereClause1,0,-4);
      $whereClause1.=" ) ";
      tep_db_free_result($temp_result);
     }
    }
 			if($whereClause1!='')
    $whereClause1.=" ) ";
    $whereClause.=$whereClause1;
   }
   // keyword ends //////
   //   location starts //////
   if(tep_not_null($location) && $_GET['location']!='location')
   {
    $whereClause1='(';
    $hidden_fields1.=tep_draw_hidden_field('location',$location);
    $search = array ("'[\s]+'");
    $replace = array (" ");
    $location = preg_replace($search, $replace, $location);
    //if($word1=='Yes')
    //{
     $explode_string=explode(',',$location);
     $whereClause1.='( ';
     for($i=0;$i<count($explode_string);$i++)
     {
      if(!tep_not_null($explode_string[$i]))
      continue;
      if($i>0 &&  $explode_string[($i-1)]!='')
      $whereClause1.='or ( ';
      $whereClause1.=" j.job_state like '%".tep_db_input($explode_string[$i])."%' or ";
      $whereClause1.=" j.job_location like '%".tep_db_input($explode_string[$i])."%' or ";

      $temp_result=tep_db_query("select zone_id from " . ZONES_TABLE . " where (".TEXT_LANGUAGE."zone_name like '%" . tep_db_input($explode_string[$i]) . "%' or zone_code like '%" . tep_db_input($explode_string[$i]) . "%')");
      if(tep_db_num_rows($temp_result) > 0)
      {
       $whereClause1.=" (  ";
       while($temp_row = tep_db_fetch_array($temp_result))
       {
        $whereClause1.=" j.job_state_id ='".$temp_row['zone_id']."' or ";
       }
       $whereClause1=substr($whereClause1,0,-4);
       $whereClause1.=" ) or ";
       tep_db_free_result($temp_result);
      }
      $whereClause1=substr($whereClause1,0,-4);
      $whereClause1.=" ) ";
     }
    //}
    $whereClause1.=" )";
    if($whereClause1!="((  )")
    {
     $whereClause=(tep_not_null($whereClause)?$whereClause.' and ':'');
     $whereClause.=$whereClause1;
    }
   }
   //   location ends //////

   // job_post_day starts //
   if(tep_not_null($_GET['job_post_day']))
   {
    $job_post_day=abs((int)($_GET['job_post_day']));
    $hidden_fields.=tep_draw_hidden_field('job_post_day',$job_post_day);
    $whereClause=(tep_not_null($whereClause)?$whereClause.' and ':'');
    $whereClause.=" ( j.re_adv >'".date("Y-m-d", mktime(0, 0, 0, date("m")  , date("d")-$job_post_day, date("Y")))."' ) ";
   }
   // job_post_day end //
   // inserted date starts //
   if(tep_not_null($_GET['inserted_date']))
   {
    $inserted_date=($_GET['inserted_date']);
    $hidden_fields.=tep_draw_hidden_field('inserted_date',$inserted_date);
    $whereClause=(tep_not_null($whereClause)?$whereClause.' and ':'');
    $whereClause.=" ( j.re_adv ='".$inserted_date."' ) ";
   }

   // inserted date end //
   // company starts //
   //*
   if(tep_not_null($_GET['company']))
   {
    $hidden_fields.=tep_draw_hidden_field('company',$company);
    $whereClause=(tep_not_null($whereClause)?$whereClause.' and ':'');
    $whereClause.=" ( r.recruiter_company_name ='".tep_db_input($company)."' )";
   }
   //*/// company ends ///
   // experience starts //
   //*
   if(tep_not_null($_GET['experience']))
   {
    $experience=$_GET['experience'];
    $hidden_fields.=tep_draw_hidden_field('experience',$experience);
    $whereClause=(tep_not_null($whereClause)?$whereClause.' and ':'');
    $explode_string=explode("-",$experience);
    $whereClause.=" ( j.min_experience='".tep_db_input(trim($explode_string['0']))."' and  j.max_experience='".tep_db_input(trim($explode_string['1']))."' ) ";
   }
   //*/// experience ends ///
   // industry job_category  starts ///
   if(tep_not_null($_GET['job_category']))
   {
    $job_category=$_GET['job_category'];
    if($job_category['0']!='0')
    {
     $job_category1=remove_child_job_category($job_category1);
     $job_category=explode(',',$job_category1);
     $count_job_category=count($job_category);
     for($i=0;$i<$count_job_category;$i++)
     {
      $hidden_fields.=tep_draw_hidden_field('job_category[]',$job_category[$i]);
     }
     $search_category1 =get_search_job_category($job_category1);
     $now=date('Y-m-d H:i:s');
     $whereClause_job_category=" select distinct (j.job_id) from ".JOB_TABLE."  as j  left join ".JOB_JOB_CATEGORY_TABLE." as jc on(j.job_id=jc.job_id ) where j.expired >='$now' and j.re_adv <='$now' and j.job_status='Yes' and ( j.deleted is NULL or j.deleted='0000-00-00 00:00:00') and jc.job_category_id in (".$search_category1.")";
     $whereClause=(tep_not_null($whereClause)?$whereClause.' and job_id in ( ':' job_id in ( ');
     $whereClause.=$whereClause_job_category;
     $whereClause.=" ) ";
    }
    else
    {
     $hidden_fields.=tep_draw_hidden_field('job_category[]','0');
    }
   }
   // industry job_category1 ends ///

   // state starts ///
   if(tep_not_null($state))
   {
    $state1=explode(',',$state);//print_r($state1);
    $whereClause=(tep_not_null($whereClause)?$whereClause.' and ( ':' ( ');
    for($i=0;$i<count($state1);$i++)
    {
     $hidden_fields.=tep_draw_hidden_field('state[]',$state1[$i]);
     $temp_result=tep_db_query("select zone_id from " . ZONES_TABLE . " where (zone_name like '%" . tep_db_input($state1[$i]) . "%' or zone_code like '%" . tep_db_input($state1[$i]) . "%')");
     $whereClause.="  ( j.job_state like '%".tep_db_input($state1[$i])."%' )  ";
     if(tep_db_num_rows($temp_result) > 0)
     {
      $whereClause.=' or ( ';
      while($temp_row = tep_db_fetch_array($temp_result))
      {
       $whereClause.=" j.job_state_id ='".$temp_row['zone_id']."' or ";
      }
      $whereClause=substr($whereClause,0,-4);
      $whereClause.="  )";
      tep_db_free_result($temp_result);
     }
     $whereClause.=" or ";
    }
    $whereClause=substr($whereClause,0,-4);
    $whereClause.="  )";

   }
   // state ends ///
if($search_zip_code==2)
   {
    ////zip code ////////////
    $whereClause=(tep_not_null($whereClause)?$whereClause.' and ':'');
    $hidden_fields1.=tep_draw_hidden_field('zip_code',$zip_code);
    $hidden_fields1.=tep_draw_hidden_field('radius',$radius);
    $hidden_fields.=tep_draw_hidden_field('search_zip_code',2);
    if($row=getAnyTableWhereData(ZIP_CODE_TABLE," zip_code='".tep_db_input($zip_code)."'",'*'))
    {
     ////////////////////
     $today=date('Y-m-d');
     if($row_cache=getAnyTableWhereData(ZIP_CODE_SEARCH_TABLE," zip_code='".tep_db_input($zip_code)."' and  radius='".tep_db_input($radius)."'",'state'))
     {
      $state_array =explode(',',$row_cache['state']);
     }
     else
     {
      $state_array=array();
     // echo ("select distinct(state) as state from " . ZIP_CODE_TABLE. " where ( 3959 * acos( cos( radians( ".tep_db_input($row['latitude']).") ) * cos( radians( latitude ) ) * cos( radians( longitude ) - radians(".tep_db_input($row['longitude']).") ) + sin( radians( ".tep_db_input($row['latitude']).") ) * sin( radians( latitude ) ) ) ) <=".tep_db_input($radius)."");
      $temp_state_result = tep_db_query("select distinct(state) as state from " . ZIP_CODE_TABLE. " where ( 3959 * acos( cos( radians( ".tep_db_input($row['latitude']).") ) * cos( radians( latitude ) ) * cos( radians( longitude ) - radians(".tep_db_input($row['longitude']).") ) + sin( radians( ".tep_db_input($row['latitude']).") ) * sin( radians( latitude ) ) ) ) <=".tep_db_input($radius)."");
      if(tep_db_num_rows($temp_state_result) > 0)
      {
       while($temp_row = tep_db_fetch_array($temp_state_result))
       {
        $state_array[]=trim($temp_row['state']);
       }
       $state_zip=implode(',',$state_array);
       $sql_data_search_array=array('zip_code'=>$zip_code,
                        'radius'=>$radius,
                        'state'=>$state_zip,
                        'inserted'=>$today,
                       );
       tep_db_perform(ZIP_CODE_SEARCH_TABLE, $sql_data_search_array);
      }
      tep_db_free_result($temp_state_result);
     }
     $total_state=count($state_array);
     if($total_state>0)
     {
      $whereClause.='( ';
      for($i=0;$i<$total_state;$i++)
      {
       $search_state= $state_array[$i];
       if($row_state=getAnyTableWhereData(ZONES_TABLE," zone_id='".tep_db_input($search_state)."'",'zone_name'))
        $whereClause.="  ( j.job_state = '".tep_db_input($row_state['zone_name'])."' or  j.job_state_id = '".tep_db_input($search_state)."') or ";
       else
        $whereClause.="(j.job_state_id ='".tep_db_input($search_state)."') or ";
      }
      $whereClause=substr($whereClause,0,-4);
      $whereClause.="  )";
     }
     else
     {
      $whereClause.=' 0 ';
     }
    }
	   else
    $whereClause.=' 0 ';
    ///////////////////
   }
			// country starts ///
   if(tep_not_null($country) && $country > 0)
			{
		  $hidden_fields1.=tep_draw_hidden_field('country',$country);
				$whereClause=(tep_not_null($whereClause)?$whereClause.' and ( ':' ( ');
				$whereClause.=" j.job_country_id ='".tep_db_input($country)."'";
				$whereClause.="  )";
			}
   // country ends ///
   $whereClause=(tep_not_null($whereClause)?$whereClause.' and ':'');
   ////
   $now=date('Y-m-d H:i:s');
   $table_names=JOB_TABLE." as j left outer join ".RECRUITER_LOGIN_TABLE.' as rl on (j.recruiter_id=rl.recruiter_id) left outer join '.RECRUITER_TABLE.' as r on (rl.recruiter_id=r.recruiter_id)  left outer join '.ZONES_TABLE.' as z on (j.job_state_id=z.zone_id or z.zone_id is NULL) left outer join '.COUNTRIES_TABLE.' as c on (j.job_country_id =c.id)';
   $whereClause.="   rl.recruiter_status='Yes' and j.expired >='$now' and j.re_adv <='$now' and j.job_status='Yes' and ( j.deleted is NULL or j.deleted='0000-00-00 00:00:00')";
   $field_names="j.job_id, j.job_title, j.re_adv, j.job_short_description, j.recruiter_id,j.min_experience,j.max_experience,j.job_salary,j.job_industry_sector,j.job_type,j.expired,j.recruiter_id,r.recruiter_company_name,r.recruiter_logo,j.job_recruiter_type,r.recruiter_type,j.job_source,j.post_url,j.url, j.jobg8_logo_url,j.jobg8_company,j.key1,j.key2,j.key3,j.level,j.job_featured,concat(case when j.job_location='' then '' else concat(j.job_location,', ') end, if(j.job_state_id,z.zone_name,j.job_state)) as location ,c.country_name"; //j.job_state, j.job_state_id,j.job_country_id
   //$query1 = "select count(j.job_id) as x1 from $table_names where $whereClause ";

      if($_GET['r1'] == 'Employer' and $_GET['r2'] == 'Agency'){//all
          $_SESSION["r1"]="Employer";
          $_SESSION["r2"]="Agency";
          $chk_ct='<div class="recruiter_type_chk_search">
                    <p><input tabindex="7" id="employers" type="checkbox" name="r1" value="Employer" checked><label for="employers">Employers</label></p>
                    <p><input tabindex="8" id="agencies" type="checkbox" name="r2" value="Agency" checked><label for="agencies">Agencies</label></p>
                </div>
                            ';
      }
      else if($_GET['r1'] != 'Employer' and $_GET['r2'] != 'Agency'){//none
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
          $whereClause.=" and ( j.job_recruiter_type ='Employer' ) ";
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
          $whereClause.=" and ( j.job_recruiter_type ='Agency' ) ";
          $chk_ct='<div class="recruiter_type_chk_search">
                    <p><input tabindex="7" id="employers" type="checkbox" name="r1" value="Employer"><label for="employers">Employers</label></p>
                    <p><input tabindex="8" id="agencies" type="checkbox" name="r2" value="Agency" checked><label for="agencies">Agencies</label></p>
                </div>
                            ';
      }

      if($_GET['p'] == "t" and $_GET['c'] == "t" and $_GET['g'] == "t"){//all
          $_SESSION["p"]="t";
          $_SESSION["c"]="t";
          $_SESSION["g"]="t";
          $chk_jt='<div class="job_type_chk_search"><h2><p><input tabindex="4" id="permanent" type="checkbox" name="p" value="t"  checked/><label for="permanent">Permanent</label></p></h2>
                        <h2><p><input tabindex="5" id="contract" type="checkbox" name="c" value="t"   checked/><label for="contract">Contract</label></p></h2>
                        <h2><p><input tabindex="6" id="graduate" type="checkbox" name="g" value="t"   checked/><label for="graduate">Graduate / Internships</label></p></h2></div>';
      }elseif($_GET['p'] != "t" and $_GET['c'] != "t" and $_GET['g'] != "t"){//none
          $_SESSION["p"]="";
          $_SESSION["c"]="";
          $_SESSION["g"]="";
          $chk_jt='<div class="job_type_chk_search"><h2><p><input tabindex="4" id="permanent" type="checkbox" name="p" value="t"  /><label for="permanent">Permanent</label></p></h2>
                        <h2><p><input tabindex="5" id="contract" type="checkbox" name="c" value="t"  /><label for="contract">Contract</label></p></h2>
                        <h2><p><input tabindex="6" id="graduate" type="checkbox" name="g" value="t"  /><label for="graduate">Graduate / Internships</label></p></h2></div>';
      }elseif($_GET['p'] == "t" and $_GET['c'] == "t" and $_GET['g'] != "t"){//except grad
          $_SESSION["p"]="t";
          $_SESSION["c"]="t";
          $_SESSION["g"]="";
          $whereClause.=" and ( j.job_type ='3' or  j.job_type ='4' ) ";
          $chk_jt='<div class="job_type_chk_search"><h2><p><input tabindex="4" id="permanent" type="checkbox" name="p" value="t"   checked/><label for="permanent">Permanent</label></p></h2>
                        <h2><p><input tabindex="5" id="contract" type="checkbox" name="c" value="t"   checked/><label for="contract">Contract</label></p></h2>
                        <h2><p><input tabindex="6" id="graduate" type="checkbox" name="g" value="t"  /><label for="graduate">Graduate / Internships</label></p></h2></div>';
      }elseif($_GET['p'] == "t" and $_GET['c'] != "t" and $_GET['g'] == "t"){//except contract
          $_SESSION["p"]="t";
          $_SESSION["c"]="";
          $_SESSION["g"]="t";
          $whereClause.=" and ( j.job_type ='6' or  j.job_type ='4' ) ";
          $chk_jt='<div class="job_type_chk_search"><h2><p><input tabindex="4" id="permanent" type="checkbox" name="p" value="t"  checked /><label for="permanent">Permanent</label></p></h2>
                        <h2><p><input tabindex="5" id="contract" type="checkbox" name="c" value="t"  /><label for="contract">Contract</label></p></h2>
                        <h2><p><input tabindex="6" id="graduate" type="checkbox" name="g" value="t"  checked /><label for="graduate">Graduate / Internships</label></p></h2></div>';
      }
      elseif($_GET['p'] != "t" and $_GET['c'] == "t" and $_GET['g'] == "t"){//except permanent
          $_SESSION["c"]="t";
          $_SESSION["g"]="t";
          $_SESSION["p"]="";
          $whereClause.=" and ( j.job_type ='6' or  j.job_type ='3' ) ";
          $chk_jt='<div class="job_type_chk_search"><h2><p><input tabindex="4" id="permanent" type="checkbox" name="p" value="t"  /><label for="permanent">Permanent</label></p></h2>
                        <h2><p><input tabindex="5" id="contract" type="checkbox" name="c" value="t"  checked /><label for="contract">Contract</label></p></h2>
                        <h2><p><input tabindex="6" id="graduate" type="checkbox" name="g" value="t"   checked/><label for="graduate">Graduate / Internships</label></p></h2></div>';
      }
      elseif($_GET['p'] != "t" and $_GET['c'] != "t" and $_GET['g'] == "t"){//grad
          $_SESSION["g"]="t";
          $_SESSION["p"]="";
          $_SESSION["c"]="";
          $whereClause.=" and ( j.job_type ='6') ";
          $chk_jt='<div class="job_type_chk_search"><h2><p><input tabindex="4" id="permanent" type="checkbox" name="p" value="t"  /><label for="permanent">Permanent</label></p></h2>
                        <h2><p><input tabindex="5" id="contract" type="checkbox" name="c" value="t"  /><label for="contract">Contract</label></p></h2>
                        <h2><p><input tabindex="6" id="graduate" type="checkbox" name="g" value="t"  checked /><label for="graduate">Graduate / Internships</label></p></h2></div>';
      }
      elseif($_GET['p'] == "t" and $_GET['c'] != "t" and $_GET['g'] != "t"){//permanent
          $_SESSION["p"]="t";
          $_SESSION["c"]="";
          $_SESSION["g"]="";
          $whereClause.=" and ( j.job_type ='4') ";
          $chk_jt='<div class="job_type_chk_search"><h2><p><input tabindex="4" id="permanent" type="checkbox" name="p" value="t"  checked /><label for="permanent">Permanent</label></p></h2>
                        <h2><p><input tabindex="5" id="contract" type="checkbox" name="c" value="t"  /><label for="contract">Contract</label></p></h2>
                        <h2><p><input tabindex="6" id="graduate" type="checkbox" name="g" value="t"  /><label for="graduate">Graduate / Internships</label></p></h2></div>';
      }
      elseif($_GET['p'] != "t" and $_GET['c'] == "t" and $_GET['g'] != "t"){//contract
          $_SESSION["c"]="t";
          $_SESSION["g"]="";
          $_SESSION["p"]="";
          $whereClause.=" and ( j.job_type ='3' ) ";
          $chk_jt='<div class="job_type_chk_search"><h2><p><input tabindex="4" id="permanent" type="checkbox" name="p" value="t"  /><label for="permanent">Permanent</label></p></h2>
                        <h2><p><input tabindex="5" id="contract" type="checkbox" name="c" value="t"  checked /><label for="contract">Contract</label></p></h2>
                        <h2><p><input tabindex="6" id="graduate" type="checkbox" name="g" value="t"  /><label for="graduate">Graduate / Internships</label></p></h2></div>';
      }

//print_r($_SERVER['HTTP_HOST']);
      //print_r($_GET['job_type']);
//print_r($whereClause);

			$query = "select $field_names from $table_names where $whereClause ORDER BY if(j.job_source ='jobsite',0,1)  asc, j.job_featured='Yes' desc, RAND() desc";
//ORDER the job list above - in future by views ascending ----------------------------------------------------------------^^^---------------------------------------
			$starting=0;
			$recpage = MAX_DISPLAY_SEARCH_RESULTS;
			$obj = new pagination_class($query,$starting,$recpage,$keyword,$location,$job_type,$word1,$country,$state,$job_category,$experience,$job_post_day,$search_zip_code,$zip_code,$radius);
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
		if($row['recruiter_id']!= 394){
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

          if ($row['recruiter_id'] == 213){
            $_table_names = "indeed_job";
            $_where_clause = "job_id = " . $row["job_id"];
            $_field_names = "indeed_url";
            $indeed_job = getAnytableWhereData($_table_names,$_where_clause,$_field_names);
            $job_title = tep_db_output($row['job_title']);
            $apply_job    ='<a href="'.$indeed_job["indeed_url"].'" title="'.tep_db_output(INFO_TEXT_APPLY_TO_THIS_JOB).'" target="_blank">'.INFO_TEXT_APPLY_TO_THIS_JOB.'</a>';
          }
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
        if($row["recruiter_id"] == 419 OR $row["recruiter_id"] == 273 OR $row["recruiter_id"] == 335 OR $row["recruiter_id"] == 349 OR $row["recruiter_id"] == 322 OR $row["recruiter_id"] == 62 OR $row["recruiter_id"] == 59 OR $row["recruiter_id"] == 86 OR $row["recruiter_id"] == 423 OR $row["recruiter_id"] == 264 OR $row["recruiter_id"] == 20 OR $row["recruiter_id"] == 66 OR $row["recruiter_id"] == 663){
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
              'location' =>tep_db_output($row['location'].' '.$row['country_name']),
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
       if($keyword !== null && $location !== null){
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
  break;
 }
}
//echo  $whereClause;
if(!in_array($word1,array('Yes','No')))
 $word1='Yes';
if($action=='' && !isset($_GET['sID']))
 $country=(int)DEFAULT_COUNTRY_ID;

if($search_zip_code==2)
{
 $default_tab=2;
}
else
{
 $default_tab=1;
 $search_zip_code=1;
}

if($i_page == 0 && $keyword !== null && $location !== null){
    $searchResultsFound = ''.$i_page.' results found for "'.$keyword.'" in "'.$location.'"';
}
elseif($i_page == 0 && $keyword == "" && $location == ""){
    $searchResultsFound = ''.$i_page.' results found ';
}
elseif($i_page == 0 && $location !== "" && $location !== null){
    $searchResultsFound = ''.$i_page.' results found in "'.$location.'"';
}
elseif($i_page == 0 && $keyword !== "" && $keyword !== null){
    $searchResultsFound = ''.$i_page.' results found for "'.$keyword.'"';
}


//$cat_array=tep_get_diving_main_categories(DIVING_CATEGORY_TABLE);
$cat_array=tep_get_categories(JOB_CATEGORY_TABLE);
array_unshift($cat_array,array("id"=>0,"text"=>INFO_TEXT_ALL_JOB_CATEGORY));
if($action!='search')
{
 $template->assign_vars(array( 'hidden_fields' => $hidden_fields,
  'HEADING_TITLE'          => HEADING_TITLE,
  'form'                   => tep_draw_form('search', FILENAME_JOB_SEARCH,($edit?'sID='.$save_search_id:''),'get','').tep_draw_hidden_field('action','search').tep_draw_hidden_field('search_zip_code',$search_zip_code),
  'form1'                  => tep_draw_form('search1', FILENAME_JOB_SEARCH,'get').tep_draw_hidden_field('action','search'),
  'INFO_TEXT_KEYWORD'      => INFO_TEXT_KEYWORD,
  'INFO_TEXT_KEYWORD1'     => tep_draw_input_field('keyword', $keyword, 'size="40" class="input-big" onfocus="this.value=\'\'" onblur="this.value=\' e.g. Sales Executive\'"  value="e.g. Sales Executive" ',false),
  'INFO_TEXT_KEYWORD_CRITERIA'=>INFO_TEXT_KEYWORD_CRITERIA,
  'INFO_TEXT_KEYWORD3'     => tep_draw_radio_field('word1', 'Yes', '', $word1,'id=radio_word1').'<label for="radio_word1">'.INFO_TEXT_KEYWORD_WORD1.'</label>'.tep_draw_radio_field('word1', 'No', '', $word1,'id=radio_word2').'<label for="radio_word2">'.INFO_TEXT_KEYWORD_WORD2.'</label>',
  'INFO_TEXT_LOCATION'     => (($search_zip_code==2)?INFO_TEXT_ZIP_CODE:INFO_TEXT_LOCATION_NAME),
  'INFO_TEXT_LOCATION1'    => (($search_zip_code==2)?tep_draw_input_field('zip_code',$zip_code,'').''.zone_radius('radius',"","",$radius,true).tep_draw_hidden_field('location',''):tep_draw_input_field('location', $location,' style="width:130px" placeholder="State or City" ',false)),
  'INFO_TEXT_SEARCH_COUNTRY_STATE' => INFO_TEXT_SEARCH_COUNTRY_STATE,
  'INFO_TEXT_SEARCH_US_ZIP'=> INFO_TEXT_SEARCH_US_ZIP,
  'INFO_TEXT_COUNTRY'      => INFO_TEXT_COUNTRY,
  'INFO_TEXT_COUNTRY1'     => LIST_TABLE(COUNTRIES_TABLE,TEXT_LANGUAGE."country_name","priority","name='country' style=';'","All countries","",$country),
  'INFO_TEXT_ZIP_CODE'     => INFO_TEXT_ZIP_CODE,
  'INFO_TEXT_ZIP_CODE1'    => tep_draw_input_field('zip_code',$zip_code,''),
  'INFO_TEXT_RADIUS'       => INFO_TEXT_RADIUS,
  'INFO_TEXT_RADIUS1'      => zone_radius('radius',"","",$radius,true),
  'INFO_TEXT_DEFAULT_TAB'  => $default_tab,
	

  'INFO_TEXT_JOB_CATEGORY' => INFO_TEXT_JOB_CATEGORY,
  'INFO_TEXT_JOB_CATEGORY_TEXT' => INFO_TEXT_JOB_CATEGORY_TEXT,
  'INFO_TEXT_JOB_CATEGORY1'=> tep_draw_pull_down_menu('job_category[]', $cat_array, explode(",",$job_category1), 'style="width:220px;"', false),
  'INFO_TEXT_EXPERIENCE'   => INFO_TEXT_EXPERIENCE,
  'INFO_TEXT_EXPERIENCE1'  => experience_drop_down('name="experience" style=";"', 'Any experience', '', $experience),
  'INFO_TEXT_JOB_POSTED'   => INFO_TEXT_JOB_POSTED,
  'INFO_TEXT_JOB_POSTED1'  => LIST_SET_DATA(JOB_POSTED_TABLE,"",TEXT_LANGUAGE.'type_name','value',"priority","name='job_post_day' style=';'" ,INFO_TEXT_DEFAULT_JOB_POST_DAY,'',$job_post_day),
  'button'                 => tep_image_submit(PATH_TO_BUTTON.'button_search.gif', IMAGE_SEARCH),
  'JOB_SEARCH_LEFT'        => JOB_SEARCH_LEFT,
  'INFO_TEXT_JSCRIPT_FILE' => $jscript_file,
  ));
}
else
{$key1=(tep_not_null($keyword)?$key1=$keyword:'keyword');
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
}
/*
if($state_error)
{
 $zones_array=tep_get_country_zones($country);
 if(sizeof($zones_array) > 1)
 {
  $template->assign_vars(array( 'INFO_TEXT_STATE1' => tep_draw_pull_down_menu('state', tep_get_country_zones($country),$state)));
 }
 else
 {
  $template->assign_vars(array('INFO_TEXT_STATE1' => tep_draw_input_field('state', $state,'size="50"',false)));
 }
}
else
{
 //$template->assign_vars(array('INFO_TEXT_STATE1' => LIST_SET_DATA(ZONES_TABLE,"",'zone_name','zone_name',"zone_name",'name="state[]" ',"state",'',$state)." ".tep_draw_input_field('state1',$state,'size="20"')));
 $template->assign_vars(array('INFO_TEXT_STATE1' =>  tep_draw_input_field('state1',$state,'size="33"')));
}*/

$template->assign_vars(array(
    'chk_ct' => $chk_ct,
 'chk_jt' => $chk_jt,
 'searchResultsFound'=>$searchResultsFound,
 'RIGHT_BOX_WIDTH' => RIGHT_BOX_WIDTH1,
 'RIGHT_HTML' => RIGHT_HTML,
 'update_message' => $messageStack->output()));
if($action=='search' || $action=='save_search')
{
 $template->pparse('job_search_result');
}
else
{
 $template->pparse('job_search');
 unset($_SESSION['sess_jobsearch']);
}
?>
