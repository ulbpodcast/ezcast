<?php
$assoc_metadata=ezmam_album_orig_metadata_get($album,$asset);
$start_time= 0.0;
$end_time=$assoc_metadata['duration'];
?>
<div class="modal-header">
  <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
  <h4 class="modal-title">®Postedit_video_title®</h4>
</div>
<div class="modal-body">
  <?php if (true) {//need to be fixed
    ?>
  <div id="container" style="margin: 0 auto"></div>
  <center>
    <div style="display:inline-block;width:<?php echo ($has_slides) ? '49%' : '100%'; ?>;" class="popup_video_player"
      id="Popup_Player_<?php echo $asset; ?>_cam"></div>
      <?php if ($has_slides) {
        ?>
        <div style="display:inline-block;width: 49%;" class="popup_video_player"
        id="Popup_Player_<?php echo $asset; ?>_slide"></div>
        <?php
      } ?>
  </center>
  <center>
    <div class="container" style="position:relative">

      <div class="row">
        <div style="padding:5px; border-top:1px solid #e5e5e5;"></div>
        <button type="button" id="playToggleBtn">play</button>
        <button type="button" id="setMarkerBtn">set mark</button>
        <button type="button" id="delMarkerBtn">del mark</button>
        <button type="button" onclick="addSubBtnToggle()">test this shit</button>
        <div style="padding:5px;"></div>
      </div>
      <div class="row" id="workingCutDiv" style="display:none">
        <div style="padding:5px; border-top:1px solid #e5e5e5;"></div>
        <div id="workingCutDiv1" class="" style="display:none">
                    <input class="addSubBtn" type="button" id="workingCut1Sub1m" onclick="changeTime(1,'sub',60)" value="-1' ">
                    <input class="addSubBtn" type="button" id="workingCut1Sub1s" onclick="changeTime(1,'sub',1)" value="-1''">
                    <input type="number" id="workingCut1" step = "0.01" size="6">
                    <input class="addSubBtn" type="button" id="workingCut1Add1s" onclick="changeTime(1,'add',1)" value="+1''">
                    <input class="addSubBtn" type="button" id="workingCut1Add1m" onclick="changeTime(1,'add',60)" value="+1' ">
        </div>
        <div id="workingCutDiv2" class="" style="display:none">
                    <input class="addSubBtn" type="button" id="workingCut2Sub1m" onclick="changeTime(2,'sub',60)" value="-1' ">
                    <input class="addSubBtn" type="button" id="workingCut2Sub1s" onclick="changeTime(2,'sub',1)" value="-1''">
                    <input type="number" id="workingCut2" step = "0.01" size="6">
                    <input class="addSubBtn" type="button" id="workingCut2Add1s" onclick="changeTime(2,'add',1)" value="+1''">
                    <input class="addSubBtn" type="button" id="workingCut2Add1m" onclick="changeTime(2,'add',60)" value="+1' ">
        </div>
      </div>
      <div id="workingCut1ValidBtnDiv" class="row" style="display:none">
        <br>
        <input id="workingCut1StartValidBtn" type="button" value="Couper a partir du debut">
        <input id="workingCut1EndValidBtn" type="button" value="Couper jusqu'a la fin">
      </div>
      <div id="workingCut2ValidBtnDiv" style="display:none">
        <br>
        <input id="workingCutValidBtn" type="button" value="Valider">
      </div>
      <div class="container" id="cutTableDiv" style="display:none;position:relative;">

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
    </div>
  </center>
</div>
<div class="modal-footer">
  <button type="button" class="btn btn-default" data-dismiss="modal">®Update®</button>
  <button type="button" class="btn btn-default" data-dismiss="modal">®Cancel®</button>
</div>

<script>
(function() {
  <?php if ($has_cam) {
    echo "show_embed_player('".$album."', '".$asset."', 'low', 'cam', '" .
    $asset_token. "', 'Popup_Player_" . $asset . "_cam', '100%', '100%');";
  }

  if ($has_slides) {
    echo "show_embed_player('" . $album . "', '" . $asset . "', 'low', 'slide', '".
    $asset_token . "', 'Popup_Player_" . $asset . "_slide', '100%', '100%');";
  }
  ?>
})();


//tables generations
var cutArray=[];
var workingCutArray=[];
$(document).ready(function(){
  //play button listener
  $("#playToggleBtn").on('click',function(){
    var allVideoPlayer = $('.popup_video_player video');
    if(allVideoPlayer[0].paused){
      for(var i = 0; i < allVideoPlayer.length; i++) {
        var video = $('.popup_video_player video')[i];
        video.play();
        }
      $("#playToggleBtn").text("Pause");
    }else{
      for(var i = 0; i < allVideoPlayer.length; i++) {
        var video = $('.popup_video_player video')[i];
        video.pause();
        }
      $("#playToggleBtn").text("Play");
      }}),
  //set marker button listener
  $("#setMarkerBtn").on('click',function(){
      var curPlayValue=toFloatLim($('.popup_video_player video')[0].currentTime);
      if(workingCutArray.length==0){
        workingCutArray.push(curPlayValue);
      }else if (workingCutArray.length==1) {
        workingCutArray.push(curPlayValue);
        workingCutInv();
      }else{
        }
      console.log("current workingCutArray State= "+workingCutArray);
      updateWorkingCutInputMinMax();
      updateWorkingCutInput();
      addSubBtnToggle();
  }),
  //del button listener
  $("#delMarkerBtn").on('click',function(){
      workingCutArray=[];
      console.log("current workingCutArray State= "+workingCutArray);
      updateWorkingCutInput();
  }),
  $("#workingCut1").on('change',function(){
      workingCutArray[0]=toFloatLim($("#workingCut1").val());
      updateWorkingCutInputMinMax();
      console.log("current workingCutArray State= "+workingCutArray);
  }),
  $("#workingCut2").on('change',function(){
      workingCutArray[1]=toFloatLim($("#workingCut2").val());
      updateWorkingCutInputMinMax();
      console.log("current workingCutArray State= "+workingCutArray);
  }),
  $("#workingCut1StartValidBtn").on("click",function(){
    recordWorkingCut("min");
  }),
  $("#workingCut1EndValidBtn").on("click",function(){
    recordWorkingCut("max");
  }),
  $("#workingCutValidBtn").on("click",function(){
    recordWorkingCut("interval");

  })
});
//set min max for input
function updateWorkingCutInputMinMax() {
  console.log("updateWorkingCutInputMinMax()");
  var min=findMinValue();
  var max=findMaxValue();
  if (isset(workingCutArray[1])) {
    $("#workingCut1").attr({
      "min":min,
      "max":workingCutArray[1]
    });
    $("#workingCut2").attr({
      "min":workingCutArray[0],
      "max":max
    });
  }else {
    $("#workingCut1").attr({
      "min":min,
      "max":max
    });
  }


}
//workingCutArray & display updater function
function updateWorkingCutInput(){
  console.log("updateWorkingCutInput()");
  if(workingCutArray.length==0){
    $("#workingCut1").val("");
    $("#workingCut2").val("").removeClass('col-sm-6');
    updateDivDisplay("#workingCutDiv",false);
    updateDivDisplay("#workingCutDiv1",false);
    updateDivDisplay("#workingCutDiv2",false);
    updateDivDisplay("#workingCut1ValidBtnDiv",false);
    //updateDivDisplay("#workingCut1FirstValidBtnDiv",false);
    updateDivDisplay("#workingCut2ValidBtnDiv",false);
    $("#workingCutDiv1").removeClass('col-sm-6').removeClass('col-sm-12');
    $("#workingCutDiv1").removeClass('col-sm-6');
  }
  if(workingCutArray.length==1){
    $("#workingCut1").val(workingCutArray[0]);
    updateDivDisplay("#workingCutDiv",true);
    updateDivDisplay("#workingCutDiv1",true);
    if (isset(cutArray[0])) {
      workingCut1StartValidBtn
      btnUpdater("#workingCut1StartValidBtn","Couper a partir du denier cut");
      btnUpdater("#workingCut1EndValidBtn","Couper jusqu'au prochain cut");
    }
    else{
      btnUpdater("#workingCut1StartValidBtn","Couper a partir du debut");
      btnUpdater("#workingCut1EndValidBtn","Couper jusqu'a la fin");
      //updateDivDisplay("#workingCut1FirstValidBtnDiv",true);
    }
    updateDivDisplay("#workingCut1ValidBtnDiv",true);

    $("#workingCutDiv1").addClass('col-sm-12');
  }
  if(workingCutArray.length==2) {
    $("#workingCut1").val(workingCutArray[0]);
    $("#workingCut2").val(workingCutArray[1]);
    updateDivDisplay("#workingCutDiv2",true);
    updateDivDisplay("#workingCut2ValidBtnDiv",true);
    updateDivDisplay("#workingCut1ValidBtnDiv",false);
    $("#workingCutDiv1").addClass('col-sm-6').removeClass('col-sm-12');
    $("#workingCutDiv2").addClass('col-sm-6');

  }
}
//button value updater
function btnUpdater(btn,value) {
    $(btn).val(value);
}
//div display updater
function updateDivDisplay(divToUpdate,bool){
  console.log("updateDivDisplay("+divToUpdate+","+bool+")");
  if (bool) {
    $(divToUpdate).css("display","");
  }else{
    $(divToUpdate).css("display","none");
  }
}
//button display updater
function updBtnActivation(btnToUpdate,bool){
  console.log("updateDivDisplay("+btnToUpdate+","+bool+")");
  if (bool) {
    $(btnToUpdate).prop('disabled', false);
  }else{
    $(btnToUpdate).prop('disabled', true);
  }
}
//workingcut inversion test & execution
function workingCutInv(){
  console.log("workingCutInv()");
  if (workingCutArray.length==2) {
    if(workingCutArray[0]>workingCutArray[1]){
      var temp=workingCutArray[0];
      workingCutArray[0]=workingCutArray[1];
      workingCutArray[1]=temp;
      }
  }
}
// list add sub btn
function listAddSubBtn(){
  console.log("listAddSubBtn()");
  var btnArray=$('.addSubBtn').map(function () {
    return this.id;
    })
  return btnArray;
}
function cutTableDivToggle(){
  if (cutArray.length!=0) {
    $("#cutTableDiv").css("display","");
  } else {
    $("#cutTableDiv").css("display","none");
  }
}
//add & sub buttons activation toggle
function addSubBtnToggle(){
  console.log("addSubBtnToggle()");
  var min=findMinValue();
  var max=findMaxValue();
  var btnArray=listAddSubBtn();
  for (var i = 0; i < btnArray.length; i++) {
    var str=btnArray[i];
    //workingCut1Sub1m
    var thisWorkingCut=str.substring(0,11);
    var operation=str.substring(11,14);
    var timeCut=str.substring(14,16);
    singleAddSubBtnToggle(thisWorkingCut,operation,timeCut,min,max);
  }
  //one add or sub button activation toggle
  function singleAddSubBtnToggle(thisWorkingCut,operation,timeCut,min,max){
    console.log("singleAddSubBtnToggle("+thisWorkingCut+","+operation+","+timeCut+","+min+","+max+")");
    var curWorkingCutValue=toFloatLim($("#"+thisWorkingCut).val());
    var timeNum=0;
    if (timeCut==="1m") {
      timeNum=60;
    }
    else{
      timeNum=1;
    }
    if (operation==="Sub") {
      if(curWorkingCutValue>=(min+timeNum)){
        updBtnActivation("#"+thisWorkingCut+operation+timeCut,true);
      }
      else {
        updBtnActivation("#"+thisWorkingCut+operation+timeCut,false);
      }
      if (isset(workingCutArray[1])&&(thisWorkingCut==="workingCut2")) {
        if(curWorkingCutValue>(workingCutArray[0]+timeNum)){
          updBtnActivation("#"+thisWorkingCut+operation+timeCut,true);
        }
        else {
          updBtnActivation("#"+thisWorkingCut+operation+timeCut,false);
        }
      }
    }
    else{
      if(curWorkingCutValue<(max-timeNum)){
        console.log(thisWorkingCut+" "+operation,timeCut+" "+curWorkingCutValue+"<"+(max-timeNum)+" OK");
        updBtnActivation("#"+thisWorkingCut+operation+timeCut,true);
      }
      else {
        console.log(thisWorkingCut+" "+operation,timeCut+" "+curWorkingCutValue+">"+(max-timeNum)+" NOT OK");
        updBtnActivation("#"+thisWorkingCut+operation+timeCut,false);
      }
      if (isset(workingCutArray[1])&&(thisWorkingCut==="workingCut1")) {
        if(curWorkingCutValue<(workingCutArray[1]-timeNum)){
          updBtnActivation("#"+thisWorkingCut+operation+timeCut,true);
        }
        else {
          updBtnActivation("#"+thisWorkingCut+operation+timeCut,false);
        }
      }
    }
  }
}
//find the min value of a cut between cuttable and video start-stop
function findMinValue(){
  console.log("findMinValue()");
  var min=0.0
  if(cutArray.length>0){
    for (var i = 0; i < cutArray.length; i++) {
      if(workingCutArray[0]>cutArray[i][1]&&(workingCutArray[0]-cutArray[i][1])<(workingCutArray[0]-min)){
        min=cutArray[i][1];

      }
    }
  }
  console.log("min= "+min);
  return min;
}
//find the max value of a cut between cuttable and video start-stop
function findMaxValue(){
  console.log("findMaxValue()");
  var allVideoPlayer = $('.popup_video_player video');
  var max=allVideoPlayer[0].duration;
  if(cutArray.length>0){
    for (var i = 0; i < cutArray.length; i++) {
      if (workingCutArray[1]<cutArray[i][0]&&(cutArray[i][0]-workingCutArray[1])<(max-workingCutArray[1])) {
        max=cutArray[i][0];
      }
    }
  }
  console.log("max= "+max);
  return max;
}
//timechange updater function
function changeTime(arNum,operation,time){
  console.log("changeTime("+arNum+","+operation+","+time+")");
  var temp=workingCutArray[arNum-1];
  if (operation==="sub") {
    temp-=time;
    workingCutArray[arNum-1]=toFloatLim(temp);
  }
  else {
    temp+=time;
    workingCutArray[arNum-1]=toFloatLim(temp);
  }
  workingCutInv();
  updateWorkingCutInputMinMax();
  updateWorkingCutInput();
  addSubBtnToggle();
  console.log("current workingCutArray State= "+workingCutArray);
}
//record working cut in cutArray
function recordWorkingCut(mode) {
  var cutValid=[]
  if (mode==="min") {
    var min=findMinValue();
    if (min==0.0) {
      cutValid.push(0.0);
      cutValid.push(workingCutArray[0]);
      cutArray.push(cutValid);
    }
    else{
      if (true) {
        console.log(cutArray.length);


          for (var i = 0; i < cutArray.length; i++) {
            console.log("for for cutarray"+cutArray[i][1]+"=="+min);
            if(min==cutArray[i][1]){
              cutArray[i][1]=workingCutArray[0];
            }
          }


      }else{
        return;
      }

    }
  }
  else if (mode==="max") {
    var max=findMaxValue();
    if (max==toFloatLim($('.popup_video_player video')[0].duration)) {
      cutValid.push(workingCutArray[0]);
      cutValid.push(max);
      cutArray.push(cutValid);
    }
    else{
      if (confirm("Vous etes sur le point de fusionner ce cut avec un autre cut deja enregistrer.")) {
          for (nextCut = 0; i < cutArray.length; i++) {
            console.log(cutArray[nextCut][1]+"=="+max);
            if(min==cutArray[nextCut][0]){
              cutArray[nextCut][0]=workingCutArray[0];
              alert(cutArray);
            }
          }
      }else{
        return;
      }
    }
  }
  else{
    cutArray.push(workingCutArray);
  }
  workingCutArray=[];
  console.log("current workingCutArray State= "+workingCutArray);
  updateWorkingCutInput();
  cutTableGen();
  cutTableDivToggle();
}
//isset for js
function isset(variable){
  console.log("isset("+variable+")");
if ( typeof(variable) != "undefined" ) {
     return true;
   }
else {
     return false;
   }
}
//to float with 2 decimal
function toFloatLim(value) {
  console.log("toFloatLim("+value+")");
  var temp=value;
  if (typeof value==="string") {
    temp=parseFloat(value)
  }
  return Math.round(temp*1e2)/1e2;
}


//cutTable generations
function cutTableGen(){
  $("#cutTableBody").empty();
  for(i=0;i<cutArray.length;i++){
    var cutNb=(i+1);
    $("#cutTableBody").append("<tr><td>"+cutNb+"</td><td id='cutStart"+cutNb+"'>"+cutArray[i][0]+"</td><td id='cutStop"+cutNb+"'>"+cutArray[i][1]+"</td><td><input type='button' value='mod'><input type='button' value='del'></td></tr>");
  }
}
function sortArray() {
    var tArray=cutArray;
    if((tArray.length)>1){
      for (var i = tArray.length-1; i > 0; i--) {
        array[i]
      }
    }
    return tArray;
  }






  function allVideoPlay(){
    if($('.popup_video_player video')[0].paused){
      curPlayValue=parseFloat(document.getElementById("cursorValue").value);
      var allVideoPlayer = $('.popup_video_player video');
      for(var i = 0; i < allVideoPlayer.length; i++) {
        var video = $('.popup_video_player video')[i];
        video.play();
      }
      document.getElementById('play').value="Stop";
    }else {
      var allVideoPlayer = $('.popup_video_player video');
      for(var i = 0; i < allVideoPlayer.length; i++) {
        var video = $('.popup_video_player video')[i];
        video.pause();
      }
      document.getElementById('play').value="Preview";
      adaptVideoTime(parseFloat(document.getElementById("cursorValue").value));
    }

  }
  function adaptVideoTime(xValue) {
    var newVideoTime = xValue;
    var allVideoPlayer = $('.popup_video_player video');
    for(var i = 0; i < allVideoPlayer.length; i++) {
      var video = $('.popup_video_player video')[i];
      video.currentTime = newVideoTime;
    }
    //addPlotLine(xValue);
  }
  </script>
  <?php } ?>
