<?php

// We start with this, to define that phpBB is to be used. If we don't, we'll get a security error throughout the rest of the code.
define('IN_PHPBB', true);
// Defines where we get the phpBB files from.  This script is served from a subdirectory ( /characters/ ), so it's "../" (up)
$phpbb_root_path = '../';

// Blah blah, php extension - automatically detected from this file.
$phpEx = substr(strrchr(__FILE__, '.'), 1);
// And this is where we say, "Go get shit!" - it includes phpBB's basic libraries
include($phpbb_root_path . 'common.' . $phpEx);

// Start session management - d'dee dee.
$user->session_begin();
$auth->acl($user->data);  // moar setup...
$user->setup();  // and the official setup of this user's session. this does a bunch of special things in the background, as well as letting us access special objects further on down the line...
// Most notably, the $user->data[] array (easy access to the gateway_users table)

//echo "Um. Just kidding?";

// This is bad form, though - this leaves a huge security hole.  Anyone familiar with phpBB know what I'm talking about? I can assume. Lots of data coming in that one could look at.

// Mainly, we never EVER want to allow user-specified information to get directly into our code. They can type special characters and get into our database (SQL injections)
// Here is the beginning of our solution, but it is specific to phpBB3:
$character_name = request_var('name', ''); // this is where we pull the name of the character we're trying to get. this $_REQUEST is special, it includes both GET and POST data.
// You can submit to this via here:  http://www.roleplaygateway.com/characters/index.php?name=TYPE_NAME_HERE - won't work right now, I have it forwarded via HTTP 301. (Don't ask...)

if ($character_name) { // yay, we have a character name that isn't null.

        // Yes, I know SELECT * is lazy. ~_~ - Eric M
        $sql = "SELECT * FROM rpg_characters WHERE name = '".$db->sql_escape($character_name)."'";
        // Note that I've wrapped the $character_name inside an object->function called "sql_escape" - which does most of the heavy lifting for us
        // Yeah, eff that. Bad idea.  We are case sensitive in chat, should be here too. D:
        
        // here, we set up an object that contains our results.  Values are (query, number, [start], [cache(seconds)])
        $result = $db->sql_query_limit($sql,1,null,3600);
        // In practice, I always set a cache timer, for performance reasons. We don't have enough resources to cache everything, but it does a good job of managing itsef based on what is used most.
        
       
        
        if (count($result) == 1) {
                
                                 
                
                while($row = $db->sql_fetchrow($result)) {
                    
                        // TODO: integrate this into the former SQL query using proper JOIN statements... Eric M, 5/4/09
                        $sql = "SELECT username FROM gateway_users WHERE user_id = ".$row['owner'];
                        $owner_result = $db->sql_query_limit($sql,1);
                        while($owner_row = $db->sql_fetchrow($owner_result)) {                     
                            $owner_username = $owner_row['username'];                                                                
                       }
                       $db->sql_freeresult($owner_result); // save some memory somewhere
                    
                        $template->assign_vars(array(
                                'OWNER_USERNAME'              => $owner_username, // don't forget the comma.  we're building an array here.
                                'CHARACTER_NAME'              => $row['name'],
                                'CHARACTER_SYNOPSIS'          => $row['synopsis'],
								'CHARACTER_IMAGE'			  => '<img src="http://www.roleplaygateway.com/character/'.$row['name'].'/image" alt="Portrait of '.$row['name'].'" />',
                                'CHARACTER_DESCRIPTION'       => generate_text_for_display($row['description'], $row['description_uid'], $row['description_bitfield'], 7),
                                'CHARACTER_PERSONALITY'       => generate_text_for_display($row['personality'], $row['personality_uid'], $row['personality_bitfield'], 7),
                                'CHARACTER_EQUIPMENT'         => generate_text_for_display($row['equipment'], $row['equipment_uid'], $row['equipment_bitfield'], 7),
                                'CHARACTER_HISTORY'           => generate_text_for_display($row['history'], $row['history_uid'], $row['history_bitfield'], 7),
                                // We need...
                                /*
                                   ? CHARACTER_NAME (name)
                                   ? OWNER_USERNAME (owner)
                                   ? CHARACTER_SYNOPSIS
                                   ? CHARACTER_DESCRIPTION
                                   ? CHARACTER_PERSONALITY
                                   ? CHARACTER_EQUIPMENT
                                   ? CHARACTER_HISTORY
                                */
                                // Use $row['field_name'] - $row[] contains the result from the while() loop.
                                
                        
                        
                        ));
                        
                // Output page
                page_header($row['name'].' on RolePlayGateway');


                $template->set_filenames(array(
                        'body' => 'characters_profile_body.html')
                );

                page_footer();                        
                        
                }
                

        } else {
                
                trigger_error("This character could not be found!");
                
        }
                
} else {

        page_header('Roleplay - Character Profiles');

        $template->set_filenames(array(
                'body' => 'characters_main_body.html')
        );
        
        page_footer();
}

?>