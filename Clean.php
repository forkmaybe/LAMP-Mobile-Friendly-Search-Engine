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

updateJobs();

/*
  Function to retreive a given recruiters jobs titles
*/
function updateJobs()
{
    global $db_connection, $db;

    //adding all skills to the skills array from db
    $result = mysql_query("SELECT `skill`,`id`  FROM `skills`");

    $skills = array();
    $z = 0;
    while ($row = mysql_fetch_assoc($result)) {
        $id[] = $row["id"];
        $skills[] = preg_replace('/\s+/', '', $row["skill"]);
        echo $skills[$z];
        $query = mysql_query("UPDATE skills SET skill = '$skills[$z]' WHERE id = '$id[$z]'");

        $z++;

    }

}

    ?>
