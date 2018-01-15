function unFixNums(str){
            var unFixNumbers=String(str);
            unFixNumbers = unFixNumbers.replace(/۰/g, "0")
            unFixNumbers = unFixNumbers.replace(/۱/g, "1")
            unFixNumbers = unFixNumbers.replace(/۲/g, "2")
            unFixNumbers = unFixNumbers.replace(/۳/g, "3")
            unFixNumbers = unFixNumbers.replace(/۴/g, "4")
            unFixNumbers = unFixNumbers.replace(/۵/g, "5")
            unFixNumbers = unFixNumbers.replace(/۶/g, "6")
            unFixNumbers = unFixNumbers.replace(/۷/g, "7")
            unFixNumbers = unFixNumbers.replace(/۸/g, "8")
            unFixNumbers = unFixNumbers.replace(/۹/g, "9")
            return(unFixNumbers);
   }
function FixNums(str){
            var FixNumbers=String(str);
            FixNumbers = FixNumbers.replace(/0/g, "۰")
            FixNumbers = FixNumbers.replace(/1/g, "۱")
            FixNumbers = FixNumbers.replace(/2/g, "۲")
            FixNumbers = FixNumbers.replace(/3/g, "۳")
            FixNumbers = FixNumbers.replace(/4/g, "۴")
            FixNumbers = FixNumbers.replace(/5/g, "۵")
            FixNumbers = FixNumbers.replace(/6/g, "۶")
            FixNumbers = FixNumbers.replace(/7/g, "۷")
            FixNumbers = FixNumbers.replace(/8/g, "۸")
            FixNumbers = FixNumbers.replace(/9/g, "۹")
            return(FixNumbers);
   }
function checkNumber(obj)
{
	var out =parseInt(unFixNums(obj.value),10);
	if(isNaN(out))
		obj.value = 0;
	else
		obj.value = out;
}
function isNumber(inp){
	var out=false;
	var sht=inp.replace(/,/gi,'');
	sht=sht.replace(/\./gi,'');
	if(String(parseInt(sht,10))==String(sht)){
		out=true;
	}
	return(out);
}
//----------------------------------
function isCopyPressed(e) {
    return e.ctrlKey && getEventKeyCode(e) == 99;
}
function isPastePressed(e) {
    return e.ctrlKey && getEventKeyCode(e) == 118;
}
function getEventKeyCode(e) {
    var key;
    if (window.event)
        key = event.keyCode;
    else
        key = e.which;

    return key;
}
function ignoreKeys(key) {
    if (key == 0) { //function keys and arrow keys
        return true;
    }
    if (key == 13) { //return
        return true;
    }
    if (key == 8) { //backspace
        return true;
    }
    if (key == 9) { // tab
        return true;
    }

    return false;
}
function numbericOnKeypress(e) {

    if (isCopyPressed(e) || isPastePressed(e))
        return;
    var key = getEventKeyCode(e);

    if (key == 45 || key == 44 || key == 46) return true;// ',' '.' '-'
    if (ignoreKeys(key)) return true;
    if (isNumericKeysPressed(key)) { // Numbers
        return true;
    }
    if (window.event) //IE
        window.event.returnValue = false;
    else              //other browser
        e.preventDefault();
}
function isNumericKeysPressed(key) {

    if (key >= 48 && key <= 57) { // Numbers
        return true;
    }

    return false;
}
//----------------------------------
function monize(obj){
		
		var sht=String(obj.value).replace(/,/gi,'');
		var txt = sht.split('');
		var j=-1;
		var tmp='';
		for(var i=txt.length-1;i>=0;i--){
			//alert(txt[i]);
			if(j<2){
				j++;
				tmp=txt[i]+tmp;
			}else{
				j=0;
				tmp=txt[i]+','+tmp;
			}
		}
		obj.value = tmp;
}
function monize2(inp){
	var out=inp;
        if(1){
                var sht=String(inp).replace(/,/gi,'');
                var txt = sht.split('');
                var j=-1;
                var tmp='';
                for(var i=txt.length-1;i>=0;i--){
                        //alert(txt[i]);
                        if(j<2){
                                j++;
                                tmp=txt[i]+tmp;
                        }else{
                                j=0;
                                tmp=txt[i]+','+tmp;
                        }
                }
                out=tmp;
        }
	return(out);
}
function findOther(obj)
{
	var inps = document.getElementsByName(obj.name);
	for(var i=0;i < inps.length;i++)
		inps[i].value = obj.value;
}
function wopen(url, name, w, h)
		{
		  w += 32;
		  h += 96;
		  wleft = (screen.width - w) / 2;
		  wtop = (screen.height - h) / 2;
		  if (wleft < 0) {
		    w = screen.width;
		    wleft = 0;
		  }
		  if (wtop < 0) {
		    h = screen.height;
		    wtop = 0;
		  }
		  var win = window.open(url,
		    name,
		    'width=' + w + ', height=' + h + ', ' +
		    'left=' + wleft + ', top=' + wtop + ', ' +
		    'location=no, menubar=no, ' +
		    'status=no, toolbar=no, scrollbars=yes, resizable=yes');
		  win.resizeTo(w, h);
		  win.moveTo(wleft, wtop);
		  win.focus();
		}
