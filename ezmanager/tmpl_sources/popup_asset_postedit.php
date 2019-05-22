<div class="modal-header">
  <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
  <h4 class="modal-title">®Postedit_video_title®</h4>
</div>
<div class="modal-body" id="myModal">
    <div id="container" style="margin: 0 auto"></div>
    <div class="container container-relative container-grey" id="videoDiv">
        <center>
            <?php if ($has_cam) {
            ?>
            <div style="display:inline-block;width:<?php echo ($has_slides) ? '49%' : '100%'; ?>;" class="popup_video_player"
                id="Popup_Player_<?php echo $asset; ?>_cam">
                <?php
                echo '
                <video class="';
                echo ($has_cam) ? "firstVideo" :"";
                echo ' postedit_video" src="/ezmanager/distribute.php?action=media&amp;album='.$album.'&amp;asset='.$asset.'&amp;type=cam&amp;quality=processed&amp;token='.$asset_token.'&amp;origin=embed" type="video/h264" width="100%" height="100%" id="video_cam" controlslist="nodownload">';
                ?>
            </div>
            <?php
            } ?>
            <?php if ($has_slides) {
            ?>
            <div style="display:inline-block;width: <?php echo ($has_cam) ? '49%' : '100%'; ?>;" class="popup_video_player"
                id="Popup_Player_<?php echo $asset; ?>_slide">
                <?php
                echo '
                <video class="';
                echo (!$has_cam) ? "firstVideo" :"";
                echo ' postedit_video" src="/ezmanager/distribute.php?action=media&amp;album='.$album.'&amp;asset='.$asset.'&amp;type=slide&amp;quality=processed&amp;token='.$asset_token.'&amp;origin=embed" type="video/h264" width="100%" height="100%" id="video_slide" muted controlslist="nodownload">
                ';
                ?>
            </div>
            <?php
            } ?>
        </center>
        <center>
            <div class="container-fluid container-relative container-grey">
                <div class="row centered">
                    <div class="col-xs-1 centered">
                        <button type="button" id="btnPlay" class="btn">
                            <i id="btnPlayIcon" class="glyphicon glyphicon-play"></i>
                        </button>
                    </div>
                    <div class="col-xs-10 centered">
                        <div class="container container-relative  container-grey" id="videoSlider-container">
                            <input id="videoSlider" type="text"/><br/>
                        </div>
                    </div>
                    <div class="col-xs-1 centered">
                    </div>
                </div>
            </div>
        </center>
    </div>
    <div class="container container-relative" id="curCutDiv">
        <center>
            <div class="container container-relative">
                <div class="row">
                    <div class="col-sm-2"></div>
                    <label class="col-sm-3" for="cutStart">®Beginning®</label>
                    <div class="col-sm-2"></div>
                    <label class="col-sm-3" for="cutStop">®End®</label>
                    <div class="col-sm-2"></div>
                </div>
                <div class="row">
                    <div class="col-sm-2"></div>
                    <input class="col-sm-3" id="cutStart" type="number">
                    <div class="col-sm-2"></div>
                    <input class="col-sm-3" id="cutStop" type="number">
                    <div class="col-sm-2"></div>
                </div>
            </div>
            <div class="container-fluid container-relative">
                <div class="row">
                    <div class="col-sm-10 centered col-sm-offset-1">
                        <div class="container container-relative" id="cutSlider-container">
                            <input id="cutSlider" type="text" /><br/>
                        </div>
                    </div>
                    <div class="col-sm-1">
                    </div>
                </div>
                <div class="alert alert-danger" id="cutsFusionAlert" style="display:none;">
                    <div class="" id="cutsFusionMessage">
                        <p>®CutFusionWarning®</p>
                    </div>
                    <div class="" id="cutsFusionBtns">
                        <button type="button" name="cutsFusionValid" class="btn" id="cutsFusionValid">®Valid®</button>
                        <button type="button" name="cutsFusionCancel" class="btn" id="cutsFusionCancel">®Cancel®</button>
                    </div>
                </div>
                <div class="row">
                  <div class="col-sm-4 centered col-sm-offset-1">
                      <input class="btn" id="cutPreviewBtn" type="button" value="®Preview®">
                  </div>
                  <div class="col-sm-2">
                  </div>
                  <div class="col-sm-4 centered">
                      <input class="btn" id="cutValid" type="button" value="®CutValid®">
                  </div>
                  <div class="col-sm-1">
                  </div>
                </div>
            </div>
        </center>
    </div>
    <div class="container container-relative" id="cutTableDiv">
        <table id="cutTable" class="table table-striped">
              <thead>
                <tr>
                    <th id="cutNumberTh">®CutNumber®</th>
                    <th id="cutStartTh">®Beginning®</th>
                    <th id="cutStopTh">®End®</th>
                    <th id="cutModTh"></th>
                    <th id="cutDelTh"></th>
                </tr>
              </thead>
          <tbody id="cutTableBody">
          </tbody>
        </table>
    </div>
    <input type="hidden" id="data">
    <input type="hidden" id="preview" value="-1">
    <input type="hidden" id="previewCut" value="0">
    <input type="hidden" id="fusionValue" value="">
    <input type="hidden" id="sesskey" name="sesskey" value="<?php echo $_SESSION['sesskey']; ?>"/>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-default" id="cutlistPreviewBtn">
        ®Preview®
    </button>
    <button type="button" class="btn btn-default" data-dismiss="modal" id="asset_postedit">
        ®Valid®
    </button>
    <button type="button" class="btn btn-default" data-dismiss="modal" id="cancel_postedit">
        ®Cancel®
    </button>
