<div class="modal-header">
  <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
  <h4 class="modal-title">®Postedit_video_title®</h4>
</div>
<div class="modal-body" id="myModal">

  <div id="container" style="margin: 0 auto"></div>
  <div class="container container-relative container-grey" id="videoDiv">
      <center>
          <div style="display:inline-block;width:<?php echo ($has_slides) ? '49%' : '100%'; ?>;" class="popup_video_player"
              id="Popup_Player_<?php echo $asset; ?>_cam">
              <?php
              echo '
              <video src="/ezmanager/distribute.php?action=media&amp;album='.$album.'&amp;asset='.$asset.'&amp;type=cam&amp;quality=low&amp;token='.$asset_token.'&amp;origin=embed" type="video/h264" width="100%" height="100%" id="video_cam" controlslist="nodownload">
              ';
              ?>
          </div>
          <?php if ($has_slides) {
              ?>
              <div style="display:inline-block;width: 49%;" class="popup_video_player"
              id="Popup_Player_<?php echo $asset; ?>_slide">
              <?php
              echo '
              <video src="/ezmanager/distribute.php?action=media&amp;album='.$album.'&amp;asset='.$asset.'&amp;type=slide&amp;quality=low&amp;token='.$asset_token.'&amp;origin=embed" type="video/h264" width="100%" height="100%" id="video_slide" muted controlslist="nodownload">
              ';
              ?>
          </div>
          <?php
      } ?>
    </center>
    <center>
        <div class="container-fluid container-relative container-grey">
            <div class="row centered">
                <div class="col-sm-1 centered">
                    <button type="button" id="btnPlay" class="btn">
                        <i id="btnPlayIcon" class="glyphicon glyphicon-play"></i>
                    </button>
                </div>
                <div class="col-sm-10 centered">

                    <div class="container container-relative  container-grey" id="videoSlider-container">
                        <input id="videoSlider" type="text"/><br/>
                    </div>
                </div>
                <div class="col-sm-1 centered">
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
                <div class="col-sm-1">

                </div>
                <div class="col-sm-10 centered">
                    <div class="container container-relative" id="cutSlider-container">
                        <input id="cutSlider" type="text"/><br/>
                    </div>
                </div>
                <div class="col-sm-1">

                </div>
            </div>
            <div class="row">
              <div class="col-sm-1">

              </div>
              <div class="col-sm-4 centered">
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

            <input type="button" class="btn" name="" value="test this shit" onclick="printJSON();">

        </div>
    </center>
</div>
<div class="container container-relative" id="cutTableDiv">

    <table id="cutTable" class="table table-striped">
      <thead>
        <tr>
          <th>cutNumber</th>
          <th>Start</th>
          <th>end</th>
          <th></th>
        </tr>
      </thead>
      <tbody id="cutTableBody">

      </tbody>
    </table>

</div>
<input type="hidden" id="data">
<div class="modal-footer">
  <button type="button" class="btn btn-default" data-dismiss="modal">®Update®</button>
  <button type="button" class="btn btn-default" data-dismiss="modal">®Cancel®</button>
</div>


