<?php
// define the temporary directory
// and where audio files will be written to after conversion
$tmpdir = "/var/www/vhosts/roleplaygateway.com/httpdocs/cache";
$audiodir = "/var/www/vhosts/roleplaygateway.com/httpdocs/files/audio";

// if the Text-To-Speech button was click, process the data
if (isset($_POST["make_audio"])) {
  $speech = stripslashes(trim($_POST["speech"]));
  $speech = substr($speech, 0, 1024);
  $volume_scale = intval($_POST["volume_scale"]);
  if ($volume_scale <= 0) { $volume_scale = 1; }
  if ($volume_scale > 100) { $volume_scale = 100; }
  if (intval($_POST["save_mp3"]) == 1) { $save_mp3 = true; }

  // continue only if some text was entered for conversion
  if ($speech != "") {
    // current date (year, month, day, hours, mins, secs)
    $currentdate = date("ymdhis",time());
    // get micro seconds (discard seconds)
    list($usecs,$secs) = microtime();
    // unique file name
    $filename = "{$currentdate}{$usecs}";
    // other file names
    $speech_file = "{$tmpdir}/{$filename}";
    $wave_file = "{$audiodir}/{$filename}.wav";
    $mp3_file  = "{$audiodir}/{$filename}.mp3";

    // open the temp file for writing
    $fh = fopen($speech_file, "w+");
    if ($fh) {
      if (!fwrite($fh, $speech)) {
			echo "Couldn't write to the file.";
	  }
      fclose($fh);
    } else {
		echo "Couldn't open filehandle.";
	}

    // if the speech file exists, use text2wave
    if (file_exists($speech_file)) {
      // create the text2wave command and execute it
      $text2wave_cmd = sprintf("text2wave -o %s -scale %d %s",$wave_file,$volume_scale,$speech_file);
      exec($text2wave_cmd);

      // create an MP3 version?
      if ($save_mp3) {
        // create the lame command and execute it
        $lame_cmd = sprintf("lame %s %s",$wave_file,$mp3_file);
        exec($lame_cmd);
        // delete the WAV file to conserve space
        unlink($wave_file);
      }
      
      // delete the temp speech file
      unlink($speech_file);

      // which file name and type to use? WAV or MP3
      $listen_file = (($save_mp3 == true) ? basename($mp3_file) : basename($wave_file));
      $file_type = (($save_mp3 == true) ? "MP3" : "WAV");

      // show audio file link
      $show_audio = true;
    }
  }
} else {
  // default values
  $speech = "Hello there!";
  $volume_scale = 50;
  $save_mp3 = true;
}
?>
<html>
<head>
<title>Festival: Linux Text-To-Speech Demo</title>
<style type="text/css">
<!--
body { background-color:#ffffff; font-family:Arial, Helvetica, sans-serif; font-size:10pt; color: #000000; }
h1 { font-family:Arial, Helvetica, sans-serif; font-size:18pt; color: #000000; }
.tblfont { font-family:Arial, Helvetica, sans-serif; font-size:10pt; color: #000000; }
-->
</style>
</head>
<body>
<h1>Linux Festival Text-To-Speech Demo</h1>
<form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
  <table width="400" border="0" cellspacing="5" cellpadding="0" class="tblfont">
    <tr> 
      <td colspan="2"><textarea name="speech" wrap="VIRTUAL" style="width:350px;height:100px;"><?php echo $speech; ?></textarea></td>
    </tr>
    <tr> 
      <td width="135">Volume Scale 
        <input name="volume_scale" type="text" size="3" maxlength="3" value="<?php echo $volume_scale; ?>"> 
      </td>
      <td width="265">Save as MP3 
        <input name="save_mp3" type="checkbox" value="1"<?php if ($save_mp3 == 1) { echo " checked"; } ?>> 
      </td>
    </tr>
    <tr> 
      <td><input name="make_audio" type="submit" value="Text-To-Speech"></td>
      <td> 
        <?php if ($show_audio) { ?>
        <a href="files/audio/<?php echo $listen_file; ?>">Listen to the <?php echo $file_type; ?> file</a> 
        <?php } ?>
      </td>
    </tr>
  </table>
</form>
</body>
</html>