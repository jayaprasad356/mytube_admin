<?php

$date1 = new DateTime("");
$date2 = new DateTime("2009-06-28");
$interval = $date1->diff($date2);
$days = $interval->days;
echo "difference " . $days . " days ";
?>