<script>
//video INIT
var testArray=[[10.4,25.87],[45.34,105.43]];
var occ=0;
(function() {
    console.log("document ready passage");

    //initialisation

        var videos=document.getElementsByTagName("video");
        console.log(videos);
        var video=videos[0];
        video.addEventListener('loadedmetadata',function() {
            var allVideoPlayer = document.getElementsByTagName("video");
            var duration=allVideoPlayer[0].duration;
            initJSON(duration,testArray);
            var json=JSON.parse($("#data").val());
            initSlider();
            setInputsMinMax();
            initTable(json.cutArray);
    //end initialisation
        });
    //console.log(slider);
    $("#cutSlider").on('slide change', function()
    {
        console.log($("#cutSlider").slider('getValue'));
        setInputValue($("#cutSlider").slider('getValue'));
        setJSONCut($("#cutSlider").slider('getValue'));
    });

    $("#cutStart").on('change', function() {
        var array=sortInputs(parseFloat($("#cutStart").val()),parseFloat($("#cutStop").val()));
        setInputValue(array);
        console.log(array);
        setJSONCut(array);
        updateCutSlider(array);
    });

    $("#cutStop").on('change', function() {
        var array=sortInputs(parseFloat($("#cutStart").val()),parseFloat($("#cutStop").val()));
        setInputValue(array);
        console.log(array);
        setJSONCut(array);
        updateCutSlider(array);
    });

    $("#btnPlay").on('click', function() {
        console.log("click button pressed");
        var allVideoPlayer = document.getElementsByTagName("video");
        if (allVideoPlayer[0].paused) {
            for(var i = 0; i < allVideoPlayer.length; i++) {
                var video = allVideoPlayer[i];
                video.play();
             }
             $("#btnPlayIcon").attr('class', "glyphicon glyphicon-pause");

        }else {
            for(var i = 0; i < allVideoPlayer.length; i++) {
                var video = allVideoPlayer[i];
                video.pause();
             }
             $("#btnPlayIcon").attr('class', "glyphicon glyphicon-play");
        }
    });

    $("#video_cam").on('timeupdate',function() {
        console.log("appel video.timeupdate ");
        if (!$("#video_cam")[0].paused) {
            $("#videoSlider").slider('setValue',$("#video_cam")[0].currentTime,true);
        }

    });
    $("#videoSlider").on('change', function(e)
    {
        console.log(e);
        console.log("appel videoSlider.change");
        var newTime=$("#videoSlider").slider('getValue');
        var allVideoPlayer = document.getElementsByTagName("video");
        if (!allVideoPlayer[0].paused) {
            for(var i = 0; i < allVideoPlayer.length; i++) {
                var video = allVideoPlayer[i];
                video.pause();
             }
             $("#btnPlayIcon").attr('class', "glyphicon glyphicon-play");
        }
        for(var i = 0; i < allVideoPlayer.length; i++) {
            var video = allVideoPlayer[i];
            console.log($("#videoSlider").slider('getValue'));
            video.currentTime=newTime;
         }
    });
    $("#cutPreviewBtn").on('click', function() {
        var json=JSON.parse($("#data").val());
        var prevTime=$("#video_cam")[0].currentTime;
        var allVideoPlayer = document.getElementsByTagName("video");
        console.log(allVideoPlayer);
        if (allVideoPlayer[0].paused) {
            for(var i = 0; i < allVideoPlayer.length; i++) {
                console.log(allVideoPlayer[i]);
                console.log(json.curCut[0]);
                console.log((json.curCut[0]-5));
                var video = allVideoPlayer[i];
                console.log(video);
                video.currentTime=(json.curCut[0]-5);
                video.play();
             }
             while ($("#video_cam")[0].currentTime<json.curCut[0]) {
                 console.log(video.currentTime+" "+json.curCut[0]);
                setTimeout(1000);
             }
             for(var i = 0; i < allVideoPlayer.length; i++) {
                 var video = allVideoPlayer[i];
                 video.pause();
                 video.currentTime=json.curCut[1];
                 video.play();
              }
              // while ($("#video_cam")[0].currentTime<json.curCut[1]+10) {
              //     setTimeout(1000);
              // }
              for(var i = 0; i < allVideoPlayer.length; i++) {
                var video = allVideoPlayer[i];
                  video.pause();

               }
        } else {
            for(var i = 0; i < allVideoPlayer.length; i++) {
                var video = allVideoPlayer[i];
                video.pause();

                video.currentTime=json.curCut[0];

             }
        }
    })
})();

function initJSON(duration,array)
{
    // var allVideoPlayer = document.getElementsByTagName("video");
    // var max=allVideoPlayer[0].duration;
    var json=JSON.parse('{"duration":'+duration+',"cutArray":[],"curCut":[0,'+duration+']}');

    console.log(json);
    //test array integration to delete
    for (var i = 0; i < array.length; i++) {
        json.cutArray.push(array[i]);
    }
    myJson=JSON.stringify(json);
    $("#data").val(myJson);
    console.log($("#data").val());
}

function initSlider()
{
    //init of the cutSlider
    console.log("init slider passage");
    var json=JSON.parse($("#data").val());

    $("#cutSlider").slider({ id: "cutSliderSlider",  min: 0, max: json.duration, range: true, step: 0.01, value: [0,json.duration],rangeHighlights: updateCutSliderBackground(json.cutArray) });
    $("#videoSlider").slider({ id: "videoSliderSlider", class: "container-grey", min: 0, max: json.duration, step: 0.01, value: 0});
    //updateCutSliderBackground(json.cutArray);
}

function setInputValue(array)
{
    $("#cutStart").val(array[0]);
    $("#cutStop").val(array[1]);
}

function setInputsMinMax()
{
    var json=JSON.parse($("#data").val());
    $("#cutStart").attr({
       "max" : json.curCut[1],        // substitute your own
       "min" : 0          // values (or variables) here
    });
    $("#cutStop").attr({
       "max" : json.duration,        // substitute your own
       "min" : json.curCut[0]          // values (or variables) here
    });
}

function sortInputs(start,stop)
{
    if (start<=stop) {
        var array=[start,stop];
    }else {
        var array=[stop,start];
    }
    return array;
}

function setJSONCut(array)
{
    var json=JSON.parse($("#data").val());
    json.curCut[0]=array[0];
    json.curCut[1]=array[1];
    myJson=JSON.stringify(json);
    $("#data").val(myJson);
}

function updateCutSlider(array)
{
    $("#cutSlider").slider('setValue',array,true);
}

function updateCutSliderBackground(array)
{
    console.log("update slider background");
    var tArray=[];
    for (var i = 0; i < array.length; i++) {
        var json={
            "start":array[i][0],
            "end":array[i][1]
        };
        tArray.push(json);
    }
    console.log(tArray);
    return tArray;

}

//test functions
function printJSON() {
    console.log($("#data").val());
}
function testFun() {

}

</script>