</div>
<script>
//initialisation on modal show to prevent modal hide issues
$("#modal").on('shown.bs.modal',function()
{
    $("#btnPlay").off( 'click' ).on('click', function()
    {
        toggleVideosPlay();
    });
    var allVideoPlayer = document.getElementsByClassName("postedit_video");
    var duration = allVideoPlayer[0].duration;
    var firstCut = [0,duration];
    var array = [];
    if (true)
    {
        //to be fill later when input from already existing cut exist
    }
    var json = initJSON(duration,array,firstCut);
    setSlider(json.duration,json.cutArray,json.curCut);
    setInputsMinMax();
    updateCutTable(json.cutArray);
    cutFusionOtherBtn(true);
    $("#cutsFusionAlert").hide();

    //Events on the cut inputs
    $("#cutStart", "#cutStop").off('change').on('change', function()
    {
        var start = parseFloat($("#cutStart").val());
        var stop = parseFloat($("#cutStop").val());
        if ( !(isNaN(start)) && !(isNaN(stop)) )
        {
            var array=sortInputs(start,stop);
            setInputValue(array);
            setJSONCut(array);
            updateCutSlider(array);
        }else
        {
            var json=JSON.parse($("#data").val());
            setInputValue(json.curCut);
        }
    });
    // $("#cutStop").off('change').on('change', function() {
    //     var start=parseFloat($("#cutStart").val());
    //     var stop=parseFloat($("#cutStop").val());
    //     if ( !(isNaN(start)) && !(isNaN(stop)))
    //     {
    //         var array=sortInputs(start,stop);
    //         setInputValue(array);
    //         setJSONCut(array);
    //         updateCutSlider(array);
    //     }else
    //     {
    //         var json = JSON.parse($("#data").val());
    //         setInputValue(json.curCut);
    //     }
    // });


    //Event on the first video timechange to update the video slider
    // Will jump or pause the video if the preview or cutpreview was clicked
    $(".firstVideo").on('timeupdate',function()
    {

        if (!$(".firstVideo")[0].paused)
        {
            $("#videoSlider").slider('setValue',$(".firstVideo")[0].currentTime,true);
        }
        var json=JSON.parse($("#data").val());
        var allVideoPlayer = document.getElementsByClassName("postedit_video");
        if ($("#previewCut").val()==="1")
        {
            if ($(".firstVideo")[0].paused) {

                for(var i = 0; i < allVideoPlayer.length; i++)
                {
                    var video = allVideoPlayer[i];
                    video.currentTime=json.curCut[0];
                }
                $("#videoSlider").slider('setValue',$(".firstVideo")[0].currentTime,true);
                $("#previewCut").val("0");
            } else {
                if($(".firstVideo")[0].currentTime > json.curCut[0] && $(".firstVideo")[0].currentTime < json.curCut[1])
                {
                    if (json.curCut[1]==json.duration) {

                        for(var i = 0; i < allVideoPlayer.length; i++)
                        {
                            var video = allVideoPlayer[i];
                            video.pause();
                        }
                    } else {

                        for(var i = 0; i < allVideoPlayer.length; i++)
                        {
                            var video = allVideoPlayer[i];
                            video.currentTime=json.curCut[1];
                        }
                    }
                }
                else if ($(".firstVideo")[0].currentTime>(json.curCut[1]+10))
                {

                    for(var i = 0; i < allVideoPlayer.length; i++)
                    {
                        var video = allVideoPlayer[i];
                        video.pause();
                        video.currentTime=json.curCut[0];
                    }
                    $("#previewCut").val("0");
                    $("#btnPlayIcon").attr('class', "glyphicon glyphicon-play");
                    $("#videoSlider").slider('setValue',$(".firstVideo")[0].currentTime,true);
                }
            }
        }

        if ($("#preview").val() != -1 )
        {
            if ($("#preview").val() < json.cutArray.length && !$(".firstVideo")[0].paused && $(".firstVideo")[0].currentTime>(json.cutArray[$("#preview").val()][0])) {
                for(var i = 0; i < allVideoPlayer.length; i++)
                {
                    var video = allVideoPlayer[i];
                    video.currentTime=json.cutArray[$("#preview").val()][1];
                    $("#preview").val(parseInt($("#preview").val())+1);
                }
            } else if ($("#preview").val() >= json.cutArray.length) {
                $("#preview").val(-1);
            }
        }
    });
    // Event on the video slider to update the video(s) current time
    $("#videoSlider").off('change').on('change', function(e)
    {
        var newTime=$("#videoSlider").slider('getValue');
        var allVideoPlayer = document.getElementsByClassName("postedit_video");
        if (!allVideoPlayer[0].paused)
        {
            for(var i = 0; i < allVideoPlayer.length; i++)
            {
                var video = allVideoPlayer[i];
                video.pause();
            }
            $("#btnPlayIcon").attr('class', "glyphicon glyphicon-play");
        }
        for(var i = 0; i < allVideoPlayer.length; i++)
        {
            var video = allVideoPlayer[i];
            video.currentTime=newTime;
        }
    });
    //Event on the cutTable Buttons
    //
    //will set the current cut to the cut clicked and delete the cut from the table
    $("#cutTable").off('click').on('click','.modBtn',function(event)
    {
        var json=JSON.parse($("#data").val());
        var index=parseInt(this.id.substring(6 , this.id.length));
        var tArray=json.cutArray;
        json.curCut=json.cutArray[index];
        tArray.splice(index,1);
        json.cutArray=tArray;
        updateFromJson(json);
        updateCutTable(json.cutArray);
    })
    //
    //will delete the cut from the table
    .on('click','.delBtn',function(event)
    {
        var json=JSON.parse($("#data").val());
        var index=parseInt(this.id.substring(6,7));
        var tArray=json.cutArray;
        tArray.splice(index,1);
        json.cutArray=tArray;
        updateFromJson(json);
        updateCutTable(json.cutArray);
    });


    //Event on the cut preview Button
    //set the preview tag on 1 then start the player(s)
    $("#cutPreviewBtn").off('click').on('click', function()
    {
        var allVideoPlayer = document.getElementsByClassName("postedit_video");
        if ($("#previewCut").val() == 0 && !allVideoPlayer[0].paused) {
            for (var i = 0; i < allVideoPlayer.length; i++)
            {
                var video = allVideoPlayer[i];
                video.pause();
            }
        }
        $("#previewCut").val("1");
        var json=JSON.parse($("#data").val());
        if (allVideoPlayer[0].paused)
        {
            var start;
            if (json.curCut[0]==0) {
                var start=json.curCut[1];
            }else {
                var start=(json.curCut[0]-5);
                if (start<0)
                {
                    start=0;
                }
            }
            for(var i = 0; i < allVideoPlayer.length; i++)
            {
                var video = allVideoPlayer[i];
                video.currentTime=start;
                video.play();
            }
            $("#btnPlayIcon").attr('class', "glyphicon glyphicon-pause");
        } else
        {
            for (var i = 0; i < allVideoPlayer.length; i++)
            {
                var video = allVideoPlayer[i];
                video.pause();
                video.currentTime=json.curCut[0];
            }
            $("#btnPlayIcon").attr('class', "glyphicon glyphicon-play");
            $("#previewCut").val("0");
        }
    });
    //Event on the cuts preview Button
    //set the preview tag on 1 then start the player(s)
    $("#cutlistPreviewBtn").off('click').on('click', function()
    {
        var allVideoPlayer = document.getElementsByClassName("postedit_video");
        if ($("#preview").val() == -1 && !allVideoPlayer[0].paused) {
            for (var i = 0; i < allVideoPlayer.length; i++)
            {
                var video = allVideoPlayer[i];
                video.pause();
            }
        }
        $("#preview").val("0");
        var json=JSON.parse($("#data").val());
        if (allVideoPlayer[0].paused)
        {
            var start;
            if (json.cutArray[0][0]==0) {
                var start=json.cutArray[0][1];
            }else {
                var start=(json.cutArray[0][0]-5);
                if (start<0)
                {
                    start=0;
                }
            }
            for(var i = 0; i < allVideoPlayer.length; i++)
            {
                var video = allVideoPlayer[i];
                video.currentTime=start;
                video.play();
            }
            $("#btnPlayIcon").attr('class', "glyphicon glyphicon-pause");
        } else
        {
            for (var i = 0; i < allVideoPlayer.length; i++)
            {
                var video = allVideoPlayer[i];
                video.pause();
            }
            $("#btnPlayIcon").attr('class', "glyphicon glyphicon-play");
            $("#preview").val("-1");
        }
    });

    //test the cut and insert it in the cuttable and the cutarray in the right position
    $("#cutValid").off('click').on('click', function()
    {
        var test = false;
        var intersectedCut=[];
        var json=JSON.parse($("#data").val());
        //testing integrity of the current cut before insertion
        if ((json.curCut[0] != json.curCut[1])
            && !(isNaN(json.curCut[0]))
            && !(isNaN(json.curCut[1]))
            && (typeof json.curCut[0] != undefined)
            && (typeof json.curCut[1] != undefined)
            && (json.curCut[0]!=null)
            && (json.curCut[1]!=null))
        {
            //counting the number of intersected cuts
            var tArray=[];
            for (var i = 0; i < json.cutArray.length; i++)
            {
                if ((json.curCut[0] >= json.cutArray[i][0]
                        && json.curCut[0] <= json.cutArray[i][1])
                    || (json.curCut[1] >= json.cutArray[i][0]
                        && json.curCut[1] <= json.cutArray[i][1])
                    || (json.curCut[0] <= json.cutArray[i][0]
                        &&json.curCut[1] >= json.cutArray[i][1]))
                {
                    intersectedCut.push(i);
                }
            }
            //calling the validation alert if there is at least one intersected cut
            if (intersectedCut.length!=0)
            {
                var fusionJson=JSON.parse('{"cutFusionArray":[]}');
                for (var i = 0; i < intersectedCut.length; i++)
                {
                    var curTArray=[intersectedCut[i],
                        json.cutArray[intersectedCut[i]][0],
                        json.cutArray[intersectedCut[i]][1]];
                    fusionJson.cutFusionArray.push(curTArray);
                }
                $("#fusionValue").val(JSON.stringify(fusionJson));
                $("#cutsFusionAlert").show();
                cutFusionOtherBtn(false);
            //inserting the current cut in the correct position in the cut Array
            }else{
                var inserted = false;
                for (var i = 0; i < json.cutArray.length; i++)
                {
                    if ((json.curCut[0] < json.cutArray[i][0])
                        && !inserted)
                    {
                        tArray.push(json.curCut);
                        inserted=true;
                    }
                    tArray.push(json.cutArray[i]);
                }
                if (!inserted)
                {
                    tArray.push(json.curCut);
                }
                json.cutArray=tArray;
                json.curCut=[0,json.duration];
                updateFromJson(json);
                updateCutTable(json.cutArray);
            }
        }
    });
    //fire change on cutslider event on the video and the curCut input
    $("#cutSlider-container").off('slide change').on('slide change','#cutSlider', function()
    {
        var allVideoPlayer = document.getElementsByClassName("postedit_video");
        //test if what cursor was moved then update videos current time
        if ($("#cutSlider").slider('getValue')[0] != $("#cutStart").val())
        {
            for(var i = 0; i < allVideoPlayer.length; i++)
            {
                var video = allVideoPlayer[i];
                video.currentTime = $("#cutSlider").slider('getValue')[0];
            }
        }else if ($("#cutSlider").slider('getValue')[1] != $("#cutStop").val())
        {
            for(var i = 0; i < allVideoPlayer.length; i++)
            {
                var video = allVideoPlayer[i];
                video.currentTime = $("#cutSlider").slider('getValue')[1];
            }
        }
        //update the current cut input
        setInputValue($("#cutSlider").slider('getValue'));
        setJSONCut($("#cutSlider").slider('getValue'));
        $("#videoSlider").slider('setValue',$(".firstVideo")[0].currentTime,true);
    });

    //valid button on the cut fusion validation alert
    $("#cutsFusionValid").off('click').on('click' , function(event)
    {
        var json = JSON.parse($("#data").val());
        var fusionJson = JSON.parse($("#fusionValue").val());
        var tArray = [];
        for (var i = 0; i < fusionJson.cutFusionArray[0][0]; i++)
        {
            tArray.push(json.cutArray[i]);
        }
        var cutToValid=[];
        if (json.curCut[0] < json.cutArray[fusionJson.cutFusionArray[0][0]][0])
        {
            cutToValid.push(json.curCut[0]);
        }else{
            cutToValid.push(json.cutArray[fusionJson.cutFusionArray[0][0]][0]);
        }
        if (json.curCut[1] > json.cutArray[fusionJson.cutFusionArray[(fusionJson.cutFusionArray.length-1)][0]][1])
        {
            cutToValid.push(json.curCut[1]);
        } else {
            cutToValid.push(json.cutArray[fusionJson.cutFusionArray[(fusionJson.cutFusionArray.length-1)][0]][1]);
        }
        tArray.push(cutToValid);
        for (var i = (fusionJson.cutFusionArray[(fusionJson.cutFusionArray.length-1)][0]+1); i < json.cutArray.length; i++)
        {
            tArray.push(json.cutArray[i]);
        }
        json.cutArray=tArray;
        json.curCut=[0,json.duration]
        updateFromJson(json);
        updateCutTable(json.cutArray);
        $("#cutsFusionAlert").hide();
        cutFusionOtherBtn(true);
    });

    //close the cut fusion alert
    $("#cutsFusionCancel").off('click').on('click' , function(event)
    {
        $("#cutsFusionAlert").hide();
        cutFusionOtherBtn(true);
    });

    //submit button
    $('#asset_postedit').off('click').on('click', function(event)
    {
        submit_postedit_form();
    })

    $("#modal").off('hidden.bs.modal').on('hidden.bs.modal',function()
    {
        var allVideoPlayer = document.getElementsByClassName("postedit_video");
        if (!allVideoPlayer[0].paused)
        {
            for(var i = 0; i < allVideoPlayer.length; i++)
            {
                var video = allVideoPlayer[i];
                video.pause();
            }
        }
    });

    //disable or enable buttons on the cut fusion alert show
    function cutFusionOtherBtn(action) {
        $('#btnPlay').prop('disabled', !action);
        $('#cutStart').prop('disabled', !action);
        $('#cutStop').prop('disabled', !action);
        $('#cutPreviewBtn').prop('disabled', !action);
        $('#cutValid').prop('disabled', !action);
        $('.modBtn').prop('disabled', !action);
        $('.delBtn').prop('disabled', !action);
        $('#asset_postedit').prop('disabled', !action);
        $('#cancel_postedit').prop('disabled', !action);
        if (action)
        {
            $("#cutSlider").slider("enable");
            $("#videoSlider").slider("enable");
        }else
        {
            $("#cutSlider").slider("disable");
            $("#videoSlider").slider("disable");
        }

    }

    //togle the video players and show the right icon
    function toggleVideosPlay()
    {
        var allVideoPlayer = document.getElementsByClassName("postedit_video");
        if (allVideoPlayer[0].paused)
        {
            for(var i = 0; i < allVideoPlayer.length; i++)
            {
                var video = allVideoPlayer[i];
                video.play();
            }
            $("#btnPlayIcon").attr('class', "glyphicon glyphicon-pause");
        }else
        {
            for(var i = 0; i < allVideoPlayer.length; i++)
            {
                var video = allVideoPlayer[i];
                video.pause();
            }
            $("#btnPlayIcon").attr('class', "glyphicon glyphicon-play");
        }
    }
    //json hidden input initialization
    function initJSON(duration,array,curCut)
    {
        var json=JSON.parse('{"duration":'+duration+',"cutArray":[],"curCut":['+curCut[0]+','+curCut[1]+']}');
        for (var i = 0; i < array.length; i++)
        {
            json.cutArray.push(array[i]);
        }
        myJson=JSON.stringify(json);
        $("#data").val(myJson);
        return json;
    }

    //init the 2 slider
    function setSlider(duration,array,cut)
    {
        $("#cutSlider").slider(
        {
            id: "cutSliderSlider",
            min: 0,
            max: duration,
            range: true,
            step: 0.01,
            value: [cut[0],cut[1]],
            rangeHighlights: updateCutSliderBackground(array)
        });
        $("#videoSlider").slider(
        {
            id: "videoSliderSlider",
            class: "container-grey",
            min: 0,
            max: duration,
            step: 0.01,
            value: 0
        });
    }

    function setInputValue(array)
    {
        $("#cutStart").val(array[0]);
        $("#cutStop").val(array[1]);
    }

    function setInputsMinMax()
    {
        var json = JSON.parse($("#data").val());
        $("#cutStart").attr({
           "max" : json.curCut[1],
           "min" : 0
        });
        $("#cutStop").attr({
           "max" : json.duration,
           "min" : json.curCut[0]
        });
    }

    function sortInputs(start,stop)
    {
        if (start<=stop)
        {
            var array = [start,stop];
        }else {
            var array = [stop,start];
        }
        return array;
    }

    function setJSONCut(array)
    {
        var json=JSON.parse($("#data").val());
        json.curCut[0] = array[0];
        json.curCut[1] = array[1];
        myJson = JSON.stringify(json);
        $("#data").val(myJson);
    }

    function updateCutSlider(array)
    {
        $("#cutSlider").slider('setValue',array,true);
    }

    function updateCutSliderBackground(array)
    {
        var tArray=[];
        for (var i = 0; i < array.length; i++)
        {
            var json =
            {
                "start":array[i][0],
                "end":array[i][1]
            };
            tArray.push(json);
        }
        return tArray;
    }
    //empty and redraw the cut table with the data cutarray input
    function updateCutTable(array)
    {
        $("#cutTableBody").empty();
        for(i=0;i<array.length;i++)
        {
          $("#cutTableBody").append("<tr><td>"+(i+1)+"</td><td>"+array[i][0]+"</td><td>"+array[i][1]+"</td><td><button type='button' id='modBtn"+i+"' class='btn modBtn'><i class='glyphicon glyphicon-edit'></i></button></td><td><button type='button' id='delBtn"+i+"' class='btn delBtn'><i class='glyphicon glyphicon-remove-sign'></i></button></td></tr>");
        }
    }

    // function updateFusionTable(curCutArray,cutFusionArray)
    // {
    //     $("#curCutBody").empty();
    //     $("#cutFusionBody").empty();
    //     $("#curCutBody").append("<tr><td>current cut</td><td>"+curCutArray[0]+"</td><td>"+curCutArray[1]+"</td></tr>");
    //     for(i=0;i<cutFusionArray.length;i++)
    //     {
    //         var cutNb=((cutFusionArray[i][0])+1);
    //         $("#cutFusionBody").append("<tr><td>"+cutNb+"</td><td>"+cutFusionArray[i][1]+"</td><td>"+cutFusionArray[i][2]+"</td></tr>");
    //     }
    // }

    function updateFromJson(json)
    {
        myJson=JSON.stringify(json);
        $("#data").val(myJson);
        setInputValue(json.curCut);
        $("#cutSlider").slider('destroy');
        setSlider(json.duration,json.cutArray,json.curCut);
    }

    function submit_postedit_form()
    {
        var json=JSON.parse('{"cutArray":[]}');
        var cutArray=(JSON.parse($("#data").val())).cutArray;
        for (var i = 0; i < cutArray.length; i++) {
            json.cutArray.push(cutArray[i]);
        }
        myJson=encodeURIComponent(JSON.stringify(json));
        $('#modal').modal('hide');
        display_bootstrap_modal_url($('#modal'), 'index.php?action=submit_postedit&album=<?php echo $album; ?>&asset=<?php echo $asset_name; ?>&sesskey=<?php echo $_SESSION['sesskey']; ?>&cutArray='+myJson);
        setTimeout(function()
        {
            refresh_album_view();
        }, 100);

    }
});
</script>