function trim(str){
        var out=str.replace(/^\s\s*/, '').replace(/\s\s*$/, '');
        return out;
        }
        function improveDateFields(field_name)
        {
                var inps = document.getElementsByTagName("input");
                var tarikhs = document.getElementsByTagName("span");
                for(var i=0;i<inps.length;i++)
                {
                        if(inps[i].id.indexOf("_"+field_name)>0)
                        {
                                if(document.getElementById(inps[i].id+"_back"))
                                {
                                        inps[i].value = trim(document.getElementById(inps[i].id+"_back").innerHTML);
                                }
                                else
                                {
                                        inps[i].value = "";
                                }
                        }
                }
        }
function show_hide(id,tobj)
{
	var txt = 'مشاهده';
        if(document.getElementById(id))
        {
                var obj = document.getElementById(id);
                if(obj.style.display == 'none')
		{
                        obj.style.display = '';
			txt = 'عدم مشاهده';
		}
                else
                        obj.style.display = 'none';
        }
	tobj.innerHTML = txt;
}
function change_color(obj,stat)
{
	if(stat=='in')
		obj.style.backgroundColor='#FF8C40';
	else if(stat=='out')
		obj.style.backgroundColor='';
		
}
var output_json={};
function addJSON(key,pvalue,value)
{
        output_json[key] = {'pvalue':pvalue , 'value':value}; 
}
function checkTag(catchFrase,tagname,type,prop)
{
        var out = null;
        var inps = document.getElementsByTagName(tagname);
        for(var i = 0;i < inps.length;i++)
        {
                if((inps[i].type == type || type == '') && document.getElementById(catchFrase+inps[i].id) && document.getElementById(catchFrase+inps[i].id)[prop] != inps[i][prop])
                        addJSON(inps[i].id,document.getElementById(catchFrase+inps[i].id)[prop],inps[i][prop]);
        }
}
function fetchJSON(catchFrase)
{
        if(!catchFrase || catchFrase=='')
                catchFrase = 'mirror_';
        checkTag(catchFrase,'input','text','value');
        checkTag(catchFrase,'select','','value');
        checkTag(catchFrase,'input','checkbox','checked');
        checkTag(catchFrase,'input','radio','checked');
        return(JSON.stringify(output_json));
}
function mehrdad_pdate(obj){
	if(obj){
		if(obj.value){
			var str=unFixNums(String(obj.value));
			var datearr=str.split('/')
			var b=true;
			if(datearr.length==3){
				var d=datearr[0];
				var m=datearr[1];
				var y=datearr[2];
				var tmp;
				if(parseInt(d,10)>parseInt(y,10)){
					tmp=d;
					d=y;
					y=tmp;
				}
				if((parseInt(y,10)>=100)&&(parseInt(y,10)<=1356)){
					b=false;
				}
				if((parseInt(m,10)<=0)||(parseInt(m,10)>=13)){
					b=false;
				}
				
				if((parseInt(d,10)<=0)||(parseInt(d,10)>31)){
					b=false;
				}
				if(parseInt(y,10)<1300){
					y=String(parseInt(y,10)+1300);
				}
				if((parseInt(d,10)<10)&&(d.length==1)){
					d='0'+d;
				}
				if((parseInt(m,10)<10)&&(m.length==1)){
					m='0'+m;
				}
				if(b){
					obj.value=FixNums(y+'/'+m+'/'+d);
				}else{
					obj.value='';
					obj.focus();
					alert('فرمت تاریخ ایراددار است');
				}
			}
			else{
				alert('تاریخ صحیح نمی باشد');
				obj.value='';
				obj.focus();
			}
		}
	}
}
function umonize(inp){
	var out='0';
	var sht=inp.replace(/,/gi,'');
	sht=sht.replace(/\./gi,'');
	out=sht;
	return(out);
}
//------------
function isCopyPressed(e) {
    return e.ctrlKey && getEventKeyCode(e) == 99;
}
function isPastePressed(e) {
    return e.ctrlKey && getEventKeyCode(e) == 118;
}
function getEventKeyCode(e) {
    var key;
    if (window.event)
        key = event.keyCode;
    else
        key = e.which;

    return key;
}
function ignoreKeys(key) {
    if (key == 0) { //function keys and arrow keys
        return true;
    }
    if (key == 13) { //return
        return true;
    }
    if (key == 8) { //backspace
        return true;
    }
    if (key == 9) { // tab
        return true;
    }

    return false;
}
function numbericOnKeypress(e) {

    if (isCopyPressed(e) || isPastePressed(e))
        return;
    var key = getEventKeyCode(e);

    if (key == 45 || key == 44 || key == 46) return true;// ',' '.' '-'
    if (ignoreKeys(key)) return true;
    if (isNumericKeysPressed(key)) { // Numbers
        return true;
    }
    if (window.event) //IE
        window.event.returnValue = false;
    else              //other browser
        e.preventDefault();
}
function isNumericKeysPressed(key) {

    if (key >= 48 && key <= 57) { // Numbers
        return true;
    }

    return false;
}
function dump(arr,level) {
	var dumped_text = "";
	if(!level) level = 0;
	
	//The padding given at the beginning of the line.
	var level_padding = "";
	for(var j=0;j<level+1;j++) level_padding += "    ";
	
	if(typeof(arr) == 'object') { //Array/Hashes/Objects 
		for(var item in arr) {
			var value = arr[item];
			
			if(typeof(value) == 'object') { //If it is an array,
				dumped_text += level_padding + "'" + item + "' ...\n";
				dumped_text += dump(value,level+1);
			} else {
				dumped_text += level_padding + "'" + item + "' => \"" + value + "\"\n";
			}
		}
	} else { //Stings/Chars/Numbers etc.
		dumped_text = "===>"+arr+"<===("+typeof(arr)+")";
	}
	return dumped_text;
}
//------------------------
