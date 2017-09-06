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

<h2> ®Submit_record® </h2>
<div id="form">
   <form action="index.php" method="post" id="submit_form" >
       <input type="hidden" name="action" value="submit_media_infos"/>
       <input type="hidden" name="album" value="<?php echo $album; ?>"/>
       <input type="hidden" name="moderation" value="<?php echo $moderation; ?>"/>
         <p>
             ®Album®: <?php echo $album; ?> (<?php echo ($moderation) ? '®Private_album®' : '®Public_album®'; ?>)
             <br/><br/>
             <label>®Title®: 
             <input name="title" type="text" style="width: 250px;" />
         </label>
             <br/><br/>
             <label>®Type®: 
                 <select name="type" style="width: 250px;">
                 <option value="cam">®Video®</option>
                 <option value="slide">®Slides®</option>
                 </select>
         </label><br/>
         <br/><label>®Description®: 
                 <textarea name="description" rows="4" style="width: 250px;"  ></textarea>
             </label>
         <br/>
         <br/>
         <input type="submit" name="®Next®" /></p>
   </form>
</div>