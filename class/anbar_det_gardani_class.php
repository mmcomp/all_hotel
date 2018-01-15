<?php
	class anbar_det_gardani_class
	{
                public function sabtKalaGardani($kala_id,$anbar_id,$anbar_user_id,$user_id)
                {
                        $out = TRUE;
                        $ghimat_kol = 0;
                        $anbar_id = (int)$anbar_id;
                        $anbar_user_id = (int)$anbar_user_id;
                        $anbar = new anbar_class($anbar_id);
                        $moeen_id_anbardar = $anbar->moeen_anbardar_id;
                        $user_id = (int)$user_id;
                        $sanadOk = FALSE;
                        if(!is_array($kala_id))
                                return(FALSE);
                        anbar_det_class::voroodKala($anbar_id,$kala_id,$anbar_user_id,$user_id);
                        for($i = 0;$i < count($kala_id);$i++)
                        {
                                $tedad= (int)$kala_id[$i]['tedad'];
                                $tedad_new = (int)$kala_id[$i]['tedad_new'];
                                $ghimat = (int)anbar_det_class::calcGhimatGardani($kala_id[$i]['id'],abs($tedad-$tedad_new),FALSE,TRUE);
                                if($ghimat > 0)
                                        if($kala_id[$i]['tedad_new']<$kala_id[$i]['tedad'])
                                        {
                                                $sanadOk = TRUE;
                                                $ghimat_kol += $ghimat;
                                                sanadzan_class::anbarGardaniSabt($kala_id[$i]['id'],$anbar_id,$tedad-$tedad_new,$ghimat,$moeen_id_anbardar,$user_id,TRUE);
                                        }
                        }
                        if($sanadOk)
                                sanadzan_class::anbarGardaniSabt(-1,$anbar_id,0,$ghimat_kol,$moeen_id_anbardar,$user_id,FALSE);
                        return($out);
                }

	}
?>
