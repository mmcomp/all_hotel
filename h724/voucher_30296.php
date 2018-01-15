<?php
include("../kernel.php");

	$q=$_GET["q"];
	$reserve_id=substr($q,10,strlen($q)-17);
	$host = "localhost";
	$db = "mirsamie_koorosh-1";
	$user = "mirsamie_koorosh";
	$pass = "Koorosh@159951";

	$connection = new mysqli ( $host,$user, $pass, $db );
	$connection->set_charset ( 'utf8' );
	
///hotel_reserve
	$query = 'select * from hotel_reserve where reserve_id='.$reserve_id;
	$hotel_reserve = $connection->query ( $query )->fetch_assoc ();
///room_det
	$query= 'select * from room_det where reserve_id='.$reserve_id;
	$room_det = $connection->query ( $query )->fetch_assoc ();
///khadamat_det
	$query = 'select * from khadamat_det where reserve_id='.$reserve_id;
	$khadamat_det = $connection->query ( $query )->fetch_assoc ();
////ajans
	$query='select * from ajans where 	id='.$hotel_reserve["ajans_id"];
	$ajans=$connection->query ( $query )->fetch_assoc ();
///room_typ
	$query='SELECT  `room_typ`.name as room_typ, COUNT(  `room_typ`.id ) AS count,`hotel`.name as hotel,`room_det`.aztarikh,`room_det`.tatarikh ,`room_det`.nafar
	FROM  `room_det` 
	JOIN  `room` ON  `room_det`.room_id =  `room`.id
	JOIN  `hotel` ON  `hotel`.id =  `room`.hotel_id
	JOIN  `room_typ` ON  `room`.room_typ_id =  `room_typ`.id
	WHERE  `room_det`.reserve_id ='.$reserve_id.'
	GROUP BY  `room_typ`.id';
	$result=$connection->query ( $query );
	$rooms=array();
$sum_person=0;
	while($row=$result->fetch_assoc ())
	{
		array_push($rooms,$row);
		$sum_person+=$row["nafar"];
	}
 ?>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>هتل آپارتمان کوروش </title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="icon" href="images/logo1.jpg">
  
  
	<link href="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.1/css/bootstrap.min.css" rel="stylesheet" />
	<link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.1/css/bootstrap-theme.min.css" />

  <!-- Bootstrap  -->
  <link rel="stylesheet" href="css/bootstrap.css">
  <style>
  td { 
    padding: 10px;
		text-align:center;
    }
    th
    {
    text-align:center;
    padding: 10px;
    }
		.text-right
		{
			text-align:right;
		}
		.col-md-2,.col-md-3,.col-md-4,.col-md-5,.col-md-6
		{
			float:right;
		}
  </style>
</head>
<body>
  <div class="container">
    <!--     START TOP -->
    <div class="row"  style="margin:100px 0 10px 0;">
            <table border="1" class="col-md-6">
            <tr><td><?=$reserve_id?></td><th class="col-md-8 text-right">شماره رزرو</th></tr>
            <tr><td><?=jdate('Y-m-d',$hotel_reserve["regdat"])?></td><th class="col-md-8 text-right">تاریخ درخواست</th></tr>
            <tr><td><?=$hotel_reserve["m_hotel"]?></td><th class="col-md-8 text-right">مبلغ کل</th></tr>
<!--             <tr><td class="col-md-8">Confirmation numbers</td><td></td></tr> -->
            </table>
    </div>
    <!--     END TOP -->
    <!--     START AGENCY INFO -->
    <div class="row" style="margin:10px 0 10px 0;">
      <table border="1" class="col-md-12" >
        <tr><th class="col-md-4"> تعداد افراد</th><th class="col-md-4"> نام مسافر</th><th class="col-md-4">نام آژانس</th></tr>
        <tr><td class="col-md-4"><?=$sum_person?></td><td class="col-md-4"><?=$hotel_reserve["fname"]." ".$hotel_reserve["lname"]?></td><td class="col-md-4">...</td></tr>
      </table>
    </div>
    <!--     END AGENCY INFO -->
    <!--     START HOTEL INFO -->
    <div class="row"  style="margin:10px 0 10px 0;">
      <table border="1" class="col-md-12">
        <tr>
					<th class="col-md-2">تاریخ خروج</th>
					<th class="col-md-2">تاریخ ورود</th>
					<th class="col-md-2">تعداد اتاق</th>
					<th class="col-md-2">نوع اتاق</th>
					<th class="col-md-4">هتل </th>
				</tr>
<?php
				foreach($rooms as $r)
				{
				  echo '<tr>
					<td class="col-md-2">'.jdate('Y-M-d',strtotime($r["tatarikh"])).'</td>
					<td class="col-md-2">'.jdate('Y-M-d',strtotime($r["aztarikh"])).'</td>
					<td class="col-md-2">'.$r["count"].'</td>
					<td class="col-md-2">'.$r["room_typ"].'</td>
					<td class="col-md-4">'.$r["hotel"].'</td>
					</tr>';
				}
				
				?>
			</table>
    </div>
    <!--     END HOTEL INFO -->
    <!--     START TRANSFER INFO -->
<!--     <div class="row"  style="margin:10px 0 10px 0;">
      <table border="1" class="col-md-12">
        <tr><th class="col-md-4">transfer</th><th class="col-md-4">Breakfast . lunch</th><th class="col-md-4">City tours</th></tr>
        <tr><td class="col-md-4"></td><td class="col-md-4"></td><td class="col-md-4"></td></tr>
      </table>
    </div> -->
    <!--     END TRANSFER INFO -->
    <!--     START  -->

<!--     <div class="row"  style="margin:10px 0 10px 0;">
       <table border="1" class="col-md-12">
        <tr>
        <th class="col-md-3"></th>
        <th class="col-md-2">DATA</th>
        <th class="col-md-2">TIME</th>
        <th class="col-md-2">FLIGHT NO</th>
        <th class="col-md-2">AIRLINE</th>
        </tr>
        <tr>
        <td class="col-md-3">ARRVAL</td>
        <td class="col-md-2">8 JUL</td>
        <td class="col-md-2">0500</td>
        <td class="col-md-2"></td>
        <td class="col-md-2">TABAN</td>
        </tr>
      <tr>
        <td class="col-md-3">DEPARTURE</td>
        <td class="col-md-2">12 JUL</td>
        <td class="col-md-2">1130</td>
        <td class="col-md-2"></td>
        <td class="col-md-2">TABAN</td>
      </tr>
      </table>
    </div> -->
    <!--     END-->
<!--     <div class="row"  style="margin:10px 0 10px 0;">
     <table border="1" class="col-md-12">
      <tr><th>Other comments</th></tr>
      <tr>
        <td>
        <div class="col-md-12">1. Total payment have to be done three days before check -in</div>
        <div class="col-md-12">2. Please incloude the invoice number on your payment receipt</div>
        </td>
       </tr>
      <tr>
        <td>
        <div class="col-md-12">Yahoo email:agency.golden@yahoo.com</div>
        <div class="col-md-12">Hotel website: </div>
        </td>
       </tr>
       <tr>
       <td>
        <div class="col-md-12">Account holder:Mehdi karampoor</div>
        <div class="col-md-12">Account no:</div>
        <div class="col-md-12">Card no:sheba no:6104-3378-4457-8565</div>
        <div class="col-md-12">Phone:00374 55 322 644</div>
       </td>
       </tr>
    </table>
    </div> -->
  </div>
</body>

</html>