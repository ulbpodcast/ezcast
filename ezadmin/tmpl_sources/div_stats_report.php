
<div class="page_title">®report_title®</div>

<br /><br />
<h4>Général</h4>
<ul>
    <li>
        Nombre de total d'utilisateurs différents (ayant soumis des vidéos et/ou 
        ayant enregistré en auditoire) depuis le début d'EZCast
        <b><?php echo $report->get_nbr_list_all_author(); ?></b>
        <br />
        <ul>
            <?php foreach($report->get_list_all_author() as $author => $nbr) {
                echo '<li>'.$nbr.' -> '.$author.'</li>';
            } ?>
        </ul>
    </li>
    <li>
        Nombre total d'utilisateurs différents ayant soumis des vidéos
        depuis le début d'EZcast 
        <b><?php echo $report->get_nbr_list_all_submit_author(); ?></b>
        <br />
        <ul>
            <?php foreach($report->get_list_all_submit_author() as $author => $nbr) {
                echo '<li>'.$nbr.' -> '.$author.'</li>';
            } ?>
        </ul>
    </li>
    <li>
        Nombre total d'utilisateurs différents ayant enregistré en 
        auditoire depuis le début d'EZcast
        <b><?php echo $report->get_nbr_list_all_record_author(); ?></b>
        <br />
        <ul>
            <?php foreach($report->get_list_all_record_author() as $author => $nbr) {
                echo '<li>'.$nbr.' -> '.$author.'</li>';
            } ?>
        </ul>
    </li>
    <li>
        Nombre total de cours différents (contenant des capsules et/ou
        des enregistrements en auditoire) depuis le début d'EZcast 
        <b><?php echo $report->get_nbr_list_all_cours(); ?></b>
        <br />
        <ul>
            <?php foreach($report->get_list_all_cours() as $author => $nbr) {
                echo '<li>'.$nbr.' -> '.$author.'</li>';
            } ?>
        </ul>
    </li>
    <li>
        Nombre total de cours différents contenant des capsules depuis
        le début d'EZcast
        <b><?php echo $report->get_nbr_list_all_cours_submit(); ?></b>
        <br />
        <ul>
            <?php foreach($report->get_list_all_cours_submit() as $author => $nbr) {
                echo '<li>'.$nbr.' -> '.$author.'</li>';
            } ?>
        </ul>
    </li>
    <li>
        Nombre total de cours différents contenant des enregistrements
        faits en auditoire depuis le début d'EZcast 
        <b><?php echo $report->get_nbr_list_all_cours_record(); ?></b>
        <br />
        <ul>
            <?php foreach($report->get_list_all_cours_record() as $author => $nbr) {
                echo '<li>'.$nbr.' -> '.$author.'</li>';
            } ?>
        </ul>
    </li>
