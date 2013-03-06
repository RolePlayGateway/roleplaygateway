<?php

function calculate_flesch($text) {
	return (206.835 - (1.015 * average_words_sentence($text)) - (84.6 * average_syllables_word($text)));
}

function calculate_flesch_grade($text) {
	return ((.39 * average_words_sentence($text)) + (11.8 * average_syllables_word($text)) - 15.59);
}

function average_words_sentence($text) {
	$sentences = strlen(preg_replace('/[^\.!?]/', '', $text));
	$words = strlen(preg_replace('/[^ ]/', '', $text));
	
	if ($sentences < 1) { $sentences = 1; }
	
	return ($words/$sentences);
}

function average_syllables_word($text) {
	$syllables = 0;
	$words = explode(' ', $text);
	for ($i = 0; $i < count($words); $i++) {
		$syllables = $syllables + count_syllables($words[$i]);
	}
	return ($syllables/count($words));
}

function count_syllables($word) {
	  
	$subsyl = Array(
		'cial'
		,'tia'
		,'cius'
		,'cious'
		,'giu'
		,'ion'
		,'iou'
		,'sia$'
		,'.ely$'
	);
					 
	$addsyl = Array(
		'ia'
		,'riet'
		,'dien'
		,'iu'
		,'io'
		,'ii'
		,'[aeiouym]bl$'
		,'[aeiou]{3}'
		,'^mc'
		,'ism$'
		,'([^aeiouy])\1l$'
		,'[^l]lien'
		,'^coa[dglx].'
		,'[^gq]ua[^auieo]'
		,'dnt$'
	);
					  
	// Based on Greg Fast's Perl module Lingua::EN::Syllables
	$word = preg_replace('/[^a-z]/is', '', strtolower($word));
	$word_parts = preg_split('/[^aeiouy]+/', $word);
	foreach ($word_parts as $key => $value) {
		if ($value <> '') {
			$valid_word_parts[] = $value;
		}
	}
 
	$syllables = 0;
	// Thanks to Joe Kovar for correcting a bug in the following lines
	foreach ($subsyl as $syl) {
		$syllables -= preg_match('~'.$syl.'~', $word);
	}
	foreach ($addsyl as $syl) {
		$syllables += preg_match('~'.$syl.'~', $word);
	}
	if (strlen($word) == 1) {
		$syllables++;
	}
	$syllables += count($valid_word_parts);
	$syllables = ($syllables == 0) ? 1 : $syllables;
	return $syllables;
}

function gunning_fog_score($text) {
	return ((average_words_sentence($text) + percentage_number_words_three_syllables($text)) * 0.4);
}

function percentage_number_words_three_syllables($text) {
	$syllables = 0;
	$words = explode(' ', $text);
	for ($i = 0; $i < count($words); $i++) {
		if (count_syllables($words[$i]) > 2) {
			$syllables ++;
		}
	}
	 
	$score = number_format((($syllables / count($words)) * 100));
	  
	return ($score);
}


$message = @$_REQUEST['message']; 

if (!empty($message)) {

	$flesh_score = round(calculate_flesch($message));
	$flesh_grade = round(calculate_flesch_grade($message));
	$gunning_fog_score = round(gunning_fog_score($message));
	
	echo "<span class=\"supersize\"><strong>Flesch-Kincaid Reading Ease:</strong> ".$flesh_score."</span>";
	echo "<p><em>Ideally, roleplay should be around the 60 to 80 mark on this scale. The higher the score, the more readable the text.</em></p>
	<span class=\"supersize\"><strong>Flesch-Kincaid Grade Level:</strong> ".$flesh_grade."</span>";
	echo "<p><em>Ideally, roleplay should be around the 6 to 7 mark on this scale. The lower the score, the more readable the text.</em></p>
	<span class=\"supersize\"><strong>Gunning-Fog Index:</strong> ".$gunning_fog_score."</span>";
	echo "<p><em>Ideally, roleplay should be between 11 and 15 on this scale. The lower the score, the more readable the text. (Anything over 22 should be considered the equivalent of post-graduate level text).</em></p>";

	echo "<strong class=\"supersize\">Your original writing:</strong> <p name=\"original\">".$message."</p>";

	} else {
	echo "<strong>You didn't send any writing to be analyzed!</strong>";
}

?>