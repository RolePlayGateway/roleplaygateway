<?php
/** 
*
* help_faq [Standard french]
* translated originally by PhpBB-fr.com <http://www.phpbb-fr.com/> and phpBB.biz <http://www.phpBB.biz>
*
* @package language
* @version $Id: help_faq.php, v1.27 2010/02/25 15:58:00 Elglobo Exp $
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
		1 => 'Problèmes d’identification et d’inscription'
	),
	array(
		0 => 'Pourquoi ne puis-je pas me connecter?',
		1 => 'Plusieurs raisons peuvent expliquer cela. Premièrement, vérifiez que vos nom d’utilisateur et mot de passe sont corrects. S’ils le sont, contactez l’administrateur pour vérifier que vous n’avez pas été banni. Il est possible aussi que l’administrateur ait une erreur de configuration de son côté, et qu’il soit nécessaire de la corriger.'
	),
	array(
		0 => 'Pourquoi dois-je m’inscrire après tout?',
		1 => 'Vous pouvez ne pas en avoir besoin mais l’administrateur peut décider si vous devez vous inscrire pour poster des messages. Par ailleurs, l’inscription vous permet de bénéficier de fonctionnalités supplémentaires inaccessibles aux visiteurs comme les avatars personnalisés, la messagerie privée, l’envoi d’e-mails aux autres membres, l’adhésion à des groupes, etc. L’inscription est rapide et vivement conseillée.'
	),
	array(
		0 => 'Pourquoi suis-je automatiquement déconnecté?',
		1 => 'Si vous ne cochez pas la case <em>Me connecter automatiquement à chaque visite</em> lors de votre connexion, vous ne resterez connecté que pendant une durée déterminée. Cela empêche l’utilisation abusive de votre compte. Pour rester connecté, cochez cette case lors de la connexion. Ce n’est pas recommandé si vous utilisez un ordinateur public pour accéder au forum (bibliothèque, cybercafé, université, etc.). Si vous ne voyez pas cette case, cela signifie que l’administrateur a désactivé cette fonctionnalité.'
	),
	array(
		0 => 'Comment empêcher mon nom d’apparaître dans la liste des utilisateurs connectés?',
		1 => 'Vous trouverez dans votre panneau de l’utilisateur, onglet “Préférences du forum”, l’option <em>Cacher mon statut en ligne</em>. Mettez cette option sur <samp>Oui</samp> ainsi seuls les administrateurs, les modérateurs et vous verrez votre nom dans la liste. Vous serez compté parmi les utilisateurs invisibles.'
	),
	array(
		0 => 'J’ai perdu mon mot de passe!',
		1 => 'Pas de panique! Bien que votre mot de passe ne puisse pas être récupéré, il peut toutefois être réinitialisé. Pour cela, sur la page de connexion, cliquez sur <em>J’ai oublié mon mot de passe</em>. Suivez les instructions et vous devriez pouvoir à nouveau vous connecter.'
	),
	array(
		0 => 'Je suis enregistré mais je ne peux pas me connecter!',
		1 => 'Vérifiez, en premier, vos nom d’utilisateur et mot de passe. S’ils sont corrects, il y a deux possibilités. Si la gestion COPPA est active et si vous avez indiqué avoir moins de 13 ans lors de l’inscription, vous devrez alors suivre les instructions reçues. Certains forums nécessitent que toute nouvelle inscription soit activée par vous-même ou par l’administrateur avant que vous puissiez vous connecter. Cette information est indiquée lors de l’inscription. Si vous avez reçu un e-mail, suivez ses instructions. Si vous n’avez pas reçu d’e-mail, il se peut que vous ayez fourni une adresse incorrecte ou que l’e-mail ait été traité par un filtre anti-spam. Si vous êtes sûr de l’adresse e-mail fournie, contactez l’administrateur.'
	),
	array(
		0 => 'Je me suis enregistré par le passé mais je ne peux plus me connecter?!',
		1 => 'Commencez par vérifier vos nom d’utilisateur et mot de passe dans l’e-mail reçu lors de votre inscription et réessayez. Il est possible aussi que l’administrateur ait supprimé ou désactivé votre compte. En effet, il est courant de supprimer régulièrement les utilisateurs ne postant pas pour réduire la taille de la base de données. Si cela vous arrive, tentez de vous réinscrire et soyez plus investi dans le forum.'
	),
	array(
		0 => 'Que signifie COPPA?',
		1 => 'COPPA (ou <em>Child Online Privacy and Protection Act</em> de 1998) est une loi aux Etats-Unis qui dit que les sites Internet pouvant recueillir des informations de mineurs de moins de 13 ans doivent obtenir le consentement <strong>écrit</strong> des parents (ou d’un tuteur légal) pour la collecte de ces informations permettant d’identifier un mineur de moins de 13 ans. Si vous n’êtes pas sûr que cela s’applique à vous, lorsque vous vous inscrivez, ou au site Internet auquel vous tentez de vous inscrire, demandez une assistance légale. Notez que l’équipe du forum ne peut pas fournir de conseil légal et ne saurait être contactée pour des questions légales de toute sorte, à l’exception de celles soulignées ci-dessous.',
	),
	array(
		0 => 'Pourquoi ne puis-je pas m’inscrire?',
		1 => 'Il est possible que le propriétaire du site ait banni votre IP ou interdit le nom d’utilisateur que vous souhaitez utiliser. Le propriétaire du site peut également avoir désactivé l’inscription pour en empêcher de nouvelles. Contactez l’administrateur pour plus de renseignements.',
	),
	array(
		0 => 'A quoi sert “Supprimer les cookies du forum”?',
		1 => 'Cela supprime tous les cookies créés par phpBB3 qui conservent votre identification et votre connexion au forum. Ils fournissent aussi des fonctionnalités telles que l’enregistrement du statut des messages, lu ou non-lu, si cela a été activé par l’administrateur. Si vous avez des problèmes de connexion/déconnexion, la suppression des cookies peut les corriger.',
	),
	array(
		0 => '--',
		1 => 'Préférences et paramètres de l’utilisateur'
	),
	array(
		0 => 'Comment modifier mes paramètres?',
		1 => 'Tous vos paramètres (si vous êtes inscrit) sont enregistrés dans notre base de données. Pour les modifier, visitez le lien <em>Panneau de l’utilisateur</em> (généralement affiché en haut de toutes les pages du forum). Cela vous permettra de modifier tous vos paramètres et préférences.'
	),
	array(
		0 => 'Les heures ne sont pas correctes!',
		1 => 'Il est possible que l’heure affichée soit sur un fuseau horaire différent de celui dans lequel vous êtes. Dans ce cas, vous devez modifier vos préférences pour le fuseau horaire de votre zone (Londres, Paris, New York, Sydney, etc.) dans le panneau de l’utilisateur. Notez que la modification du fuseau horaire, comme la plupart des paramètres n’est accessible qu’aux utilisateurs enregistrés. Donc si vous n’êtes pas inscrit, c’est le bon moment pour le faire.'
	),
	array(
		0 => 'J’ai changé mon fuseau horaire et l’heure est encore incorrecte!',
		1 => 'Si vous êtes sûr d’avoir correctement paramétré votre fuseau horaire et l’heure d’été, il se peut que le serveur ne soit pas à l’heure. Signalez ce problème à l’administrateur.'
	),
	array(
		0 => 'Ma langue n’est pas dans la liste!',
		1 => 'La raison la plus probable est que l’administrateur n’a pas installé votre langue ou bien que personne n’a encore traduit phpBB3 dans votre langue. Essayez de demander à l’administrateur d’installer la langue désirée. Si elle n’existe pas, vous êtes alors libre de créer une nouvelle traduction. Vous trouverez plus d’informations sur le site du groupe phpBB (voir le lien en bas de page).'
	),
	array(
		0 => 'Comment puis-je afficher une image avec mon nom d’utilisateur?',
		1 => 'Il peut y avoir deux images avec un nom d’utilisateur sur la page de consultation des messages. La première est associée à votre rang, généralement des étoiles ou des blocs indiquant votre nombre de messages ou votre statut sur le forum. La seconde, une image plus grande, connue sous le nom d’avatar est généralement unique et personnelle à chaque utilisateur. C’est à l’administrateur d’activer les avatars et de décider de la manière dont ils sont mis à disposition. Si vous ne pouvez pas utiliser d’avatar, c’est peut-être une décision de l’administrateur. Vous pouvez le contacter pour lui demander ses raisons.'
	),
	array(
		0 => 'Qu’est-ce que mon rang et comment le modifier?',
		1 => 'Les rangs qui apparaissent sous le nom d’utilisateur indiquent le nombre de messages postés ou identifient certains utilisateurs tels que les modérateurs et administrateurs. En général, vous ne pouvez pas directement modifier l’intitulé d’un rang car il est paramétré par l’administrateur. Si vous abusez des forums en postant des messages dans le seul but d’augmenter votre rang, un modérateur ou un administrateur peut rabaisser votre compteur de messages.'
	),
	array(
		0 => 'Lorsque je clique sur le lien <em>e-mail</em> d’un utilisateur, on me demande de me connecter?',
		1 => 'Seuls les utilisateurs enregistrés peuvent s’envoyer des e-mails via le formulaire intégré (si la fonction a été activée par l’administrateur). Ceci pour empêcher un usage abusif de la fonctionnalité par les invités.'
	),
	array(
		0 => '--',
		1 => 'Problèmes liés aux envois de messages'
	),
	array(
		0 => 'Comment poster dans un forum?',
		1 => 'Cliquez sur le bouton adéquat (Nouveau ou Répondre) sur la page du forum ou des sujets. Il se peut que vous ayez besoin d’être enregistré pour écrire un message. Une liste des options disponibles est affichée en bas des pages des forums et des sujets, exemple: Vous <strong>pouvez</strong> poster des nouveaux sujets, Vous <strong>pouvez</strong> participer aux votes, etc.'
	),
	array(
		0 => 'Comment modifier ou supprimer un message?',
		1 => 'A moins d’être administrateur ou modérateur, vous ne pouvez modifier ou supprimer que vos propres messages. Vous pouvez modifier un message (quelquefois dans une durée limitée après sa publication) en cliquant sur le bouton <em>éditer</em> du message correspondant. Si quelqu’un a déjà répondu au message, un petit texte s’affichera en bas du message indiquant qu’il a été édité, le nombre de fois qu’il a été modifié ainsi que la date et l’heure de la dernière édition. Ce message n’apparaîtra pas si un modérateur ou un administrateur modifie le message, cependant ils ont la possibilité de laisser une note indiquant qu’ils ont modifié le message de leur propre initiative. Notez que les utilisateurs ne peuvent pas supprimer un message une fois que quelqu’un y a répondu.'
	),
	array(
		0 => 'Comment ajouter une signature à mes messages?',
		1 => 'Vous devez d’abord créer une signature dans votre panneau de l’utilisateur. Une fois créée, vous pouvez cocher <em>Attacher sa signature</em> sur le formulaire de rédaction de message. Vous pouvez aussi ajouter la signature par défaut à tous vos messages en activant la case correspondante dans le panneau de l’utilisateur (onglet <em>Préférences du forum --> Modifier les préférences de message</em>). Par la suite, vous pourrez toujours empêcher une signature d’être ajoutée à un message en décochant la case <em>Attacher sa signature</em> dans le formulaire de rédaction de message.'
	),
	array(
		0 => 'Comment créer un sondage?',
		1 => 'Il est facile de créer un sondage, lors de la publication d’un nouveau sujet ou la modification du premier message d’un sujet (si vous en avez les permissions), cliquez sur l’onglet <em>Sondage</em> sous la partie message (si vous ne le voyez pas, vous n’avez probablement pas le droit de créer des sondages). Saisissez le titre du sondage et au moins deux options possibles, entrez une option par ligne dans le champ des réponses. Vous pouvez aussi indiquer le nombre de réponses qu’un utilisateur peut choisir lors de son vote dans “Option(s) par l’utilisateur”, limiter la durée en jours du sondage (mettre “0” pour une durée illimitée) et enfin permettre aux utilisateurs de modifier leur vote.'
	),
	array(
		0 => 'Pourquoi ne puis-je pas ajouter plus d’options à mon sondage?',
		1 => 'Le nombre d’options maximum par sondage est défini par l’administrateur. Si vous avez besoin de indiquer plus d’options, contactez-le.'
	),
	array(
		0 => 'Comment modifier ou supprimer un sondage?',
		1 => 'Comme pour les messages, les sondages ne peuvent être modifiés que par l’auteur original, un modérateur ou un administrateur. Pour modifier un sondage, cliquez sur le bouton <em>éditer</em> du premier message du sujet (c’est toujours celui auquel est associé le sondage). Si personne n’a voté, l’auteur peut modifier une option ou supprimer le sondage. Autrement, seuls les modérateurs et les administrateurs peuvent le modifier ou le supprimer. Ceci pour empêcher le trucage en changeant les intitulés en cours de sondage.'
	),
	array(
		0 => 'Pourquoi ne puis-je pas accéder à un forum?',
		1 => 'Certains forums peuvent être réservés à certains utilisateurs ou groupes. Pour les consulter, les lire, y poster, etc., vous devez avoir une permission spéciale. Seuls les modérateurs de groupes et les administrateurs peuvent accorder cet accès, vous devez donc les contacter.'
	),
	array(
		0 => 'Pourquoi ne puis-je pas joindre des fichiers à mon message?',
		1 => 'La possibilité d’ajouter des fichiers joints peut être accordée par forum, par groupe, ou par utilisateur. L’administrateur peut ne pas avoir autorisé l’ajout de fichiers joints pour le forum dans lequel vous postez, ou peut-être que seul un groupe peut en joindre. Contactez l’administrateur si vous ne savez pas pourquoi vous ne pouvez pas ajouter de fichiers joints sur un forum.'
	),
	array(
		0 => 'Pourquoi ai-je reçu un avertissement?',
		1 => 'Chaque administrateur a son propre ensemble de règles pour son site. Si vous avez dérogé à une règle, vous pouvez recevoir un avertissement. Notez que c’est la décision de l’administrateur, et que le groupe phpBB n’est pas concerné par les avertissements d’un site donné. Contactez l’administrateur si vous ne comprenez pas les raisons de votre avertissement.'
	),
	array(
		0 => 'Comment rapporter des messages à un modérateur?',
		1 => 'Si l’administrateur l’a permis, allez sur le message à signaler et vous devriez voir un bouton pour rapporter le message. En cliquant dessus, vous accéderez aux étapes nécessaires pour ce faire.'
	),
	array(
		0 => 'A quoi sert le bouton “Sauvegarder” dans la page de rédaction de message?',
		1 => 'Il vous permet d’enregistrer les messages à terminer pour les poster plus tard. Pour les recharger, allez dans le panneau de l’utilisateur (onglet <em>Aperçu --> Gestion des brouillons</em>).'
	),
	array(
		0 => 'Pourquoi mon message doit être validé?',
		1 => 'L’administrateur peut avoir décidé que le forum dans lequel vous postez nécessite la validation des messages. Il est possible aussi que l’administrateur vous ait placé dans un groupe dont les messages doivent être validés avant d’être affichés. Contactez l’administrateur pour plus d’informations.'
	),
	array(
		0 => 'Comment remonter mon sujet?',
		1 => 'En cliquant sur le lien “Remonter le sujet” lors de sa consultation, vous pouvez <em>remonter</em> le sujet en haut du forum sur la première page. Par ailleurs, si vous ne voyez pas ce lien, cela signifie que la remontée de sujet est désactivée ou que l’intervalle de temps pour autoriser la remontée n’est pas atteint. Il est également possible de remonter un sujet simplement en y répondant. Néanmoins, assurez-vous de respecter les règles du forum en le faisant.'
	),
	array(
		0 => '--',
		1 => 'Mise en forme et types de sujet'
	),
	array(
		0 => 'Que sont les BBCodes?',
		1 => 'Le BBCode est une variante du HTML, offrant un large contrôle de mise en forme des éléments d’un message. L’administrateur peut décider si vous pouvez utiliser les BBCodes, vous pouvez aussi les désactiver dans chacun de vos messages en utilisant l’option appropriée du formulaire de rédaction de message. Le BBCode lui-même est similaire au style HTML, mais les balises sont incluses entre crochets [ et ] plutôt que &lt; et &gt;. Pour plus d’informations sur le BBCode, consultez le guide accessible depuis la page de rédaction de message.'
	),
	array(
		0 => 'Puis-je utiliser le HTML?',
		1 => 'Non, il n’est pas possible de publier du HTML sur ce forum. La plupart des mises en forme permises par le HTML peuvent être appliquées avec les BBCodes.'
	),
	array(
		0 => 'Que sont les smileys?',
		1 => 'Les smileys, ou émoticônes, sont de petites images utilisées pour exprimer des sentiments avec un code simple, exemple: :) signifie joyeux, :( signifie triste. La liste complète des smileys est visible sur la page de rédaction de message. Essayez toutefois de ne pas en abuser. Ils peuvent rapidement rendre un message illisible et un modérateur peut décider de les retirer ou simplement d’effacer le message. L’administrateur peut aussi avoir défini un nombre maximum de smileys par message.'
	),
	array(
		0 => 'Puis-je publier des images?',
		1 => 'Oui, vous pouvez afficher des images dans vos messages. Par ailleurs, si l’administrateur a autorisé les fichiers joints, vous pouvez charger une image sur le forum. Autrement, vous devez lier une image placée sur un serveur Web public, exemple: http://www.exemple.com/mon-image.gif. Vous ne pouvez pas lier des images de votre ordinateur (sauf si c’est un serveur Web public) ni des images placées derrière des mécanismes d’authentification, exemple: Boîtes e-mail Hotmail ou Yahoo!, sites protégés par un mot de passe, etc. Pour afficher l’image, utilisez la balise BBCode [img].'
	),
	array(
		0 => 'Que sont les annonces globales?',
		1 => 'Les annonces globales contiennent des informations importantes que vous devez lire dès que possible. Elles apparaissent en haut de chaque forum et dans votre panneau de l’utilisateur. La possibilité de publier des annonces globales dépend des permissions définies par l’administrateur.'
	),
	array(
		0 => 'Que sont les annonces?',
		1 => 'Les annonces contiennent souvent des informations importantes concernant le forum que vous consultez et doivent être lues dès que possible. Les annonces apparaissent en haut de chaque page du forum dans lequel elles sont publiées. Comme pour les annonces globales, la possibilité de publier des annonces dépend des permissions définies par l’administrateur.'
	),
	array(
		0 => 'Que sont les post-it?',
		1 => 'Un post-it apparaît en dessous des annonces sur la première page du forum dans lequel il a été publié. Il contient des informations relativement importantes et vous devez le consulter régulièrement. Comme pour les annonces et les annonces globales, la possibilité de publier des post-it dépend des permissions définies par l’administrateur.'
	),
	array(
		0 => 'Que sont les sujets verrouillés?',
		1 => 'Vous ne pouvez plus répondre dans les sujets verrouillés et tout sondage y étant contenu est alors terminé. Les sujets peuvent être verrouillés pour différentes raisons par un modérateur ou un administrateur. Selon les permissions accordées par l’administrateur, vous pouvez ou non verrouiller vos propres sujets.'
	),
	array(
		0 => 'Que sont les icônes de sujet?',
		1 => 'Les icônes de sujet sont des images qui peuvent être associées à des messages pour refléter leur contenu. La possibilité d’utiliser des icônes de sujet dépend des permissions définies par l’administrateur.'
	),
	// This block will switch the FAQ-Questions to the second template column
	array(
		0 => '--',
		1 => '--'
	),
	array(
		0 => '--',
		1 => 'Niveaux d’utilisateurs et groupes'
	),
	array(
		0 => 'Qui sont les administrateurs?',
		1 => 'Les administrateurs sont les utilisateurs qui ont le plus haut niveau de contrôle sur tout le forum. Ils contrôlent tous les aspects du forum comme les permissions, le bannissement, la création de groupes d’utilisateurs ou de modérateurs, etc., selon les permissions que le fondateur du forum a attribuées aux autres administrateurs. Ils peuvent aussi avoir toutes les capacités de modération sur l’ensemble des forums, selon ce que le fondateur a autorisé.'
	),
	array(
		0 => 'Que sont les modérateurs?',
		1 => 'Les modérateurs sont des utilisateurs (ou groupes d’utilisateurs) dont le travail consiste à vérifier au jour le jour le bon fonctionnement du forum. Ils ont le pouvoir de modifier ou supprimer des messages, de verrouiller, déverrouiller, déplacer, supprimer et diviser les sujets des forums qu’ils modèrent. Généralement, les modérateurs empêchent que les utilisateurs partent en <em>hors-sujet</em> ou publient du contenu abusif ou offensant.'
	),
	array(
		0 => 'Que sont les groupes d’utilisateurs?',
		1 => 'Les groupes sont la manière pour les administrateurs de regrouper et gérer des utilisateurs. Chaque utilisateur peut appartenir à plusieurs groupes et chaque groupe peut avoir des permissions particulières. Cela fournit aux administrateurs une façon simple de modifier les permissions de plusieurs utilisateurs en une fois, telles que rendre plusieurs utilisateurs modérateurs d’un forum ou leur donner accès à un forum privé.'
	),
	array(
		0 => 'Comment adhérer à un groupe d’utilisateurs?',
		1 => 'Pour adhérer à un groupe, cliquez sur le lien <em>Groupes d’utilisateurs</em> dans votre panneau de l’utilisateur, vous pouvez ensuite voir tous les groupes. Tous les groupes ne sont pas en <em>accès libre</em>. Certains peuvent nécessiter une validation, certains sont fermés et d’autres peuvent même être masqués. Si le groupe est ouvert, vous pouvez le rejoindre en cliquant sur le bouton approprié. Si le groupe requiert une validation, vous pouvez demander à le rejoindre en cliquant sur le bouton approprié. Un modérateur de groupe devra confirmer votre requête et pourra vous demander pourquoi vous voulez rejoindre le groupe. N’importunez pas le modérateur s’il annule votre requête, il a sûrement ses raisons.'
	),
	array(
		0 => 'Comment devenir modérateur de groupe?',
		1 => 'Lorsque des groupes sont créés par l’administrateur, il leur est attribué un modérateur. Si vous désirez créer un groupe d’utilisateurs, contactez l’administrateur en premier lieu en lui envoyant un message privé.',
	),
	array(
		0 => 'Pourquoi certains groupes d’utilisateurs apparaissent dans une couleur différente?',
		1 => 'L’administrateur peut attribuer des couleurs aux membres d’un groupe pour les rendre facilement identifiables.'
	),
	array(
		0 => 'Qu’est-ce qu’un “Groupe par défaut”?',
		1 => 'Si vous êtes membre de plus d’un groupe, celui par défaut est utilisé pour déterminer le rang et la couleur de groupe affichés par défaut. L’administrateur peut vous permettre de changer votre groupe par défaut via votre panneau de l’utilisateur.'
	),
	array(
		0 => 'Qu’est-ce que le lien “L’équipe du forum”?',
		1 => 'Cette page donne la liste des membres de l’équipe du forum, y compris les administrateurs et modérateurs ainsi que d’autres détails tels que les forums qu’ils modèrent.'
	),
	array(
		0 => '--',
		1 => 'Messagerie privée'
	),
	array(
		0 => 'Je ne peux pas envoyer de messages privés!',
		1 => 'Il y a trois raisons pour cela: vous n’êtes pas enregistré et/ou connecté, l’administrateur a désactivé la messagerie privée sur l’ensemble du forum, ou l’administrateur vous a empêché d’envoyer des messages. Contactez l’administrateur pour plus d’informations.'
	),
	array(
		0 => 'Je reçois sans arrêt des messages indésirables!',
		1 => 'Vous pouvez empêcher un utilisateur de vous envoyer des messages en utilisant les filtres de message dans les paramètres de votre messagerie privée. Si vous recevez des messages privés abusifs d’un utilisateur en particulier, informez l’administrateur. Ce dernier a la possibilité d’empêcher complètement un utilisateur d’envoyer des messages privés.'
	),
	array(
		0 => 'J’ai reçu un e-mail ou un courrier abusif d’un utilisateur de ce forum!',
		1 => 'Le formulaire de courrier électronique du forum comprend des sécurités pour suivre les utilisateurs qui envoient de tels messages. Envoyez à l’administrateur une copie complète de l’e-mail reçu. Il est très important d’inclure les en-têtes (ils contiennent des informations sur l’expéditeur de l’e-mail). L’administrateur pourra alors prendre les mesures nécessaires.'
	),
	array(
		0 => '--',
		1 => 'Amis et ignorés'
	),
	array(
		0 => 'Que sont mes listes d’amis et d’ignorés?',
		1 => 'Vous pouvez utiliser ces listes pour organiser les autres membres du forum. Les membres ajoutés à votre liste d’amis seront affichés dans votre panneau de l’utilisateur pour un accès rapide, voir leur état de connexion et leur envoyer des messages privés. Selon les thèmes graphiques, leurs messages peuvent être mis en valeur. Si vous ajoutez un utilisateur à votre liste d’ignorés, tous ses messages seront masqués par défaut.'
	),
	array(
		0 => 'Comment puis-je ajouter/supprimer des utilisateurs de ma liste d’amis ou d’ignorés?',
		1 => 'Vous pouvez ajouter des utilisateurs à votre liste de deux manières. Dans le profil de chaque membre, il y a un lien pour l’ajouter dans votre liste d’amis ou d’ignorés. Ou, depuis votre panneau de l’utilisateur, vous pouvez ajouter directement des membres en saisissant leur nom d’utilisateur. Vous pouvez également supprimer des utilisateurs de votre liste depuis cette même page.'
	),
	array(
		0 => '--',
		1 => 'Recherche dans les forums'
	),
	array(
		0 => 'Comment rechercher dans les forums?',
		1 => 'Saisissez un terme à rechercher dans la zone de recherche située en haut des pages d’index, de forums ou de sujets. La recherche avancée est accessible en cliquant sur le lien “Recherche avancée” disponible sur toutes les pages du forum. L’accès à la recherche peut dépendre des thèmes graphiques utilisés.'
	),
	array(
		0 => 'Pourquoi ma recherche ne renvoie aucun résultat?',
		1 => 'Votre recherche est probablement trop vague ou comprend plusieurs termes courants non indexés par phpBB 3. Vous pouvez affiner votre recherche en utilisant les options disponibles dans la recherche avancée.'
	),
	array(
		0 => 'Pourquoi ma recherche retourne une page blanche!?',
		1 => 'Votre recherche renvoie plus de résultats que ne peut gérer le serveur Web. Utilisez la “Recherche avancée” et soyez plus précis dans le choix des termes utilisés et des forums concernés par la recherche.'
	),
	array(
		0 => 'Comment rechercher des membres?',
		1 => 'Allez sur la page “Membres”, cliquez sur le lien  “Rechercher un utilisateur” et remplissez les options nécessaires.'
	),
	array(
		0 => 'Comment puis-je trouver mes propres messages et sujets?',
		1 => 'Vos messages peuvent être retrouvés en cliquant sur “Voir vos messages” dans le panneau de l’utilisateur ou via votre propre page de profil. Pour rechercher vos sujets, utilisez la page de recherche avancée et choisissez les paramètres appropriés.'
	),
	array(
		0 => '--',
		1 => 'Surveillance des sujets et favoris'
	),
	array(
		0 => 'Quelle est la différence entre les favoris et la surveillance?',
		1 => 'Les favoris dans phpBB 3 sont comme les favoris de votre navigateur. Vous n’êtes pas nécessairement averti des mises à jour, mais vous pouvez revenir plus tard sur le sujet. A l’inverse, la surveillance vous préviendra lorsqu’un sujet ou un forum sera mis à jour via votre choix de préférence.'
	),
	array(
		0 => 'Comment surveiller des forums ou sujets particuliers?',
		1 => 'Pour surveiller un forum particulier, une fois entré sur celui-ci, cliquez sur le lien “Surveiller ce forum”. Pour surveiller un sujet, vous pouvez soit répondre à ce sujet et cocher la case du formulaire de rédaction de message pour le surveiller, soit cliquer sur le lien “Surveiller ce sujet” disponible en consultant le sujet lui-même.'
	),
	array(
		0 => 'Comment puis-je supprimer mes surveillances de sujets?',
		1 => 'Pour supprimer vos surveillances, allez dans votre panneau de l’utilisateur (onglet <em>Aperçu --> Gestion des surveillances</em>) et suivez les instructions.'
	),
	array(
		0 => '--',
		1 => 'Fichiers joints'
	),
	array(
		0 => 'Quels fichiers joints sont autorisés sur ce forum?',
		1 => 'L’administrateur peut autoriser ou interdire certains types de fichiers joints. Si vous n’êtes pas sûr de ce qui est autorisé à être chargé, contactez l’administrateur pour plus d’informations.'
	),
	array(
		0 => 'Comment trouver tous mes fichiers joints?',
		1 => 'Pour trouver la liste des fichiers joints que vous avez chargés, allez dans votre panneau de l’utilisateur puis <em>Gestion des fichiers joints</em>.'
	),
	array(
		0 => '--',
		1 => 'Concernant phpBB 3'
	),
	array(
		0 => 'Qui sont les auteurs de ce forum?',
		1 => 'Ce logiciel (dans sa forme originale) est produit, distribué et son copyright est détenu par le <a href="http://www.phpbb.com/">Groupe phpBB</a>. Il est rendu accessible sous la Licence Publique Générale GNU et peut être distribué gratuitement. Consultez le lien pour plus d’informations.'
	),
	array(
		0 => 'Pourquoi la fonctionnalité X n’est pas disponible?',
		1 => 'Ce programme a été écrit et mis sous licence par le Groupe phpBB. Si vous pensez qu’une fonctionnalité nécessite d’être ajoutée, visitez le site Internet phpbb.com et voyez ce que le Groupe phpBB en dit. N’envoyez pas de requêtes de fonctionnalités sur le forum de phpbb.com, le groupe utilise SourceForge pour gérer ces nouvelles requêtes. Lisez les forums pour voir leur position, s’ils en ont une, par rapport à cette fonctionnalité, et suivez la procédure donnée là-bas.'
	),
	array(
		0 => 'Qui contacter pour les abus ou les questions légales concernant ce forum?',
		1 => 'Contactez n’importe lequel des administrateurs de la liste “L’équipe du forum”. Si vous restez sans réponse alors prenez contact avec le propriétaire du domaine (en faisant une <a href="http://www.google.com/search?q=whois">recherche sur whois</a>) ou si un service gratuit est utilisé (exemple: Yahoo!, Free, f2s.com, etc.), avec le service de gestion ou des abus. Notez que le groupe phpBB <strong>n’a absolument aucun contrôle</strong> et ne peut être en aucune façon tenu pour responsable sur <em>comment</em>, <em>où</em> ou <em>par qui</em> ce forum est utilisé. Il est inutile de contacter le groupe phpBB pour toute question légale (cessions et désistements, responsabilité, propos diffamatoires, etc.) <strong>non directement liée</strong> au site Internet phpbb.com ou au logiciel phpBB lui-même. Si vous adressez un e-mail au groupe phpBB à propos de l’utilisation <strong>d’une tierce partie</strong> de ce logiciel vous devez vous attendre à une réponse très courte voire à aucune réponse du tout.'
	)
);

?>