<?php
/** 
*
* help_bbcode [Standard french]
* translated originally by PhpBB-fr.com <http://www.phpbb-fr.com/> and phpBB.biz <http://www.phpBB.biz>
*
* @package language
* @version $Id: help_bbcode.php, v1.25 2009/10/16 13:08:00 Elglobo Exp $
* @copyright (c) 2005 phpBB Group 
* @license http://opensource.org/licenses/gpl-license.php GNU Public License 
*
*/

/**
* DO NOT CHANGE
*/
if (!defined('IN_PHPBB'))
{
	exit;
}

// DEVELOPERS PLEASE NOTE
//
// All language files should use UTF-8 as their encoding and the files must not contain a BOM.
//
// Placeholders can now contain order information, e.g. instead of
// 'Page %s of %s' you can (and should) write 'Page %1$s of %2$s', this allows
// translators to re-order the output of data while ensuring it remains correct
//
// You do not need this where single placeholders are used, e.g. 'Message %d' is fine
// equally where a string contains only two placeholders which are used to wrap text
// in a url you again do not need to specify an order e.g., 'Click %sHERE%s' is fine

$help = array(
	array(
		0 => '--',
		1 => 'Introduction'
	),
	array(
		0 => 'Qu’est-ce que le BBCode?',
		1 => 'Le BBCode est une implémentation spéciale du HTML. L’administrateur détermine si le BBCode peut être utilisé dans vos messages sur le forum. Vous pouvez désactiver le BBCode dans certains messages via le formulaire de rédaction de message. Le BBCode ressemble au HTML, les balises sont entre crochets [ et ] au lieu de &lt; et &gt;, et offre une meilleure maîtrise de l’affichage du contenu. Selon le thème utilisé, vous pouvez également ajouter les BBCodes en cliquant dans l’interface au-dessus du formulaire de rédaction de message. Même avec cela, ce guide peut vous être utile.'
	),
	array(
		0 => '--',
		1 => 'Mise en forme du texte'
	),
	array(
		0 => 'Comment utiliser le gras, l’italique et le souligné',
		1 => 'Le BBCode permet de modifier rapidement la forme du texte grâce aux méthodes suivantes:<ul><li>Encadrez le texte entre <strong>[b][/b]</strong> pour mettre en gras, exemple:<br /><br /><strong>[b]</strong>Salut<strong>[/b]</strong><br /><br />devient <strong>Salut</strong></li><li>Utilisez <strong>[u][/u]</strong> pour souligner, exemple:<br /><br /><strong>[u]</strong>Bonjour<strong>[/u]</strong><br /><br />devient <span style="text-decoration: underline">Bonjour</span></li><li>Utilisez <strong>[i][/i]</strong> pour l’italique, exemple:<br /><br />C’est <strong>[i]</strong>génial!<strong>[/i]</strong><br /><br />donne C’est <em>génial!</em></li></ul>'
	),
	array(
		0 => 'Changer la couleur et la taille du texte',
		1 => 'Pour modifier la couleur ou la taille de votre texte, vous pouvez utiliser les balises suivantes. Gardez à l’esprit que la façon dont votre message s’affichera dépendra du navigateur et du système:<ul><li>Vous pouvez changer la couleur d’un texte en l’encadrant entre les balises <strong>[color=][/color]</strong>. Vous pouvez indiquer un nom de couleur connu (comme: red, blue, yellow, etc.) ou bien indiquer un code hexadécimal, c.-à-d. #FFFFFF, #000000. Par exemple, pour du texte rouge:<br /><br /><strong>[color=red]</strong>Salut!<strong>[/color]</strong><br /><br />ou<br /><br /><strong>[color=#FF0000]</strong>Salut!<strong>[/color]</strong><br /><br />afficheront tous les deux <span style="color:red">Salut!</span></li><li>Vous pouvez changer la taille du texte de façon analogue avec les balises <strong>[size=][/size]</strong>. Cette balise dépend du thème utilisé mais le format recommandé est une valeur numérique représentant la hauteur du texte en pourcentage, à partir de 20 (tellement petit que vous ne pourrez pas le voir) jusqu’à 200 (très grand). Par exemple:<br /><br /><strong>[size=30]</strong>PETIT<strong>[/size]</strong><br /><br />sera affiché <span style="font-size:30%;">PETIT</span><br /><br />alors que<br /><br /><strong>[size=200]</strong>ENORME!<strong>[/size]</strong><br /><br />donnera <span style="font-size:200%;">ENORME!</span></li></ul>'
	),
	array(
		0 => 'Puis-je combiner des balises?',
		1 => 'Oui, bien évidemment, vous pouvez écrire pour attirer l’attention:<br /><br /><strong>[size=200][color=red][b]</strong>REGARDEZ-MOI!<strong>[/b][/color][/size]</strong><br /><br />ce qui donne <span style="color:red;font-size:200%;"><strong>REGARDEZ-MOI!</strong></span><br /><br />Attention toutefois à ne pas en abuser! Retenez aussi que c’est à vous, l’auteur, de vous assurer que les balises sont correctement fermées. Par exemple, ceci est incorrect:<br /><br /><strong>[b][u]</strong>C’est faux!<strong>[/b][/u]</strong>'
	),
	array(
		0 => '--',
		1 => 'Citations et textes à espacement fixe'
	),
	array(
		0 => 'Citer du texte dans les réponses',
		1 => 'Il y a deux manières de citer un texte, avec ou sans référence.<ul><li>Lorsque vous utilisez la fonction de citation pour répondre à un message sur le forum, vous pouvez noter que le texte du message est ajouté à l’intérieur d’un bloc <strong>[quote=&quot;&quot;][/quote]</strong>. Cette méthode vous permet de citer avec une référence à une personne ou toute autre référence! Par exemple, pour citer un texte de M. Goutte, vous mettrez:<br /><br /><strong>[quote=&quot;M. Goutte&quot;]</strong>Le texte de M. Goutte ira ici<strong>[/quote]</strong><br /><br /><em>M. Goutte a écrit:</em> sera ajouté automatiquement avant le texte. Souvenez-vous que vous <strong>devez</strong> encadrer entre &quot;&quot; le nom à citer, ce n’est pas optionnel.</li><li>La deuxième méthode vous permet de faire des citations en aveugle. Pour l’utiliser, encadrez le texte avec les balises <strong>[quote][/quote]</strong>. Lorsque vous consulterez le message, cela affichera, en fonction du thème utilisé, <em>Citation:</em> avant le texte lui-même.</li></ul>'
	),
	array(
		0 => 'Afficher du code ou des données à espacement fixe',
		1 => 'Si vous désirez insérer du code ou quoi que ce soit qui nécessite une police à largeur fixe, par exemple: une police de type Courier, encadrez votre texte entre les balises <strong>[code][/code]</strong>:<br /><br /><strong>[code]</strong>echo &quot;Un peu de code&quot;;<strong>[/code]</strong><br /><br />Le format utilisé entre les balises <strong>[code][/code]</strong> est enregistré pour une consultation ultérieure. La syntaxe PHP peut être mise en valeur en utilisant <strong>[code=php][/code]</strong> et c’est recommandé lorsque des extraits de code PHP sont publiés afin d’en améliorer la lisibilité.'
	),
	array(
		0 => '--',
		1 => 'Génération de listes'
	),
	array(
		0 => 'Création d’une liste non ordonnée',
		1 => 'Le BBCode gère deux types de listes: ordonnées ou non. Elles sont les mêmes que leur équivalent HTML. Une liste non ordonnée affiche chaque élément de la liste séquentiellement l’un après l’autre, chacun indenté par une puce. Utilisez <strong>[list][/list]</strong> pour créer une liste non ordonnée et définissez chaque élément avec <strong>[*]</strong>. Par exemple, pour la liste de vos couleurs préférées, utilisez:<br /><br /><strong>[list]</strong><br /><strong>[*]</strong>Rouge<br /><strong>[*]</strong>Bleu<br /><strong>[*]</strong>Jaune<br /><strong>[/list]</strong><br /><br />Ce qui générera la liste suivante:<ul><li>Rouge</li><li>Bleu</li><li>Jaune</li></ul>'
	),
	array(
		0 => 'Création d’une liste ordonnée',
		1 => 'Le deuxième type de liste, la liste ordonnée, vous permet de décider de ce qui s’affiche avant chaque élément. Utilisez <strong>[list=1][/list]</strong> pour créer une liste ordonnée numérotée ou <strong>[list=a][/list]</strong> pour une liste alphabétique. Comme pour les listes non ordonnées, les éléments sont indiqués avec <strong>[*]</strong>. Par exemple:<br /><br /><strong>[list=1]</strong><br /><strong>[*]</strong>Faire les courses<br /><strong>[*]</strong>Acheter un nouvel ordinateur<br /><strong>[*]</strong>Jurer quand le PC plante<br /><strong>[/list]</strong><br /><br />affichera<ol style="list-style-type: arabic-numbers"><li>Faire les courses</li><li>Acheter un nouvel ordinateur</li><li>Jurer quand le PC plante</li></ol>Alors que pour une liste alphabétique, vous utiliserez:<br /><br /><strong>[list=a]</strong><br /><strong>[*]</strong>Réponse 1<br /><strong>[*]</strong>Réponse 2<br /><strong>[*]</strong>Réponse 3<br /><strong>[/list]</strong><br /><br />et vous obtiendrez<ol style="list-style-type: lower-alpha"><li>Réponse 1</li><li>Réponse 2</li><li>Réponse 3</li></ol>'
	),
	// This block will switch the FAQ-Questions to the second template column
	array(
		0 => '--',
		1 => '--'
	),
	array(
		0 => '--',
		1 => 'Création de liens'
	),
	array(
		0 => 'Liens vers un autre site',
		1 => 'Le BBCode permet de créer des URI (Indicateurs de Ressources Uniformes) ou URL de différentes façons.<ul><li>La première est d’utiliser les balises <strong>[url=][/url]</strong>, ce que vous taperez après le signe = se comportera comme une URL. Par exemple, pour un lien vers phpBB-fr.com, vous pouvez utiliser:<br /><br /><strong>[url=http://forums.phpbb-fr.com/]</strong>Visitez phpBB!<strong>[/url]</strong><br /><br />Ce qui générera le lien, <a href="http://forums.phpbb-fr.com/">Visitez phpBB!</a> Vous noterez que le lien s’ouvre soit dans la même fenêtre, soit dans une nouvelle fenêtre selon les préférences du navigateur.</li> <li>Si vous désirez que l’URL elle-même soit affichée comme un lien, vous pouvez simplement utiliser:<br /><br /><strong>[url]</strong>http://forums.phpbb-fr.com/<strong>[/url]</strong><br /><br />Ce qui générera le lien, <a href="http://forums.phpbb-fr.com/">http://forums.phpbb-fr.com/</a></li><li>De plus, phpBB autorise les <em>Liens Magiques</em>, ce qui transforme automatiquement les URL correctement écrites en lien sans indiquer de balise ou même http://. Par exemple, si vous tapez forums.phpbb-fr.com, un lien <a href="http://forums.phpbb-fr.com/">forums.phpbb-fr.com</a> sera affiché automatiquement à la lecture de votre message.</li><li>La même chose s’applique aux adresses e-mails, vous pouvez indiquer l’adresse explicitement, comme par exemple:<br /><br /><strong>[email]</strong>personne@domain.adr<strong>[/email]</strong><br /><br />ce qui affichera <a href="mailto:personne@domain.adr">personne@domain.adr</a> ou bien vous pouvez simplement taper personne@domain.adr dans votre message et cela sera automatiquement converti lors de la consultation.</li></ul>Comme avec toutes les balises BBCode, vous pouvez encapsuler avec des URL d’autres balises telles que <strong>[img][/img]</strong> (voir l’entrée suivante), <strong>[b][/b]</strong>, etc. Comme avec les balises de mise en forme, c’est à vous de vous assurer de les ouvrir et de les fermer correctement, par exemple:<br /><br /><strong>[url=http://forums.phpbb-fr.com/][img]</strong>http://forums.phpbb-fr.com/images/phplogo.gif<strong>[/url][/img]</strong><br /><br />n’est <span style="text-decoration: underline">pas</span> correct ce qui peut entraîner la suppression de votre message, donc faites attention.'
	),
	array(
		0 => '--',
		1 => 'Afficher des images dans les messages'
	),
	array(
		0 => 'Ajout d’une image dans un message',
		1 => 'Le BBCode permet d’inclure des images dans vos messages à l’aide d’une balise. Il y a deux choses importantes à se rappeler lors de l’utilisation de cette balise qui sont d’une part, que beaucoup d’utilisateurs n’apprécient pas qu’il y ait beaucoup d’images dans les messages et d’autre part, que l’image affichée doit être disponible sur Internet (elle ne peut être uniquement que sur votre ordinateur, sauf si vous avez un serveur Web!). Pour afficher une image, vous devez encadrer son URL entre des balises <strong>[img][/img]</strong>. Par exemple:<br /><br /><strong>[img]</strong>http://www.google.com/intl/en_ALL/images/logo.gif<strong>[/img]</strong><br /><br />Comme noté dans la section URL ci-dessus, vous pouvez entourer l’image entre des balises <strong>[url][/url]</strong> si désiré, exemple:<br /><br /><strong>[url=http://www.google.com/][img]</strong>http://www.google.com/intl/en_ALL/images/logo.gif<strong>[/img][/url]</strong><br /><br />affichera<br /><br /><a href="http://www.google.com/"><img src="http://www.google.com/intl/en_ALL/images/logo.gif" alt="" /></a>'
	),
	array(
		0 => 'Joindre un fichier à un message',
		1 => 'Les fichiers peuvent maintenant être joints à n’importe quel endroit du message en utilisant le BBcode <strong>[attachment=][/attachment]</strong>, si l’administrateur a activé cette fonctionnalité et si vous en avez la permission. Dans l’écran de rédaction de message, vous trouverez un menu déroulant (respectivement un bouton) pour joindre vos fichiers en ligne.'
	),
	array(
		0 => '--',
		1 => 'Autres questions'
	),
	array(
		0 => 'Puis-je ajouter mes balises?',
		1 => 'Si vous êtes administrateur du forum et avez les autorisations nécessaires, vous pouvez ajouter des BBcodes supplémentaires via la section &quot;BBcodes&quot; dans le sous menu &quot;Messages&quot;.'
	)
);

?>