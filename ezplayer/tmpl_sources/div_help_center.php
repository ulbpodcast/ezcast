<?php
/*
 * EZCAST EZplayer
 *
 * Copyright (C) 2016 Université libre de Bruxelles
 *
 * Written by Michel Jansens <mjansens@ulb.ac.be>
 * 	      Arnaud Wijns <awijns@ulb.ac.be>
 *            Carlos Avidmadjessi
 * UI Design by Julien Di Pietrantonio
 *
 * This software is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 3 of the License, or (at your option) any later version.
 *
 * This software is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with this software; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/
?>
<div id="help-content">
    <h2>Aide à l'utilisation d'EZplayer</h2>
    <p>Ce tutoriel a pour objectif de vous permettre d’utiliser les fonctionnalités essentielles de l’interface 
        de visualisation des podcasts “EZplayer” et de pouvoir créer des discussions et gérer une liste de signets pour chacune des vidéos.</p>
    <p>Si vous ne trouvez pas l'aide que vous recherchez, contactez : <a href="mailto:<?php global $mailto_alert; echo $mailto_alert; ?>"><?php echo $mailto_alert; ?></a></p>
    <p>Ce tutoriel existe au format .pdf ; vous pouvez le télécharger dans l'espace étudiants du site 
        "<a href="¤tuto¤">¤organization¤</a>"</p>
    <ul id="topics">
        <li>
            <a>1.  PRÉSENTATION & CONCEPTS</a>
            <div>
                <p>«<b>EZplayer</b>» est une interface vous permettant de visualiser les vidéos enregistrées en auditoire 
                    ou soumises par vos professeurs au moyen de la solution «<b> ¤organization¤ </b>». En plus d’être un lecteur 
                    de vidéos en ligne, «<b> EZplayer </b>» offre aussi la possibilité d’annoter, de chapitrer et de partager 
                    les vidéos, de consulter une liste de signets soumise par votre professeur (on parle de «<b> signets officiels </b>») 
                    ou encore, de passer facilement d’une vidéo à un diaporama et inversement.</p>

                <p>L’interface fonctionne selon <b>trois niveaux distincts</b>. Le premier niveau, autrement dit la 
                    page d’accueil, contient la liste de vos <b>cours</b> favoris. Le second niveau, atteint lorsqu’un 
                    cours a été selectionné au niveau un, représente tous les <b>enregistrements</b> disponibles pour 
                    le cours sélectionné. Enfin, le troisième et dernier niveau, représente les <b>détails de l’enregistrement</b> 
                    sélectionné au niveau deux.</p>

                <p>Différentes interactions, que nous allons passer en revue dans la suite de ce tutoriel, seront disponibles 
                    en fonction du niveau dans lequel on se trouve. </p>

                <p>Sachez aussi qu’il existe <b>deux modes de connexion</b> à “<b>EZplayer</b>”; la connexion en tant 
                    qu’utilisateur <b>anonyme</b>, qui vous permettra uniquement de consulter les vidéos et la liste 
                    des signets officiels, et la connexion en tant qu’utilisateur <b>identifié</b>, qui vous offrira 
                    de nombreuses possibilités, telles que l’ajout de signets et de discussions sur les vidéos, le partage d’un moment 
                    précis d’une vidéo, l’import/export de signets, la gestion de vos cours favoris, etc.</p>
            </div>
        </li>

        <li>
            <a>2.  SE CONNECTER</a>
            <div>
                <b class="title">Introduction</b>
                <p>Cette section décrit comment vous connecter à l’interface “<b>EZplayer</b>” pour consulter et 
                    interagir avec les différentes vidéos auxquelles vous avez accès. 
                </p>

                <b class="title">Adresse</b>
                <p>Entrez l’URL suivante dans la barre d’adresse de votre navigateur Internet : 
                    <a href="¤serveur_player¤" style="text-transform: none;">¤serveur_player¤</a></p>

                <b class="title">Login et mot de passe</b>
                <p>
                    - Le "<b>NetID</b>" est votre NetID de l’Université libre de Bruxelles
                    <br/>- Le "<b>Password</b>" est le mot de passe associé à votre NetId 
                </p>

                <b class="title">Ecran d'accueil de l'interface</b>
                <p><img alt="Ecran d'accueil EZplayer" src="./images/Help_v2/screen_001.png"/></p>

                <b class="title">Ecran une fois connecté à l'interface</b>
                <p>Voici l’écran d’accueil de l’interface si vous vous connectez pour la première fois. Votre liste de cours 
                    favoris est vide et une vidéo tutoriel vous explique comment ajouter des cours sur votre page d’accueil. </p>
                <p><img alt="Ecran d'accueil EZplayer" src="./images/Help_v2/screen_002.png"/></p>

                <b class="title">Se déconnecter</b>
                <p>Pour vous déconnecter, cliquez sur le bouton “<b>Déconnexion</b>” en haut à droite de l’interface.</p>
                <p><img alt="se déconnecter d'EZplayer" src="./images/Help_v2/screen_003.png"/></p>

                <b class="title">Remarque</b>
                <p>L’interface “EZplayer” est bilingue : vous pouvez opter pour le <b>français</b> ou l’<b>anglais</b> 
                    au moment de la connexion.</p>
                <p><img alt="Choix de la langue" src="./images/Help_v2/screen_004.png"/></p>

            </div>
        </li>

        <li>
            <a>3.  AJOUTER UN COURS SUR LA PAGE D’ACCUEIL</a>
            <div>
                <b class="title">Introduction</b>
                <p>Cette section décrit la marche à suivre pour ajouter un cours sur la page d’accueil.
                </p>

                <b class="title">Définition</b>
                <p><b>Cours</b> : un cours est un regroupement de plusieurs enregistrements. 
                </p>

                <b class="title">Marche à suivre</b>
                <p>Voici la marche à suivre pour ajouter un cours en favori :
                </p>
                <p><b>1.</b> Rendez vous dans l’espace dédié à votre cours podcasté sur l’<b>Université Virtuelle</b>. 
                    Cliquez sur le lien “<b>Accéder aux podcasts du cours</b>”. 
                </p>
                <p><img alt="Icônes de l'Université Virtuelle" src="./images/Help_v2/screen_005.png"/></p>
                <p><b>2.</b> Si vous n’êtes pas encore connecté à l’interface EZplayer, vous devez vous connecter 
                    (référez vous au point “<i>2. Se connecter</i>” de ce tutoriel). Vous arrivez alors sur la page 
                    qui vous permet de <b>sélectionner un enregistrement</b> parmi tous ceux disponibles pour le cours. </p>
                <p><img alt="Sélectionner un enregistrement" src="./images/Help_v2/screen_006.png"/></p>

                <b class="title">Résultat</b>
                <p>Lorsque vous retournez sur la page d’accueil, via le lien “<b>Accueil</b>” en haut à gauche de la page, 
                    votre cours a été ajouté à la liste de vos favoris. 
                </p>
                <p><img alt="Cours ajouté sur la page d'accueils" src="./images/Help_v2/screen_007.png"/></p>

                <b class="title">Remarque</b>
                <p>Utilisez toujours les <b>liens de navigation</b> prévus dans l’application pour naviguer entre 
                    les différents niveaux du site. Si vous utilisez les flèches “Précédent” et “Suivant” de 
                    votre navigateur, vous ne serez <b>pas toujours redirigé</b> vers les bonnes pages.
                </p>

            </div>
        </li>

        <li>
            <a>4.  NAVIGUER AU SEIN D’EZPLAYER</a>
            <div>                
                <b class="title">Introduction</b>
                <p>Maintenant que nous avons un cours disponible, nous allons pouvoir détailler le processus 
                    de <b>navigation</b> au sein de l’application. 
                </p>

                <b class="title">Définitions</b>
                <p><b>Naviguer</b> : Action de parcourir les différents niveaux du site, pour accéder au contenu. 
                </p>
                <p><b>Niveau</b> : Le site se décompose en <b>trois niveaux</b> distincts, eux-mêmes subdivisés en 
                    <b>deux parties</b> pour deux d’entre-eux. Le premier niveau contient la liste des 
                    <b>cours favoris</b>. Le second niveau contient la liste des <b>enregistrements</b> pour le cours 
                    sélectionné. Le troisième niveau contient le <b>détail</b> de l’enregistrement sélectionné. 
                </p>
                <p><b>Signet</b> : Un signet est un <b>marquage temporel</b> au sein d’une vidéo. Typiquement, 
                    un signet est déterminé par un titre, une description, une série de mots clés et un niveau 
                    hiérarchique et pointe vers une <b>seconde précise</b> d’une vidéo. <br/> 
                    Un signet peut être “<b>personnel</b>”, c’est-à-dire qu’il est créé par l’utilisateur connecté 
                    et n’est visible que par ce même utilisateur, ou “<b>officiel</b>”, s’il est créé par un 
                    titulaire de cours. Dans ce cas, il est visible par tous les utilisateurs du cours. 
                </p>

                <b class="title">Attention</b>
                <p>La navigation au sein du site doit se faire uniquement au moyen des <b>liens prévus</b> dans 
                    l’application. Il est déconseillé d’utiliser les boutons “Précédent” et “Suivant” du 
                    navigateur pour naviguer entre les différents niveaux du site. 
                </p>

                <b class="title">Marche à suivre</b>
                <p>Voici la marche à suivre pour naviguer au sein du site :
                </p>
                <p><b>1.</b> Après vous être connecté , vous arrivez sur la page d’accueil, c’est-à-dire, 
                    le <b>niveau un</b> du site. Vous y trouvez la liste de vos <b>cours favoris</b>. 
                    En vis-à-vis des cours peut figurer une “<b>bulle</b>”, contenant un nombre (A). Il s’agit du nombre de 
                    vidéos <b>non visionnées</b> contenues dans le cours. (Cette option peut être activée en modifiant vos préférences. Référez vous au point “<i>21. Gérer ses préférences</i>” de ce tutroriel)
                </p>
                <p><img alt="Vidéos non visionnées" src="./images/Help_v2/screen_008.png"/></p>
                <p><b>2.</b> Lorsque vous sélectionnez un de vos cours favoris, vous accédez au 
                    <b>niveau deux</b> du site. Cet écran est divisé en <b>trois parties</b>; la partie de <b>gauche</b> (A), 
                    qui contient la liste des <b>enregistrements</b> pour le cours sélectionné, celle de <b>droite</b> (B) 
                    qui contient la liste des <b>signets</b> relatifs à tous les enregistrements liés au cours sélectionné et 
                    celle du dessous (C) - pas toujours présente - qui affiche les <b>dernières discussions</b>. <br/>
                    Le panneau de droite est lui-même subdivisé en <b>deux onglets</b>; l’onglet des signets <b>personnels</b> 
                    (I), qui est alimenté par vous-même et qui est propre à chaque utilisateur et l’onglet des signets 
                    <b>officiels</b> (II), qui est tenu à jour par un titulaire du cours et qui est visible par tous 
                    les utilisateurs du cours. Les signets apparaissant à ce niveau-ci de l’application sont tous relatifs 
                    au cours sélectionné. <br/>
                    En vis-à-vis de certains enregistrements peut apparaître un <b>marqueur</b> (III). 
                    Celui-ci désigne une vidéo qui n’a pas encore été visionnée. (Cette option peut être activée en modifiant vos préférences. Référez vous au point “<i>21. Gérer ses préférences</i>” de ce tutroriel).
                    
                </p>
                <p><img alt="Structure du niveau deux" src="./images/Help_v2/screen_009.png"/></p>
                <p>3. Enfin, en sélectionnant un enregistrement parmi la liste, vous arrivez au <b>niveau trois</b> du site. 
                    Ce dernier niveau est, lui aussi, divisé en <b>trois parties</b>: à gauche (A), le lecteur de vidéo (I) 
                    et les actions (II) qui s’y réfèrent, à droite (B), la liste des signets relatifs à l’enregistrement 
                    sélectionné. Encore une fois, le panneau de droite est subdivisé en deux onglets; les signets 
                    personnels (III) et les signets officiels (IV). En-dessous (C), la description de la vidéo (V - Facultative) et les discussions (VI)</p>
                <p><img alt="Structure du niveau trois" src="./images/Help_v2/screen_010.png"/></p>
            </div>
        </li>

        <li>
            <a>5.  GÉRER SES COURS</a>
            <div>
                <b class="title">Introduction</b>
                <p>Cette section décrit la marche à suivre pour gérer les cours favoris apparaissant sur la page d’accueil. 
                    La gestion des cours se limite à déterminer l’<b>ordre d’affichage</b> des cours favoris et à 
                    <b>supprimer</b> les cours que vous ne souhaitez pas voir sur la page d’accueil. </p>

                <b class="title">Marche à suivre</b>
                <p>Voici la marche à suivre pour gérer les cours favoris :</p>
                <p><b>1.</b> Pour modifier l’<b>ordre d’affichage</b> des cours sur la page d’accueil, utilisez les 
                    flèches “<b>monter</b>” et “<b>descendre</b>” présentes devant le titre du cours à déplacer.</p>
                <p><img alt="Organiser les cours favoris" src="./images/Help_v2/screen_011.png"/></p>
                <p><b>2.</b> Pour retirer un cours de vos favoris, cliquez sur la petite croix du cours à supprimer. </p>
                <p><img alt="Supprimer un cours favori" src="./images/Help_v2/screen_012.png"/></p>
                <p><b>3.</b> Une boîte de dialogue s’ouvre. Cliquez sur le bouton “<b>Supprimer</b>” pour retirer le 
                    cours de vos favoris. </p>
                <p><img alt="Supprimer un cours favori" src="./images/Help_v2/screen_013.png"/></p>                

                <b class="title">Attention</b>
                <p>Lorsque vous retirez un cours de vos favoris, tous les <b>signets</b> que vous avez créés 
                    pour ce cours sont <b>supprimés</b> en même-temps. Si vous souhaitez, malgré tout, conserver 
                    vos signets, exportez les comme expliqué dans la rubrique “<i>16. Exporter des signets</i>” de 
                    ce tutoriel. </p>

                <b class="title">Résultat</b>
                <p>La page d'accueil est réorganisée.</p>
            </div>
        </li>

        <li>
            <a>6.   VISIONNER UNE VIDÉO</a>
            <div>
                <b class="title">Introduction</b>
                <p>Cette section décrit la marche à suivre pour visionner une vidéo. </p>

                <b class="title">Marche à suivre</b>
                <p><b>1.</b> Sélectionnez le cours dans lequel se trouve l’enregistrement à visionner. </p>
                <p><img alt="Sélectionner un cours" src="./images/Help_v2/screen_014.png"/></p> 
                <p><b>2.</b> Cliquez sur l’enregistrement que vous souhaitez visionner.</p>
                <p><img alt="Sélectionner un enregistrement" src="./images/Help_v2/screen_015.png"/></p> 
                <p><b>3.</b> Lancez la lecture via le bouton “<b>Play</b>” dans le lecteur (ou utilisez le raccourci
                    clavier [ESPACE]). Par défaut, 
                    la vidéo est jouée en qualité <b>standard</b>. Vous pouvez sélectionner la haute résolution 
                    en cliquant sur le bouton <b>HD</b> et revenir à la résolution standard en cliquant sur 
                    le bouton <b>SD</b> (C). </p>
                <p><img alt="Controles du lecteur" src="./images/Help_v2/screen_016.png"/></p>
                <p>Dans le cas où une vidéo est disponible en différentes versions, c’est-à-dire, s’il existe 
                    une vidéo <b>caméra</b> et une vidéo <b>diaporama</b> de la même leçon, vous pouvez passer 
                    de la vidéo caméra au diaporama en cliquant sur les boutons prévus à cet effet (B) 
                    ou en utilisant le raccourci clavier [S].</p>
                <p>Dans certains navigateurs compatibles, vous avez la possibilité de jouer la vidéo en différentes 
                    vitesses (0.5x à 2.0x la vitesse originale). Pour cela, cliquez sur le <b>bouton indiquant 
                        la vitesse de lecture</b> (A) ou utilisez les raccourcis clavier <b>[+]</b> et <b>[-]</b>.</p>
                <p>Il est aussi possible de regarder la vidéo en <b>plein écran</b>, tout en gardant accès à la 
                    barre de controle de la vidéo. Cliquez simplement sur le <b>bouton plein écran</b> (D) 
                    (ou utilisez le raccourci clavier <b>[F]</b>) pour ouvrir ou quitter le mode plein écran.</p>
                <p>Vous pouvez aussi lancer la lecture d’une vidéo directement en cliquant sur un <b>signet</b> au niveau 
                    deux de l’application. De cette manière, la vidéo sera lue à partir de la seconde mentionnée 
                    dans le signet sélectionné. </p>
                <p>Utilisez, de la même manière, les signets qui se trouvent dans l’onglet à droite de la vidéo 
                    pour naviguer au sein de la vidéo. </p>                
            </div>
        </li>


        <li>
            <a>7.   MODE PLEIN ECRAN</a>
            <div>
                <b class="title">Introduction</b>
                <p>Cette section décrit la marche à suivre pour visionner et contrôler la vidéo en mode plein écran</p>

                <b class="title">Marche à suivre</b>
                <p><b>1.</b> Lorsque vous êtes sur la page de consultation des vidéos, cliquez sur le <b>dernier
                        bouton</b> pour passer en mode plein écran ou utilisez la touche <b>[F]</b> de votre clavier.</p>
                <p><img alt="Mode plein écran" src="./images/Help_v2/screen_044.png"/></p> 
                <p><b>2.</b> La vidéo occupe alors toute la surface de la page dans votre navigateur web.</p>
                <p><img alt="Vue plein écran" src="./images/Help_v2/screen_045.png"/></p> 
                <p><b>3.</b> Lorsque le mode plein écran est activé, vous pouvez utiliser le <b>bouton des raccourcis</b> 
                    (A) pour afficher la liste des raccourcis disponibles (ou la touche <b>[R]</b> de votre clavier). 
                    Quittez le mode plein écran en cliquant sur le <b>bouton plein écran</b> (B) ou en appuyant 
                    sur la touche <b>[ESC]</b> de votre clavier. Vous pouvez aussi afficher la <b>liste des signets</b> 
                    en appuyant sur le dernier bouton (C) ou en utilisant le raccourci clavier <b>[B]</b>.
                </p>
                <p><img alt="Vue plein écran" src="./images/Help_v2/screen_046.png"/></p> 

                <b class="title">Remarque</b>
                <p>Pour accéder au mode plein écran, utilisez le bouton plein écran présent dans la 
                    <b>barre de controle</b> de la vidéo plutôt que celui par défaut du lecteur vidéo 
                    pour avoir accès aux différentes fonctionnalités enrichies du lecteur “EZplayer".</p>
            </div>
        </li>        
        
        <li>
            <a>8.   CRÉER UNE DISCUSSION</a>
            <div>
                <b class="title">Introduction</b>
                <p>Cette section décrit la marche à suivre pour une discussion dans une vidéo.</p>

                <b class="title">Définitions</b>
                <p><b>Discussion</b> : Une discussion est la base d'une <b>interaction</b> entre différents 
                    utilisateurs (Etudiants / Enseignants). Elle permet de créer un <b>sujet</b> qui sera discuté 
                    sous forme de commentaires par l'ensemble des utilisateurs identifiés ayant accès à la vidéo.
                    A l'instar des signets, les discussions sont <b>liées temporellement</b> à un moment précis de la 
                    vidéo. Une discussion peut être <b>ouverte</b> à tout les utilisateurs d'EZplayer, ou en <b>accès limité</b> 
                    pour les étudiants uniquement. Cela signifie qu'une discussion en accès limité ne sera accessible par
                    <b>aucun professeur</b> ayant accès à EZplayer, qu'il soit lié ou non au cours contenant la vidéo.<br/> 
                    Une discussion est représentée par un titre et une description, et pointe vers une <b>seconde 
                    précise</b> d'une vidéo.
                </p>

                <b class="title">Marche à suivre</b>
                <p><b>1.</b> Sélectionnez le cours dans lequel se trouve l’enregistrement à discuter.</p>
                <p><img alt="Sélectionner un cours" src="./images/Help_v2/screen_014.png"/></p> 
                <p><b>2.</b> Cliquez sur l’enregistrement pour lequel vous souhaitez créer une discussion.</p>
                <p><img alt="Sélectionner un enregistrement" src="./images/Help_v2/screen_017.png"/></p> 
                <p><b>3.</b> Lors de la lecture de la vidéo, dans les <b>contrôles</b>, en-dessous de la vidéo, 
                    sélectionnez l’action “<b>Créer une discussion</b>” (A) (ou utilisez le raccourci clavier <b>[D]</b>). 
                    La vidéo est mise en pause et un formulaire se déploie.<br/>
                    Le code temps est complété automatiquement (mais peut être modifié), les autres champs 
                    sont <b>obligatoires</b>. Vous avez certaines possibilités d'édition du texte dans le champ de saisie. <br/>
                    Publiez (B) la discussion ou appuyez sur le bouton “Annuler” pour annuler la saisie de la discussion. 
                </p>
                <p><img alt="Créer une discussion" src="./images/Help_v2/screen_047.png"/></p> 
                <p><b>4.</b> Si vous n'êtes pas titulaire d'un cours, une fenêtre s'ouvre pour vous permettre de 
                    choisir la visibilité de la discussion.<br/>
                    Sélectionnez 'Etudiants + professeurs' pour ouvrir la discussion à tout le monde, ou 'Etudiants' pour 
                    limiter l'accès aux seuls étudiants.</p> 
                <p><img alt="Choisir la visibilité de la discussion" src="./images/Help_v2/screen_048.png"/></p> 

                <b class="title">Remarques</b>
                <p>Votre discussion apparaitra sous forme de <b>notification</b> lors de la lecture de la vidéo par les
                    autres utilisateurs.</p>
                <p>En tant qu'enseignant, il n'est pas possible de créer une discussion en accès limité.</p>
                <p>Votre nom et prénom sont affichés dans l'entête de votre discussion, il n'est pas possible de publier 
                    une discussion anonyme.</p>

                <b class="title">Résultat</b>
                <p>La discussion apparaît dans la liste des discussions, en-dessous de la vidéo.</p>
                <p><img alt="Discussions ajoutée" src="./images/Help_v2/screen_049.png"/></p> 

            </div>
        </li>
        
                <li>
            <a>9.   PARCOURIR ET COMPRENDRE LES DISCUSSIONS</a>
            <div>
                <b class="title">Introduction</b>
                <p>Cette section décrit la marche à suivre pour parcourir et comprendre les discussions.</p>

                <b class="title">Marche à suivre</b>
                <p><b>1.</b> Sélectionnez le cours dont vous souhaitez voir les discussions.</p>
                <p><img alt="Sélectionner un cours" src="./images/Help_v2/screen_014.png"/></p> 
                <p><b>2.</b> A ce niveau, on observe en bas de page, les discussions qui ont été mises à jour 
                    le plus récemment (A). Seules les 5 dernières discussions parmi tous les enregistrements de l'album 
                    sont affichées à ce niveau-ci.</p>
                <p>Chacune des discussions est isolée des autres par un trait pointillé (B) et affiche les informations suivantes:
                    <br/> - Le <b>titre de l'enregistrement</b> dans lequel elle se trouve (I)
                    <br/> - Le <b>titre de la discussion</b> (II)
                    <br/> - La <b>date de modification</b> et le <b>nom</b> de la personne qui a, en dernier, <b>créé</b>, 
                    <b>modifié</b> ou <b>répondu</b> à la discussion (III)                    
                </p>
                <p><img alt="Dernières discussions" src="./images/Help_v2/screen_050.png"/></p> 
                <p>En cliquant sur une discussion, vous arrivez dans l'enregistrement concerné par la discussion, au moment 
                de la vidéo ciblé par la discussion. L'affichage présente les details de la discussion (reportez-vous au point 5 ci-dessous).</p>
                <p><b>3.</b> Sélectionnez un enregistrement pour consulter l'ensemble des discussions qui s'y rapportent. 
                </p>
                <p><img alt="Sélectionner un enregistrement" src="./images/Help_v2/screen_017.png"/></p> 
                <p><b>4.</b> En bas de page, on retrouve l'ensemble des discussions liées à l'enregistrement sélectionné. Les discussions sont 
                    isolées les unes des autres par un trait pointillé (A) et affichent les informations suivantes:
                    <br/> - La <b>visibilité</b> de la discussion (I), elle peut être à accès limité (un seul personnage) ou ouverte à tous (deux personnages).
                    <br/> - Le <b>moment</b> (II) de la vidéo ciblé par la discussion (en cliquand dessus, la vidéo se place au bon endroit)
                    <br/> - Le <b>nom de la personne</b> qui a créé la discussion (III)
                    <br/> - Le <b>titre</b> de la discussion (IV)
                    <br/> - La <b>date</b> de modification</b> et le <b>nom</b> de la personne qui a, en dernier, <b>créé</b>, 
                    <b>modifié</b> ou <b>répondu</b> à la discussion (V)  
                    <br/> - Le nombre de réponses à la discussion (VI)
                </p>
                <p><img alt="Liste des discussions" src="./images/Help_v2/screen_051.png"/></p> 
                
                <b class="title">Remarques</b>
                <p>Il est possible d'afficher la description d'une discussion en cliquant sur la petite flèche se trouvant à droite des 
                    informations de la discussion.                    
                </p>
                <p><img alt="Déployer une discussion" src="./images/Help_v2/screen_052.png"/></p> 
                <p>Vous pouvez rafraichir la liste des discussions en cliquant sur les flèches circulaires dans l'entête des discussions.</p>
                <p><img alt="Choisir la visibilité de la discussion" src="./images/Help_v2/screen_053.png"/></p> 
                
                <p><b>5.</b> En cliquant sur une discussion, on affiche le <b>contenu</b> de celle-ci.<br/>
                On compte trois parties distinctes:
                <br/> - La discussion initiale (A)
                <br/> - La meilleure réponse (B): Elle n'est pas toujours présente et dépend du vote de l'ensemble des utilisateurs
                <br/> - Le flux des commentaires (C) pour la discussion.</p> 
                <p><img alt="Décomposition d'une discussion" src="./images/Help_v2/screen_054.png"/></p> 
                <p>Dans la partie réservée à la discussion initiale, on retrouve :
                    <br/> - La <b>visibilité</b> (I) de la discussion (ouvert à tous ou accès limité) 
                    <br/> - Le <b>moment</b> (II) de la vidéo ciblé par la discussion (en cliquand dessus, la vidéo se place au bon endroit)
                    <br/> - Le <b>titre</b> de la discussion (III)
                    <br/> - Le <b>nom de la personne</b> qui a créé la discussion et la date de création (IV)
                    <br/> - La <b>description</b> de la discussion (V)</p>
                <p><img alt="Discussion initiale" src="./images/Help_v2/screen_055.png"/></p> 
                <p>Dans la partie réservée à la meilleure réponse, on retrouve simplement le commentaire et le nombre de votes obtenus.</p>
                <p><img alt="Meilleure réponse" src="./images/Help_v2/screen_056.png"/></p> 
                <p>Dans le flux des commentaires, on retrouve, pour chacun des commentaires: 
                    <br/> - Le <b>nom de l'auteur</b> du commentaire (I)
                    <br/> - La <b>date</b> de la réponse (II)
                    <br/> - La <b>réponse</b> (III)
                    <br/> - Le nombre de <b>votes</b> (IV) et la possibilité d'ajouter un vote positif ou négatif</p>
                <p><img alt="Flux des commentaires" src="./images/Help_v2/screen_057.png"/></p> 
                <p>Certains commentaires peuvent être <b>approuvés</b> par le titulaire du cours. Dans ce cas, ils arborent une entête 
                marquant cette approbation.</p>
                <p><img alt="Réponse approuvée" src="./images/Help_v2/screen_058.png"/></p> 

                <p><b>6.</b> Vous pouvez retourner à la liste des discussions en cliquant sur l'icone de liste (A) ou 
                rafraichir le contenu de la discussion en cliquant sur les flèches circulaires (B) dans l'entête de discussion. <br/><p>
                <p><img alt="Notification de discussions" src="./images/Help_v2/screen_060.png"/></p> 
                <b class="title">Remarques</b>
                <p>En cliquant sur la meilleure réponse, la page défile jusqu'a son emplacement dans le flux des commentaires.</p>
                <p>Au moment de la lecture de la vidéo, apparaissent des notifications de discussion qui indiquent qu'une discussion 
                est disponible pour ce moment précis de la vidéo. En cliquant sur cette notification, l'affichage présente le 
                contenu de la discussion.</p>
                <p><img alt="Notification de discussions" src="./images/Help_v2/screen_059.png"/></p> 
                <p>En tant qu'enseignant, il n'est pas possible d'afficher et de participer aux discussions en accès limité.</p>
                <p style="color: #7C0505">Si les discussions ne s'affichent pas, vérifiez qu'elles sont bien activées dans vos préférences (reportez vous au 
                    point "<i>21. Gérer ses préférences</i>" de ce tutoriel)</p>

            </div>
        </li>
        
        <li>
            <a>10. PARTICIPER A UNE DISCUSSION</a>
            <div>
                <b class="title">Introduction</b>
                <p>Cette section décrit la marche à suivre pour participer à une discussion. </p>

                <b class="title">Marche à suivre</b>
                <p><b>1.</b> Sélectionnez le cours dans lequel se trouve la discussion. </p>
                <p><img alt="Sélectionner un cours" src="./images/Help_v2/screen_014.png"/></p> 
                <p><b>2.</b> Cliquez sur l’enregistrement ciblé par la discussion.</p>
                <p><img alt="Sélectionner un enregistrement" src="./images/Help_v2/screen_015.png"/></p> 
                <p><b>3.</b> Sélectionner la discussion à laquelle vous souhaitez participer.</p>
                <p><img alt="Sélectionner une discussion" src="./images/Help_v2/screen_061.png"/></p> 
                <p><b>4.</b> Dans la discussion, cliquez sur "Répondre à la discussion". </p>
                <p><img alt="Répondre à la discussion" src="./images/Help_v2/screen_062.png"/></p>
                <p>Un champ de saisie se déploie, vous permettant d'entrer votre commentaire. Placez votre curseur sur 
                    chacune des icones de l'éditeur de texte pour savoir ce qu'elles permettent de faire. <br/>
                    Insérez votre commentaire et validez le en cliquant sur "Répondre"</p>
                <p><img alt="Répondre à la discussion" src="./images/Help_v2/screen_063.png"/></p>
                
                <b class="title">Résultat</b>
                <p>Votre commentaire apparait en-dessous de la discussion.</p>
                <p><img alt="Commentaire ajouté" src="./images/Help_v2/screen_064.png"/></p>       
                
                <b class="title">Remarque</b>
                <p>S'il y a déjà des commentaires en-dessous de la discussion, vous avez la possibilité de 
                    <b>répondre à la discussion initiale</b>, en cliquant sur "Répondre à la discussion" (A). Votre commentaire 
                    apparaitra alors <b>tout en bas</b> du flux des commentaires. Mais vous avez aussi la possibilité de <b>répondre 
                        à chacun des commentaires</b> en cliquant sur le bouton "Répondre" relatif au commentaire sur lequel vous
                        souhaitez intervenir. A ce moment-là, votre réponse <b>s'intercalera</b> à la suite du commentaire auquel vous 
                répondez.</p>
                <p><img alt="Commentaire ajouté" src="./images/Help_v2/screen_065.png"/></p>     
            </div>
        </li>
        
        <li>
            <a>11. VOTER POUR UN COMMENTAIRE</a>
            <div>
                <b class="title">Introduction</b>
                <p>Cette section décrit la marche à suivre pour voter pour un commentaire. </p>

                <b class="title">Marche à suivre</b>
                <p><b>1.</b> Sélectionnez le cours dans lequel se trouve la discussion. </p>
                <p><img alt="Sélectionner un cours" src="./images/Help_v2/screen_014.png"/></p> 
                <p><b>2.</b> Cliquez sur l’enregistrement ciblé par la discussion.</p>
                <p><img alt="Sélectionner un enregistrement" src="./images/Help_v2/screen_015.png"/></p> 
                <p><b>3.</b> Sélectionner la discussion contenant le commentaire.</p>
                <p><img alt="Sélectionner une discussion" src="./images/Help_v2/screen_061.png"/></p> 
                <p>En-dessous de chaque commentaire, vous avez la possibilité de voter pour le commentaire. 
                Chaque utilisateur peut soumettre un seul vote positif ou négatif par commentaire. Il n'y a pas moyen de 
                modifier son vote par la suite.</p>
                <p><img alt="Voter pour un commentaire" src="./images/Help_v2/screen_066.png"/></p>
                
                <p><b>5.</b> Les titulaires de l'album contenant la vidéo à laquelle se rapporte le commentaire ont la 
                    possibilité d'approuver un commentaire. Cela signifie qu'ils marquent leur accord avec le commentaire sans devoir
                    l'expliciter davantage. Pour cela, il suffit de cliquer sur la petite étoile présente à droite des votes. </p>
                <p><img alt="Répondre à la discussion" src="./images/Help_v2/screen_067.png"/></p>
                <p>Un ruban clairement identifiable apparait alors sur le commentaire, marquant l'approbation du professeur.</p>
                <p><img alt="Répondre à la discussion" src="./images/Help_v2/screen_068.png"/></p>
                
                <b class="title">Remarques</b>
                <p>Le commentaire qui obtient le plus de votes positifs apparait en tête des commentaires dans la rubrique "Meilleure réponse"</p>
                <p><img alt="Meilleure réponse" src="./images/Help_v2/screen_056.png"/></p>       
                <p>Lorsqu'un commentaire est modifié par son auteur (voir "<i>12. Modifier un commentaire</i>"), les votes et approbation des professeurs 
                sont réinitialisés.</p>
                   
            </div>
        </li>
        
        <li>
            <a>12.  MODIFIER UN COMMENTAIRE</a>
            <div>
                <b class="title">Introduction</b>
                <p>Cette section décrit la marche à suivre pour modifier un commentaire.</p>

                <b class="title">Marche à suivre</b>
                <p><b>1.</b> Rendez vous dans le contenu d'une discussion. En-dessous de chaque commentaire dont vous 
                    êtes l'auteur se trouve une icone représentant un crayon, à gauche du bouton "Répondre".</p>
                <p><img alt="Editer un commentaire" src="./images/Help_v2/screen_069.png"/></p> 
                <p>En cliquant sur cette icone, le formulaire de réponse se déploie, vous permettant de modifier votre commentaire. 
                Modifiez votre commentaire et soumettez le en cliquant sur "Soumettre".</p>
                <p><img alt="Editer un commentaire" src="./images/Help_v2/screen_070.png"/></p> 

                <b class="title">Remarques</b>
                <p>Lorsque vous éditez un commentaire, tous les votes et approbation des professeurs sont réinitialisés. Cela signifie que 
                si votre commentaire avait été élu meilleure réponse par l'ensemble des votants, elle ne le sera plus suite à votre modification.</p>
                <p>Il est aussi possible de modifier une discussion en respectant les mêmes étapes.</p>
            </div>
        </li>
        
        <li>
            <a>13.   AJOUTER UN SIGNET PERSONNEL</a>
            <div>
                <b class="title">Introduction</b>
                <p>Cette section décrit la marche à suivre pour ajouter un signet personnel à la liste des signets.</p>

                <b class="title">Définitions</b>
                <p><b>Signet</b> : Un signet est un <b>marquage temporel</b> au sein d’une vidéo. Typiquement, 
                    un signet est déterminé par un titre, une description, une série de mots clés et un niveau 
                    hiérarchique et pointe vers une <b>seconde précise</b> d’une vidéo. <br/> 
                    Un signet peut être “<b>personnel</b>”, c’est-à-dire qu’il est créé par l’utilisateur connecté 
                    et n’est visible que par ce même utilisateur, ou “<b>officiel</b>”, s’il est créé par un 
                    titulaire de cours. Dans ce cas, il est visible par tous les utilisateurs du cours. 
                </p>
                <p><b>Niveau hiérarchique d’un signet</b> : Le niveau d’un signet détermine comment il sera 
                    <b>représenté</b> dans l’onglet des signets. A la façon d’une table des matières, un signet 
                    sera plus ou moins <b>indenté</b> en fonction de son niveau hiérarchique. </p>

                <b class="title">Marche à suivre</b>
                <p><b>1.</b> Sélectionnez le cours dans lequel se trouve l’enregistrement à annoter.</p>
                <p><img alt="Sélectionner un cours" src="./images/Help_v2/screen_014.png"/></p> 
                <p><b>2.</b> Cliquez sur l’enregistrement sur lequel vous souhaitez placer un signet.</p>
                <p><img alt="Sélectionner un enregistrement" src="./images/Help_v2/screen_017.png"/></p> 
                <p><b>3.</b> Lors de la lecture de la vidéo, dans les <b>contrôles</b>, en-dessous de la vidéo, 
                    sélectionnez l’action “<b>Ajouter un signet privé</b>” (A) (ou utilisez le raccourci clavier [N]). 
                    La vidéo est mise en pause et un formulaire se déploie.<br/>
                    Le code temps est complété automatiquement (mais peut être modifié), les autres champs 
                    sont <b>facultatifs</b>.<br/>
                    Soumettez (B) le formulaire et le signet apparait instantanément dans la colonne de droite. 
                    Appuyez sur le bouton “Annuler” pour annuler la saisie du signet. 
                </p>
                <p><img alt="Ajouter un signet personnel" src="./images/Help_v2/screen_018.png"/></p> 

                <b class="title">Remarques</b>
                <p>Lorsque vous créez un signet sur un enregistrement dont plusieurs vidéos sont disponibles 
                    (<b>caméra</b> et <b>diaporama</b>), le signet s’applique sur la vidéo en cours de lecture. 
                    C’est-à-dire que si vous ajoutez un signet lorsque vous êtes en train de visionner la vidéo diaporama, 
                    celui-ci pointera toujours vers cette même vidéo.</p>
                <p>D’une manière générale, il ne peut y avoir deux signets à la même seconde pour une même vidéo. 
                    C’est-à-dire que si vous avez déjà un signet à la seconde X d’une vidéo et que vous ajoutez un 
                    autre signet à cette même seconde X, le premier signet sera <b>remplacé</b> par le nouveau. </p>

                <b class="title">Résultat</b>
                <p>Le signet personnel est ajouté à votre liste de signets.</p>
                <p><img alt="Signet personnel ajouté" src="./images/Help_v2/screen_019.png"/></p> 

            </div>
        </li>

        <li>
            <a>14. MODIFIER UN SIGNET PERSONNEL</a>
            <div>
                <b class="title">Introduction</b>
                <p>Cette section décrit  la marche à suivre pour modifier un signet personnel. </p>

                <b class="title">Définitions</b>
                <p><b>Modifier un signet personnel</b> : La modification d’un signet personnel consiste à 
                    attribuer de <b>nouvelles valeurs</b> de champs à ce signet. Seuls le <b>titre</b>, 
                    la <b>description</b>, les <b>mots clés</b> et le <b>niveau</b> du signet peuvent être modifiés, 
                    le code temps étant utilisé pour identifier le signet de manière unique. </p>

                <b class="title">Marche à suivre</b>
                <p>Voici la marche à suivre pour modifier un signet personnel :</p>
                <p><b>1.</b>  Sélectionnez le cours dans lequel se trouve l’enregistrement contenant le signet à modifier.</p>
                <p><img alt="Sélectionner un cours" src="./images/Help_v2/screen_014.png"/></p> 
                <p><b>2.</b>  Cliquez sur l’enregistrement dans lequel vous souhaitez modifier un signet.</p>
                <p><img alt="Sélectionner un enregistrement" src="./images/Help_v2/screen_017.png"/></p> 
                <p><b>3.</b> Dans l’onglet contenant vos signets personnels, à droite de la vidéo, <b>déployez</b> 
                    le signet à modifier en cliquant sur la petite <b>flèche</b> visible à droite du titre du signet. </p>
                <p><img alt="Déployer un signet" src="./images/Help_v2/screen_020.png"/></p> 
                <p><b>4.</b> Cliquez sur la deuxième petite icône, représentant un <b>crayon</b>. </p>
                <p><img alt="Editer un signet" src="./images/Help_v2/screen_021.png"/></p> 
                <p><b>5.</b> Les différents champs de votre signet sont maintenant <b>éditables</b>. 
                    Complétez ou modifiez les selon vos besoins. Seul le code temps ne peut être modifié 
                    car il est utilisé pour identifier les différents signets d’un enregistrement de manière unique.<br/> 
                    Validez les nouvelles valeurs en cliquant sur le bouton “<b>Soumettre</b>” ou 
                    annulez les en cliquant sur le bouton “Annuler”.
                </p>
                <p><img alt="Editer un signet" src="./images/Help_v2/screen_022.png"/></p> 

                <b class="title">Remarque</b>
                <p>Pensez à toujours utiliser les mêmes mots clés pour annoter les mêmes concepts dans vos différents signets. 
                    Cela facilitera vos recherches par la suite.</p>
            </div>
        </li>

        <li>
            <a>15. SUPPRIMER UN SIGNET PERSONNEL</a>
            <div>
                <b class="title">Introduction</b>
                <p>Cette section décrit  la marche à suivre pour supprimer un signet personnel. </p>

                <b class="title">Définition</b>
                <p><b>Supprimer un signet personnel</b> : La suppression d’un signet personnel consiste à 
                    retirer ce signet de la liste des signets personnels.</p>

                <b class="title">Marche à suivre</b>
                <p>Voici la marche à suivre pour supprimer un signet personnel :</p>
                <p><b>1.</b> Sélectionnez le cours dans lequel se trouve l’enregistrement contenant 
                    le signet à supprimer. </p>
                <p><img alt="Sélectionner un cours" src="./images/Help_v2/screen_014.png"/></p> 
                <p><b>2.</b>  Dans l’onglet de droite, contenant vos signets personnels, 
                    déployez le signet que vous souhaitez supprimer au moyen de la petite <b>flèche</b> 
                    située à droite du titre du signet.</p>
                <p><img alt="Sélectionner un signet" src="./images/Help_v2/screen_023.png"/></p> 
                <p><b>3.</b> Cliquez sur l’icône représentant une petite <b>croix</b>. </p>
                <p><img alt="Supprimer un signet" src="./images/Help_v2/screen_024.png"/></p> 
                <p><b>4.</b> Une boite de dialogue apparait, vous demandant de confirmer la suppression du signet.<br/>
                    Cliquez sur “<b>Supprimer</b>” pour effectivement supprimer le signet, sur 
                    “Annuler” dans le cas contraire.
                </p>
                <p><img alt="Message de suppression" src="./images/Help_v2/screen_025.png"/></p> 

                <b class="title">Deuxième approche</b>

                <p><b>1.</b> Une seconde approche vous permet de supprimer <b>plusieurs</b> signets en même temps. 
                    Pour cela, toujours au second niveau de l’application, déployez le <b>menu</b> qui se trouve au-dessus de 
                    l’onglet des signets personnels.  </p>
                <p><img alt="Menu des signets personnels" src="./images/Help_v2/screen_026.png"/></p> 
                <p><b>2.</b> Sélectionnez la troisième entrée du menu, “<b>Supprimer les signets</b>”. </p>
                <p><img alt="Suppression de signets personnels" src="./images/Help_v2/screen_027.png"/></p> 
                <p><b>3.</b> Une boite de dialogue apparait, affichant la liste de tous les signets personnels relatifs 
                    au cours sélectionné. <br/>
                    Sélectionnez le(s) signet(s) que vous souhaitez supprimer et validez votre action en cliquant 
                    sur le bouton “<b>Supprimer</b>” ou “Annuler” dans le cas contraire. 
                </p>
                <p><img alt="Suppression de signets personnels" src="./images/Help_v2/screen_028.png"/></p> 
                <p>Attention, il n’y a <b>pas de confirmation</b> après avoir cliqué sur le bouton “Supprimer”. 
                    Les signets sont directement retirés de l’onglet des signets personnels</p>

                <b class="title">Remarque</b>
                <p>Toutes ces opérations sont aussi valables au niveau trois de l’application, et s’appliquent 
                    alors sur les signets de l’enregistrement et non plus au niveau du cours sélectionné.</p>
            </div>
        </li>

        <li>
            <a>16. EXPORTER DES SIGNETS</a>
            <div>
                <b class="title">Introduction</b>
                <p>Cette section décrit  la marche à suivre pour exporter des signets.</p>

                <b class="title">Définition</b>
                <p>
                    <b>Exporter des signets</b> : L’exportation des signets consiste à les <b>enregistrer</b> 
                    sous forme de <b>fichier xml</b> sur son propre ordinateur. Cela en vue d’en conserver 
                    une copie, par exemple, ou de les partager avec d’autres utilisateurs de la solution “EZplayer”.<br/>
                    Aussi bien les signets personnels que les signets officiels peuvent être exportés sur un ordinateur.

                </p>

                <b class="title">Marche à suivre</b>
                <p>Voici la marche à suivre pour exporter des signets :</p>
                <p><b>1.</b>  Sélectionnez le cours dans lequel se trouvent les signets à exporter.</p>
                <p><img alt="Sélectionner un cours" src="./images/Help_v2/screen_014.png"/></p> 
                <p><b>2. Déployez</b> le menu qui se trouve au-dessus de l’onglet des signets personnels 
                    si vous souhaitez exporter vos signets personnels ou le menu des signets officiels 
                    si vous souhaitez récupérer les signets officiels et sélectionnez la première entrée du menu, 
                    “<b>Exporter les signets</b>”.</p>
                <p><img alt="Menu des signets" src="./images/Help_v2/screen_029.png"/></p> 
                <p><b>3.</b> Une boite de dialogue apparait, affichant la liste de tous les signets 
                    (personnels ou officiels selon l’option choisie) relatifs au cours sélectionné. <br/>
                    Sélectionnez le(s) signet(s) que vous souhaitez exporter et validez votre action en 
                    cliquant sur le bouton “Exporter” ou “Annuler” dans le cas contraire.</p>
                <p><img alt="Sélection des signets à exporter" src="./images/Help_v2/screen_030.png"/></p> 

                <b class="title">Résultat</b>
                <p>Un fichier xml contenant les signets sélectionnés est téléchargé sur votre ordinateur. </p>

                <b class="title">Remarque</b>
                <p>Toutes ces opérations sont aussi valables au niveau trois de l’application, et s’appliquent alors 
                    sur les signets de l’enregistrement et non plus du cours sélectionné.</p>
            </div>
        </li>

        <li>
            <a>17. IMPORTER DES SIGNETS </a>
            <div>
                <b class="title">Introduction</b>
                <p>Cette section décrit  la marche à suivre pour importer des signets parmi ses signets personnels.</p>

                <b class="title">Définition</b>
                <p><b>Importer des signets</b> : L’importation des signets consiste à alimenter l’onglet des 
                    signets personnels à partir d’un <b>fichier xml</b> se trouvant sur son ordinateur. <br/>
                    Ce fichier xml peut soit provenir d’une <b>exportation</b> (partagée par un autre utilisateur 
                    ou issue des signets officiels), soit être <b>créé</b> manuellement par un utilisateur 
                    si les contraintes de structure sont respectées.</p>

                <b class="title">Marche à suivre</b>
                <p>Voici la marche à suivre pour importer des signets :</p>
                <p><b>1.</b>  Sélectionnez le cours dans lequel vous souhaitez importer des signets.</p>
                <p><img alt="Sélectionner un cours" src="./images/Help_v2/screen_014.png"/></p> 
                <p><b>2.</b> Déployez le <b>menu</b> qui se trouve au-dessus de l’onglet des signets personnels 
                    et sélectionnez la seconde entrée du menu, “<b>Importer les signets</b>”. </p>
                <p><img alt="Importer des signets personnels" src="./images/Help_v2/screen_031.png"/></p> 
                <p><b>3.</b> Une boite de dialogue apparait, vous offrant la possibilité de <b>soumettre un fichier</b> 
                    depuis votre ordinateur. </p>
                <p><b style="color: #7C0505">Attention, ce fichier doit respecter plusieurs contraintes</b> : 
                    il doit être de type <b>xml</b>, ne peut être plus volumineux que <b>2Mo</b> et doit respecter 
                    une <b>structure</b>  spécifique (référez vous à un fichier exporté depuis l’interface 
                    “EZplayer” – voir “<i>16. Exporter des signets</i>” - pour observer la structure imposée) 
                    pour être accepté par le système.</p>
                <p><img alt="Importer des signets personnels" src="./images/Help_v2/screen_032.png"/></p> 
                <p><b>5.</b> La boite de dialogue est rechargée et affiche maintenant la liste des <b>signets</b>, 
                    issus du fichier xml soumis, <b>disponibles pour le cours sélectionné</b>. 
                    Seuls les signets correspondants au cours sélectionné sont affichés. Tous les signets n’appartenant pas 
                    au cours sélectionné sont ignorés par le système. </p>
                <p>Les signets apparaissant en <b>noir</b> sont ceux qui peuvent être importés sans “collision”. 
                    Si un signet apparait en <b>rouge</b>, cela signifie qu’il entre en <b>conflit</b> avec vos 
                    signets personnels déjà existants. En effet, un signet est caractérisé par un code temps et il 
                    ne peut y avoir qu’un seul signet pour chaque seconde d’une vidéo. Si vous sélectionnez un 
                    signet apparaissant en rouge dans la liste des signets importés, celui-ci <b>remplacera</b> 
                    automatiquement le signet que vous aviez précédemment dans l’onglet des signets personnels. 
                    En <b>survolant</b> un signet apparaissant en rouge avec la souris, vous avez un aperçu de 
                    votre signet personnel qui sera remplacé (et donc supprimé). </p>
                <p><img alt="Importer des signets personnels" src="./images/Help_v2/screen_033.png"/></p> 
                <p>5. Sélectionnez le(s) signet(s) que vous souhaitez importer et validez votre action en 
                    cliquant sur le bouton “<b>Importer</b>” ou “Annuler” dans le cas contraire. </p>
                <p><img alt="Importer des signets personnels" src="./images/Help_v2/screen_034.png"/></p> 

                <b class="title">Résultat</b>
                <p>L’onglet contenant vos signets personnels est mis à jour et affiche les signets qui viennent d’être 
                    importés.</p>

                <b class="title">Remarque</b>
                <p>Toutes ces opérations sont aussi valables au niveau trois de l’application, et s’appliquent alors 
                    sur les signets de l’enregistrement et non plus du cours sélectionné. Cela signifie que seuls les 
                    signets correspondants à l’enregistrement sélectionné pourront être importés depuis le fichier soumis 
                    au système.</p>

            </div>
        </li>

        <li>
            <a>18. PARTAGER DES SIGNETS </a>
            <div>
                <b class="title">Introduction</b>
                <p>Cette section décrit  la marche à suivre pour partager des signets avec d’autres utilisateurs.</p>

                <b class="title">Définition</b>
                <p><b>Partager des signets</b> : Le partage de signets peut être envisagé selon deux modes distincts : 
                    le <b>partage d’un moment précis</b> de la vidéo, sans partage des informations annexes 
                    (titre, description, mots clés, …) et le partage d’une <b>liste de signets</b> au moyen de 
                    l’option d’import des signets.</p>

                <b class="title">Marche à suivre</b>
                <p>Voici la marche à suivre pour importer des signets :</p>

                <b class="title">Première approche</b>
                <p><b>1.</b>  Sélectionnez le cours contenant l’enregistrement dont vous souhaitez 
                    partager un moment précis.</p>
                <p><img alt="Sélectionner un cours" src="./images/Help_v2/screen_014.png"/></p> 
                <p><b>2.</b> Sélectionnez l’enregistrement dont vous souhaitez partager un moment précis.</p>
                <p><img alt="Sélectionner un enregistrement" src="./images/Help_v2/screen_035.png"/></p> 
                <p><b>3.</b> Lorsque vous visionnez la vidéo, cliquez sur la dernière icône à droite, dans la 
                    barre de controle de la vidéo (elle représente une <b>chaine</b>).</p>
                <p><img alt="Lecture de la vidéo" src="./images/Help_v2/screen_036.png"/></p> 
                <p><b>4.</b> Une boite de dialogue apparait. Celle-ci contient un <b>lien direct</b> 
                    vers un l’instant précis de la vidéo. Si l’enregistrement contient différentes versions 
                    de la vidéo (vidéo caméra et vidéo diaporama), c’est la vidéo <b>en cours</b> de visualisation 
                    qui sera pointée par le lien.</p>
                <p>Sélectionnez le lien et copiez le dans votre presse-papier avec la combinaison de touches “<b>CTRL + V</b>” 
                    ou cliquez sur le bouton “<b>Copier dans le presse-papier</b>”.</p>
                <p><img alt="Partage d'un moment précis" src="./images/Help_v2/screen_037.png"/></p> 
                <p><b>6.</b> Partagez ce lien avec un autre utilisateur pour qu’il le visionne dans son navigateur web. </p>
                <p>Attention, <b>l’utilisateur</b> qui reçoit le lien <b>doit posséder le cours</b> contenant 
                    l’enregistrement parmi ses cours favoris (page d’accueil) pour pouvoir accéder à la vidéo. 
                    Si ce n’est pas le cas, il recevra un message d’erreur l’informant qu’il ne dispose pas de 
                    permissions suffisantes pour accéder à la vidéo. </p>

                <b class="title">Deuxième approche</b>
                <p><b>1.</b> Exportez les signets que vous souhaitez partager avec un autre utilisateur en vous 
                    référant à la rubrique “10. Exporter des signets” de ce tutoriel. </p>
                <p><b>2.</b> Envoyez le fichier xml ainsi obtenu à l’utilisateur avec qui vous souhaitez 
                    partager vos signets.</p>
                <p><b>3.</b> L’utilisateur qui reçoit le fichier xml contenant les signets peut les sauver dans son 
                    cours en se connectant à la solution “EZplayer” et  en les important comme expliqué dans la section 
                    “<i>17. Importer des signets</i>” de ce tutoriel. </p>                
            </div>
        </li>

        <li>
            <a>19. RECHERCHER PARMI LES SIGNETS ET DISCUSSIONS</a>
            <div>
                <b class="title">Introduction</b>
                <p>Cette section décrit  la marche à suivre pour faire une recherche parmi les signets. </p>

                <b class="title">Définition</b>
                <p><b>Rechercher parmi les signets</b> : Une recherche permettra de retrouver 
                    facilement des signets et discussions parmi tous les cours disponibles selon des critères plus ou moins affinés.</p>

                <b class="title">Marche à suivre</b>
                <p>Voici la marche à suivre pour rechercher parmi les signets et discussions :</p>
                <p><b>1.</b>  Sur chacune des pages du site se trouve un <b>champ de saisie</b> pour effectuer une recherche.</p>
                <p>Par défaut, la recherche de base est effectuée sur <b>tous les signets et discussions</b> – <b>officiels et personnels</b>
                    - de tous les cours favoris de l’utilisateur.  </p>
                <p><img alt="Champ de recherche" src="./images/Help_v2/screen_038.png"/></p> 
                <p><b>2.</b>  Il existe aussi une fonction de <b>recherche avancée</b>. Celle-ci est disponible 
                    en cliquant sur la petite <b>flèche</b> présente dans le champ de recherche de base.</p>
                <p><img alt="Recherche avancée" src="./images/Help_v2/screen_039.png"/></p>
                <p><b>3.</b> La <b>recherche avancée</b> permet d’affiner ses critères de recherche. Elle permet de 
                    choisir le <b>niveau de recherche</b> par l’option “<b>Chercher dans</b>:” (A). Si vous sélectionnez 
                    “<b>Tout</b>”, la recherche s’effectuera parmi tous les signets de tous les cours favoris. 
                    En sélectionnant “<b>Cours</b>”, vous aurez la possibilité de sélectionner les différents cours 
                    sur lesquels vous souhaitez appliquer la recherche. Enfin, l’option “<b>Courant</b>” dépendera 
                    du niveau dans lequel vous vous trouvez ; sur la page d’accueil, elle équivaut à l’option “Tout”, 
                    au niveau deux la recherche portera uniquement sur l’album sélectionné et au niveau trois, 
                    elle ne s’appliquera que sur l’enregistrement sélectionné. </p>
                <p>L’option “<b>Chercher parmi</b>” (B) vous permet, quant à elle, de définir les domaines de recherche 
                    ainsi que les champs relatifs à chacun d'eux. <b>Les domaines de recherche</b> définissent la portée 
                    de la recherche et correspondent aux signets officiels, personnels et/ou aux discussions. 
                    En désélectionnant un des domaines de recherche, vous 
                    enlevez la possibilité qu'un résultat soit trouvé parmi ce domaine (signet officiel, personnel ou discussion). 
                    Les <b>champs</b> relatifs à chacun d'eux vous permettront 
                    d'affiner encore la recherche pour ne la restraindre qu'aux champs ciblés. <br/></p>
                <p>En cochant ou décochant les options “<b>Titre</b>”, “<b>Description</b>” 
                    et “<b>Mots clés</b>” (I), vous permettez ou non la recherche sur ces mêmes champs dans les signets.<br/>
                    En cochant ou décochant les options "<b>Titre</b>" et "<b>Commentaire</b>" (II), vous permettez ou non la recherche 
                    sur ces mêmes champs dans les discussions.</p>
                <p>Enfin, il est possible de faire une recherche sur le <b>niveau des signets</b>. 
                    Le niveau 0 indique que la recherche porte sur tous les signets, les niveaux 1, 2 et 3 restreindront, 
                    par contre, la recherche aux seuls signets du niveau correspondant. </p>
                <p>Pour valider votre recherche, cliquez sur la petite loupe (C), à côté de la zone de saisie.  </p>
                <p><img alt="Recherche avancée" src="./images/Help_v2/screen_040.png"/></p>
                <p><b>5.</b> Une boite de dialogue apparait, contenant la liste de tous les résultats correspondants aux 
                    critères de recherche soumis. Les mots recherchés apparaissent en <b>jaune</b>.<br/>
                    En cliquant sur le titre d'une catégorie ('Discussions', 'Signets personnels', 'Signets officiels'), 
                    vous en affichez ou masquez les résultats.</p>
                <p><img alt="Recherche avancée" src="./images/Help_v2/screen_041.png"/></p>
                <p>L’outil de recherche permet l’utilisation des <b>guillemets doubles</b> (“) pour différencier une
                    <b>expression</b> d’un mot simple. En effet, par défaut, la recherche retournera tous les résultats 
                    contenant un des mots à trouver, quel que soit leur ordre parmis les champs des signets. 
                    En utilisant des guillemets doubles, on s’assure que seules les expressions seront recherchées 
                    (on peut par exemple faire une recherche sur deux mots séparés par un espace). </p>
                <p>L’outil de recherche tient compte des <b>accents</b> mais ne tient pas compte de la <b>casse</b>, 
                    c’est-à-dire que “Résultat” est différent de “Resultat” qui est lui-même équivalent à “ReSuLtAt”.</p>

                <b class="title">Remarques</b>
                <p>Tous les mots clés des signets renvoient eux-mêmes vers le résultat de la recherche correspondante.</p>
            </div>
        </li>

        <li>
            <a>20. TÉLÉCHARGER UNE VIDÉO</a>
            <div>
                <b class="title">Introduction</b>
                <p>Cette section décrit  la marche à suivre pour télécharger une vidéo. </p>
                
                <b class="title">Remarque</b>
                <p style='color: #7C0505'>Toutes les vidéos ne sont pas téléchargeables. Seul votre professeur a la possibilité de définir si la vidéo peut être 
                téléchargée ou non par les utilisateurs d'EZplayer. Ainsi, les boutons de téléchargement ne seront pas forcément disponibles
                sur toutes les vidéos auxquelles vous aurez accès.</p>

                <b class="title">Marche à suivre</b>
                <p>Voici la marche à suivre pour télécharger une vidéo :</p>
                <p><b>1.</b>  Sélectionnez le cours dans lequel se trouve l’enregistrement à télécharger. </p>
                <p><img alt="Sélectionner un cours" src="./images/Help_v2/screen_014.png"/></p> 
                <p><b>2.</b>  Cliquez sur l’enregistrement que vous souhaitez télécharger.</p>
                <p><img alt="Sélectionner un enregistrement" src="./images/Help_v2/screen_035.png"/></p> 
                <p><b>3.</b> En-dessous de la vidéo, à droite du titre, se trouve un bouton de téléchargement.
                    Cliquez sur “<b>Télécharger le diaporama</b>” ou “<b>Télécharger la vidéo</b>” selon 
                    leur présence respective. </p>
                <p><img alt="Télécharger un enregistrement" src="./images/Help_v2/screen_042.png"/></p> 
                <p><b>4.</b> Une boite de dialogue apparait, vous offrant la possibilité de télécharger
                    la vidéo en haute ou basse résolution selon votre choix.  </p>
                <p><img alt="Télécharger un enregistrement" src="./images/Help_v2/screen_043.png"/></p> 
            </div>
        </li>  
        
        <li>
            <a>21. GÉRER SES PRÉFÉRENCES</a>
            <div>
                <b class="title">Introduction</b>
                <p>Cette section décrit comment gérer les préférences relatives à EZplayer. </p>

                <b class="title">Définition</b>
                <p><b>Préférences d'EZplayer</b> : Les préférences vont vous permettre de définir certaines fonctionnalités 
                    que vous souhaitez activer ou non lors de votre navigation dans EZplayer. Ces préférences sont les suivantes: 
                    <br/> - <b>Afficher la notification de nouvelles vidéos</b>: Cela agit sur l'affichage ou non du compteur d'enregistrements
                    non visionnés se trouvant sur la page d'accueil, en vis-à-vis de chaque album favori.
                    <br/> - <b>Afficher la notification des discussions durant les vidéos</b>: Cela définit si une notification de 
                    discussion doit apparaitre lors de la lecture d'une vidéo.
                    <br> - <b>Afficher les discussions</b>: Cela détermine si les discussions doivent être affichées ou non dans EZplayer.
                </p>               
                
                <b class="title">Marche à suivre</b>
                <p><b>1.</b>  Durant votre navigation au sein d'EZplayer, cliquez sur l'onglet des préférences se trouvant dans l'entête du site. </p>
                <p><img alt="Gestion des préférences" src="./images/Help_v2/screen_071.png"/></p> 
                <p><b>2.</b> L'onglet se déploie vous permettant d'activer ou désactiver certaines fonctionnalités. </p>
                <p><img alt="Gestion des préférences" src="./images/Help_v2/screen_072.png"/></p> 
                <p><b>Afficher la notification de nouvelles vidéos</b> (I) modifie l'affichage du compteur de vue. Cette option est désactivée par défaut.</p>                
                <p><img alt="Affichage des nouvelles vidéos" src="./images/Help_v2/screen_073.png"/></p> 
                <p><b>Afficher la notification des discussions durant les vidéos</b> (II) modifie l'affichage des notifications de discussion durant la lecture d'une vidéo. 
                    Cette option est activée par défaut.</p>                
                <p><img alt="Affichage des notifications de discussions" src="./images/Help_v2/screen_059.png"/></p> 
                <p><b>Afficher les discussions</b> (III) affiche ou non l'ensemble des discussions. Cette option est activée par défaut.</p>               
                  
            </div>
        </li>
        <li>
            <a style="color:red">22. TRUCS ET ASTUCES</a>
            <div>
                <b class="title">Introduction</b>
                <p>Cette section décrit certaines pratiques pour enrichir l’utilisation d’EZplayer. </p>

                <b class="title">Astuces</b>
                <p>EZplayer dispose d'une série de raccourcis claviers qui peuvent être affichés avec le 
                    raccourci clavier [R]:</p>
                <p>
                    <b>[ESPACE]</b> >> Play / Pause <br/>
                    <b>[Flècge gauche][Flèche droite]</b> >> Retour / Avance (15 secondes) <br/>
                    <b>[+][-]</b> >> Vitesse de lecture de la vidéo <br/>
                    <b>[Flèche haut][Flèche Bas]</b> >> Augmenter / Diminuer le volume audio <br/>
                    <b>[M]</b> >> Muet <br/>
                    <b>[B]</b> >> Afficher l'onglet des signets (en mode plein écran uniquement) <br/>
                    <b>[L]</b> >> Partager un lien vers ce moment précis de la vidéo <br/>
                    <b>[F]</b> >> Passer en mode plein écran <br/>
                    <b>[N]</b> >> Ajouter un nouveau signet personnel <br/>
                    <b>[O]</b> >> Ajouter un nouveau signet officiel <br/>
                    <b>[D]</b> >> Créer une nouvelle discussion <br/>
                    <b>[S]</b> >> Basculer de la vidéo caméra au diaporama et inversément <br/>
                    <b>[ESC]</b> >> Quitter le mode plein écran / Fermer les fenêtre pop-up / Libérer le 
                    focus dans les champs de saisie<br/>
                </p>
                <p>
                    Il est possible d’intégrer des <b>liens</b> (url) dans la description des signets et dans les discussions. 
                    Certains liens sont automatiquement reconnus. C’est le cas des liens commençant par 
                    “<b>http://</b>”, “<b>https://</b>”, “<b>www</b>” et “<b>mailto</b>:”.<br/>
                    Si vous souhaitez définir vous-même un lien, il suffit d’insérer le lien entre 
                    <b>deux astérisques</b>. Voici un exemple concret: <b>**google.com**</b><br/>
                    Si vous voulez donner un <b>alias</b> (renommer) un lien dans votre description, 
                    utilisez la syntaxe suivante: <b>**lien alias**</b>. <br/>
                    Par exemple: **https://www.google.be/search?q=google&oq=go&aqs=chrome.0.0j69i57j69i60l4.2193j0&sourceid=chrome&ie=UTF-8#q=exemple Voici un exemple**
                </p> 
                <p>Si vous consultez le site en mode anonyme et que vous y ajoutez des albums en favoris, 
                    connectez-vous avec votre identifiant pour que les albums s’ajoutent automatiquement 
                    à votre compte.</p>
                <p>Lorsque vous partagez un lien vers une vidéo avec un autre utilisateur qui n’a pas accès 
                    à l’album contenant la vidéo, celui-ci aura un message d’erreur car il ne dispose pas des 
                    permissions suffisantes.<br/>
                    Pour éviter ce désagrément, il faut impérativement que cet utilisateur ait consulté, 
                    au préalable, l’album contenant la vidéo.
                </p>
            </div>
        </li>
    </ul>
</div><!-- end #help-content -->
