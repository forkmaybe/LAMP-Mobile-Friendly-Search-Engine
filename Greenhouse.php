<?php

/**
 * Description:
 * ------------
 * Script takes a list of xml feeds,
 * parses each job out and checks the database for its existance.
 * If the job already exists, it is skipped. Otherwise its added.
 *
 * If expired date is not entered the job will not appear on the site.
 *
 * this script also establishes those jobs which are nolonger available on the
 * various jobs sites and updates the database by marking the "job_status" fields
 * as "No" and also updates the time the record was last updated.
 **/

// Db connection setup
$db_connection = mysql_connect( 'localhost', 'admin', 'sm4rt1010' );
$db = mysql_select_db( "temp");

if (!$db_connection || !$db) {
    die("Problem with connection to the database.");
}

function check404( $url ) {

    $handle = curl_init( $url );
    curl_setopt( $handle,  CURLOPT_RETURNTRANSFER, TRUE );
    /* Get the HTML or whatever is linked in $url. */
    $response = curl_exec( $handle );

    /* Check for 404 (file not found). */
    $httpCode = curl_getinfo( $handle, CURLINFO_HTTP_CODE );

    if ( $httpCode == 404 ) {
        curl_close( $handle );
        return True;
    }
    else {
        curl_close( $handle );
        return False;
    }
}


/*
  Function to retreive a given recruiters jobs titles
*/
function getRecruitersJobs($recruiter_id) {
    global $db_connection, $db;
    $query = "SELECT `url`, `job_title` FROM `jobs` WHERE `recruiter_id` = $recruiter_id";
    $query_result = mysql_query($query);

    $recruiter_jobs = Array();

    if (mysql_num_rows($query_result) == 0) {
        echo "\nNo jobs found for this recruiter ID $recruiter_id.<Br/>\n";
    }
    else {
        while ($row = mysql_fetch_assoc($query_result)) {
            $recruiter_jobs[$row["url"]] = $row["job_title"];
        }
        mysql_free_result($query_result);
    }
    return $recruiter_jobs;
}

//Function to retreive recruiter name
function getRecruiter($recruiter_id) {
    global $db_connection, $db;
    $query = "SELECT `recruiter_first_name`,`recruiter_id`,`recruiter_ats` FROM `recruiter` WHERE `recruiter_id` = $recruiter_id";
    $query_result = mysql_query($query);

    if (mysql_num_rows($query_result) == 0) {
        echo "\nNo recruiter found for recruiter ID $recruiter_id.<Br/>\n\n";
    }
    else {
        $row = mysql_fetch_assoc($query_result);
    }
    return $row;
}

function CheckIfExists( $url, $title ) {
    global $db_connection, $db;
    $query = "SELECT * FROM `jobs` WHERE `url` = '$url' AND `job_title` = '$title'";

    // $db_connection = mysql_connect( 'localhost', 'admin', 'sm4rt1010' );
    if ( !$db_connection ) {
        die( 'Could not connect: ' . mysql_error() );
    }

    // $db = mysql_select_db( "temp" );
    if ( !$db ) {
        die( 'Invalid query: ' . mysql_error() );
    }

    $result = mysql_query( $query );
    if ( !$result ) {
        die( 'Invalid query: ' . mysql_error() );
    }

    $row = mysql_fetch_assoc( $result );
    return count( $row );
}

//$temp = CheckIfExists("https://www.dropbox.com/jobs/listing/434","Co9re Account Executive");

//if ($temp > 1) echo "\nMatches were found";

//else { echo "No matches were found";}


