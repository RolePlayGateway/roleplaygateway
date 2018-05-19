<?php
			case '/attack':

			
				if (strlen($textParts[1]) >= 2) {
				
					$defender = $textParts[1];
					
					$attack = $this->rollDice(20);
					$defense = $this->rollDice(20);
					
					if ($attack > $defense) {
						
						$damage = $this->rollDice(6);
						$damage += $this->rollDice(6);
					
						$outcome = "Success! 2d6 damage; $damage";
					} else {
						$outcome = "Failure!";
					}
			
					$this->insertChatBotMessage( $this->getChannel(), $this->getUserName() . " attacks " . $defender .": he rolls 1d20 and gets $attack, while the defender rolls 1d20 and gets $defense - $outcome" );
					
				} else {
					$this->insertChatBotMessage($this->getPrivateMessageID(),"You didn't specify who you were attacking.");
				}
				
				return true;
			break;
?>