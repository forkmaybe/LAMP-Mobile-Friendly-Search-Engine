<?php
include_once('bitly.php');//-------------------------------------------------------------bitly file--------------------------------
// Db connection setup
$db_connection = mysql_connect( 'localhost', 'admin', 'sm4rt1010' );
$db = mysql_select_db( "temp");

if (!$db_connection || !$db) {
    die("Problem with connection to the database.");
}
global $db_connection, $db;
mysql_query("TRUNCATE TABLE tweets");
mysql_query("TRUNCATE TABLE preTweets");
prepTweet(0);

function prepTweet($upToFourDays) {
    $star='<img src="star.png"/>';
    $brokenCompany='<img src="redx.png"/>';
/*
    echo "<style>p {background-color: lightblue;}  .right{position:absolute;right:10px;top:0;} a{text-decoration: none !important;}img{height:0.8em;width:0.8em;margin:1 1 1 1;}
p {
  display: inline-block;
  padding: 16px;
  margin: 5px 0;
  max-width: 500px;
  border: #ddd 1px solid;
  border-top-color: #eee;
  border-bottom-color: #bbb;
  border-radius: 5px;
  box-shadow: 0 1px 3px rgba(0,0,0,0.15);
  font: bold 14px/18px Helvetica, Arial, sans-serif;
  color: #000;
}

p {
  font: normal 18px/24px Georgia, 'Times New Roman', Palatino, serif;
  margin: 0 5px 10px 0;
}

</style>";

    echo '<h1>Tweets to Tweet</h1>   <div class="right"> '.$star.' = featured    '.$brokenCompany.' = not active</div>';
    //adding all skills to the skills array from db
*/
    $result = mysql_query("SELECT `skill` FROM `skills`");

    $skills = array();
    $z=0;
    while($row = mysql_fetch_assoc($result))
    {
        $skills[] = $row["skill"];
        $z++;

    }//now we have all skills

    $q=0;
    $now = date("Y/m/d H:i", $_SERVER['REQUEST_TIME']);//converts from unix time to formatted time for php

    $date1=date_create($now);
    $now = date("d", $_SERVER['REQUEST_TIME']);
    $month = date("m", $_SERVER['REQUEST_TIME']);
    $year = date("Y", $_SERVER['REQUEST_TIME']);

    $minArray=array("5M", "10M", "15M","20M", "25M", "30M","35M", "40M", "45M","50M", "55M");
    $minutes=rand(0, 10);
    $minutes=$minArray[$minutes];

    $tomorrow = $now+1;
    $hour="08";//8am 1st tweet every day
    if(strlen($tomorrow)==1){
        $tomorrow = "0".$tomorrow;
    }
    $time = "$month/$tomorrow/$year $hour:$minutes";//tweets ready for tomorrow
    $time= new DateTime($time);
    $time = $time->format('m/d/Y H:i');
    $name = array();
    $join=" as j left outer join recruiter_login as rl on (j.recruiter_id=rl.recruiter_id)";
    $dates = mysql_query("SELECT * FROM `jobs` $join WHERE `key3` !='' and `job_recruiter_type`= 'Employer' and `job_status`='Yes' and rl.recruiter_status='Yes' ORDER BY RAND()desc");//no agencies

    $nj = 0;
    $count=0;
    $numberJobsNeeded = 100;
    $numberOfTweets=0;
    if (mysql_num_rows($dates) == 0) {
        echo "No jobs found.<Br/>";
    }
    else {
        while ($row = mysql_fetch_assoc($dates) and $numberOfTweets < 100) {
            $featured = $row["job_featured"];
            $job_id[] = $row["job_id"];
            $date_time_inserted[] = $row["re_adv"];//time inserted
            $insertedDateTime = $date_time_inserted[$q];
            $date2 = date_create($insertedDateTime);
            $diff = date_diff($date1, $date2);
            $diff = $diff->format("%a");
            //echo $diff;
            $diff = (int)$diff;
            //var_dump($diff);

            //below is the jobs from the last few days
            if (preg_match('/^[a-zA-Z -,\']+$/', $row["job_location"]) == 1) {

                $job_link = $title_trim = $title_format = $featured = $title = $url = $jobType = $location = $ht1 = $ht2 = $ht3 = $companyTwitter = $companyName = "";
                $featured = $row["job_featured"];
                $location = $row["job_location"];
                $url = $row["url"];
                $id = $row["recruiter_id"];
                $title_trim = $row['job_title'];
                $title_trim = preg_replace('/[^a-zA-Z0-9\-]/', '-', $title_trim);
                $title_trim = preg_replace('/^[\-]+/', '-', $title_trim);
                $title_trim = preg_replace('/[\-]+$/', '-', $title_trim);
                $title_trim = preg_replace('/[\-]{2,}/', '-', $title_trim);
                $title_format = str_replace(' ', '-', $title_trim);
                $title_format = trim($title_format, "-");
                $title_format = strtolower($title_format);
                $job_link = 'www.siliconarmada.com/' . $job_id[$q] . '/' . $title_format . '.html';
                $params = array();
                $params['access_token'] = '8bc356b29971fdf99d6aae09d7d2206ac5639455';
                $params['longUrl'] = 'http://' . $job_link;
                $results = bitly_get('shorten', $params);
                $bitly = $results["data"];
                $job_link = $bitly["url"];
                //$job_link = "www.thisisatest.com";
                //$saURL = "www.siliconarmada.com/$job_id[$q]/job.com";
                $allRecs = mysql_query("SELECT * FROM `recruiter` where `recruiter_id` = '$id'");

                while ($r = mysql_fetch_assoc($allRecs)) {
                    $companyName = $r["recruiter_company_name"];
                    $companyTwitter = $r["recruiter_twitter_username"];
                }//now we have all recs


                $jt = $row["job_type"];
                $jtWord = mysql_query("SELECT * FROM `job_type` where `id` = '$jt'");

                while ($jtRow = mysql_fetch_assoc($jtWord)) {
                    $jobType = $jtRow["type_name"];
                }//3rd hashtag

                if ($row["key1"] != null and $row["key1"] != "") {
                    $skill = $row["key1"];
                    $skillHashTags = mysql_query("SELECT * FROM `skills` where `skill` = '$skill'");

                    while ($skillRow = mysql_fetch_assoc($skillHashTags)) {
                        $ht1 = $skillRow["twitterKeyword"];
                        $account = $skillRow["twitter1"];
                    }//1st hashtag
                    if ($row["key2"] != null and $row["key2"] != "") {
                        $skill = $row["key2"];
                        $skillHashTags = mysql_query("SELECT * FROM `skills` where `skill` = '$skill'");

                        while ($skillRow = mysql_fetch_assoc($skillHashTags)) {
                            $ht2 = $skillRow["twitterKeyword"];
                            $account2nd = $skillRow["twitter1"];
                        }//2nd hashtag
                        if ($row["key3"] != null and $row["key3"] != "") {
                            $skill = $row["key3"];
                            $skillHashTags = mysql_query("SELECT * FROM `skills` where `skill` = '$skill'");

                            while ($skillRow = mysql_fetch_assoc($skillHashTags)) {
                                $ht3 = $skillRow["twitterKeyword"];
                                $account3rd = $skillRow["twitter1"];
                            }//3rd hashtag
                        }
                    }
                }
                if ($row["job_status"] == "No") {
                    $bc = $brokenCompany;
                } else {
                    $bc = "";
                }

                if ($featured == "Yes") {
                    $s = $star;
                } else {
                    $s = "";
                }
                //echo $companyTwitter;
                //var_dump($companyTwitter);

                $title_trim = preg_replace('/[-]/', ' ', $title_trim);
                if(mb_strtoupper(preg_replace('/[ ]/', '', $title_trim), 'utf-8') == preg_replace('/[ ]/', '', $title_trim)){
                    ucwords(strtolower($title_trim));
                }
                $title_trim = str_replace(' m f ', ' ', $title_trim);

                $title_trim = trim(preg_replace('/\s+/', ' ', $title_trim));
                $location = preg_replace('/[()]/', ' ', $location);
                $location = str_replace('Location:', '', $location);
                $location = str_replace('Location', '', $location);
                $location = str_replace(',', ' ', $location);
                $location = str_replace('United States', '', $location);
                $location = str_replace('United Kingdom', '', $location);
                $location = str_replace('Germany', '', $location);
                $location = str_replace('Ireland', '', $location);

                $location = trim(preg_replace('/\s+/', ' ', $location));
                $job_link = trim(preg_replace('/\s+/', ' ', $job_link));
                $ht1 = trim(preg_replace('/\s+/', ' ', $ht1));
                $ht2 = trim(preg_replace('/\s+/', ' ', $ht2));
                $ht3 = trim(preg_replace('/\s+/', ' ', $ht3));
                $tweet = "<p>" . $s . "" . $bc . "" . $title_trim . " " . $companyTwitter . " " . $location . " <a href=" . $job_link . ">" . $job_link . "</a> " . $ht1 . " " . $ht2 . " " . $ht3 . "</p>";
                //$accounts = "<p>".$account."      ".$account2nd."      ".$account3rd."</p>";

                //sort tweets into correct account

                //echo "$accounts";
                //echo strlen($tweet);

                $tweet = $title_trim . ' ' . $companyTwitter . ' ' . $location . ' ' . $job_link . ' ' . $ht1 . ' ' . $ht2 . ' ' . $ht3;
                $tweet = preg_replace('~[\r\n]+~', '', $tweet);

                $tweet = str_replace(',', '', $tweet);
                $tweet = str_replace('United States', '', $tweet);
                $tweet = str_replace('United Kingdom', '', $tweet);
                $tweet = str_replace('Germany', '', $tweet);
                $tweet = str_replace('United Kingdom', '', $tweet);
                $tweet = str_replace('Ireland', '', $tweet);

                //$tweet = trim(preg_replace('/\((.*?)\)/', ' ', $tweet));

                $tweet = trim(preg_replace('/\s+/', ' ', $tweet));
                if (strlen($tweet) < 141 and strlen($location) < 20 and strlen($location) > 1 and strpos($job_link, 'bit.ly')) {
                    echo "<141";
                    $characters = strlen($tweet);
                    $numberOfTweets++;
                    $query = mysql_query("INSERT INTO `preTweets`(`time`,`title_trim`,`companyTwitter`,`location`,`job_link`,`ht1`,`ht2`,`ht3`,`featured`,`characters`) VALUES ('$time','$title_trim','$companyTwitter','$location','$job_link','$ht1','$ht2','$ht3','$featured','$characters')");
                    //$query = mysql_query("INSERT INTO `tweets`(`time`, `tweet`) VALUES ('$time','$tweet')");
                    $minutes=rand(0, 10);
                    $minutes=$minArray[$minutes];
                    $time = new DateTime($time);
                    $time->add(new DateInterval('PT00H'.$minutes.''));
                    $time = $time->format('m/d/Y H:i');
                }

//check if under 141 characters
                echo "$tweet";

                $count++;
                $new_job[] = $row["job_id"];
                //echo $count . "\t" . $new_job[$nj] . "\t" . $diff->format("%R%a") . "\t" . $jobType . "\t" . $location. "\t" . $companyName . "\t" . $companyTwitter . "\t". $row["level"] . "\t"  . $ht1 . "\t" . $ht2 . "\t" . $ht3. "\n";
                $nj++;
                //addPotentialTweet($new_job[$nj]);
            }
            $q++;
        }

        $minutes=rand(0, 10);
        $minutes=$minArray[$minutes];
        $time = new DateTime($time);
        $time->add(new DateInterval('PT00H'.$minutes.''));
        $time = $time->format('m/d/Y H:i');

        $query1 = mysql_query("SELECT `time`,`title_trim`,`companyTwitter`,`location`,`job_link`,`ht1`,`ht2`,`ht3` FROM `preTweets`");
        $number = 0;
        $uniqueLocation="";
        $uniqueCompany="";
        if (mysql_num_rows($query1) == 0) {
            echo "No jobs found.<Br/>";
        } else {
            while ($row = mysql_fetch_assoc($query1) and $number < 23) {

                if(!strpos($uniqueLocation,$row['location']) and !strpos($uniqueCompany,$row['companyTwitter'])){
                    $uniqueLocation.=$row['location']." ";
                    $uniqueCompany.=$row['companyTwitter']." ";

                    $tweet = $row['title_trim'] . ' ' . $row['companyTwitter'] . ' ' . $row['location'] . ' ' . $row['job_link'] . ' ' . $row['ht1'] . ' ' . $row['ht2'] . ' ' . $row['ht3'];
                    $tweet = trim(preg_replace('/\s+/', ' ', $tweet));
                    $minutes=rand(0, 10);
                    $minutes=$minArray[$minutes];
                    $hourArray =array("08","09","10","11","12","13","14","15","16","17","18","19","20","21","22","23","24","01","02","03","04","05","06","07");
                    $hour=$hourArray[$number];
                    $time = "$month/$tomorrow/$year $hour:$minutes";//tweets ready for tomorrow
                    $time= new DateTime($time);
                    $time = $time->format('m/d/Y H:i');
                    $query = mysql_query("INSERT INTO `tweets`(`time`, `tweet`) VALUES ('$time','$tweet')");

                    $number++;
                }

            }

        }
    }
    function exportMysqlToCsv($sql_query,$table,$filename = 'export.csv')
    {
        $csv_terminated = "\n";
        $csv_separator = ",";
        $csv_enclosed = '"';
        $csv_escaped = "\\";


        // Gets the data from the database
        $result = mysql_query($sql_query);
        $fields_cnt = mysql_num_fields($result);


        $schema_insert = '';

        for ($i = 0; $i < $fields_cnt; $i++)
        {
            $l = $csv_enclosed . str_replace($csv_enclosed, $csv_escaped . $csv_enclosed,
                    stripslashes(mysql_field_name($result, $i))) . $csv_enclosed;
            $schema_insert .= $l;
            $schema_insert .= $csv_separator;
        } // end for

        $out = trim(substr($schema_insert, 0, -1));
        $out .= $csv_terminated;

        // Format the data
        while ($row = mysql_fetch_array($result))
        {
            $schema_insert = '';
            for ($j = 0; $j < $fields_cnt; $j++)
            {
                if ($row[$j] == '0' || $row[$j] != '')
                {

                    if ($csv_enclosed == '')
                    {
                        $schema_insert .= $row[$j];
                    } else
                    {
                        $schema_insert .= $csv_enclosed .
                            str_replace($csv_enclosed, $csv_escaped . $csv_enclosed, $row[$j]) . $csv_enclosed;
                    }
                } else
                {
                    $schema_insert .= '';
                }

                if ($j < $fields_cnt - 1)
                {
                    $schema_insert .= $csv_separator;
                }
            } // end for

            $out .= $schema_insert;
            $out .= $csv_terminated;
        } // end while

        header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
        header("Content-Length: " . strlen($out));
        // Output to browser with appropriate mime type, you choose ;)
        header("Content-type: text/x-csv");
        //header("Content-type: text/csv");
        //header("Content-type: application/csv");
        header("Content-Disposition: attachment; filename=$filename");
        echo $out;
        exit;

    }

    /*If($_POST["export"]){
        $sql_query = "Select * from tweets";
        exportMysqlToCsv($sql_query);
    }
    echo '<form method="post"><input type="submit" name="export" value="export"></form>';
    */
    function addPotentialTweet($job_id){


        $array = array(
            "job_id" => "' '",
            "job_source" => "jobsite",

        );
    }


    class  Tweet{
        /* Member variables */
        var $timeZone;
        var $timeToTweet;
        var $amountCharacters;
        var $country;
        var $featured;
        var $title;

        /* Member functions */
        function setTimeZone($par){
            $this->timeZone = $par;
        }

        function getTimeZone(){
            echo $this->timeZone;
        }

        function setTitle($par){
            $this->title = $par;
        }

        function getTitle(){
            echo $this->title;
        }
    }


    function timeDif() {
        $now = date("Y/m/d H:i:s", $_SERVER['REQUEST_TIME']);

    }

}



?>
