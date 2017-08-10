<?php
function index($param = array()) {
global $repository_basedir;
    
    if(isset($_GET['fileToChange']) && $_GET['fileToChange']!=''){
         $resultat = copy($repository_basedir.'/repository/'.$_GET['album'].'/'.$_GET['asset'].'/thumbnails/'.$_GET['fileToChange'],$repository_basedir.'/repository/'.$_GET['album'].'/'.$_GET['asset'].'/thumbnails/thumbnail.png');        
     
          if($resultat) echo json_encode("Miniature téléchargée avec succès! ");
          else echo json_encode("Echec du chargement de la miniature ");
    }
    
    else {
     
          $fichier = basename($_FILES['image']['name']);

     //Check if it is a image         
     
          $extensions = array('.png', '.jpg', '.jpeg');
        $extension = strrchr($_FILES['image']['name'], '.');
        if(!in_array($extension, $extensions)) //Si l'extension n'est pas dans le tableau
          {
               $erreur = 'Vous devez uploader un fichier de type png, jpg, jpeg...';
          }
          
          //Check the file size
                 
        /*  $taille_maxi = 1000000;
          $taille = filesize($_FILES['image']['tmp_name']);
          if($taille>$taille_maxi)
          {
               $erreur = 'ECHEC!!! Taille du fichier trop importante, veuillez introduire un fichier de max '.$taille_maxi.' Octets';
          }
          
          */
        if(isset($erreur) && $erreur!='') echo json_encode($erreur);
        
        else {
          $_FILES['image']['name'] = strtr($_FILES['image']['name'], 'ÀÁÂÃÄÅÇÈÉÊËÌÍÎÏÒÓÔÕÖÙÚÛÜÝàáâãäåçèéêëìíîïðòóôõöùúûüýÿ', 'AAAAAACEEEEIIIIOOOOOUUUUYaaaaaaceeeeiiiioooooouuuuyy');
          $_FILES['image']['name'] = preg_replace('/([^.a-z0-9]+)/i', '-', $_FILES['image']['name']);
          
          if(!is_dir($repository_basedir.'/repository/'.$_GET['album'].'/'.$_GET['asset'].'/thumbnails')) mkdir($repository_basedir.'/repository/'.$_GET['album'].'/'.$_GET['asset'].'/thumbnails');
          
          $newpathfile=$repository_basedir.'/repository/'.$_GET['album'].'/'.$_GET['asset'].'/thumbnails/'.$_FILES['image']['name'];
          $resultat = move_uploaded_file($_FILES['image']['tmp_name'],$newpathfile);    
          
          
          
          // Resize image
          
          $ImageNews = getimagesize($newpathfile);
          if($extension=='jpg' || $extension=='jpeg') {
             $ImageChoisie = imagecreatefromjpeg($newpathfile);
          }
          else if($extension=='png'){
           $ImageChoisie = imagecreatefrompng($newpathfile); 
          }
          else if($extension=='gif'){
           $ImageChoisie = imageCreateFromGif($newpathfile); 
          }
          
          $TailleImageChoisie = getimagesize($newpathfile);
          $NouvelleLargeur = 350; //Largeur choisie à 350 px mais modifiable
          
          $NouvelleHauteur = ( ($TailleImageChoisie[1] * (($NouvelleLargeur)/$TailleImageChoisie[0])) );
          
          $NouvelleImage = imagecreatetruecolor($NouvelleLargeur , $NouvelleHauteur) or die ("Erreur");
          
          imagecopyresampled($NouvelleImage , $ImageChoisie  , 0,0, 0,0, $NouvelleLargeur, $NouvelleHauteur, $TailleImageChoisie[0],$TailleImageChoisie[1]);
          imagedestroy($ImageChoisie);
          $NomImageChoisie = explode('.', $ImageNews);
          $NomImageExploitable = time();
          
          if($extension=='jpg' || $extension=='jpeg') {
           imagejpeg($NouvelleImage , $newpathfile, 100);
          }
          else if($extension=='png'){
           imagepng($NouvelleImage , $newpathfile, 100);
          }
          else if($extension=='gif'){
            imagegif($NouvelleImage , $newpathfile, 100);
          }
          
          $resultat = copy($newpathfile,$repository_basedir.'/repository/'.$_GET['album'].'/'.$_GET['asset'].'/thumbnails/thumbnail.png');
          
          
          if($resultat) echo json_encode("Miniature téléchargée avec succès! ");
          else echo json_encode("Echec du chargement de la miniature ");
        }
      }
      

     //return "Miniature téléchargée avec succès! ";
     
}
?>