<?
include_once("general_functions/indeed_xml_import.php");
$host = $_SERVER['HTTP_HOST'];
$z=0;
    $publisher_id = 3242257164732114;
    $country_code = "";
    $sort_by = "date";
    $limit=2;
if($i_page < $recpage){
    if($i_page == 0){
        $limit=$recpage;
    }
    else{
        $limit=$recpage - $i_page;
    }
}
$start=0;
if($location=="" and $keyword=="") {//nothing-----------------should be limit of 2 then 4 ---- start goes up 2 each page
    $content = read_indeed_xml('http://api.indeed.com/ads/apisearch?publisher=' . urlencode($publisher_id) . '&q=' . urlencode("software") . '&l=&sort=' . urlencode($sort_by) . '&radius=&st=&jt=' . urlencode($jtIndeed) . '&start='. urlencode($start).'&limit=' . urlencode($limit) . '&fromage=%20&filter=&latlong=&co=&chnl=' . urlencode("sa") . '&userip=' . urlencode($_SERVER['REMOTE_ADDR']) . '&v=2&useragent='. urlencode($_SERVER['HTTP_USER_AGENT']));
    //echo $job_title."nothing---";
}
else if(tep_not_null($location) and $location!="" and tep_not_null($keyword) and $keyword!=""){//both
    $find = mysql_query("SELECT country_code, MATCH(location) AGAINST ('.$location.') as score from indeed_feed where MATCH(location) AGAINST ('.$location.')order by score desc limit 1");
    if (mysql_num_rows($find) == 0) {
        //echo "0----------------";
        $content = read_indeed_xml('http://api.indeed.com/ads/apisearch?publisher=' . urlencode($publisher_id) . '&q=' . urlencode($keyword) . '&l=' . urlencode($location) . '&sort=' . urlencode($sort_by) . '&radius=&st=&jt=' . urlencode($jtIndeed) . '&start=' . urlencode($start) . '&limit=' . urlencode($limit) . '&fromage=%20&filter=&latlong=&co=&chnl=' . urlencode("sa") . '&userip=' . urlencode($_SERVER['REMOTE_ADDR']) . '&v=2&useragent=' . urlencode($_SERVER['HTTP_USER_AGENT']));
    }
    else {
        while ($row = mysql_fetch_assoc($find)) {
            $country_code=$row['country_code'];
            //echo "0----------------$country_code";
            $content = read_indeed_xml('http://api.indeed.com/ads/apisearch?publisher=' . urlencode($publisher_id) . '&q=' . urlencode($keyword) . '&l=' . urlencode($location) . '&sort=' . urlencode($sort_by) . '&radius=&st=&jt=' . urlencode($jtIndeed) . '&start=' . urlencode($start) . '&limit=' . urlencode($limit) . '&fromage=%20&filter=&latlong=&co=' . urlencode($country_code) . '&chnl=' . urlencode("sa") . '&userip=' . urlencode($_SERVER['REMOTE_ADDR']) . '&v=2&useragent=' . urlencode($_SERVER['HTTP_USER_AGENT']));
            //echo $job_title."both---";
            //echo 'http://api.indeed.com/ads/apisearch?publisher=' . urlencode($publisher_id) . '&q=' . urlencode($keyword) . '&l=' . urlencode($location) . '&sort=' . urlencode($sort_by) . '&radius=&st=&jt=' . urlencode($jtIndeed) . '&start=' . urlencode($start) . '&limit=' . urlencode($limit) . '&fromage=%20&filter=&latlong=&co=' . urlencode($country_code) . '&chnl=' . urlencode("sa") . '&userip=' . urlencode($_SERVER['REMOTE_ADDR']) . '&v=2&useragent='.urlencode($_SERVER['HTTP_USER_AGENT']);
        }
    }
}
else if(tep_not_null($location) and $location!=""){//location
    $find = mysql_query("SELECT country_code, MATCH(location) AGAINST ('.$location.') as score from indeed_feed where MATCH(location) AGAINST ('.$location.')order by score desc limit 1");
    if (mysql_num_rows($find) == 0) {
        $content = read_indeed_xml('http://api.indeed.com/ads/apisearch?publisher=' . urlencode($publisher_id) . '&q=' . urlencode("software") . '&l=' . urlencode($location) . '&sort=' . urlencode($sort_by) . '&radius=&st=&jt=' . urlencode($jtIndeed) . '&start=' . urlencode($start) . '&limit=' . urlencode($limit) . '&fromage=%20&filter=&latlong=&co=&chnl=' . urlencode("sa") . '&userip=' . urlencode($_SERVER['REMOTE_ADDR']) . '&v=2&useragent=' . urlencode($_SERVER['HTTP_USER_AGENT']));

    }
    else {
        while ($row = mysql_fetch_assoc($find)) {
            $country_code=$row['country_code'];
            $content = read_indeed_xml('http://api.indeed.com/ads/apisearch?publisher=' . urlencode($publisher_id) . '&q=' . urlencode("software") . '&l=' . urlencode($location) . '&sort=' . urlencode($sort_by) . '&radius=&st=&jt=' . urlencode($jtIndeed) . '&start=' . urlencode($start) . '&limit=' . urlencode($limit) . '&fromage=%20&filter=&latlong=&co=' . urlencode($country_code) . '&chnl=' . urlencode("sa") . '&userip=' . urlencode($_SERVER['REMOTE_ADDR']) . '&v=2&useragent=' . urlencode($_SERVER['HTTP_USER_AGENT']));
            //echo $job_title."both---";
        }
    }
}
else if( tep_not_null($keyword) and $keyword!=""){//keyword
    $content = read_indeed_xml('http://api.indeed.com/ads/apisearch?publisher=' . urlencode($publisher_id) . '&q=' . urlencode($keyword) . '&l=&sort=' . urlencode($sort_by) . '&radius=&st=&jt=' . urlencode($jtIndeed) . '&start='.urlencode($start).'&limit=' . urlencode($limit) . '&fromage=%20&filter=&latlong=&co=&chnl=' . urlencode("sa") . '&userip=' . urlencode($_SERVER['REMOTE_ADDR']) . '&v=2&useragent='. urlencode($_SERVER['HTTP_USER_AGENT']));
    //echo $job_title."keyword---";
}

