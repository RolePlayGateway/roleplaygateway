<?php
/**
 *
 * @author David Lewis (Highway of Life) http://startrekguide.com
 * @package phpBB3
 * @version $Id: top_posters.php 1097 2008-12-12 08:16:43Z xhotshotx $
 * @copyright 2007 Star Trek Guide Group
 *
 */
define('IN_PHPBB', true);
$phpbb_root_path = './../';
$jpgraph_src_path = './jpgraph/src/';
$phpEx = substr(strrchr(__FILE__, '.'), 1);
include($phpbb_root_path . 'common.' . $phpEx);
include($jpgraph_src_path . 'jpgraph.' . $phpEx);
include($jpgraph_src_path . 'jpgraph_bar.' . $phpEx);

$user->add_lang('common');

$bg_image = request_var('bg', '');
$width = request_var('w', 0);
$height = request_var('h', 0);
$line_color = request_var('c', 'black');
$chart_type = request_var('type', 'posts');

if (file_exists($bg_image) && (!$width && !$height))
{
    list($width, $height) = getimagesize($bg_image);
    $file_exists = true;
}
else
{
    $file_exists = ($bg_image) ? true : false;
    $width = ($width) ? (int) $width : 500;
    $height = ($height) ? (int) $height : 300;
}

switch ($chart_type)
{
    case 'referrals':
        $sql_select = 'user_referrals';
        $order_by = $sql_select;
        $header = 'Referrals';
    break;
    default:
    case 'posts':
        $sql_select = 'user_posts';
        $order_by = $sql_select;
        $header = 'Posters';
    break;
}

$sql = "SELECT username, user_colour, $sql_select
        FROM " . USERS_TABLE . "
        ORDER BY $sql_select DESC
        LIMIT 0,10";
$result = $db->sql_query($sql);
$rowset = $db->sql_fetchrowset($result);
$db->sql_freeresult($result);

foreach ($rowset as $row)
{
    $name[] = $row['username'];
    $color[] = ($row['user_colour']) ? '#' . $row['user_colour'] : 'black';
    $data_y[][0] = $row[$sql_select];
}
// Setup the colors with transparency (alpha channel)
$alpha = '@0.1';
$shadow_alpha = '@0.5';

// Create the basic graph
$graph = new Graph($width, $height, 'auto');
$graph->SetScale('textlin');
$graph->img->SetMargin(40, 80, 30, 40);

// Adjust the position of the legend box
$graph->legend->Pos(0.02, 0.15);

// Adjust the color for theshadow of the legend
$graph->legend->SetShadow('darkgray@0.5');
$graph->legend->SetFillColor('lightblue@0.3');

// Get localised version of the month names
//$graph->xaxis->SetTickLabels($gDateLocale->GetShortMonth());
$graph->xaxis->SetTickLabels(array($sql_select));

// Set a nice  image
if ($file_exists)
{
    $graph->SetBackgroundImage($bg_image, BGIMG_COPY);
}

// Set axis titles and fonts
$graph->xaxis->title->Set($user->format_date(time()));
$graph->xaxis->title->SetFont(FF_ARIAL, FS_NORMAL, 10); //FF_FONT1, FS_BOLD);
$graph->xaxis->title->SetColor($line_color);

$graph->xaxis->SetFont(FF_ARIAL, FS_NORMAL, 10); //FF_FONT1, FS_BOLD);
$graph->xaxis->SetColor($line_color);

$graph->yaxis->SetFont(FF_ARIAL, FS_NORMAL, 10); //FF_FONT1, FS_BOLD);
$graph->yaxis->SetColor($line_color);

//$graph->ygrid->Show(false);
$graph->ygrid->SetColor($line_color . '@0.5');

// Setup graph title
$graph->title->Set('Top ' . $header);
// Some extra margin (from the top)
$graph->title->SetMargin(3);
$graph->title->SetFont(FF_ARIAL, FS_NORMAL, 12);
$graph->title->SetColor($line_color);

$b_plot = $c_plot = array();
// Create the three var series we will combine
foreach ($data_y as $offset)
{
    $b_plot[] = new BarPlot($offset);
}

for ($i = 0; $i < sizeof($b_plot); $i++)
{
    $b_plot[$i]->SetFillColor($color[$i] . $alpha);
    $b_plot[$i]->SetLegend($name[$i]);
    $b_plot[$i]->SetShadow('black' . $shadow_alpha);
    $b_plot[$i]->value->SetFormat('%d');
    $b_plot[$i]->value->Show();
    $b_plot[$i]->value->SetColor($color[$i]);
    $b_plot[$i]->value->SetFont(FF_FONT1, FS_BOLD, 9);
}

$my_ary = array();
foreach ($b_plot as $plot)
{
    $my_ary[] = $plot;
}
$gbarplot = new GroupBarPlot($my_ary);
$gbarplot->SetWidth(0.9);
$graph->Add($gbarplot);

$graph->Stroke();
?>