function addjob( $title, $shortdesc, $desc, $location, $joburl, $rid , $job_type, $s1, $s2, $s3, $l) {
    global $db_connection, $db;
    // $db_connection = mysql_connect( 'localhost', 'admin', 'sm4rt1010' );
    if ( !$db_connection ) {
        die( 'Could not connect: ' . mysql_error() );
    }

    $advertised_date = new DateTime();
    $re_adv = $advertised_date->format( 'Y-m-d h:i:s');
    $e_object = new DateTime();
    $expired_date = $e_object->add(new DateInterval('P365D'));
    $expired = $expired_date->format( 'Y-m-d h:i:s' );
    $d_object = new DateTime();
    $deleted_date = $d_object->add(new DateInterval('P365D'));
    $deleted = $deleted_date->format( 'Y-m-d h:i:s' );

    $array = array(
        "job_id" => "' '",
        "display_id" => 'HJ9',
        "recruiter_id" => "$rid",
        "recruiter_user_id" => "$rid",
        "job_source" => "jobsite",
        "inserted" => date( "Y/m/d" ),
        "updated" =>  "' '",
        "deleted" =>  "",
        "re_adv" => "$re_adv",
        "expired" => "$expired",
        "job_title" => "$title",
        "job_reference" =>  "' '",
        "job_country_id" => "' '",
        "job_state_id" => 0,
        "job_state" => '',
        "job_location" => "$location",
        "currency" => 0,
        "job_salary" =>  "' '",
        "job_allowance" =>  "' '",
        "job_industry_sector" =>  "' '",
        "job_short_description" => "$shortdesc",
        "job_description" => "$desc",
        "job_type" => "$job_type",
        "job_relocate" => "yes",
        "min_experience" => '0',
        "max_experience" => '12',
        "job_vacancy_period" => "200",
        "job_status" => "yes",
        "job_featured" => "no",
        "post_url" => "yes",
        "url" =>"$joburl",
        "skill1" =>"$s1",
        "skill2" =>"$s2",
        "skill3" =>"$s3",
        "level" =>"$l"
    );

    $query = "INSERT INTO `jobs`(`job_id`, `display_id`, `recruiter_id`, `recruiter_user_id`, `job_source`, `inserted`, `updated`, `deleted`, `re_adv`, `expired`, `job_title`, `job_reference`, `job_country_id`, `job_state_id`, `job_state`, `job_location`, `currency`, `job_salary`, `job_allowance`, `job_industry_sector`, `job_short_description`, `job_description`, `job_type`, `job_relocate`, `min_experience`, `max_experience`, `job_vacancy_period`, `job_status`, `job_featured`, `post_url`, `url`, `key1`, `key2`, `key3`, `level`) VALUES (";
    foreach ( $array as $key => $value ) {
        $query .= '"' . $value . '"' . ",";
    }

    $query = substr( $query, 0, -1 );
    $query = $query .= ")";
    // $db = mysql_select_db( "temp" );

    if ( !$db ) {
        die( 'Invalid query: ' . mysql_error() );
    }

    $result = mysql_query( $query );
    if ( !$result ) {
        die( 'Invalid query: ' . mysql_error() );
    }
}

