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
                  echo '" src="/ezmanager/distribute.php?action=media&amp;album='.$album.'&amp;asset='.$asset.'&amp;type=cam&amp;quality=processed&amp;token='.$asset_token.'&amp;origin=embed" type="video/h264" width="100%" height="100%" id="video_cam" controlslist="nodownload">
                  ';
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
              echo '" src="/ezmanager/distribute.php?action=media&amp;album='.$album.'&amp;asset='.$asset.'&amp;type=slide&amp;quality=processed&amp;token='.$asset_token.'&amp;origin=embed" type="video/h264" width="100%" height="100%" id="video_slide" muted controlslist="nodownload">
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
                <label class="col-sm-3" for="cutStart">debut</label>
                <div class="col-sm-2"></div>
                <label class="col-sm-3" for="cutStop">fin</label>
                <div class="col-sm-2"></div>
            </div>
            <div class="row">
                <div class="col-sm-2"></div>
                <input class="col-sm-3" id="cutStart" type="number" name="" value="">
                <div class="col-sm-2"></div>
                <input class="col-sm-3" id="cutStop" type="number" name="" value="">
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
            <div class="row">
              <div class="col-sm-4 centered col-sm-offset-1">
                  <input class="btn" id="cutPreviewBtn" type="button" name="" value="preview">
              </div>
              <div class="col-sm-2">

              </div>
              <div class="col-sm-4 centered">
                  <input class="btn" id="cutValid" type="button" name="" value="valider la coupure">
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
          <th id="cutNumberTh">cutNumber</th>
          <th id="cutStartTh">Start</th>
          <th id="cutStopTh">end</th>
          <th id="cutModTh"></th>
          <th id="cutDelTh"></th>
        </tr>
      </thead>
      <tbody id="cutTableBody">

      </tbody>
    </table>
</div>
<input type="hidden" id="data">
<input type="hidden" id="preview" value="0">
<input type="hidden" id="fusionValue" value="">
<input type="hidden" id="sesskey" name="sesskey" value="<?php echo $_SESSION['sesskey']; ?>"/>
<div class="modal-footer">
    <a class="btn btn-default" onclick="submit_postedit_form()" data-dismiss="modal" id="asset_postedit">
        ®OK®
    </a>
    <button type="button" class="btn btn-default" data-dismiss="modal">
        ®Cancel®
    </button>
</div>
<div class="modal fade" id="cutsFusionModal" tabindex="-1" role="dialog" aria-labelledby="cutsFusionModal" aria-hidden="true">
    <div class="modal-dialog" id="cutsFusionModal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" aria-hidden="true">&times;</button>
                <h4 class="modal-title" id=""></h4>
            </div>
            <div class="modal-body">
                <div class="container container-relative">
                    <div class="row centered">
                        <p>Vous etes sur le point de fusionner</p>
                    </div>
                    <div class="row centered">
                        <table class="table table-striped">
                            <thead>
                              <tr>
                                <th class="cutsFusionNbTh">cutNumber</th>
                                <th class="cutsFusionStartTh">Start</th>
                                <th class="cutsFusionStopTh">end</th>
                              </tr>
                            </thead>
                            <tbody id="curCutBody">
                            </tbody>
                        </table>
                    </div>
                    <div class="row centered">
                        <p>Avec les coupures deja existantes suivantes</p>
                    </div>
                    <div class="row centered">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th class="cutsFusionNbTh">cutNumber</th>
                                    <th class="cutsFusionStartTh">Start</th>
                                    <th class="cutsFusionStopTh">end</th>
                                </tr>
                            </thead>
                            <tbody id="cutFusionBody">
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="modal-footer centered">
              <button type="button" class="btn btn-warning" id="cutsFusionValid">Validate</button>
              <button type="button" class="btn" id="cutsFusionCancel">Close</button>
            </div>
        </div>
    </div>
</div>
<script>

