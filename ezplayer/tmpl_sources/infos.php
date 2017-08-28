<!DOCTYPE html>

<!--
 This page is meant to contain a FAQ/tutorial on how to use the service
-->

<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

        <title>®ezplayer_page_title®</title>
        <?php include_once template_getpath('head_css_js.php'); ?>

        <script>
            $(document).ready(function () {
                $('#topics li a').click(function () {
                    $(this).siblings().toggle(200);
                });
            });
        </script>

    </head>
    <body>
        <div class="container">
            <?php include_once template_getpath('div_help_header.php'); ?>
            <div id="global">
                <div id="div_center">
                    <div id="infos-content">
                        <div style="text-align: center; margin-bottom: 30px;">
                        <video width="720" controls src="./videos/discussions.mp4">
                            Les discussions dans EZplayer</video></div>
                        <br/>
                        <p>
                            Pour le deuxième quadrimestre, nous avons mis tous nos efforts dans l'élaboration d'un module de <b>discussions</b>. Cela pour plusieurs raisons: vous offrir un espace 
                            supplémentaire pour <b>collaborer</b> entre vous autour des enregistrements et vous aider ainsi à compléter vos notes, les structurer et répondre ensemble 
                            à des problématiques soulevées lors du cours.
                            <br/>C'est ce que l'on vous propose de découvrir un peu plus en détail dans la suite de cette page d'information.
                        </p>
                        <b class="title">En quoi consistent les discussions ? </b>
                        <p>Les discussions sont un système de commentaires collaboratifs contextuels. On parle de <b>commentaires</b> 
                            car ils s'affichent de manière chronologique en-dessous de la vidéo. Les commentaires sont dits <b>collaboratifs</b> car 
                            chaque utilisateur authentifié sur EZplayer a la possibilité de commenter la vidéo en créant une discussion ou en intervenant dans une discussion existante. 
                            En ce sens, la collaboration naît de l'échange questions-réponses entre les utilisateurs. Enfin, on parle de commentaires <b>contextuels</b> car 
                            chaque discussion est liée à un moment précis de la vidéo. Cela permet à tout qui crée la discussion de la relier à son contexte dans la vidéo, sans 
                            nécessairement devoir l'expliciter à l'écrit. En outre, une notification de discussion apparaît dans la vidéo au moment de la lecture du passage
                            concerné par la discussion.
                        </p>
                        <b class='title'>Comment fonctionnent-elles ?</b>
                        <p>
                            Lors de la lecture de la vidéo, quand vous souhaitez créer la discussion, 
                            cliquez sur l'icône <img style="border: none;" width="22px" src="./images/Generale/add-thread-a.png"/>, dans les contrôles situés en-dessous de la vidéo. 
                            Dans le formulaire qui se déploie, complétez les zones de texte dédiés au titre et à la description.
                            Le champ de saisie correspondant au code temps de la vidéo est complété automatiquement mais peut être édité au besoin. 
                            Lorsque tous les champs sont complétés, soumettez le formulaire en cliquant sur le bouton "Publier" pour voir apparaître votre discussion en-dessous
                            de la vidéo.
                            A partir de ce moment, les autres utilisateurs qui parcourent la vidéo verront qu'une discussion se rapporte à ce passage et pourront, eux aussi, y 
                            joindre leur commentaire. 
                        </p>

                        <b class='title'>A propos des autres fonctionnalités</b>
                        <p>
                            Pour améliorer les discussions et les rendre le plus efficace possible, nous avons mis en place un système de vote sur les 
                            commentaires. Ainsi, chaque utilisateur peut soumettre un <b>vote positif</b> ou <b>négatif</b> sur chacun des commentaires. De cette manière, le commentaire
                            comptabilisant le plus de votes positifs sera placé en tête des commentaires, lui offrant une meilleure visibilité.<br/>
                            En outre, les titulaires du cours ont la possibilité d'<b>approuver</b> certains commentaires, pour leur donner un caractère plus officiel. Cela donne une 
                            indication sur la fiabilité du commentaire. Le commentaire arbore alors un ruban distinctif qui présente la mention "Réponse approuvée par le professeur".                         
                        </p>
                        <b class='title'>Définir la visibilité des discussions</b>
                        <p>
                            En tant qu'étudiant, lorsque vous créez une discussion, vous avez la possibilité de définir sa <b>visibilité</b>.
                            Vous pouvez rendre votre discussion disponible pour <b>tout le monde</b> (y compris le titulaire du cours) ou en <b>accès limité</b> (aucun professeur ne
                            pourra voir votre discussion). Pour cela, il suffit de sélectionner la visibilité souhaitée lors de la soumission du formulaire de création des discussions.
                            <br/>La visibilité s'applique à l'ensemble de la discussion et ne s'applique donc pas au cas par cas sur chacun des commentaires.
                        </p>
                        <b class='title'>Pour une explication plus détaillée et visuelle, reportez-vous à la documentation ci-dessous</b>
                        <ul id="topics">
                            <li>
                                <a>1.   CRÉER UNE DISCUSSION</a>
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
                                <a>2.   PARCOURIR ET COMPRENDRE LES DISCUSSIONS</a>
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
                                                <a>3. PARTICIPER A UNE DISCUSSION</a>
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
                                                <a>4. VOTER POUR UN COMMENTAIRE</a>
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
                                                <a>5.  MODIFIER UN COMMENTAIRE</a>
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
                                            </ul>
                                            </div>

                                            </div><!-- div_center END -->
                                            </div><!-- global -->
                                            <!-- FOOTER - INFOS COPYRIGHT -->
                                            <?php include_once template_getpath('div_main_footer.php'); ?>
                                            <!-- FOOTER - INFOS COPYRIGHT [FIN] -->
                                            </div><!-- Container fin -->
                                            </body>
                                            </html>
