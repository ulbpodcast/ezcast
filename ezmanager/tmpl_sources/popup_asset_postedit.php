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
<div class="modal-footer">
  <button type="button" class="btn btn-default" data-dismiss="modal">®Update®</button>
  <button type="button" class="btn btn-default" data-dismiss="modal">®Cancel®</button>
</div>


<script>
//video INIT
var testArray=[[10.4,25.87],[45.34,105.43]];
(function()
    {
    //initialisation
        var videos=document.getElementsByTagName("video");
        console.log(videos);
        var video=videos[0];
        video.addEventListener('loadedmetadata',function() {
            var allVideoPlayer = document.getElementsByTagName("video");
            var duration=allVideoPlayer[0].duration;
            var firstCut=[0,duration]
            initJSON(duration,testArray,firstCut);
            var json=JSON.parse($("#data").val());
            initSlider(json.duration,json.cutArray,json.curCut);
            setInputsMinMax();
            updateCutTable(json.cutArray);
    //end initialisation
        });
    $("#cutStart").on('change', function() {
        var array=sortInputs(parseFloat($("#cutStart").val()),parseFloat($("#cutStop").val()));
        setInputValue(array);
        setJSONCut(array);
        updateCutSlider(array);
    });

    $("#cutStop").on('change', function() {
        var array=sortInputs(parseFloat($("#cutStart").val()),parseFloat($("#cutStop").val()));
        setInputValue(array);
        setJSONCut(array);
        updateCutSlider(array);
    });

    $("#btnPlay").on('click', function() {
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
        if (!$("#video_cam")[0].paused) {
            $("#videoSlider").slider('setValue',$("#video_cam")[0].currentTime,true);
        }
        console.log("preview state = " + $("#preview").val());
        if ($("#preview").val()==="1") {
            var json=JSON.parse($("#data").val());
            if($("#video_cam")[0].currentTime>json.curCut[0]&&$("#video_cam")[0].currentTime<json.curCut[1]){
                var allVideoPlayer = document.getElementsByTagName("video");
                for(var i = 0; i < allVideoPlayer.length; i++) {
                    var video = allVideoPlayer[i];
                    video.currentTime=json.curCut[1];
                 }
            }
            else if ($("#video_cam")[0].currentTime>(json.curCut[1]+10)) {
                var allVideoPlayer = document.getElementsByTagName("video");
                for(var i = 0; i < allVideoPlayer.length; i++) {
                    var video = allVideoPlayer[i];
                    video.pause();
                 }
                 $("#preview").val("0");
            }
        }
    });
    $("#videoSlider").on('change', function(e)
    {
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
            video.currentTime=newTime;
         }
    });
    $("#cutTable").on('click','.modBtn',function(event) {
        var json=JSON.parse($("#data").val());
        var index=parseInt(this.id.substring(6,7));
        var tArray=json.cutArray;
        console.log("tArray "+tArray[0]);
        json.curCut=json.cutArray[this.id.substring(6,7)];
        tArray.splice(index,1);
        json.cutArray=tArray;
        myJson=JSON.stringify(json);
        $("#data").val(myJson);
        setInputValue(json.curCut);
        $("#cutSlider").slider('destroy');
        initSlider(json.duration,json.cutArray,json.curCut);
        updateCutTable(json.cutArray);

    }).on('click','.delBtn',function(event) {
        var json=JSON.parse($("#data").val());
        var index=parseInt(this.id.substring(6,7));
        var tArray=json.cutArray;
        console.log("tArray "+tArray[0]);
        tArray.splice(index,1);
        json.cutArray=tArray;
        myJson=JSON.stringify(json);
        $("#data").val(myJson);
        setInputValue(json.curCut);
        $("#cutSlider").slider('destroy');
        initSlider(json.duration,json.cutArray,json.curCut);
        updateCutTable(json.cutArray);

    });
    $("#cutPreviewBtn").on('click', function() {
        $("#preview").val("1");
        var json=JSON.parse($("#data").val());
        // var prevTime=$("#video_cam")[0].currentTime;
        var allVideoPlayer = document.getElementsByTagName("video");
        // console.log(allVideoPlayer);
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
        } else {
            for(var i = 0; i < allVideoPlayer.length; i++) {
                var video = allVideoPlayer[i];
                video.pause();

                video.currentTime=json.curCut[0];

             }
             $("#preview").val("0");
        }
    });
    $("#cutValid").on('click', function() {
        console.log("appel");
        var test = false;
        var intersectedCut=[];
        var json=JSON.parse($("#data").val());
        var tArray=[];
        for (var i = 0; i < json.cutArray.length; i++) {
            console.log("passage for");
            if ((json.curCut[0]>=json.cutArray[i][0]&&json.curCut[0]<json.cutArray[i][1])||(json.curCut[1]>json.cutArray[i][0]&&json.curCut[1]<=json.cutArray[i][1])||(json.curCut[0]<=json.cutArray[i][0]&&json.curCut[1]>=json.cutArray[i][1])) {
                console.log("intersection avec le cut numero "+(i+1));
                intersectedCut.push(i);
            }
        }
        console.log(intersectedCut);
        if (intersectedCut.length!=0) {
            for (var i = 0; i < intersectedCut[0]; i++) {
                tArray.push(json.cutArray[i]);
            }
            var cutToValid=[];
            if (json.curCut[0]<json.cutArray[intersectedCut[0]][0]) {
                cutToValid.push(json.curCut[0]);
            }else{
                cutToValid.push(json.cutArray[intersectedCut[0]][0]);
            }
            if (json.curCut[1]>json.cutArray[intersectedCut[(intersectedCut.length-1)]][1]) {
                cutToValid.push(json.curCut[1]);
            } else {
                cutToValid.push(json.cutArray[intersectedCut[(intersectedCut.length-1)]][1]);
            }
            tArray.push(cutToValid);
            for (var i = (intersectedCut[(intersectedCut.length-1)]+1); i < json.cutArray.length; i++) {
                tArray.push(json.cutArray[i]);
            }
            console.log(tArray);
        }else{
            var inserted = false;
            for (var i = 0; i < json.cutArray.length; i++) {
                if ((json.curCut[0]<json.cutArray[i][0])&&!inserted) {
                    tArray.push(json.curCut);
                    inserted=true;
                }
                tArray.push(json.cutArray[i]);
            }
            if (!inserted) {
                tArray.push(json.curCut);
            }
            console.log(tArray);
        }
        json.cutArray=tArray;
        json.curCut=[0,json.duration]
        myJson=JSON.stringify(json);
        $("#data").val(myJson);
        setInputValue(json.curCut);
        $("#cutSlider").slider('destroy');
        initSlider(json.duration,json.cutArray,json.curCut);
        updateCutTable(json.cutArray);
    });
    $("#cutSlider-container").on('slide change','#cutSlider', function()
    {
        var allVideoPlayer = document.getElementsByTagName("video");
        if ($("#cutSlider").slider('getValue')[0]!=$("#cutStart").val()) {
            for(var i = 0; i < allVideoPlayer.length; i++) {
                var video = allVideoPlayer[i];
                video.currentTime=$("#cutSlider").slider('getValue')[0];
            }
        }else if ($("#cutSlider").slider('getValue')[1]!=$("#cutStop").val()) {
            for(var i = 0; i < allVideoPlayer.length; i++) {
                var video = allVideoPlayer[i];
                video.currentTime=$("#cutSlider").slider('getValue')[1];
            }
        }
        setInputValue($("#cutSlider").slider('getValue'));
        setJSONCut($("#cutSlider").slider('getValue'));
    });

})();

