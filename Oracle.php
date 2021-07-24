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

  "http://52.33.123.93/Taleo/oracle_270.xml" => 270

);

// Workaround for older versions
foreach ( $employers as $employer => $key ) {
    $checkResult = check404($employer);
    if ( check404( $employer ) == False ) {
        addwholecompany( $employer, $key );
    }
}

?>