function addwholecompany( $xml, $recid ) {

    $existing_jobs = getRecruitersJobs($recid);
    //var_dump(addjob("network engineer","Duties include...","Duties for this position include maintenance of servers"));
    //$xml = 'airspeed.xml';

    $xmlDoc = new DOMDocument();
    $xmlDoc->load( $xml );

    $channel=$xmlDoc->getElementsByTagName( 'channel' )->item( 0 );
    $res = $channel->getElementsByTagName( 'job' );
    $elem = $res->length;
//adding all skills to the skills array from db
    $result = mysql_query("SELECT `skill` FROM `skills`");

    $skills = array();
    $resultsCount = array();
    $z=0;
    while($row = mysql_fetch_assoc($result))
    {
        $skills[] = $row["skill"];
        $z++;

    }//now we have all skills
    $allLevels = mysql_query("SELECT `level` FROM `levels`");

    $levels = array();
    $levelCount=0;
    while($row = mysql_fetch_assoc($allLevels))
    {
        $levels[] = $row["level"];
        $levelCount++;
    }//now we have all skills

    $i = 0;
    while ( $i < $elem ) {
        $jobtitle = mysql_escape_string($channel->getElementsByTagName('job')->item($i)->childNodes->item(0)->nodeValue);
        $location = mysql_escape_string($channel->getElementsByTagName('job')->item($i)->childNodes->item(1)->nodeValue);
        $field = mysql_escape_string($channel->getElementsByTagName('job')->item($i)->childNodes->item(2)->nodeValue);
        $description = mysql_escape_string($channel->getElementsByTagName('job')->item($i)->childNodes->item(4)->nodeValue);
        $shortdescription = mysql_escape_string(substr($description, 0, 250));
        $shortdescription = str_replace(array('\r', '\n'), ' ', $shortdescription);
        $joburl = $channel->getElementsByTagName('job')->item($i)->childNodes->item(5)->nodeValue;
        $key1 = "";
        $key2 = "";
        $key3 = "";
        $level = "";

        for ($x = 0; $x < count($levels); $x++) {
            $levelSearch = strpos($jobtitle, $levels[$x]);
            if ($levelSearch !== false) {//if true then skill is in title
                $level = $levels[$x];
            }
        }

        for ($x = 0; $x < count($skills); $x++) {
            $search = strpos($jobtitle, $skills[$x]);
            if ($search !== false) {//if true then skill is in title
                if ($key1 == "" and $key2 != $skills[$x] and $key3 != $skills[$x]) {//if skill is already here
                    $key1 = $skills[$x];
                } elseif ($key2 == "" and $key1 != $skills[$x] and $key3 != $skills[$x]) {//^then put in here
                    $key2 = $skills[$x];
                } elseif ($key3 == "" and $key1 != $skills[$x] and $key2 != $skills[$x]) {
                    $key3 = $skills[$x];
                }
            }
        }//end job title skill check & begin description
        for ($x = 0; $x < count($skills); $x++) {
            $search = strpos($description, $skills[$x]);
            if ($search !== false) {//if true then skill is in description
                if ($key1 == "" and $key2 != $skills[$x] and $key3 != $skills[$x]) {//if skill is already here
                    $key1 = $skills[$x];
                } elseif ($key2 == "" and $key1 != $skills[$x] and $key3 != $skills[$x]) {//^then put in here
                    $key2 = $skills[$x];
                } elseif ($key3 == "" and $key1 != $skills[$x] and $key2 != $skills[$x]) {//checks if other key variables already have the skill
                    $key3 = $skills[$x];
                }
            }
        }

        if (strcasecmp($jobtitle, "Permanent") == 0 || strcasecmp($jobtitle, "Full time") == 0 || strcasecmp($jobtitle, "Full-time") == 0) {//not case sensitive
            $job_type = 4;
        } elseif (strcasecmp($jobtitle, "Contract") == 0 || strcasecmp($jobtitle, "temporary") == 0) {
            $job_type = 3;
        } elseif (strcasecmp($jobtitle, "Internship") == 0 || strcasecmp($jobtitle, "Intern") == 0 || strcasecmp($jobtitle, "Graduate") == 0) {
            $job_type = 6;
        } else {
            $job_type = 4;
        }

        if ($existing_jobs[$joburl]) {
            unset($existing_jobs[$joburl]);
        }

        $temp = CheckIfExists($joburl, $jobtitle);

        if ($temp > 1) {
            echo "\nMatches were found, file has been skipped<br/><br/>\n";
        } else {
            echo "\nNo matches were found &mdash; adding job.<br/><br/>\n";
            $time = time();


            //checks here

            //print_r($key1);


            addjob($jobtitle, $shortdescription, $description, $location = $location, $joburl, $recid, $job_type, $key1, $key2, $key3, $level);


            $i++;
        }

        // Determine which jobs are to be marked as deleted on the site and update the job record
        // accordingly.
        foreach ($existing_jobs as $key => $value) {
            $date_job_updated = new DateTime();
            $str_date_job_updated = $date_job_updated->format('Y-m-d h:i:s');
            $update_job = "UPDATE jobs SET updated = '$str_date_job_updated', job_status = 'No' WHERE url = '$key'";
            $result = mysql_query($update_job);
            if (!$result) {
                echo "Failed to update row: " . mysql_error();
            }
        }
    }
    $recruiter = getRecruiter($recid);
    echo " Finished update for ".implode(",",$recruiter)." ";
}