function initJSON(duration,array,curCut)
{
    var json=JSON.parse('{"duration":'+duration+',"cutArray":[],"curCut":['+curCut[0]+','+curCut[1]+']}');
    //test array integration to delete
    for (var i = 0; i < array.length; i++) {
        json.cutArray.push(array[i]);
    }
    myJson=JSON.stringify(json);
    $("#data").val(myJson);
}

function initSlider(duration,array,cut)
{
    //init of the cutSlider
    $("#cutSlider").slider({ id: "cutSliderSlider",  min: 0, max: duration, range: true, step: 0.01, value: [cut[0],cut[1]],rangeHighlights: updateCutSliderBackground(array)});
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
    if (start<=stop) {
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
    for (var i = 0; i < array.length; i++) {
        var json = {
            "start":array[i][0],
            "end":array[i][1]
        };
        tArray.push(json);
    }
    console.log(tArray);
    return tArray;
}

function updateCutTable(array) {
    $("#cutTableBody").empty();
    for(i=0;i<array.length;i++){
      var cutNb=(i+1);
      $("#cutTableBody").append("<tr><td>"+cutNb+"</td><td>"+array[i][0]+"</td><td>"+array[i][1]+"</td><td><button type='button' id='modBtn"+i+"' class='btn modBtn'><i class='glyphicon glyphicon-edit'></i></button></td><td><button type='button' id='delBtn"+i+"' class='btn delBtn'><i class='glyphicon glyphicon-remove-sign'></i></button></td></tr>");
    }
}
</script>
