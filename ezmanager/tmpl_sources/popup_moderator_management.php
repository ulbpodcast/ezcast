<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
    <h4 class="modal-title">®Moderator_manage®</h4>
</div>
<div class="modal-body">
    <div id="div_ezplayer_url">
        <h4 class="text-center">®sharemyalbum® </h4><br>
        <div id="display_error" class="alert alert-danger alert-dismissible fade in" role="alert" style="display: none;">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">×</span></button>
                    <p>Erreur</p>
        </div>
        <div id="display_success" class="alert alert-success alert-dismissible fade in" role="alert" style="display: none;">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">×</span></button>
                <p>L'envoie du mail a été réalisé avec succés</p>
        </div>
        <div class="BlocPodcastMenu">

            <!--Pour partager cet album sur "EZmanager", ajouter des adresses mail afin d'y envoyer le lien :  <br/><br/>-->
            ®Manager_URL_message_mail® <br/><br/>
            <div class="row">
                <div class="col-lg-12">
                    <div class="input-group">
                        <input type="text" class="form-control" id="email">
                        <span class="input-group-btn">
                            <button class="btn btn-success" type="button" id='addEmail'>Ajouter</button>
                        </span>
                    </div><!-- /input-group -->
                </div><!-- /.col-lg-6 -->
            </div><!-- /.row -->
            <br/>
            <div class='well' id="emails" style="display: none;">
            </div>
            <br />
            <div class="wrapper_clip" style="position:relative; text-align: center;">
                <span onclick="send_email();" class="btn btn-default">
                    Envoyer le lien par mail
                </span>
            </div>
        </div>
    </div>
    <div class="row">

        <br />
        <div class="col-md-12">
            <h4 class="text-center">®Moderator_List®</h4><br>
            <table class="table table-hover text-left" >
                <?php for ($i=0; $i < count($tbusercourse); $i++) {
    $userId = $tbusercourse[$i]['user_ID'];
    echo '<tr>';
    echo '<td>';
    echo $userId;
    echo '</td>';
    if (count($tbusercourse) != 1) { // avoid suppression of the last admin
        echo '<td>'; ?>
                                <a class="btn-xs btn btn-danger delete_user_course pointer" id="delete_user_course_<?php echo $userId; ?>"
                                    onclick="setTimeout(function(){ display_bootstrap_modal($('#modal'), $('#delete_user_course_<?php echo $userId; ?>'));
                                        $('#modal').modal('show'); }, 500);"
                                    href="index.php?action=show_popup&amp;popup=moderator_delete&amp;album=<?php echo $album; ?>&amp;id_user=<?php echo $tbusercourse[$i]['user_ID']; ?>&sesskey=<?php echo $_SESSION['sesskey']; ?>" 
                                    data-remote="false" data-toggle="modal" data-target="#modal" >
                                <?php
                                    echo '<span>®Delete®</span>';
        echo '</a>';
        echo '</td>';
    }
    echo '</tr>';
} ?>
            </table>
        </div>
    </div>
</div>
<script type="text/javascript">

    $("#addEmail").click(function()
    {
        var new_email = ""+$("#email").val()+"";
        var entry = false;
        var email_correct = false;

        if($("#emails > div").length > 0)
        {
            var nb = 0;
            $("#emails > div").each(function( index )
            {
                if(new_email.toLowerCase() == $( this ).text().toLowerCase())
                {
                    nb++;
                }
            });

            if(nb == 0)
            {
                entry = true;
            }
            if(validateEmail(new_email))
            {
                email_correct = true;
            }
        }
        else
        {
            if(validateEmail(new_email))
            {
                email_correct = true;
            }
            entry = true;
        }

        if(entry && email_correct)
        {
            $("#emails").css('display', 'block');
            $("#emails").append('<div><span class="label label-primary" style="font-size:100%;">'+new_email+'<a href="#" onclick="delete_email(this);"><span class="remove glyphicon glyphicon-remove-sign glyphicon-white" style="margin-left: 7px;color: white;font-size: 100%;"></span></a></span><br/><br/></div>');
            $("#display_error").css('display', 'none');
            $("#email").val('');
        }
        else if(email_correct == false && new_email != '')
        {
            $("#display_error").css('display', 'block');
            $('#display_error > p').text("L'adresse mail suivante est incorrecte: "+new_email+". Veuillez indiquer une adresse mail correcte.");
        }
        else
        {
            $("#email").val('');
        }
    });

    function validateEmail(email)
    {
        var re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
        return re.test(String(email).toLowerCase());
    }

    function delete_email(elem)
    {
        $(elem).parent().parent().remove();

        if($("#emails > div").length == 0)
            $("#emails").css('display', 'none');
    }

    function send_email()
    {
        $("#display_success").css('display', 'none');
        $("#display_error").css('display', 'none');

        //essayer ici avec un tableau et json encode .. !!!
        var all_email = '';
        var total = $("#emails > div").length;

        if(total > 0)
        {
            $("#emails > div").each(function( index )
            {
                if(index == total - 1)
                {
                    all_email += ''+$( this ).text()+'';
                }
                else
                {
                    all_email += ''+$( this ).text()+',';
                }
            });

            var url = encodeURIComponent('<?php echo $manager_url; ?>');
            var album = '<?php echo $album_name_full; ?>';
            $.ajax({
                url: "index.php?action=send_link_moderator",
                type: "POST",
                data: "email="+all_email+"&url="+url+"&album="+album+"",
                success: function(data)
                {
                    var myArray = JSON.parse(data);
                    var html = '';

                    switch(myArray.identifiant)
                    {
                        case 'success':
                            $("#display_success").css('display', 'block');
                            $('#display_success > p').text(myArray.message);
                            break;
                        case 'email_error':
                            $("#display_error").css('display', 'block');
                            $('#display_error > p').text(myArray.message);
                            break;
                        case 'empty':
                             $("#display_error").css('display', 'block');
                             $('#display_error > p').text(myArray.message);
                            break;
                    }

                    if(myArray.identifiant == 'success')
                    {
                        $("#emails").html('');
                        $("#emails").css('display', 'none');
                    }
                },
                error: function(data)
                {
                    //$("#display_error").css('display', 'inline-block');
                }
            });
        }
        else
        {
            $("#display_error").css('display', 'block');
            $('#display_error > p').text("Vous n'avez indiqué aucune adresse mail.");
        }
    }
</script>
