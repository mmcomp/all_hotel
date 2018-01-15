<?php
	session_start();
	include_once("../kernel.php");
        if(!isset($_SESSION['user_id']))
                die(lang_fa_class::access_deny);
        $se = security_class::auth((int)$_SESSION['user_id']);
        if(!$se->can_view)
                die(lang_fa_class::access_deny);
	$out = '';
	$id = ((isset($_POST['id']))?(int)$_POST['id']:-1);
	$cost_kala_id = ((isset($_POST['cost_kala_id']))?(int)$_POST['cost_kala_id']:-1);
	$cost_tedad = ((isset($_POST['cost_tedad']))?(int)$_POST['cost_tedad']:0);
	if($cost_kala_id>0)
		$cost_kala = new cost_kala_class($cost_kala_id);
	$now = audit_class::hamed_pdate(date("Y-m-d"));
	$anbar_factor = new anbar_factor_class($id);
	$anbar_typ = new anbar_typ_class($anbar_factor->anbar_typ_id);
	$resid = $anbar_typ->name;
	$user = new user_class((int)$_SESSION['user_id']);
	$user = $user->fname.' '.$user->lname;
	$moshtari = new moshtari_class((int)$_SESSION['moshtari_id']);

$out = "
    <div class=\"modal-dialog modal-lg\">
		<div class=\"modal-content\">
			
			<div class=\"modal-header\" style=\"background-color: #5e87b0;color: white;border-top-left-radius: 5px;border-top-right-radius: 5px;min-height:140px;\">
                <div class=\"col-md-12\">
                    <div class=\"col-md-8\" style=\"text-align:center;border-left:solid thin\">
                        <h4 class=\"modal-title\">$moshtari->name</h4>
                        <hr>
                        <h4> رسید $resid</h4>";
						if($cost_kala_id==-1)
                            $j="";
                        else
                            $j="جهت $cost_tedad $cost_kala->name";
                       $out.="$j                            
                    </div>                    
                    
                    <div class=\"col-md-4\" style=\"text-align:center\">
                        <h5>تاریخ چاپ: $now</h5>
                        <h5>صادر کننده: $user</h5>
                    </div>
                </div>
				
            </div>
			<div class=\"modal-body\" style=\"max-height:400px;overflow-y:scroll\">
               <div class=\"dataTable_wrapper\" id=\"myTable\">
                                <table style=\"width:100%;margin-right:10px;\" class=\"table table-striped table-bordered table-hover\" id=\"dataTables-example\">";
                                    





                                


	//echo $anbar_factor->anbar_typ_id;
	if($anbar_typ->typ==1)
	{
	      $out.='
          
            <thead>
                                        <tr>
                                            <th style=\"text-align:right;\">نام کالا</th>
                                            <th style=\"text-align:right;\">تاریخ</th>
                                            <th style=\"text-align:right;\">تعداد</th>
                                            <th style=\"text-align:right;\">واحد</th>
                                            <th style=\"text-align:right;\">قیمت واحد</th>
                                            <th style=\"text-align:right;\">قیمت کل</th>
                                            <th style=\"text-align:right;\">تحویل دهنده</th>
                                                       
                                            
                                        </tr>
                                    </thead>';
          
  
	      $ghimat_kol = 0;
	      mysql_class::ex_sql("select * from `anbar_det` where `anbar_factor_id`=$id",$q);
        $out.='<tbody>';
        $i=1;
	      while($r = mysql_fetch_array($q))
	      {
		  $kala = new kala_class($r['kala_id']);
		  $tarikh =audit_class::hamed_pdate($r['tarikh']);
		  $vahed = new kala_vahed_class($kala->vahed_id);
		  $ghimat_vahed = ($r['tedad']==0)?'تعریف نشده':monize($r['ghimat']/$r['tedad']);
		  $other_user = new user_class((int)$r['other_user_id']);
		  $other_user = $other_user->fname.' '.$other_user->lname;
		  $ghimat_kol += (int)$r['ghimat'];
              if(fmod($i,2)!=0){
                  $out .='<tr class="odd">';
                  $out .='<td>'.$kala->name.'</td>';
                  $out .='<td>'.$tarikh.'</td>';
                  $out .='<td>'.(int)$r['tedad'].'</td>';
                  $out .='<td>'.$vahed->name.'</td>';
                  $out .='<td>'.$ghimat_vahed.'</td>';
                  $out .='<td>'.monize($r['ghimat']).'</td>';
                  $out .='<td>'.$other_user.'</td>';
                  $out .="</tr>";
                  $i++;
                  
              }
              else{
                  $out .='<tr class="even">';
                  $out .='<td>'.$kala->name.'</td>';
                  $out .='<td>'.$tarikh.'</td>';
                  $out .='<td>'.(int)$r['tedad'].'</td>';
                  $out .='<td>'.$vahed->name.'</td>';
                  $out .='<td>'.$ghimat_vahed.'</td>';
                  $out .='<td>'.monize($r['ghimat']).'</td>';
		          $out .='<td>'.$other_user.'</td>';
                  $out .="</tr>";
                  $i++;
                  
              }
		  
	      }
	      $out .="<tr><td></td><td></td><td></td><td></td><td>جمع قیمت کل:</td><td>".monize($ghimat_kol)."</td><td>&nbsp;</td></tr>";
        $out.="</tbody>";
	}
	else if((int)$anbar_typ->typ==-1)
	{
		$out.='
        
            <thead>
                                        <tr>
                                            <th style=\"text-align:right;\">نام کالا</th>
                                            <th style=\"text-align:right;\">تاریخ</th>
                                            <th style=\"text-align:right;\">تعداد</th>
                                            <th style=\"text-align:right;\">واحد</th>
                                            <th style=\"text-align:right;\">تحویل گیرنده</th>
                                                       
                                            
                                        </tr>
                                        </thead>';
            
        
		mysql_class::ex_sql("select * from `anbar_det` where `anbar_factor_id`=$id",$q);
        $i=1;
        $out.='<tbody>';
		while($r = mysql_fetch_array($q))
		{
		    $kala = new kala_class($r['kala_id']);
		    $tarikh =audit_class::hamed_pdate($r['tarikh']);
		    $vahed = new kala_vahed_class($kala->vahed_id);
		    $ghimat_vahed = ($r['tedad']==0)?'تعریف نشده':monize($r['ghimat']/$r['tedad']);
		    $other_user = new user_class((int)$r['other_user_id']);
		    $other_user = $other_user->fname.' '.$other_user->lname;
            if(fmod($i,2)!=0){
                $out .='<tr class="odd">';
                $out .='<td>'.$kala->name.'</td>';
                $out .='<td>'.$tarikh.'</td>';
                $out .='<td>'.(int)$r['tedad'].'</td>';
                $out .='<td>'.$vahed->name.'</td>';
                $out .='<td>'.$other_user.'</td>';
                $out .="</tr>";
                $i++;
            }
            else{
                
                $out .='<tr class="even">';
                $out .='<td>'.$kala->name.'</td>';
                $out .='<td>'.$tarikh.'</td>';
                $out .='<td>'.(int)$r['tedad'].'</td>';
                $out .='<td>'.$vahed->name.'</td>';
                $out .='<td>'.$other_user.'</td>';
                $out .="</tr>";
                $i++;
            }
		    
		}
        $out.='</tbody>';
	}
$out.='</table>
                    </div>
                            <!-- /.table-responsive -->
            </div>
			<div class="col-md-12" style="border-top: 1px solid #e5e5e5;height:40px;">
                <div class="col-md-8" style="text-align:center;border-left: 1px solid #e5e5e5;">
                    <h3>'.$conf->title.'</h3>
                </div>
                <div class="col-md-4" style=\"text-align:right"> <h3>امضا</h3></div>
            </div>
            <br/>
            <div class="modal-footer">
				<button type="button" class="btn btn-info" data-dismiss="modal">تایید</button>
            </div>
        </div>
	</div>';
echo $out;
?>