</ul>
<hr />
<ul>
    <li>
        Nombre total d'assets contenus dans le repository
        (capsules + cours enregistrés depuis le début d'EZcast)<br />
        Ne tient pas compte des assets supprimés ni des tests 
        <b><?php echo $report->get_count_total_asset(); ?></b> 
    </li>
    <li>
        Nombre total de capsules contenues dans le repository<br />
        Ne tient pas compte des assets supprimés ni des tests
        <b><?php echo $report->get_count_submit_asset(); ?></b>
    </li>
    <li>
        Nombre total de cours enregistrés contenus dans le repository<br />
        Ne tient pas compte des assets supprimés ni des tests
        <b><?php echo $report->get_count_record_asset(); ?></b>
    </li>
</ul>
<hr />
<br />



<h4>EZPlayer</h4>
<ul>
    <li>
        Nombre total d'utilisateurs différents depuis la création d'EZplayer 
        <b><?php echo $report->get_nbr_total_user(); ?></b>
    </li>
    <li>
        Nombre total de discussions créées depuis la création d'EZplayer (V2)
        <b><?php echo $report->get_ezplayer_total_thread(); ?></b>
    </li>
    <li>
        Liste des cours contenant des discussions depuis la création d'EZplayer (V2) <br />
        Le nombre indique le nombre de discussions créées pour le cours.
        <b><?php echo $report->get_ezplayer_nbr_list_cours_thread(); ?></b>
        <br />
        <ul>
            <?php foreach($report->get_ezplayer_list_cours_thread() as $cours => $nbr) {
                echo '<li>'.$nbr.' -> '.$cours.'</li>';
            } ?>
        </ul>
    </li>
    <li>
        Nombre total de commentaires ajoutés depuis la création d'EZplayer (V2)
        <b><?php echo $report->get_ezplayer_total_comment(); ?></b>
    </li>
    <li>
        Liste des cours contenant des commentaires depuis la création d'EZplayer (V2)<br />
        Le nombre indique le nombre de commentaires créés pour le cours.
        <b><?php echo $report->get_ezplayer_nbr_list_cours_comment(); ?></b>
        <br />
        <ul>
            <?php foreach($report->get_ezplayer_list_cours_comment() as $cours => $nbr) {
                echo '<li>'.$nbr.' -> '.$cours.'</li>';
            } ?>
        </ul>
    </li>
    <li>
        Nombre total de signets (personnels et officiels) ajoutés
        depuis la création d'EZplayer (traces)
        <b><?php echo $report->get_ezplayer_total_bookmark(); ?></b>
    </li>
    <li>
        Nombre total de signets officiels ajoutés depuis la création 
        d'EZplayer (traces)
        <b><?php echo $report->get_ezplayer_total_offi_bookmark(); ?></b>
    </li>
    <li>
        Nombre total de signets personnels ajoutés depuis la création
        d'EZplayer (traces)
        <b><?php echo $report->get_ezplayer_total_pers_bookmark(); ?></b>
    </li>
    <li>
        Nombre total d'utilisateurs différents ayant ajouté des signets
        officiels depuis la création d'EZplayer (traces)
        <b><?php echo $report->get_ezplayer_nbr_list_user_offi_bookmark(); ?></b>
    </li>
    <li>
        Nombre total d'utilisateurs différents ayant ajouté des signets
        personnels depuis la création d'EZplayer (traces)
        <b><?php echo $report->get_ezplayer_nbr_list_user_pers_bookmark(); ?></b>
    </li>
    
</ul>

<?php if(isset($start_date) && isset($end_date)) { ?>
<h3>Information pour la période</h3>
<p>Les informations suivante concernent la période choisie.  Cette période commence au 
    <?php echo $start_date; ?> et s'achève au <?php echo $end_date; ?>
<ul>
    <li>
        Nombre d'utilisateurs différents (ayant soumis des vidéos et/ou
        ayant enregistré en auditoire) pour la période donnée
        <b><?php echo $report->get_nbr_date_list_author(); ?></b>
        <br />
        <ul>
            <?php foreach($report->get_date_list_author() as $author => $nbr) {
                echo '<li>'.$nbr.' -> '.$author.'</li>';
            } ?>
        </ul>
    </li>
    <li>
        Nombre d'utilisateurs différents ayant soumis des vidéos pour la période donnée
        <b><?php echo $report->get_nbr_date_list_submit_author(); ?></b>
        <br />
        <ul>
            <?php foreach($report->get_date_list_submit_author() as $author => $nbr) {
                echo '<li>'.$nbr.' -> '.$author.'</li>';
            } ?>
        </ul>
    </li>
    <li>
        Nombre d'utilisateurs différents ayant enregistré en auditoire pour la période donnée
        <b><?php echo $report->get_nbr_date_list_record_author(); ?></b>
        <br />
        <ul>
            <?php foreach($report->get_date_list_record_author() as $author => $nbr) {
                echo '<li>'.$nbr.' -> '.$author.'</li>';
            } ?>
        </ul>
    </li>
    <li>
        Nombre de cours différents (contenant des capsules et/ou
        des enregistrements en auditoire) pour la période donnée
        <b><?php echo $report->get_nbr_date_list_cours(); ?></b>
        <br />
        <ul>
            <?php foreach($report->get_date_list_cours() as $author => $nbr) {
                echo '<li>'.$nbr.' -> '.$author.'</li>';
            } ?>
        </ul>
    </li>
    <li>
        Nombre de cours différents contenant des capsules pour la
        période donnée
        <b><?php echo $report->get_nbr_date_list_cours_submit(); ?></b>
        <br />
        <ul>
            <?php foreach($report->get_date_list_cours_submit() as $author => $nbr) {
                echo '<li>'.$nbr.' -> '.$author.'</li>';
            } ?>
        </ul>
    </li>
    <li>
        Nombre de cours différents contenant des enregistrements 
        faits en auditoire pour la période donnée
        <b><?php echo $report->get_nbr_date_list_cours_record(); ?></b>
        <br />
        <ul>
            <?php foreach($report->get_date_list_cours_record() as $author => $nbr) {
                echo '<li>'.$nbr.' -> '.$author.'</li>';
            } ?>
        </ul>
    </li>
</ul>
<hr />
<ul>
    <li>
        Nombre d'assets ajoutés au repository pour la période donnée
        (capsules + cours enregistrés)<br />
        Ne tient pas compte des assets supprimés ni des tests
        <b><?php echo $report->get_date_count_asset(); ?></b> 
    </li>
    <li>
        Nombre de capsules soumises dans le repository pour la période <br />
        Ne tient pas compte des assets supprimés ni des tests
        <b><?php echo $report->get_date_count_submit_asset(); ?></b>
    </li>
    <li>
        Nombre de cours enregistrés ajoutés au repository pour la
        période donnée<br />
        Ne tient pas compte des assets supprimés ni des tests 
        <b><?php echo $report->get_date_count_record_asset(); ?></b>
    </li>
</ul>
<h7>Utilisation des auditoires</h7>
<ul>
    <?php foreach($report->get_date_classroom_record_time() as $classroom => $value) {
        echo '<li>'.$classroom.' => ('.$value['nbr'].') '.$value['time'].'</li>';
    } ?>
</ul>
<h7>Auditoire inutilisé</h7>
<ul>
    <?php foreach($classroom_not_use as $classroom) {
        echo '<li>'.$classroom.'</li>';
    } ?>
</ul>
<br />
Taux de vidéos enregistrées : <?php echo $percentAuditoir; ?><br />
Taux de vidéos soumises: <?php echo $percentSubmit; ?>
<div class="progress">
    <div class="progress-bar progress-bar-success" style="width: <?php echo $percentSubmit; ?>%">
        <span class="sr-only">Taux de vidéos soumises</span>
        <?php echo $percentSubmit; ?>% soumises
    </div>
    <div class="progress-bar progress-bar-info" style="width: <?php echo $percentAuditoir; ?>%">
        <span class="sr-only">Taux de vidéos enregistrées</span>
        <?php echo $percentAuditoir; ?>% enrégistrées
    </div>
</div>

<h4>EZPlayer</h4>
<ul>
    <li>
        Nombre d'utilisateurs (authentifiés) différents pour la période donnée 
        <b><?php echo $report->get_ezplayer_nbr_date_list_user_login(); ?></b>
        <ul>
            <?php $i = 0;
            foreach($report->get_ezplayer_date_list_user_login() as $user => $nbr) { 
                if(++$i > 10) { break; }
                echo '<li>'.$nbr.' '.$user.'</li>';
            } ?>
        </ul>
    </li>
    <li>
        Nombre d'utilisateurs (anonymes) pour la période donnée <br />
        Approximation basée sur les adresses ip différentes
        <b><?php echo $report->get_ezplayer_nbr_date_list_ip_login(); ?></b> 
    </li>
    <li>
        Classement des navigateurs web | OS par ordre d'utilisation pour la période donnée
        <ul>
            <?php foreach($report->get_ezplayer_date_list_user_browser() as $info => $nbr) { 
                echo '<li>'.$nbr.' '.$info.'</li>';
            } ?>
        </ul>
    </li>
    <li>
        Nombre d'albums différents consultés pour la période donnée
        (parcourir l'album, sans forcément cliquer sur un asset)<br />
        Action: view_album_assets (voir desc. actions)
        <b><?php echo $report->get_ezplayer_nbr_date_list_album(); ?></b>
        <ul>
            <?php foreach($report->get_ezplayer_date_list_album() as $album => $nbr) { 
                echo '<li>'.$nbr.' '.$album.'</li>';
            } ?>
        </ul>
    </li>
    <li>
        Nombre d'albums différents contenant au moins un asset ayant 
        été consulté pour la période donnée. 
        contrairement aux albums consultés, qui peuvent avoir été 
        parcourus sans avoir cliqué sur aucune vidéo, ce nombre-ci 
        ne tient compte que des albums dans lesquels au moins un 
        utilisateur a consulté au moins un asset. <br />
        Action: view_asset_details (voir desc. actions)  
        <b><?php echo $report->get_ezplayer_nbr_date_list_album_click(); ?></b>
        <ul>
            <?php foreach($report->get_ezplayer_date_list_album_click() as $album => $nbr) { 
                echo '<li>'.$nbr.' '.$album.'</li>';
            } ?>
        </ul>
    </li>
    <li>
        Nombre d'albums différents contenant au moins un asset ayant 
        été consulté pour la période donnée. 
        contrairement aux albums consultés, qui peuvent avoir été 
        parcourus sans avoir cliqué sur aucune vidéo, ce nombre-ci 
        ne tient compte que des albums dans lesquels au moins un 
        utilisateur a consulté au moins un asset. <br />
        Action: view_asset_details (voir desc. actions)  
        <b><?php echo $report->get_ezplayer_nbr_date_list_album_click(); ?></b>
        <ul>
            <?php foreach($report->get_ezplayer_date_list_album_click() as $album => $nbr) { 
                echo '<li>'.$nbr.' '.$album.'</li>';
            } ?>
        </ul>
    </li>
    <li>
        Nombre d'assets différents consultés pour la période donnée<br />
        Action: view_asset_details (voir desc. actions)
        <b><?php echo $report->get_ezplayer_nbr_date_unique_asset(); ?></b>
    </li>
    <li>
        Nombre de consultations d'assets total pour la période donnée <br />
        (Un même asset peut avoir été consulté plusieurs fois)
        <b><?php echo $report->get_ezplayer_nbr_date_asset(); ?></b>
    </li>
    <li>
        Nombre de consultations d'assets par mois pour la période donnée<br />
        (Un même asset peut avoir été consulté plusieurs fois) 
        <ul>
            <?php for($i = 1; $i <= 12; ++$i) { 
                echo '<li>'.$i.':  ('.
                        round(($mountAsset[$i]/$report->get_ezplayer_nbr_date_asset())*100, 2).
                    '%) '.$mountAsset[$i].'</li>';
            } ?>
        </ul>
        Top 10 des assets les plus consultés<br />
        Le nombre indique le nombre de fois que l'asset a été consulté
        <ul>
            <?php $i = 0;
            foreach($report->get_ezplayer_date_unique_asset() as $asset => $nbr) { 
                if(++$i > 10) { break; }
                echo '<li>'.$nbr.' '.$asset.'</li>';
            } ?>
        </ul>
    </li>
    <li>
        Nombre de discussions crées pour la période donnée<br />
        Action: thread_add (voir desc. actions)
        <b><?php echo $report->get_ezplayer_nbr_date_cours_thread(); ?></b>
        <ul>
            <?php $i = 0;
            foreach($report->get_ezplayer_date_cours_thread() as $cours => $nbr) { 
                if(++$i > 10) { break; }
                echo '<li>'.$nbr.' '.$cours.'</li>';
            } ?>
        </ul>
    </li>
    <li>
        Nombre de commentaires ajoutés pour la période donnée<br />
        Action: comment_add (voir desc. actions)
        <b><?php echo $report->get_ezplayer_date_nbr_comment(); ?></b>
    </li>
    <li>
        Nombre de signets personnels créés pour la période donnée<br />
        Action: bookmark_add (voir desc. actions)
        <b><?php echo $report->get_ezplayer_date_pers_bookmark(); ?></b>
    </li>
    <li>
        Nombre d'utilisateurs différents ayant créé des signets personnels pour la période donnée<br />
        Action: bookmark_add (voir desc. actions)
        <b><?php echo $report->get_ezplayer_nbr_date_user_pers_bookmark(); ?></b>
    </li>
    <li>
        Top 10 des cours dans lesquels le plus de signets (officiels et personnels)
        ont été ajoutés. <br />
        Le nombre indique le nombre de signets ajoutés pour la période donnée
        <ul>
            <?php $i = 0;
            foreach($report->get_ezplayer_date_cours_pers_bookmark() as $cours => $nbr) { 
                if(++$i > 10) { break; }
                echo '<li>'.$nbr.' '.$cours.'</li>';
            } ?>
        </ul>
    </li>
    <li>
        Nombre de signets personnels créés pour la période donnée<br />
        Action: bookmark_add (voir desc. actions)
        <b><?php echo $report->get_ezplayer_date_offi_bookmark(); ?></b>
    </li>
    <li>
        Nombre d'utilisateurs différents ayant créé des signets personnels pour la période donnée<br />
        Action: bookmark_add (voir desc. actions)
        <b><?php echo $report->get_ezplayer_nbr_date_user_offi_bookmark(); ?></b>
    </li>
    <li>
        Top 10 des utilisateurs ajoutant le plus de signets officiels.<br />
        Le nombre indique le nombre de signets ajoutés pour la période donnée
        <ul>
            <?php $i = 0;
            foreach($report->get_ezplayer_date_user_offi_bookmark() as $user => $nbr) { 
                if(++$i > 10) { break; }
                echo '<li>'.$nbr.' '.$user.'</li>';
            } ?>
        </ul>
    </li>
</ul>

<?php 
}