for ($i = 0; $i < count($content); $i++) {
    $indeed_url = tep_db_prepare_input($content[$i]['url']);
    $job_title = tep_db_prepare_input($content[$i]['job_title']);
    $company = tep_db_prepare_input($content[$i]['company']);
    $job_city = tep_db_prepare_input($content[$i]['job_city']);
    $job_description = tep_db_prepare_input($content[$i]['description']);
    $indeed_click = tep_db_prepare_input($content[$i]['onmousedown']);
    if (strlen($content[$i]['job_title']) > 2) {
        $job[$i] = '<a title="' . $job_title . '" class="jobAnchor" target="_blank" rel="nofollow" href="' . $indeed_url . '" onmousedown=' . $indeed_click . '>
        <div id="mobileRow" class="table-row" >
    <div id="mobileLogo" class="table-cell company">
        <img src="//' . $host . '/image.php?image_name=logo/20140622150017indeed.png&size=120" border="0" alt="">
        </div>
        <div class="table-cell description">
        <div class="name">' . $job_title . '</div>
        <span class="location-company" ><div class="companyName">' . $company . '</div><img class="location-img" src="img/location.png" width="12" height="17" alt=""/>   ' . $job_city . '  </span>
        <p class="descriptionSummary">' . $job_description . '</p>
        </div>

    <div id="mobileInnerRow" class="table-cell name640">
        <div id="mobileName" class="name">' . $job_title . '</div>
        <span id="mobileLocation-company" class="location-company" ><div id="mobileCompanyName" class="companyName">' . $company . '</div><img id="mobileLocation-img" class="location-img" src="img/location.png" width="16" height="21" alt=""/>   ' . $job_city . '  </span>
    </div>
    </div>
        </a>';
        //$totalNumberOfJobs= $i_page+$i+1;
    }
    else{ $job[$i]=null;}
}
//echo "'http://api.indeed.com/ads/apisearch?publisher=' . urlencode($publisher_id) . '&q=' . urlencode('software') . '&l=&sort=' . urlencode($sort_by) . '&radius=&st=&jt=' . urlencode($jtIndeed) . '&start='. urlencode($start).'&limit=' . urlencode($limit) . '&fromage=%20&filter=&latlong=&co=&chnl=' . urlencode('12345') . '&userip=' . urlencode('12345') . '&v=2&useragent='. urlencode('12345')";

//add array to session or pass to ajax
if(count($content)>0){
    shuffle($job);
    $indeedApiLogo='<span id=indeed_at><a href="http://www.indeed.com/">some jobs</a> by <a href="http://www.indeed.com/" title="Job Search"><img src="http://www.indeed.com/p/jobsearch.gif" style="border: 0;vertical-align: middle;" alt="Indeed job search"></a></span>';
}

?>
