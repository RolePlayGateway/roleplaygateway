<?php
/**
 *
 * @author David Lewis (Highway of Life) http://startrekguide.com
 * @package phpBB3
 * @version $Id: daily_posts.php 1097 2008-12-12 08:16:43Z xhotshotx $
 * @copyright 2007 Star Trek Guide Group
 *
 */
define('IN_PHPBB', true);
$phpbb_root_path = './../';
$jpgraph_src_path = './jpgraph/src/';
$phpEx = substr(strrchr(__FILE__, '.'), 1);
include($phpbb_root_path . 'common.' . $phpEx);
include($jpgraph_src_path . 'jpgraph.' . $phpEx);
include($jpgraph_src_path . 'jpgraph_line.' . $phpEx);

// starting from 7 days ago, find the begin and end timestamp of those days
// and place them into an array
for($i = -6; $i < 1; $i++)
{
    $begin = gmmktime(0, 0, 0, date("m"), date("d") + $i, date("Y"));
    $end = gmmktime(0, 0, 0, date("m"), date("d") + ($i +1), date("Y"));
    $date[] = array($begin, $end, date("D d", $end));
}

// define the fields we are going to use in the query
$days = array(
    'day_one',
    'day_two',
    'day_three',
    'day_four',
    'day_five',
    'day_six',
    'day_seven',
);

// Begin constructing the query
$sql = 'SELECT ';

// setup the fields to grab
$sql .= implode(', ', $days);

$sql .= ' FROM ';

$i = 0;
$size = sizeof($date);

// grab a post time for each of the previously defined days
// taking them out of an array, and putting them in a string for query use
foreach($date as $day)
{
    $sql .= '(SELECT COUNT(post_id) AS ' . $days[$i] . '
FROM gateway_posts
WHERE post_time < ' . $day[1] . ' AND post_time > ' . $day[0] . ') AS d' . $i;

    $sql .= (($size - 1) == $i) ? '' : ', ';

    $labels[] = $day[2];
    // increment
    $i++;
}

// now run the query and grab the result.
$result = $db->sql_query($sql);
$row = $db->sql_fetchrow($result);
$db->sql_freeresult($result);
foreach ($row as $offset => $value)
{
    $ydata[] = $value;
}

// Create the graph. These two calls are always required
$graph = new Graph(500, 300, "auto");
$graph->SetScale("textlin");

// Adjust the margin
$graph->img->SetMargin(40,30,35,40);
//$graph->SetShadow();

// Create the linear plot
$lineplot=new LinePlot($ydata);
$lineplot->mark->SetType(MARK_UTRIANGLE);
$lineplot->value->SetFormat('%d');
$lineplot->value->Show();
$lineplot->value->SetColor('red');
$lineplot->value->SetFont(FF_FONT1,FS_BOLD, 11);

// Add the plot to the graph
$graph->Add($lineplot);

$graph->xaxis->SetTickLabels($labels);

$graph->title->Set('Posts Per Day this Week');
$graph->xaxis->title->Set('Day');
$graph->yaxis->title->Set('Posts');

$graph->title->SetFont(FF_FONT1,FS_BOLD);
$graph->yaxis->title->SetFont(FF_FONT1,FS_BOLD);
$graph->xaxis->title->SetFont(FF_FONT1,FS_BOLD);

$lineplot->SetColor("blue");
$lineplot->SetWeight(2);


// Display the graph
$graph->Stroke();
?>