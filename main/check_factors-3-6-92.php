<?php	session_start();
	include('../kernel.php');
	conf::setMoshtari(1);
	include('../simplejson.php');
	function facToIndx($all,$fac)
	{
		$out = -1;
		foreach($all as $indx=>$obj)
			if($obj['shomare_fac'] == $fac)
				$out = $indx;
		return($out);
	}
	if(isset($_REQUEST['print']))
	{
		$date1=date("Y-m-d");
		$printed = array();
		$q = null;
		mysql_class::ex_sql("SELECT `shomareh_fac` FROM `factor` ",$q);
		while($r = mysql_fetch_array($q))
			$printed[] = (int)$r['shomareh_fac'];
		$whereClause = '';
		if(count($printed)>0)
			$whereClause = ' and not `factor_shomare` in ('.implode(',',$printed).')';
		$outAll = array();
		$factors = array();
		$inserted=array();
		mysql_class::ex_sql("SELECT *  FROM `sandogh_factor` WHERE date(`tarikh`)='$date1' and `tedad`!='0' and `en`='1' $whereClause",$que1);
		while($res1=mysql_fetch_array($que1))
		{	
			
			$sh2=$res1['factor_shomare'];
			$date2=date("Y-m-d H:i:s");		
			if(!in_array($sh2,$inserted))
				mysql_class::ex_sqlx("INSERT INTO `factor` (`shomareh_fac`,`gdate`) VALUES ('$sh2','$date2')");
			if(facToIndx($outAll,$sh2) == -1)
				$outAll[] = array('shomare_fac'=>$sh2,'data'=>array());
			$factor = array();
			$cl1 =new mehman_class;
			$mehman = $cl1->loadByReserveId($res1['reserve_id']);
			$name=$mehman[0]->fname .' ' .$mehman[0]->lname;
			$cl2=new  room_class($res1['room_id']);
			$room_name=$cl2->name;
			$cl3=new  sandogh_item_class($res1['sandogh_item_id']);
			$name_food=$cl3->name;
			$hazineh_food=$cl3->mablagh_det;
			$cl4=new  user_class($res1['user_id']);
			$user=$cl4->lname;
			$factor['food_num']=$res1['tedad'];
			$factor['customer_name']=$name;
			$factor['room_name']=$room_name;
			$factor['food_name']=$name_food;
			$factor['food_cost']=$hazineh_food;
			$factor['user_name']=$user;
			$factor['shomare']=$res1['factor_shomare'];
			$factor['reserve_id'] = (int)$res1['reserve_id'];
			
			$factors[] = $factor;
			$outAll[facToIndx($outAll,$res1['factor_shomare'])]['data'][] = $factor;
		}
		
		$out = toJSON((array)$factors);
		die(toJSON($outAll));
	}
?>
<html>
	<head>
		
	<style>
		table,tr,th,td
		{
			border-bottom:1px solid blue;
			font:9px Arial,tahoma,sans-serif;
						
		}
		table
		{
			border:1px solid blue;
		}
	</style>
		<script type="text/javascript" src="../js/jquery/jquery.js"></script>
		<script>
		var ou;//=<?php //echo($out); ?>;
		var ouAll;//=<?php //echo(toJSON($outAll));?>;
		$(document).ready(function()
		{
			fetchFactors();
		});
		function printDocument()
		{
			if((typeof ouAll !='undefined') && (ouAll.length>0))
				window.print();
			console.log(ouAll);
			setTimeout(function(){
				fetchFactors();
			},5000);
		}
		function fetchFactors()
		{
			$("#div1").html('<img src="../img/status_fb.gif"></img>');
			$.getJSON("check_factors.php?print=print&",function(result){
				$("#div1").html('');
				ouAll = result;
				for(i in ouAll)
					pre(ouAll[i].data);
				printDocument();
			});
		}
		function pre(ou)
		{
			var tb="<table width='8cm' align='center' dir='rtl'>";
			tb+="<tr><td colspan='12'  align='center'><h1> فاکتور مشتری </h1></td></tr>";
			tb+="<tr align='center'><td nowrap> نام مشتری:  </td><td>"+ou[0].customer_name+"</td>";
			tb+="<td nowrap>  شماره رزرو:  </td><td>"+ou[0].reserve_id+"</td>";
			tb+="<td nowrap>شماره فاکتور:   </td><td>"+ou[0].shomare+"</td>";
			tb+="<td nowrap>  شماره اتاق:  </td><td>"+ou[0].room_name+"</td>";
			tb+="<tr align='center'><td> ردیف  </td>";
			tb+="<td nowrap> لیست غذا  </td>";
			tb+="<td>  تعداد   </td>";
			tb+="<td nowrap>  قیمت غذا   </td>";
			tb+="<td colspan='5' nowrap> جمع </td></tr>";
			for(var i=0;i<ou.length;i++)
			{
				tb+="<tr align='center'><td>"+(i+1)+"</td>";
				tb+="<td nowrap>"+ou[i].food_name+"</td>";
				tb+="<td>"+ou[i].food_num+"</td>";
				tb+="<td>"+ou[i].food_cost+"</td>";
				tb+="<td colspan='5'>"+ou[i].food_num*ou[i].food_cost+"</td></tr>";
				
			}
			tb+="</table";
			$("#div1").append(tb);
			
		}
		function st()
		{
		week= new Array("يكشنبه","دوشنبه","سه شنبه","چهارشنبه","پنج شنبه","جمعه","شنبه")
		months = new Array("فروردين","ارديبهشت","خرداد","تير","مرداد","شهريور","مهر","آبان","آذر","دي","بهمن","اسفند");
		a = new Date();
		d= a.getDay();
		day= a.getDate();
		var h=a.getHours();
      		var m=a.getMinutes();
  		var s=a.getSeconds();
		month = a.getMonth()+1;
		year= a.getYear();
		year = (year== 0)?2000:year;
		(year<1000)? (year += 1900):true;
		year -= ( (month < 3) || ((month == 3) && (day < 21)) )? 622:621;
		switch (month) 
		{
			case 1: (day<21)? (month=10, day+=10):(month=11, day-=20); break;
			case 2: (day<20)? (month=11, day+=11):(month=12, day-=19); break;
			case 3: (day<21)? (month=12, day+=9):(month=1, day-=20); break;
			case 4: (day<21)? (month=1, day+=11):(month=2, day-=20); break;
			case 5:
			case 6: (day<22)? (month-=3, day+=10):(month-=2, day-=21); break;
			case 7:
			case 8:
			case 9: (day<23)? (month-=3, day+=9):(month-=2, day-=22); break;
			case 10:(day<23)? (month=7, day+=8):(month=8, day-=22); break;
			case 11:
			case 12:(day<22)? (month-=3, day+=9):(month-=2, day-=21); break;
			default: break;
		}
		//document.write(" "+week[d]+" "+day+" "+months[month-1]+" "+ year+" "+h+":"+m+":"+s);
			var total=" "+week[d]+" "+day+" "+months[month-1]+" "+ year+" "+h+":"+m+":"+s;
			    document.getElementById("tim").innerHTML=total;
   			    setTimeout('st()',500);
		}
		</script>
	</head>
	<body onload='st()'>
		
		<div id="div1">
		</div>
		<br>
		<br>
		<center>
		<span id='tim' >test2
		</span>
		</center>
	</body>
</html>
