function MM_DisplayHideLayers() { //v9.0
  var i,p,v,obj,args=MM_DisplayHideLayers.arguments;
  for (i=0; i<(args.length-2); i+=3) 
  with (document) if (getElementById && ((obj=getElementById(args[i]))!=null)) { v=args[i+2];
    if (obj.style) { obj=obj.style; v=(v=='show')?'block':(v=='hide')?'none':v; }
    obj.display=v; }
}


function visibilite(thingId)
{
    var targetElement;
    targetElement = document.getElementById(thingId) ;
    if (targetElement.style.display == "none")
    {
        targetElement.style.display = "" ;
    } else {
        targetElement.style.display = "none" ;
    }
}