$employers = array(

   "http://80.240.128.84/Greenhouse/acquia_480.xml" => 480,
   "http://80.240.128.84/Greenhouse/aclima_501.xml" => 501,
   "http://80.240.128.84/Greenhouse/airware_502.xml" => 502,
   "http://80.240.128.84/Greenhouse/axiomzen_404.xml" => 404,
   "http://80.240.128.84/Greenhouse/box_334.xml" => 334,
   "http://80.240.128.84/Greenhouse/cloudreach_355.xml" => 355,
   "http://80.240.128.84/Greenhouse/continuum_505.xml" => 505,
   "http://80.240.128.84/Greenhouse/crosslend_475.xml" => 475,
   "http://80.240.128.84/Greenhouse/doordash_506.xml" => 506,
   "http://80.240.128.84/Greenhouse/digitalocean_391.xml" => 391,
   "http://80.240.128.84/Greenhouse/disqus_311.xml" => 311,
   "http://80.240.128.84/Greenhouse/docker_533.xml" => 533,
   "http://80.240.128.84/Greenhouse/duedil_478.xml" => 478,
   "http://80.240.128.84/Greenhouse/formlabs_537.xml" => 537,
   "http://80.240.128.84/Greenhouse/generalassembly_477.xml" => 477,
   "http://80.240.128.84/Greenhouse/genscape_661.xml" => 661,
   "http://80.240.128.84/Greenhouse/gocardless_508.xml" => 508,
   "http://80.240.128.84/Greenhouse/grovo_599.xml" => 599,
   "http://80.240.128.84/Greenhouse/gusto_403.xml" => 403,
   "http://80.240.128.84/Greenhouse/hinge_457.xml" => 457,
   "http://80.240.128.84/Greenhouse/justworks_401.xml" => 401,
   "http://80.240.128.84/Greenhouse/iix_509.xml" => 509,
   "http://80.240.128.84/Greenhouse/khanacademy_455.xml" => 455,
   "http://80.240.128.84/Greenhouse/kreditech_392.xml" => 392,
   "http://80.240.128.84/Greenhouse/livestream_320.xml" => 320,
   "http://80.240.128.84/Greenhouse/logentries_535.xml" => 535,
   "http://80.240.128.84/Greenhouse/mainstreethub_511.xml" => 511,
   "http://80.240.128.84/Greenhouse/metromile_512.xml" => 512,
   "http://80.240.128.84/Greenhouse/mobileiron_341.xml" => 341,
   "http://80.240.128.84/Greenhouse/mongodb_127.xml" => 127,
   "http://80.240.128.84/Greenhouse/newstore_482.xml" => 482,
   "http://80.240.128.84/Greenhouse/picarro_430.xml" => 430,
   "http://80.240.128.84/Greenhouse/purestorage_531.xml" => 531,
   "http://80.240.128.84/Greenhouse/qualtrics_346.xml" => 346,
   "http://80.240.128.84/Greenhouse/rubrik_529.xml" => 529,
   "http://80.240.128.84/Greenhouse/shopkeep_514.xml" => 514,
   "http://80.240.128.84/Greenhouse/showpad_497.xml" => 497,
   "http://80.240.128.84/Greenhouse/signpost_515.xml" => 515,
   "http://80.240.128.84/Greenhouse/simplesurance_516.xml" => 516,
   "http://80.240.128.84/Greenhouse/smarterhq_517.xml" => 517,
   "http://80.240.128.84/Greenhouse/snapchat_351.xml" => 351,
   "http://80.240.128.84/Greenhouse/sparkcentral_481.xml" => 481,
   "http://80.240.128.84/Greenhouse/surveymonkey_399.xml" => 399,
   "http://80.240.128.84/Greenhouse/techstarts_518.xml" => 518,
   "http://80.240.128.84/Greenhouse/tripadvisor_474.xml" => 474,
   "http://80.240.128.84/Greenhouse/truecaller_352.xml" => 352,
   "http://80.240.128.84/Greenhouse/thumbtack_459.xml" => 459,
   "http://80.240.128.84/Greenhouse/tubemogul_350.xml" => 350,
   "http://80.240.128.84/Greenhouse/twillio_342.xml" => 342,
   "http://80.240.128.84/Greenhouse/vimeo_359.xml" => 359,
   "http://80.240.128.84/Greenhouse/voxy_402.xml" => 402,
   "http://80.240.128.84/Greenhouse/wikimedia_354.xml" => 354,
   "http://80.240.128.84/Greenhouse/wrike_353.xml" => 353,
   "http://80.240.128.84/Greenhouse/genscape_661.xml" => 661,
   "http://80.240.128.84/Greenhouse/yplan_377.xml" => 377
);

// Workaround for older versions
foreach ( $employers as $employer => $key ) {
    $checkResult = check404($employer);
    if ( check404( $employer ) == False ) {
        addwholecompany( $employer, $key );
    }
}

?>
