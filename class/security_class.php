<?php
	class security_class
	{
		public $can_view = FALSE;
		public $allDetails = array();
		public $pages = array();
		public $upages = array();
		public function blockIfBlocked($se,$msg='')
		{
			$out = '';
			$user = new user_class((int)$_SESSION['user_id']);
			if(get_class($se) == 'security_class' && $se->detailAuth('block') && $user->user!='mehrdad')
				$out = "<div style=\"opacity:0.8;color:#fff;filter: alpha(opacity = 80);background-color:#000;width:100%;height:100%;z-index:10;top:0;left:0;position:fixed;\">\n$msg\n</div>";
			return($out);
		}
		public function auth($user_id)
		{
			$user = new user_class((int)$user_id);
			$grp_id = $user->typ;
			$pages = access_class::loadByGroup($grp_id);
			$upages = access_class::loadByUser($user_id);
			$can_view = FALSE;
			$allDetails = array();
			$acc_user = $user->killuser((int)$user_id);
			$acc_user = TRUE;
      if ($acc_user)
			{
				$acc_id = security_class::isInArray($pages,security_class::thisPage());
				$uacc_id = security_class::isInArray($upages,security_class::thisPage());
				if($acc_id !== FALSE || $uacc_id !== FALSE)
                                {
                                        $can_view = TRUE;
		                        $user->refresh();
                                }
                                if ($uacc_id !== FALSE && $acc_id === FALSE)
					$acc_id = $uacc_id;
			}
			if($can_view)
				$allDetails = access_det_class::loadByAcc($acc_id);
			$se = new security_class;
			$se->can_view = $can_view;
			$se->allDetails = $allDetails;
			$se->pages = $pages;
			$se->upages = $upages;
			return($se);
		}
		public function isInArray($arr,$val)
		{
			$out = FALSE;
			foreach($arr as $key => $value)
				if($value == $val)
					$out = $key;
			return($out);
		}
		public function detailAuth($frase)
		{
			$out = FALSE;
			if(security_class::isInArray($this->allDetails,$frase)!==FALSE)
				$out = TRUE;
			return($out);
		}
		public function thisPage()
		{
			$out = '';
			//$tmp = $_SERVER["REQUEST_URI"];
			$tmp = $_SERVER["PHP_SELF"];
			$tmp = explode('/',$tmp);
			$tmp = $tmp[count($tmp)-1];
			$tmp = explode('?',$tmp);
			$tmp = $tmp[0];
			$out = trim($tmp);
			return($out);
		}
	}
?>
