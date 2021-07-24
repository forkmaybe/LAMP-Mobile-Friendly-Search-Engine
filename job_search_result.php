<?
/*
***********************************************************

*/
include_once("../include_files.php");
ini_set('max_execution_time','0');
include_once(PATH_TO_MAIN_PHYSICAL_LANGUAGE.$language.'/'.FILENAME_JOB_SEARCH);
if(isset($_GET['starting'])&& !isset($_REQUEST['submit']))
{
    $starting=$_GET['starting'];
}
else
{
    $starting=0;
}
//print_r($_GET);
$recpage = 14;
if(isset($_GET['keyword']) && (($_GET['keyword']!='keyword') && ($_GET['keyword']!='job search keywords')))
{
    $keyword=tep_db_prepare_input($_GET['keyword']);
}
if(isset($_GET['location']) && ($_GET['location']!='location'))
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


if($_SESSION['r1'] == 'Employer' and $_SESSION['r2'] == 'Agency'){//all
    $ct="";
    $_SESSION["r1"]="Employer";
    $_SESSION["r2"]="Agency";
    $chk_ct='<div class="recruiter_type_chk_search">
                    <p><input tabindex="7" id="employers" type="checkbox" name="r1" value="Employer" checked><label for="employers">Employers</label></p>
                    <p><input tabindex="8" id="agencies" type="checkbox" name="r2" value="Agency" checked><label for="agencies">Agencies</label></p>
                </div>
                            ';
}
else if($_SESSION['r1'] != 'Employer' and $_SESSION['r2'] != 'Agency'){//none
    $ct="";
    $_SESSION["r1"]="";
    $_SESSION["r2"]="";
    $chk_ct='<div class="recruiter_type_chk_search">
                    <p><input tabindex="7" id="employers" type="checkbox" name="r1" value="Employer"><label for="employers">Employers</label></p>
                    <p><input tabindex="8" id="agencies" type="checkbox" name="r2" value="Agency"><label for="agencies">Agencies</label></p>
                </div>
                            ';
}
elseif($_SESSION['r1'] == 'Employer' and $_SESSION['r2'] != 'Agency'){//employer
    $_SESSION["r1"]="Employer";
    $_SESSION["r2"]="";
    $ct=" and job_recruiter_type ='Employer'";
    $chk_ct='<div class="recruiter_type_chk_search">
                    <p><input tabindex="7" id="employers" type="checkbox" name="r1" value="Employer" checked><label for="employers">Employers</label></p>
                    <p><input tabindex="8" id="agencies" type="checkbox" name="r2" value="Agency"><label for="agencies">Agencies</label></p>
                </div>
                            ';
}
elseif($_SESSION['r2'] == 'Agency' and $_SESSION['r1'] != 'Employer')//agency
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
if($_SESSION["p"] == "t" and $_SESSION['c'] == "t" and $_SESSION['g'] == "t"){//all
    $jt="";
    $_SESSION["c"]="t";
    $_SESSION["g"]="t";
    $_SESSION["p"]="t";
    $chk_jt='<div class="job_type_chk_search"><h2><p><input id="permanent" type="checkbox" name="p" value="t"  checked/><label for="permanent">Permanent</label></p></h2>
                        <h2><p><input id="contract" type="checkbox" name="c" value="t"   checked/><label for="contract">Contract</label></p></h2>
                        <h2><p><input id="graduate" type="checkbox" name="g" value="t"   checked/><label for="graduate">Graduate / Internships</label></p></h2></div>';
}elseif($_SESSION['p'] != "t" and $_SESSION['c'] != "t" and $_SESSION['g'] != "t"){//none
    $jt="";
    $_SESSION["c"]="";
    $_SESSION["g"]="";
    $_SESSION["p"]="";
    $chk_jt='<div class="job_type_chk_search"><h2><p><input id="permanent" type="checkbox" name="p" value="t"  /><label for="permanent">Permanent</label></p></h2>
                        <h2><p><input id="contract" type="checkbox" name="c" value="t"  /><label for="contract">Contract</label></p></h2>
                        <h2><p><input id="graduate" type="checkbox" name="g" value="t"  /><label for="graduate">Graduate / Internships</label></p></h2></div>';
}elseif($_SESSION['p'] == "t" and $_SESSION['c'] == "t" and $_SESSION['g'] != "t"){//except grad
    $jt=" and (job_type =3 or job_type =4 ) ";
    $_SESSION["c"]="t";
    $_SESSION["g"]="";
    $_SESSION["p"]="t";
    $chk_jt='<div class="job_type_chk_search"><h2><p><input id="permanent" type="checkbox" name="p" value="t"   checked/><label for="permanent">Permanent</label></p></h2>
                        <h2><p><input id="contract" type="checkbox" name="c" value="t"   checked/><label for="contract">Contract</label></p></h2>
                        <h2><p><input id="graduate" type="checkbox" name="g" value="t"  /><label for="graduate">Graduate / Internships</label></p></h2></div>';
}elseif($_SESSION['p'] == "t" and $_SESSION['c'] != "t" and $_SESSION['g'] == "t"){//except contract
    $jt=" and ( job_type =6 or job_type =4 ) ";
    $_SESSION["c"]="";
    $_SESSION["g"]="t";
    $_SESSION["p"]="t";
    $chk_jt='<div class="job_type_chk_search"><h2><p><input id="permanent" type="checkbox" name="p" value="t"  checked /><label for="permanent">Permanent</label></p></h2>
                        <h2><p><input id="contract" type="checkbox" name="c" value="t"  /><label for="contract">Contract</label></p></h2>
                        <h2><p><input id="graduate" type="checkbox" name="g" value="t"  checked /><label for="graduate">Graduate / Internships</label></p></h2></div>';
}
elseif($_SESSION['p'] != "t" and $_SESSION['c'] == "t" and $_SESSION['g'] == "t"){//except permanent
    $jt=" and (job_type =6 or job_type =3 ) ";
    $_SESSION["c"]="t";
    $_SESSION["g"]="t";
    $_SESSION["p"]="";
    $chk_jt='<div class="job_type_chk_search"><h2><p><input id="permanent" type="checkbox" name="p" value="t"  /><label for="permanent">Permanent</label></p></h2>
                        <h2><p><input id="contract" type="checkbox" name="c" value="t"  checked /><label for="contract">Contract</label></p></h2>
                        <h2><p><input id="graduate" type="checkbox" name="g" value="t"   checked/><label for="graduate">Graduate / Internships</label></p></h2></div>';
}
elseif($_SESSION['p'] != "t" and $_SESSION['c'] != "t" and $_SESSION['g'] == "t"){//grad
    $jtIndeed="internship";
    $jt=" and (job_type =6) ";
    $_SESSION["c"]="";
    $_SESSION["g"]="t";
    $_SESSION["p"]="";
    $chk_jt='<div class="job_type_chk_search"><h2><p><input id="permanent" type="checkbox" name="p" value="t"  /><label for="permanent">Permanent</label></p></h2>
                        <h2><p><input id="contract" type="checkbox" name="c" value="t"  /><label for="contract">Contract</label></p></h2>
                        <h2><p><input id="graduate" type="checkbox" name="g" value="t"  checked /><label for="graduate">Graduate / Internships</label></p></h2></div>';
}
elseif($_SESSION['p'] == "t" and $_SESSION['c'] != "t" and $_SESSION['g'] != "t"){//permanent
    $jtIndeed="fulltime";
    $jt=" and ( job_type =4) ";
    $_SESSION["c"]="";
    $_SESSION["g"]="";
    $_SESSION["p"]="t";
    $chk_jt='<div class="job_type_chk_search"><h2><p><input id="permanent" type="checkbox" name="p" value="t"  checked /><label for="permanent">Permanent</label></p></h2>
                        <h2><p><input id="contract" type="checkbox" name="c" value="t"  /><label for="contract">Contract</label></p></h2>
                        <h2><p><input id="graduate" type="checkbox" name="g" value="t"  /><label for="graduate">Graduate / Internships</label></p></h2></div>';
}
elseif($_SESSION['p'] != "t" and $_SESSION['c'] == "t" and $_SESSION['g'] != "t"){//contract
    $jtIndeed="contract";
    $jt=" and ( job_type =3 ) ";
    $_SESSION["c"]="t";
    $_SESSION["g"]="";
    $_SESSION["p"]="";
    $chk_jt='<div class="job_type_chk_search"><h2><p><input id="permanent" type="checkbox" name="p" value="t"  /><label for="permanent">Permanent</label></p></h2>
                        <h2><p><input id="contract" type="checkbox" name="c" value="t"  checked /><label for="contract">Contract</label></p></h2>
                        <h2><p><input id="graduate" type="checkbox" name="g" value="t"  /><label for="graduate">Graduate / Internships</label></p></h2></div>';
}
///------------------------------------------------
$searchCombo= trim($keyword." ".$location," ");
$selectJob="SELECT * from jobs ";
$smallKeyword="";
$smallLike="";
if($keyword !="") {
    $keyPieces = explode(" ", $keyword);
    if (count($keyPieces) > 3) {
        if (strlen($keyPieces[0]) < 3) {
            $smallKeyword = $keyPieces[0];
        }
        if (strlen($keyPieces[1]) < 3) {
            $smallKeyword = $keyPieces[1];
        }
        if (strlen($keyPieces[2]) < 3) {
            $smallKeyword = $keyPieces[3];
        }
        if (strlen($keyPieces[3]) < 3) {
            $smallKeyword = $keyPieces[3];
        }
    } elseif (count($keyPieces) > 2) {
        if (strlen($keyPieces[0]) < 3) {
            $smallKeyword = $keyPieces[0];
        }
        if (strlen($keyPieces[1]) < 3) {
            $smallKeyword = $keyPieces[1];
        }
        if (strlen($keyPieces[2]) < 3) {
            $smallKeyword = $keyPieces[3];
        }
    } elseif (count($keyPieces) > 1) {
        if (strlen($keyPieces[0]) < 3) {
            $smallKeyword = $keyPieces[0];
        }
        if (strlen($keyPieces[1]) < 3) {
            $smallKeyword = $keyPieces[1];
        }

    }

    if ($smallKeyword != "") {
        $noSmallKeyword="";
        for ($x = 0; $x < count($keyPieces); $x++) {
            if(strlen($keyPieces[$x])>2){
                $noSmallKeyword .= $keyPieces[$x]." ";
            }
        }
        $noSmallKeyword= trim($noSmallKeyword);
        $smallLike = " and(key1 LIKE '$smallKeyword%' or key2 LIKE '$smallKeyword%' or key3 LIKE '$smallKeyword%')";
    }
}
$random=", rand() desc";
$companyLike=" or r.recruiter_company_name LIKE '%$keyword%')";
$join=" as j left outer join recruiter as r on (j.recruiter_id=r.recruiter_id) left outer join recruiter_login as rl on (j.recruiter_id=rl.recruiter_id)";
if(strpos($keyword,"+")){//both ,key has +
    $query = "$selectJob $join where job_location LIKE '%$location%'$smallLike and (key1 LIKE '$keyword%' or key2 LIKE '$keyword%' or key3 LIKE '$keyword%' or r.recruiter_company_name LIKE '%$keyword%')and rl.recruiter_status='Yes' $jt $ct and job_status='Yes'  order by job_featured='Yes' desc $random";
}
elseif(strlen($keyword)<3 and $keyword!="" and $location!=""){//both ,key<3
    $query = "$selectJob $join where job_location LIKE '%$location%'$smallLike and (key1 LIKE '$keyword%' or key2 LIKE '$keyword%' or key3 LIKE '$keyword%' or r.recruiter_company_name LIKE '%$keyword%')and rl.recruiter_status='Yes' $jt $ct and job_status='Yes' order by job_featured='Yes' desc $random";
}
elseif(strlen($keyword)<3 and $keyword!=""){//keyword only, key<3
    $query = "$selectJob $join where job_title LIKE '%$keyword%'$smallLike and (key1 LIKE '$keyword%' or key2 LIKE '$keyword%' or key3 LIKE '$keyword%' or r.recruiter_company_name LIKE '%$keyword%')and rl.recruiter_status='Yes' $jt $ct and job_status='Yes' order by job_featured='Yes' desc $random";
}
elseif($smallLike!="" and $keyword!=""){//small words beast query
    $query = "$selectJob $join where (job_title LIKE '%$noSmallKeyword%' $companyLike and job_location LIKE '%$location%'$smallLike and rl.recruiter_status='Yes' $jt $ct and job_status='Yes' order by job_featured='Yes' desc $random";
}
elseif($keyword!="" and $location!=""){
    $query = "SELECT *, MATCH(job_title) AGAINST (\"$keyword\" IN BOOLEAN MODE) as rel1,MATCH(job_description) AGAINST (\"$keyword\" IN BOOLEAN MODE) as rel2 from jobs $join where (MATCH(job_title, job_description) AGAINST (\"$keyword\" IN BOOLEAN MODE)$companyLike and rl.recruiter_status='Yes' $jt $ct and job_location LIKE '%$location%'$smallLike and job_status='Yes' order by job_featured='Yes' desc , (rel1*2)+(rel2)desc $random";
}
elseif($searchCombo==""){
    $query = "$selectJob $join where job_status='Yes' and rl.recruiter_status='Yes' $jt $ct order by job_featured='Yes' desc $random";//add more sorts, random
}
elseif($keyword!="" and $location!=""){
    $query = "$selectJob $join where (job_title LIKE '%$keyword%'$companyLike and job_location LIKE '%$location%'$smallLike and rl.recruiter_status='Yes' $jt $ct and job_status='Yes'  order by job_featured='Yes' desc $random";//add more sorts, random
}
elseif($keyword!=""){
    $query = "SELECT *, MATCH(job_title) AGAINST (\"$keyword\" IN BOOLEAN MODE) as rel1,MATCH(job_description) AGAINST (\"$keyword\" IN BOOLEAN MODE) as rel2 from jobs $join where (MATCH(job_title, job_description) AGAINST (\"$keyword\" IN BOOLEAN MODE)$companyLike and rl.recruiter_status='Yes' $jt $ct $smallLike and job_status='Yes' order by job_featured='Yes' desc , (rel1*2)+(rel2)desc $random";
}
elseif($location!=""){
    $query = "$selectJob $join where job_location LIKE '%$location%' and rl.recruiter_status='Yes' $jt $ct and job_status='Yes' order by job_featured='Yes' desc $random";//add more sorts, random
}
///------------------------
//echo $query;
include_once("../i-indeed-ajax.php");
$obj = new pagination_class($query,$starting,$recpage,$keyword,$location);
$result1 = $obj->result;
$content='';
if(mysql_num_rows($result1)!=0)
{
    $count=1;
    $pages='<div id="page_contents" width="100%" border="0" cellspacing="0" cellpadding="0"><div><div width="80%">'.$obj->anchors.'</div>';

    $content.='<div class="result_table">'.$job[0].''.$job[1];

    while($row = tep_db_fetch_array($result1))
    {
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
        $ide=$row["job_id"];
        $query_string=encode_string("job_id=".$ide."=job_id");

        $recruiter_logo='';

        $title_format=encode_category($row['job_title']);
        if($row['jobg8_logo_url'] == null){
            $company_logo=$row['recruiter_logo'];
            $company_name = tep_db_output($row['recruiter_company_name']);
        }
        else{
            $company_name = $row['jobg8_company'];

            $company_logo= '<img src= '. $row['jobg8_logo_url'] .'>';
        }

        $apply_before=tep_date_long($row['expired']);
        if($company_name == "None"){//the regular jobs seem to have this often
            $company_name ="";
        }
        if($row['recruiter_id']!= 826){
            if(tep_not_null($company_logo) && is_file(PATH_TO_MAIN_PHYSICAL.PATH_TO_LOGO.$company_logo))
                $recruiter_logo=tep_image(FILENAME_IMAGE."?image_name=".PATH_TO_LOGO.$company_logo."&size=120");
        }
        else{
            $recruiter_logo= '<img src= '. $row['jobg8_logo_url'] .'>';
        }

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
        $jobDetails = ' '.tep_href_link($ide.'/'.$title_format.'.html').' ';

        //-----INSERT RECRUITER ID THAT HAS IFRAME BLOCKED TO USE NO IFRAME AND USE JUST OUR SITE INSTEAD-----
//LIST OF COMPANIES HERE NOW IS:
        if($row["recruiter_id"] == 419 OR $row["recruiter_id"] == 273 OR $row["recruiter_id"] == 335 OR $row["recruiter_id"] == 349 OR $row["recruiter_id"] == 322 OR $row["recruiter_id"] == 62 OR $row["recruiter_id"] == 59 OR $row["recruiter_id"] == 86 OR $row["recruiter_id"] == 423 OR $row["recruiter_id"] == 264 OR $row["recruiter_id"] == 20 OR $row["recruiter_id"] == 66 OR $row["recruiter_id"] == 663 OR $row["recruiter_id"] == 417){
            //new tab
            $post_url_click = $row['url'];
            $clickLink = '<a class="jobAnchor" target="_tab" href="'.$post_url_click.'">';
        }else{
            //iframe
            $clickLink = '<a class="jobAnchor" href="'.$jobDetails.'">';
        }
        $content.='

  '.$clickLink.'
  <div id="mobileRow" class="table-row" style="'.$featured.'"'.$featuredClass.'>
		<div id="mobileLogo" class="table-cell company">'.$recruiter_logo.'</div>
		<div class="table-cell description">
			<div class="name">'.tep_db_output($row['job_title']).'</div>
			<span class="location-company" ><div class="companyName">'.$company_name.'</div><img class="location-img" src="img/location.png" width="12" height="17" alt=""/ >   '.tep_db_output($row['job_location']).'</span>
			<p class="descriptionSummary">'.nl2br(tep_db_output(strip_tags($row['job_short_description']))).'</p>

		</div>

		<div class="skillGroup">
		'.$tag1.'
		'.$tag2.'
		'.$tag3.'
		    <div class="levelGroup">
                '.$job_level_word.'
                '.$job_type_word.'
		    </div>
		</div>
		<div id="mobileInnerRow" class="table-cell name640">
                            <div id="mobileName" class="name">'.tep_db_output($row['job_title']).'</div>
                            <span id="mobileLocation-company" class="location-company" ><div id="mobileCompanyName" class="companyName">'.$company_name.'</div><img id="mobileLocation-img" class="location-img" src="img/location.png" width="12" height="17" alt=""/ >   '.tep_db_output($row['location']).'</span>
                        </div>

	</div></a>';

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
        /////////////////////////////////////////////////////////
    }

    $content.=$job[2].''.$job[3]."</div>";

    echo $content;
    echo $pages;
    echo $indeedApiLogo;

    //echo $total;
}
else
{
    //$template->assign_vars(array('total'=>SITE_TITLE." ".INFO_TEXT_HAS_NOT_MATCHED." <br><br>&nbsp;&nbsp;&nbsp;"));
}
$template->assign_vars(array(
    'chk_ct' => $chk_ct,
    'chk_jt' => $chk_jt,
    'RIGHT_BOX_WIDTH' => RIGHT_BOX_WIDTH1,
    'RIGHT_HTML' => RIGHT_HTML,
    'update_message' => $messageStack->output()));
?>
