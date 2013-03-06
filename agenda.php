<?php

// Change this with your Google calendar feed
$calendarURL = 'http://www.google.com/calendar/feeds/11ek7dbrfenme9gmk76k8sp3r8%40group.calendar.google.com/public/basic';

// Nothing else to edit
$feed = file_get_contents($calendarURL);
header('Content-type: text/xml'); 
echo $feed;

?>