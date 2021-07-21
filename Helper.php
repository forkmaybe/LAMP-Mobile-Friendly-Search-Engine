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
$db_connection = mysql_connect( 'localhost', 'root', 'scraping' );
$db = mysql_select_db( "live");

if (!$db_connection || !$db) {
    die("Problem with connection to the database.");
}

updateJobs();

/*
  Function to retreive a given recruiters jobs titles
*/
function updateJobs() {
    global $db_connection, $db;
    $query = "SELECT * FROM `jobs`";
    $query_result = mysql_query($query);
    //have

    echo $query_result;
    //adding all skills to the skills array from db
    $result = mysql_query("SELECT `skill` FROM `skills`");

    $skills = array();
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
    $job_id = array();
    $jobtitle = array();
    $description = array();

    $query = "SELECT `job_id`,`job_title`,`job_description`,`job_title`  FROM `jobs`";
    $query_result = mysql_query($query);
    $q=0;

    if (mysql_num_rows($query_result) == 0) {
        echo "No jobs found.<Br/>";
    }
    else {
        while($row = mysql_fetch_assoc($query_result))
        {
            $key1 = "";
            $key2 = "";
            $key3 = "";
            $level = "";
            $job_id[] = $row["job_id"];
            $jobtitle[] =$row["job_title"];
            $description[] =$row["job_description"];


            for($x=0;$x < count($levels);$x++) {
                $levelSearch = strpos($jobtitle[$q], $levels[$x]);
                $notManager = strpos($jobtitle[$q], "Account Manager");
                if ($levelSearch !== false and $notManager == false) {//if true then skill is in title
                    $level = $levels[$x];
                }
            }

            for($x=0;$x < count($skills);$x++) {
                $search = strpos($jobtitle[$q], $skills[$x]);
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
            for($x=0;$x < count($skills);$x++) {
                $search = strpos($description[$q], $skills[$x]);
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
            $query = "UPDATE jobs SET key1 = '$key1', key2 = '$key2', key3 = '$key3', level = '$level' WHERE job_id = '$job_id[$q]'";
            $executed = mysql_query($query);
            $q++;

        }




    }





}

?>