(function()
    {
        //initialisation
        var videos=document.getElementsByTagName("video");
        var video=videos[0];
        video.addEventListener('loadedmetadata',function()
        {
            var allVideoPlayer = document.getElementsByTagName("video");
            var duration=allVideoPlayer[0].duration;
            var firstCut=[0,duration]
            var array=[];
            if (true)
            {
                //to be fill later when input from already existing cut exist
            }
            initJSON(duration,array,firstCut);
            var json=JSON.parse($("#data").val());
            initSlider(json.duration,json.cutArray,json.curCut);
            setInputsMinMax();
            updateCutTable(json.cutArray);
            // in case of the modal reloading
            $("body").on('shown.bs.modal','#modal',function(){
                initSlider(json.duration,json.cutArray,json.curCut);
            });
        //end initialisation
        });

        //Events on the cut inputs

        $("#cutStart").on('change', function()
        {
            var start = parseFloat($("#cutStart").val());
            var stop = parseFloat($("#cutStop").val());
            if ( !(isNaN(start)) && !(isNaN(stop)) )
            {
                var array=sortInputs(start,stop);
                setInputValue(array);
                setJSONCut(array);
                updateCutSlider(array);
            }else{
                var json=JSON.parse($("#data").val());
                setInputValue(json.curCut);
            }
        });

        $("#cutStop").on('change', function() {
            var start=parseFloat($("#cutStart").val());
            var stop=parseFloat($("#cutStop").val());
            if ( !(isNaN(start)) && !(isNaN(stop)))
            {
                var array=sortInputs(start,stop);
                setInputValue(array);
                setJSONCut(array);
                updateCutSlider(array);
            }else{
                var json = JSON.parse($("#data").val());
                setInputValue(json.curCut);
            }
        });

        //Events on the Cut Slider

        $("#btnPlay").on('click', function()
        {
            var allVideoPlayer = document.getElementsByTagName("video");
            if (allVideoPlayer[0].paused)
            {
                for(var i = 0; i < allVideoPlayer.length; i++)
                {
                    var video = allVideoPlayer[i];
                    video.play();
                }
                $("#btnPlayIcon").attr('class', "glyphicon glyphicon-pause");
            }else {
                for(var i = 0; i < allVideoPlayer.length; i++)
                {
                    var video = allVideoPlayer[i];
                    video.pause();
                }
                $("#btnPlayIcon").attr('class', "glyphicon glyphicon-play");
            }
        });

        //Event on the first video timechange

        $(".firstVideo").on('timeupdate',function()
        {
            if (!$(".firstVideo")[0].paused)
            {
                $("#videoSlider").slider('setValue',$(".firstVideo")[0].currentTime,true);
            }else {
                if ($("#preview").val()==="1")
                {
                    var json=JSON.parse($("#data").val());
                    var allVideoPlayer = document.getElementsByTagName("video");
                    for(var i = 0; i < allVideoPlayer.length; i++)
                    {
                        var video = allVideoPlayer[i];
                        video.currentTime=json.curCut[0];
                    }
                    $("#videoSlider").slider('setValue',$(".firstVideo")[0].currentTime,true);
                    $("#preview").val("0");
                }
            }

            if ($("#preview").val()==="1")
            {
                var json=JSON.parse($("#data").val());
                if($(".firstVideo")[0].currentTime>json.curCut[0]&&$(".firstVideo")[0].currentTime<json.curCut[1])
                {
                    var allVideoPlayer = document.getElementsByTagName("video");
                    for(var i = 0; i < allVideoPlayer.length; i++)
                    {
                        var video = allVideoPlayer[i];
                        video.currentTime=json.curCut[1];
                    }
                }
                else if ($(".firstVideo")[0].currentTime>(json.curCut[1]+10))
                {
                    var allVideoPlayer = document.getElementsByTagName("video");
                    for(var i = 0; i < allVideoPlayer.length; i++)
                    {
                        var video = allVideoPlayer[i];
                        video.pause();
                        video.currentTime=json.curCut[0];
                    }
                    $("#preview").val("0");
                    $("#videoSlider").slider('setValue',$(".firstVideo")[0].currentTime,true);
                }
            }
        });

        // Event on the video slider

        $("#videoSlider").on('change', function(e)
        {
            var newTime=$("#videoSlider").slider('getValue');
            var allVideoPlayer = document.getElementsByTagName("video");
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

        $("#cutTable").on('click','.modBtn',function(event)
        {
            var json=JSON.parse($("#data").val());
            var index=parseInt(this.id.substring(6,7));
            var tArray=json.cutArray;
            json.curCut=json.cutArray[this.id.substring(6,7)];
            tArray.splice(index,1);
            json.cutArray=tArray;
            updateFromJson(json);
            updateCutTable(json.cutArray);
        })
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

        //Event on the preview Button

        $("#cutPreviewBtn").on('click', function()
        {
            $("#preview").val("1");
            var json=JSON.parse($("#data").val());
            var allVideoPlayer = document.getElementsByTagName("video");
            if (allVideoPlayer[0].paused)
            {
                var start=(json.curCut[0]-5);
                if (start<0)
                {
                    start=0;
                }
                for(var i = 0; i < allVideoPlayer.length; i++)
                {
                    var video = allVideoPlayer[i];
                    video.currentTime=start;
                    video.play();
                }
            } else {
                for (var i = 0; i < allVideoPlayer.length; i++)
                {
                    var video = allVideoPlayer[i];
                    video.pause();
                    video.currentTime=json.curCut[0];
                }
                $("#preview").val("0");
            }
        });

        //Show the fusion confirmation modal

        $("#cutsFusionModal").on('shown.bs.modal', function(event)
        {
            var json=JSON.parse($("#data").val());
            var fusionJson=JSON.parse($("#fusionValue").val());
            updateFusionTable(json.curCut,fusionJson.cutFusionArray);
        });

        //test the cut and insert it in the cuttable and the cutarray in the right position

        $("#cutValid").on('click', function()
        {
            var test = false;
            var intersectedCut=[];
            var json=JSON.parse($("#data").val());
            if ((json.curCut[0]!=json.curCut[1])&&!(isNaN(json.curCut[0]))&&!(isNaN(json.curCut[0]))&&(typeof json.curCut[0]!=undefined)&&(typeof json.curCut[1]!=undefined)&&(json.curCut[0]!=null)&&(json.curCut[1]!=null))
            {
                var tArray=[];
                for (var i = 0; i < json.cutArray.length; i++)
                {
                    if ((json.curCut[0]>=json.cutArray[i][0]&&json.curCut[0]<=json.cutArray[i][1])||(json.curCut[1]>=json.cutArray[i][0]&&json.curCut[1]<=json.cutArray[i][1])||(json.curCut[0]<=json.cutArray[i][0]&&json.curCut[1]>=json.cutArray[i][1]))
                    {
                        intersectedCut.push(i);
                    }
                }
                if (intersectedCut.length!=0)
                {
                    var fusionJson=JSON.parse('{"cutFusionArray":[]}');
                    for (var i = 0; i < intersectedCut.length; i++)
                    {
                        var curTArray=[intersectedCut[i],json.cutArray[intersectedCut[i]][0],json.cutArray[intersectedCut[i]][1]];
                        fusionJson.cutFusionArray.push(curTArray);
                    }
                    $("#fusionValue").val(JSON.stringify(fusionJson));
                    $("#cutsFusionModal").modal();
                }else{
                    var inserted = false;
                    for (var i = 0; i < json.cutArray.length; i++)
                    {
                        if ((json.curCut[0]<json.cutArray[i][0])&&!inserted)
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

        $("#cutSlider-container").on('slide change','#cutSlider', function()
        {
            var allVideoPlayer = document.getElementsByTagName("video");
            if ($("#cutSlider").slider('getValue')[0]!=$("#cutStart").val())
            {
                for(var i = 0; i < allVideoPlayer.length; i++)
                {
                    var video = allVideoPlayer[i];
                    video.currentTime=$("#cutSlider").slider('getValue')[0];
                }
            }else if ($("#cutSlider").slider('getValue')[1]!=$("#cutStop").val())
            {
                for(var i = 0; i < allVideoPlayer.length; i++)
                {
                    var video = allVideoPlayer[i];
                    video.currentTime=$("#cutSlider").slider('getValue')[1];
                }
            }
            setInputValue($("#cutSlider").slider('getValue'));
            setJSONCut($("#cutSlider").slider('getValue'));
            $("#videoSlider").slider('setValue',$(".firstVideo")[0].currentTime,true);
        });

        //cancel button on the cut fusion validation modal

        $("#cutsFusionModal").on('click', '.close', function(event)
        {
            $("#cutsFusionModal").modal('hide');

        }).on('click', '#cutsFusionCancel', function(event)
        {
            $("#cutsFusionModal").modal('hide');

        //validate the cut fusion on the cut fusion modal and put make the needed modification to the cutTable and array

        }).on('click', '#cutsFusionValid', function(event)
        {
            var json=JSON.parse($("#data").val());
            var fusionJson=JSON.parse($("#fusionValue").val());
            var tArray=[];
            for (var i = 0; i < fusionJson.cutFusionArray[0][0]; i++)
            {
                tArray.push(json.cutArray[i]);
            }
            var cutToValid=[];
            if (json.curCut[0]<json.cutArray[fusionJson.cutFusionArray[0][0]][0])
            {
                cutToValid.push(json.curCut[0]);
            }else{
                cutToValid.push(json.cutArray[fusionJson.cutFusionArray[0][0]][0]);
            }

            if (json.curCut[1]>json.cutArray[fusionJson.cutFusionArray[(fusionJson.cutFusionArray.length-1)][0]][1])
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
            $("#cutsFusionModal").modal('hide');
        })
    })();

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
    }

    //init the 2 slider
    function initSlider(duration,array,cut)
    {
        $("#cutSlider").slider({ id: "cutSliderSlider",  min: 0, max: duration, range: true, step: 0.01, value: [cut[0],cut[1]],rangeHighlights: updateCutSliderBackground(array) });
        $("#videoSlider").slider({ id: "videoSliderSlider", class: "container-grey", min: 0, max: duration, step: 0.01, value: 0});
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
            var json = {
                "start":array[i][0],
                "end":array[i][1]
            };
            tArray.push(json);
        }
        return tArray;
    }

    function updateCutTable(array,tableField)
    {
        $("#cutTableBody").empty();
        for(i=0;i<array.length;i++)
        {
          var cutNb=(i+1);
          $("#cutTableBody").append("<tr><td>"+cutNb+"</td><td>"+array[i][0]+"</td><td>"+array[i][1]+"</td><td><button type='button' id='modBtn"+i+"' class='btn modBtn'><i class='glyphicon glyphicon-edit'></i></button></td><td><button type='button' id='delBtn"+i+"' class='btn delBtn'><i class='glyphicon glyphicon-remove-sign'></i></button></td></tr>");
        }
    }

    function updateFusionTable(curCutArray,cutFusionArray)
    {
        $("#curCutBody").empty();
        $("#cutFusionBody").empty();
        $("#curCutBody").append("<tr><td>current cut</td><td>"+curCutArray[0]+"</td><td>"+curCutArray[1]+"</td></tr>");
        for(i=0;i<cutFusionArray.length;i++)
        {
            var cutNb=((cutFusionArray[i][0])+1);
            $("#cutFusionBody").append("<tr><td>"+cutNb+"</td><td>"+cutFusionArray[i][1]+"</td><td>"+cutFusionArray[i][2]+"</td></tr>");
        }
    }

    function updateFromJson(json)
    {
        myJson=JSON.stringify(json);
        $("#data").val(myJson);
        setInputValue(json.curCut);
        $("#cutSlider").slider('destroy');
        initSlider(json.duration,json.cutArray,json.curCut);
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
        setTimeout(function()
        {
            display_bootstrap_modal_url($('#modal'), 'index.php?action=submit_postedit&album=<?php echo $album; ?>&asset=<?php echo $asset_name; ?>&sesskey=<?php echo $_SESSION['sesskey']; ?>&cutArray='+myJson);
        }, 500);
    }

</script>
