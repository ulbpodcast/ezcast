
<div class="page_title">®report_title®</div>

<ul>
    <li>
        Nombre de total d'utilisateurs différents (ayant soumis des vidéos et/ou 
        ayant enregistré en auditoire) depuis le début d'EZCast
        <b><?php echo count($generalInfos['listAuthor']); ?></b>
        <br />
        <ul>
            <?php foreach($generalInfos['listAuthor'] as $author => $nbr) {
                echo '<li>'.$nbr.' -> '.$author.'</li>';
            } ?>
        </ul>
    </li>
    <li>
        Nombre total d'utilisateurs différents ayant soumis des vidéos
        depuis le début d'EZcast 
        <b><?php echo count($generalInfos['submitAuthor']); ?></b>
        <br />
        <ul>
            <?php foreach($generalInfos['submitAuthor'] as $author => $nbr) {
                echo '<li>'.$nbr.' -> '.$author.'</li>';
            } ?>
        </ul>
    </li>
    <li>
        Nombre total d'utilisateurs différents ayant enregistré en 
        auditoire depuis le début d'EZcast
        <b><?php echo count($generalInfos['classroomAuthor']); ?></b>
        <br />
        <ul>
            <?php foreach($generalInfos['classroomAuthor'] as $author => $nbr) {
                echo '<li>'.$nbr.' -> '.$author.'</li>';
            } ?>
        </ul>
    </li>
    <li>
        Nombre total de cours différents (contenant des capsules et/ou
        des enregistrements en auditoire) depuis le début d'EZcast
        <b><?php echo count($generalInfos['classroomAuthor']); ?></b>
        <br />
        <ul>
            <?php foreach($generalInfos['classroomAuthor'] as $author => $nbr) {
                echo '<li>'.$nbr.' -> '.$author.'</li>';
            } ?>
        </ul>
    </li>
    <li>
        Nombre total de cours différents (contenant des capsules et/ou
        des enregistrements en auditoire) depuis le début d'EZcast 
        <b><?php echo count($generalInfos['listCours']); ?></b>
        <br />
        <ul>
            <?php foreach($generalInfos['listCours'] as $author => $nbr) {
                echo '<li>'.$nbr.' -> '.$author.'</li>';
            } ?>
        </ul>
    </li>
    <li>
        Nombre total de cours différents contenant des capsules depuis
        le début d'EZcast
        <b><?php echo count($generalInfos['listCoursSubmit']); ?></b>
        <br />
        <ul>
            <?php foreach($generalInfos['listCoursSubmit'] as $author => $nbr) {
                echo '<li>'.$nbr.' -> '.$author.'</li>';
            } ?>
        </ul>
    </li>
    <li>
        Nombre total de cours différents contenant des enregistrements  | 
        faits en auditoire depuis le début d'EZcast 
        <b><?php echo count($generalInfos['listCoursClassroom']); ?></b>
        <br />
        <ul>
            <?php foreach($generalInfos['listCoursClassroom'] as $author => $nbr) {
                echo '<li>'.$nbr.' -> '.$author.'</li>';
            } ?>
        </ul>
        <hr />
    </li>
    
    <li>
        Nombre total d'assets contenus dans le repository
        (capsules + cours enregistrés depuis le début d'EZcast)<br />
        Ne tient pas compte des assets supprimés ni des tests 
        <b><?php echo count($generalInfos['countAsset']); ?></b>
    </li>
    <li>
        Nombre total de capsules contenues dans le repository<br />
        Ne tient pas compte des assets supprimés ni des tests
        <b><?php echo count($generalInfos['countRecordAsset']); ?></b>
    </li>
    <li>
        Nombre total de cours enregistrés contenus dans le repository<br />
        Ne tient pas compte des assets supprimés ni des tests
        <b><?php echo count($generalInfos['countClassroomAsset']); ?></b>
    </li>
    
    
</ul>
