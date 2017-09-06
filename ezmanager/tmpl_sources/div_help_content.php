<?php 
/*
* EZCAST EZmanager
*
* Copyright (C) 2016 Université libre de Bruxelles
*
* Written by Michel Jansens <mjansens@ulb.ac.be>
* 		    Arnaud Wijns <awijns@ulb.ac.be>
*                   Antoine Dewilde
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

<div id="Bloc-ensemble">

     <div class="Helpnew">


      <h1>Aide - Utilisation &quot;EZmanager&quot;</h1>
      <p>&nbsp;</p>

      <p>Ce tutoriel a pour objectif de vous permettre d’utiliser les fonctionnalités essentielles de l’interface de gestion des podcasts, de pouvoir publier vos productions ainsi que de répondre à vos questions. Si vous ne trouvez pas la réponse, contactez : <a href="mailto:<?php global $mailto_alert; echo $mailto_alert; ?>"><?php echo $mailto_alert; ?></a></p>
      <p><br />
        Ce tutoriel existe au format .pdf ; vous pouvez le télécharger dans ¤tuto¤.</p>
<p>&nbsp;</p>

      <h1><a href="javascript:visibilite('Help0');" >Présentation & concepts</a></h1>

 <div id="Help0" style="display:none;">
   <p>«&nbsp;<strong>EZmanager&nbsp;</strong>» est une interface vous permettant de gérer vos albums-cours ainsi que de soumettre manuellement des podcasts réalisés hors auditoire. Celle-ci assure également la gestion du «&nbsp;workflow vidéo&nbsp;» – ajout des «&nbsp;intro&nbsp;», «&nbsp;titre&nbsp;» et «&nbsp;outro&nbsp;» –, le traitement/encodage des médias soumis et la diffusion des podcasts.</p>
   <p>&nbsp;</p>
   <p>L’interface fonctionne par <strong>album</strong>. Chaque album est associé à un mnémonique de cours (exemple : BIME-I-101) et regroupe plusieurs podcasts. Lorsque vous <strong>créez un album</strong>, deux albums sont automatiqument générés : <strong>l’un public, l’autre privé</strong>. Le premier sera l’album de diffusion auprès de vos étudiants et du public extérieur ; le second vous permettra de tester certains enregistrements, de recueillir vos enregistrements modérés avant de les publier dans l’album public, etc.</p>
   <p>&nbsp;</p>
   <p>C’est dans ces albums que vous allez publier des <strong>enregistrements</strong>, c’est-à-dire du contenu multimédia (vidéo, diaporama commenté, etc.), créé sur votre ordinateur et que vous allez héberger dans le système de gestion des podcasts. Ces enregistrements (vidéo et/ou diaporama) peuvent également être directement réalisés dans un auditoire équipé. Dans ce cas, les enregistrements sont automatiquement soumis au système de gestion des podcasts.</p>
   <p>     A tout moment, vous pourrez effectuer des <strong>modifications sur chaque enregistrement</strong>. Celles-ci sont de trois types : la suppression de l’enregistrement, la modification de certaines informations (titre et description) et le déplacement de l’enregistrement dans un autre album. </p>
   <p>&nbsp;</p>
   <p>Afin de mettre à disposition des étudiants le contenu d’un album, vous allez publier le lien du <strong>flux RSS</strong> de votre album public (proposé un deux résolutions). Celui-ci permettra aux étudiants de recevoir automatiquement les nouveaux enregistrements lorsque ceux-ci seront disponibles.</p>
   <p>Vous pouvez également partager un enregistrement précis : dans cette optique, vous proposerez aux étudiants de <strong>télécharger la vidéo</strong> ou de la voir directement sur un site web (grâce à un <strong>embed code</strong>).</p>
      <p>&nbsp;</p>
 </div>
<h1><a href="javascript:visibilite('Help1');" >Créer un album</a></h1>

 <div id="Help1" style="display:none;">
       <h2>Introduction</h2>
 <p>Cette section décrit la marche à suivre pour créer un album dans l’interface “EZmanager”.</p>
<h2>Définitions</h2>
<p> <strong>Album :</strong> un album est un regroupement de plusieurs enregistrements. Cet album correspond à un mnémonique de cours (exemple : BIME-I-101)</p>
<p><strong>Album public / Album privé : </strong>lorsque vous créez un album lié à un cours, deux albums sont générés : l’un public, l’autre privé. Le premier sera l’album de diffusion auprès de vos étudiants et du public extérieur ; le second vous permettra de tester certains enregistrements, de recueillir vos enregistrements modérés avant de les publier dans l’album public, etc. </p>
<h2>Marche à suivre</h2>
<p><strong>1.</strong> Cliquez sur le bouton “<strong>Créer un album</strong>” en haut à gauche de l’interface.</p>

<div class="pic">
<img src="./images/help/create_album_1.png"/>
</div>


<p><strong>2.</strong> Une boîte de dialogue s’ouvre. Celle-ci vous propose une liste d’albums correspondant aux cours liés à votre NetId. Pour créer un album, cliquez sur le cours correspondant à l’album que vous désirez créer.</p>
<div>
<img src="./images/help/create_album_2.png" /></div>

<p><strong>3.</strong> Après votre choix, une boîte de dialogue vous confirme la création effective de votre album.</p>
<div><img src="./images/help/create_album_3.png"/></div>
<h2>Résultat</h2>
<p>Votre album apparaît dans la colonne gauche de l’interface et se divise en deux abums : l’un privé, l’autre public. </p>

<div class="pic">
<img src="./images/help/create_album_4.png" />
</div>

</div>
<h1><a href="javascript:visibilite('Help2');" >Editer les propriétés d'un album</a></h1>

 <div id="Help2" style="display:none;">
       <h2>Introduction</h2>
 <p>Cette section décrit la marche à suivre pour éditer les propriétés d’un album dans l’interface “EZmanager”.</p>
<h2>Définitions</h2>
<p><strong>Propriétés éditables d’un album : </strong>Les propriétés éditables d’un album ne sont autres que le générique et 
    le titrage. Le générique est une courte séquence animée ajoutée en début de vidéo. Le titrage est un condensé d’information 
    affiché à la suite du générique. Le titrage par défaut reprend le mnémonique de cours, le titre de la vidéo, 
    le nom de l’auteur ainsi que la date de publication.<br/>
    Par cette manipulation, vous pourrez donc choisir d’intégrer ou non un générique et un titrage aux vidéos publiées dans 
    l’album sélectionné. Les modifications ne s’appliquent qu’à l’album sélectionné, c’est-à-dire que si vous choisissez 
    l’album public, seul celui-là sera modifié, l’album privé restant inchangé.<br/>
    <span class="red">Ne modifiez ces propriétés que si l’usage des vidéos liées à l’album le justifie.</span></p>
<p> <strong>Album :</strong> un album est un regroupement de plusieurs enregistrements. Cet album correspond à un mnémonique de cours (exemple : BIME-I-101)</p>
<p><strong>Album public / Album privé : </strong>lorsque vous créez un album lié à un cours, deux albums sont générés : l’un public, l’autre privé. Le premier sera l’album de diffusion auprès de vos étudiants et du public extérieur ; le second vous permettra de tester certains enregistrements, de recueillir vos enregistrements modérés avant de les publier dans l’album public, etc. </p>
<h2>Marche à suivre</h2>
<p><strong>1.</strong> Sélectionnez l’album à éditer. Les modifications ne s’appliquent qu’à l’album – 
    public <strong>ou</strong> privé – sélectionné. </p>

<div class="pic">
<img src="./images/help/properties_1.png" />
</div>


<p><strong>2.</strong> Cliquez sur le <strong>petit engrenage</strong> qui s’affiche à l’extrême droite du nom de l’album.</p>
<div class="pic">
<img src="./images/help/properties_2.png" /></div>

<p><strong>3.</strong> Un menu déroulant apparaît. Sélectionnez l’entrée “<strong>Propriétés de l’album</strong>”.</p>
<div class="pic"><img src="./images/help/properties_3.png"/></div>
<p><strong>4.</strong>Une boite de dialogue s’ouvre. Sélectionnez les propriétés que vous souhaitez appliquer à l’album au moyen 
    des <strong>listes déroulantes</strong> [1] et validez les modifications en cliquant sur 
    le bouton “<strong>Envoyer changements</strong>” [2].</p> 
<div ><img src="./images/help/properties_4.png"/></div>
<p><strong>5.</strong> Après votre choix, une boîte de dialogue vous confirme la mise à jour effective des propriétés de l’album.</p>
<div ><img src="./images/help/properties_5.png"/></div>
<h2>Résultat</h2>
<p>Les propriétés de l’album sont éditées. Tous les enregistrements soumis manuellement ou filmés en auditoire à destination 
    de cet album utiliseront les nouvelles propriétés de l’album lors de leur processus de traitement.</p>
</div>



<h1><a href="javascript:visibilite('Help3');" >Supprimer un album</a></h1>
<div id="Help3" style="display:none;">

       <h2>Introduction</h2>
 <p>Cette section décrit la marche à suivre pour supprimer un album dans l’interface “EZmanager”.</p>
<h2>Définitions</h2>
<p><strong>Supprimer un album : </strong>lorsque vous supprimez un album, cette action supprimera l’album public 
    <strong>et</strong> l’album privé ainsi que tous les podcasts associés à ces deux albums. <br/>
    Cela signifie que si l’album est partagé avec d’autres utilisateurs titulaires, ceux-ci perdront aussi l’accès à cet 
    album ainsi qu’à son contenu</p>
<p> <strong>Album :</strong> un album est un regroupement de plusieurs enregistrements. Cet album correspond à un mnémonique de cours (exemple : BIME-I-101)</p>
<p><strong>Album public / Album privé : </strong>lorsque vous créez un album lié à un cours, deux albums sont générés : l’un public, l’autre privé. Le premier sera l’album de diffusion auprès de vos étudiants et du public extérieur ; le second vous permettra de tester certains enregistrements, de recueillir vos enregistrements modérés avant de les publier dans l’album public, etc.</p>
<h2>Attention</h2>
<p>La suppression d’un album est <strong>irréversible</strong>. En ce sens, une fois l’opération effectuée, il est impossible de revenir en arrière et tous vos podcasts se trouvant dans l’album public et dans l’album privé liés à au mnémonique seront effacés.</p>
<h2>Marche à suivre</h2>
<p><strong>1. </strong>Sélectionner l’album public ou l’album privé du cours à supprimer.</p>
<div class="pic"><img src="./images/help/delete_album_1.png"/></div>
<p>&nbsp;</p>
<p><strong>2.</strong> Cliquez sur le <strong>petit engrenage</strong> qui s’affiche à l’extrême droite du nom de l’album.</p>
<div class="pic"><img src="./images/help/delete_album_2.png"/></div>
<p>&nbsp;</p>
<p><strong>3.</strong> Un menu déroulant apparaît. Sélectionnez l’entrée “<strong>Supprimer l’album</strong>”.</p>
<div class="pic"><img src="./images/help/delete_album_3.png"/></div>
<p><strong>4. </strong>Une boîte de dialogue s’ouvre. Celle-ci vous informe que l’opération est destructive et non-réversible. 
    Vous pouvez confirmer la suppression en cliquant sur “<strong>Ok</strong>” ou l’annuler en cliquant sur 
    “<strong>Annuler</strong>”.</p>
<div><img src="./images/help/delete_album_4.png"/></div>
<p><strong>4.</strong> Après votre choix, une boîte de dialogue vous confirme la suppression effective de votre album.</p>
<div ><img src="./images/help/delete_album_5.png"/></div>
<p>&nbsp;</p>
        </div>


<h1><a href="javascript:visibilite('Help4');" >Soumettre un enregistrement</a></h1>
<div id="Help4" style="display:none;">
       <h2>Introduction</h2>
 <p>Cette section décrit la marche à suivre pour ajouter des enregistrements/podcasts dans un album afin de pouvoir, ensuite, 
     les publier. </p>
<h2>Définitions</h2>
<p> <strong>Enregistrement : </strong>un enregistrement est un contenu multimédia (vidéo/podcast) que vous soumettez au système 
    de gestion des podcasts. Celui-ci peut également être un podcast (vidéo et/ou diaporama) enregistré automatiquement 
    en auditoire. Dans ce cas, l’enregistrement est automatiquement soumis au système de gestion des podcasts.</p>
<p><strong>Album :</strong> un album est un regroupement de plusieurs enregistrements. Cet album correspond à un mnémonique 
    de cours (exemple : BIME-I-101)</p>
<p><strong>Album public / Album privé : </strong>lorsque vous créez un album lié à un cours, deux albums sont générés : 
    l’un public, l’autre privé. Le premier sera l’album de diffusion auprès de vos étudiants et du public extérieur ; 
    le second vous permettra de tester certains enregistrements, de recueillir vos enregistrements modérés avant de 
    les publier dans l’album public, etc. </p>
<p><strong>Générique : </strong>Le générique est une courte séquence animée ajoutée en début de vidéo.</p>
<p><strong>Titrage : </strong>Le titrage est un condensé d’information affiché à la suite du générique. Le titrage par défaut 
    reprend le mnémonique de cours, le titre de la vidéo, le nom de l’auteur ainsi que la date de publication.</p>
<h2>Marche à suivre</h2>
<p><strong>1. </strong>Sélectionnez l’album dans lequel vous souhaitez soumettre l’enregistrement.</p>
<div class="pic"><img src="./images/help/submit_1.png"/></div>
<p>&nbsp;</p>
<p><strong>2.</strong> Cliquez sur l’onglet “<strong>Soumettre un enregistrement</strong>”.</p>
<div class="pic">
<img src="./images/help/submit_2.png" />
</div>

<p><strong>3.</strong> Une boîte de dialogue s’ouvre. Complétez les champs “<strong>Titre</strong>” (celui-ci apparaitra au 
    début de la vidéo si vous n’avez pas modifié les options de titrage) et “<strong>Description</strong>” (pour décrire 
    votre enregistrement).<br/>
    Cliquez sur “<strong>Choisir</strong>” pour sélectionner, sur votre ordinateur, le fichier à soumettre.
    <br />
    Cliquez ensuite sur “<strong>Soumettre l’enregistrement</strong>”.</p>
<div><img src="./images/help/submit_3.png"/></div>
<p><strong>4. </strong>Vous avez aussi accès à certaines options avancées. 
    <span class="red">Ces options ne devraient être utilisées que si l’utilisation de la vidéo soumise le justifie.</span><br/>
    Cliquez sur “<strong>Options avancées</strong>” pour faire apparaître ces options.<br/>
    Sélectionnez le type de <strong>générique</strong> souhaité, ainsi que le <strong>titrage</strong> dans les listes déroulantes.
    Cochez la case “<strong>Garder la qualité originale</strong>” si vous souhaitez soumettre la vidéo en qualité optimale 
    (le processus de traitement de la vidéo sera plus long).</p>
    <div class="pic"><img src="./images/help/submit_4.png"/></div>
<p><strong>5. </strong>Lors de la soumission, vous pouvez vérifier l’état de chargement grâce à une barre de progression. </p>
<div class="pic"><img src="./images/help/submit_5.png" /></div>
<p><strong>6. </strong>Une fois le fichier soumis<strong>, </strong>une boîte de dialogue s’ouvre pour vous confirmer l’envoi.</p>
<div class="pic"><img src="./images/help/submit_6.png"/></div>
<p>&nbsp;</p>
<h2>Résultat</h2>
<p>Votre fichier est en cours de traitement sur le serveur. Vous pouvez vérifier son statut dans l’album dans lequel 
    il a été soumis.</p>
<p>Le <strong>triangle vert</strong> indique que l’enregistrement est en cours de traitement.</p>
<div class="pic"><img src="./images/help/submit_7.png"/></div>
<p>&nbsp;</p>
<p>Si vous cliquez sur votre enregistrement (ici “10-12-12 I Capsule de Marjorie Castermans”), vous accédez à des informations 
    concernant votre enregistrement.<br />
  La ligne “<strong>Statut</strong>” vous confirme que celui-ci est en cours de traitement.</p>
<div class="pic"><img src="./images/help/submit_8.png" /></div>
<p>Lorsque le traitement est terminé, le triangle devient grisé et la ligne statut disparaît.</p>
<div class="pic"><img src="./images/help/submit_9.png"/></div>
<p>&nbsp;</p>
      </div>

<h1><a href="javascript:visibilite('Help5');" >Modifier un enregistrement</a></h1>

 <div id="Help5" style="display:none;">

       <h2>Introduction</h2>
 <p>Cette section décrit la marche à suivre pour supprimer, éditer ou déplacer un enregistrement.</p>
<h2>Définitions</h2>
<p><strong>Modifier un enregistrement :</strong> trois types de modification sont possibles sur un enregistrement : la suppression de l’enregistrement, la modification de certaines informations (titre et description) et le déplacement de l’enregistrement dans un autre album.</p>
<p><strong>Enregistrement :</strong> un enregistrement est un contenu multimédia (vidéo/podcast) que vous soumettez au système de gestion des podcasts. Celui-ci peut également être un podcast (vidéo et/ou diaporama) enregistré automatiquement en auditoire. Dans ce cas, l’enregistrement est automatiquement soumis au système de gestion des podcasts.</p>
<p><strong>Album :</strong> un album est un regroupement de plusieurs enregistrements. Cet album correspond à un mnémonique de cours (exemple : BIME-I-101)  </p>
<p><strong>Album public / Album privé : </strong>lorsque vous créez un album lié à un cours, deux albums sont générés : l’un public, l’autre privé. Le premier sera l’album de diffusion auprès de vos étudiants et du public extérieur ; le second vous permettra de tester certains enregistrements, de recueillir vos enregistrements modérés avant de les publier dans l’album public, etc. </p>
<h2>Marche à suivre</h2>
<p><strong>1.</strong> Sélectionnez l’album dans lequel vous souhaitez modifier un enregistrement.  </p>
<div class="pic"><img src="./images/help/edit_1.png" /></div>
<p>&nbsp;</p>
<p><strong>2.</strong> Cliquez sur l’enregistrement à modifier.</p>
<div class="pic"><img src="./images/help/edit_2.png" /></div>
<p> La fenêtre peut être décomposée en trois parties :
  <br />
  <strong>A.</strong> Les actions à mener sur l’enregistrement.
  <br />
  <strong>B.</strong> Les informations disponibles à propos de l’enregistrement. <br />
  <strong>C.</strong> La vidéo (et le diaporama lorsque celui-ci est présent) vous permettant de revoir l’enregistrement et de le publier.  </p>
<p>Nous allons ici traiter de la partie “A”. Pour la partie “C”, veuillez vous reporter à la section &quot;Publier un enregistrement&quot;.</p>
<p>&nbsp;</p>
<p><strong>3.</strong> Vous pouvez effectuer trois types de modification sur un enregistrement&nbsp;: la suppression, l’édition de certaines informations (titre et description) et le déplacement.</p>
<div class="pic"><img src="./images/help/edit_3.png"/></div>
<h2>Supprimer un enregistrement</h2>
<p>1. Cliquez sur &quot;<strong>Supprimer</strong>&quot;.</p>
<div class="pic"><img src="./images/help/edit_4.png" /></div>
<p><strong>2. </strong>Cliquez sur “<strong>Ok</strong>” pour confirmer votre choix ou “<strong>Annuler</strong>” pour annuler la suppression de l’enregistrement.</p>
<div ><img src="./images/help/edit_5.png" /></div>
<h2>Editer un enregistrement (titre &amp; description)</h2>
<p><strong>1. </strong>Cliquez sur &quot;<strong>Editer</strong>&quot;.</p>
<div class="pic"><img src="./images/help/edit_6.png" /></div>
<p><strong>2. </strong>Le titre et la description deviennent modifiables. Pour les éditer, effectuez vos changements dans les boîtes de dialogue prévues à cet effet. Ensuite, cliquez sur “<strong>Envoyer changements</strong>” pour enregistrer vos modifications ou “<strong>Annuler</strong>” pour les annuler.</p>
<p>&nbsp;</p>
<div class="pic"><img src="./images/help/edit_7.png"/></div>
<h2>Déplacer un enregistrement</h2>
<p><strong>1. </strong>Cliquez sur &quot;<strong>Déplacer</strong>&quot;.</p>
<div class="pic"><img src="./images/help/edit_8.png" /></div>
<p>&nbsp;</p>
<p><strong>2. </strong>Une boîte de dialogue s’ouvre. Vous pouvez alors choisir l’album dans lequel vous souhaitez déplacer l’enregistrement.</p>
<div class="pic"><img src="./images/help/edit_9.png" /></div>
<p>&nbsp;</p>
<p><strong>3.</strong> Vous avez également la possibilité d’utiliser le bouton de déplacement direct “<strong>Déplacer dans l’album public</strong>” – si votre enregistrement se trouve dans l’album privé – ou “<strong>Déplacer dans l’album privé</strong>” – si votre enregistrement se trouve dans l’album public.</p>
<div class="pic"><img src="./images/help/edit_10.png" /></div>
<div class="pic"><img src="./images/help/edit_11.png" /></div>

   </div>





<h1><a href="javascript:visibilite('Help6');" >Publier un album</a></h1>

 <div id="Help6" style="display:none;">
       <h2>Introduction</h2>
 <p>Cette section décrit la marche à suivre pour publier un album par l’intermédiaire d’un flux RSS.</p>
<h2>Définitions</h2>
<p><strong>Flux RSS :</strong>  Un flux RSS est un fichier dont le contenu est produit automatiquement en fonction des mises à jour d’un site Internet. Celui-ci permet aux étudiants de recevoir automatiquement les nouveaux enregistrements lorsque ceux-ci sont disponibles. </p>
<p><strong>Album :</strong> un album est un regroupement de plusieurs enregistrements. Cet album correspond à un mnémonique de cours (exemple : BIME-I-101)</p>
<p><strong>Album public / Album privé : </strong>lorsque vous créez un album lié à un cours, deux albums sont générés : l’un public, l’autre privé. Le premier sera l’album de diffusion auprès de vos étudiants et du public extérieur ; le second vous permettra de tester certains enregistrements, de recueillir vos enregistrements modérés avant de les publier dans l’album public, etc. </p>
<p><strong>Presse-papier : </strong>Le presse-papier est une fonctionnalité qui permet de stocker des données que l'on souhaite dupliquer ou déplacer. Il s’agit d’un équivalent à la fonction copier / coller (CTRL + C / CTRL + V).</p>
<h2>Marche à suivre</h2>
<p><strong>1.</strong> Sélectionnez l’album que vous souhaitez publier.</p>
<div class="pic"><img src="./images/help/publish_1.png" /></div>
<p>&nbsp;</p>
<p><strong>2.</strong> Cliquez sur le lien du flux RSS correspondant à la qualité de votre choix.</p>
<div class="pic"><img src="./images/help/publish_2.png" /></div>
<p>&nbsp;</p>
<p><strong>3.</strong> Une boîte de dialogue s’ouvre et vous propose un lien. Ce lien est celui du flux RSS de l’album dans la qualité souhaitée. Vous pouvez le partager avec vos étudiants par l’intermédiaire de l’Université Virtuelle.</p>
<p><strong>Note : </strong>Cliquez sur “<strong>Copier dans le presse-papier</strong>” pour copier le lien et pouvoir le coller ailleurs (dans l’université virtuelle par exemple). Vous êtes alors sûr de ne pas faire d’erreur lors de la copie du lien. </p>
<div><img src="./images/help/publish_3.png" /></div>
 </div>

<h1><a href="javascript:visibilite('Help7');" >Régénérer un flux RSS</a></h1>

 <div id="Help7" style="display:none;">
       <h2>Introduction</h2>
 <p>Cette section décrit la marche à suivre pour régénérer le flux RSS d’un album.</p>
<h2>Définitions</h2>
<p><strong>Flux RSS :</strong>  Un flux RSS est un fichier dont le contenu est produit automatiquement en fonction des mises à jour d’un site Internet. Celui-ci permet aux étudiants de recevoir automatiquement les nouveaux enregistrements lorsque ceux-ci sont disponibles. </p>
<p><strong>Régénérer un flux : </strong>L’action de régénérer un flux RSS consiste à invalider ce flux et lui attribuer un nouveau token.  Par ce processus, tous les utilisateurs abonnés à un album en perdent l’accès au contenu, sans que l’album ne soit supprimé. Cette manipulation est utile à faire en fin d’année académique – par exemple – pour s’assurer que les étudiant d’une année n’aient plus accès au contenu publié l’année suivante.</p>
<p><strong>Album :</strong> un album est un regroupement de plusieurs enregistrements. Cet album correspond à un mnémonique de cours (exemple : BIME-I-101)</p>
<p><strong>Album public / Album privé : </strong>lorsque vous créez un album lié à un cours, deux albums sont générés : l’un public, l’autre privé. Le premier sera l’album de diffusion auprès de vos étudiants et du public extérieur ; le second vous permettra de tester certains enregistrements, de recueillir vos enregistrements modérés avant de les publier dans l’album public, etc. </p>
<h2>Marche à suivre</h2>
<p><strong>1.</strong> Sélectionnez l’album dont vous souhaitez régénérer le flux RSS.</p>
<div class="pic"><img src="./images/help/regenerate_1.png" /></div>
<p>&nbsp;</p>
<p><strong>2.</strong> Cliquez sur le <strong>petit engrenage</strong> qui s’affiche à l’extrême droite du nom de l’album.</p>
<div class="pic"><img src="./images/help/regenerate_2.png" /></div>
<p>&nbsp;</p>
<p><strong>3.</strong> Un menu déroulant apparaît. Sélectionnez l’entrée “<strong>Régénérer RSS</strong>”.</p>
<div class="pic"><img src="./images/help/regenerate_3.png" /></div>
<p>&nbsp;</p>
<p><strong>4.</strong> Une boite de dialogue s’ouvre. Cliquez sur “<strong>Ok</strong>” pour confirmer votre choix ou “<strong>Annuler</strong>” pour annuler la suppression de l’enregistrement.</strong>”.</p>
<div><img src="./images/help/regenerate_4.png" /></div>
<p>&nbsp;</p>
<p><strong>5.</strong> Une boite de dialogue confirme que le flux RSS a été effectivement régénéré.</p>
<div><img src="./images/help/regenerate_5.png" /></div>
<p>&nbsp;</p>
<h2>Résultat</h2>
<p>Le token (élément qui permet de sécuriser le flux RSS) est régénéré. Les abonnés à l’ancien flux RSS n’y ont plus accès.	</p>
 </div>

<h1><a href="javascript:visibilite('Help8');" >Publier un enregistrement</a></h1>

   <div id="Help8" style="display:none;">

       <h2>Introduction</h2>
 <p>Cette section décrit  la marche à suivre pour publier un enregistrement en téléchargement direct ou “embed code”.</p>
<h2>Définitions</h2>
<p> <strong>Embed code : </strong>un embed code est un code HTML permettant de placer une vidéo qui s’exécutera automatiquement dans la fenêtre d’un navigateur Internet (à l’instar de Youtube). </p>
<p><strong>Album :</strong> un album est un regroupement de plusieurs enregistrements. Cet album correspond à un mnémonique de cours (exemple : BIME-I-101) </p>
<p><strong>Album public / Album privé :</strong> lorsque vous créez un album lié à un cours, deux albums sont générés : l’un public, l’autre privé. Le premier sera l’album de diffusion auprès de vos étudiants et du public extérieur ; le second vous permettra de tester certains enregistrements, de recueillir vos enregistrements modérés avant de les publier dans l’album public, etc. </p>
<p><strong>Presse-papier : </strong>Le presse-papier est une fonctionnalité qui permet de stocker des données que l'on souhaite dupliquer ou déplacer. Il s’agit d’un équivalent à la fonction copier / coller (CTRL + C / CTRL + V).</p>
<h2>Marche à suivre</h2>
<p><strong>1.</strong> Sélectionnez l’album dont est issu l’enregistrement que vous souhaitez publier.</p>
<div class="pic"><img src="./images/help/publish_record_1.png" /></div>

<p><strong>2.</strong> Cliquez sur l’enregistrement à publier.</p>
<div class="pic"><img src="./images/help/publish_record_2.png" /></div>

<p><strong>3. </strong>Cliquez sur le volet “Publication” dans la partie de droite.</p>
<div class="pic"><img src="./images/help/publish_record_3.png" /></div>
<p><strong>4.</strong> Dans le menu déroulant qui s'affiche, deux types de publication sont possibles :</p>
<p> <strong>A.</strong> La publication du fichier à télécharger par l’intermédiaire de l’onglet “<strong>Téléchargement</strong>”.
Dans ce cas, le lien que vous allez publier permettra aux utilisateurs de télécharger directement le podcast selon le format (vidéo ou diaporama) et la qualité (basse ou haute) que vous aurez préalablement définis.</p>
<div class="pic"><img src="./images/help/publish_record_4.png" /></div>
<p> <strong>B. </strong>La publication “<strong>Embed</strong>” vous fournit un code HTML à placer sur une page web afin que la vidéo qui s’exécute automatiquement dans la fenêtre du navigateur Internet de l’utilisateur (à l’instar de Youtube). </p>
<div class="pic"><img src="./images/help/publish_record_5.png" /></div>

<p><strong>4. </strong>Si vous souhaitez publier un fichier à télécharger, cliquez sur “<strong>Téléchargement</strong>”&nbsp;; si vous souhaitez publier un “Embed code”, cliquez sur “<strong>Embed</strong>”.</p>
<div class="pic"><img src="./images/help/publish_record_6.png"/></div>
<p><strong>5. </strong>Pour un type de publication comme pour l’autre, cliquez sur la qualité à que vous souhaitez fournir aux utilisateurs (haute ou basse résolution). Une boîte de dialogue s’ouvre, copiez dans votre presse-papier le lien fourni et partager-le (cfr. Section suivante).

  </p>
<div><img src="./images/help/publish_record_7.png" /> </div>
<div><img src="./images/help/publish_record_8.png" /> </div>

      </div>


<h1><a href="javascript:visibilite('Help9');" >Partager vos publications</a></h1>
     <div id="Help9" style="display:none;">

       <h2>Introduction</h2>
 <p>Cette section décrit la marche à suivre pour partager vos publications par l’intermédiaire de l’Université Virtuellle ou de toute autre plateforme.</p>
<h2>Copyright</h2>
<p> Avant toute publication de podcast, nous vous conseillons de consulter la page &quot;Droit d'auteur&quot;de ¤tuto¤.</p>
  <h2>Université Virtuelle</h2>
<p> Une fois les publications créées, vous pouvez partager les URL de celles-ci avec vos étudiants.
  <br />
  Afin de restreindre l’accès à vos publications, nous vous conseillons d’utiliser l’Université Virtuelle les liens (RSS, d’un fichier ou embed). Toutefois, vous êtes libre de partager vos publications sur tout autre endroit (site du service, iTunes U, etc.) </p>
<h2>Partager un flux RSS</h2>
<p> Pour partager un flux RSS avec d’autres utilisateurs, vous devez leur fournir le lien du flux (cfr. Section &quot;Publier un album&quot;).</p>
<h2>Partager un fichier</h2>
<p>Pour partager un enregistrement en proposant aux utilisateurs de télécharger directement le fichier, vous devez leur fournir le lien du fichier (cfr. Section &quot;Publier un enregistrement)</p>
<h2>Partager un embed code</h2>
<p>Pour partager un embed code, vous devez copier celui-ci dans le code HTML d’une page web (directement sur un site personnel ou via l’Université Virtuelle).</p>
<h2>Pictogrammes</h2>
<p>Nous mettons à votre disposition un ensemble de pictogrammes vous permettant d’illustrer vos publications sur l’université virtuelle.</p>
<div >
<img src="./images/help/3picto.jpg" width="103" height="84" /> <img src="./images/help/A.jpg" width="103" height="84" /> <img src="./images/help/basseD.jpg" width="103" height="84" /> <img src="./images/help/hauteD.jpg" width="103" height="84" /> <img src="./images/help/BFlux.png" width="103" height="84" /><div></div>
<p>Pour recevoir ces pictogrammes ou en demander d’autres : <strong>¤email¤</strong></p>
</div></div>
     </div>
</div>
