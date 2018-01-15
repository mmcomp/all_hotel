<?php
	class menu_class
	{
		public $name_array = array();
		public $arrange_array = array();
		public $output = '';
		public function __construct($name_array,$arrange_array,$se)
		{
			$this->arrange_array = $arrange_array;
			$this->name_array = $name_array;
			$this->output = "<ul id=\"nav\">\n";
			$this->output .= menu_class::fetch_array($arrange_array,$name_array,$se);
			$this->output .= "</ul>\n";
		}
		public function fetch_array($arr,$name_array,$se)
		{
			$out = FALSE;
			if(is_array($arr))
			{
				$out = '';
				foreach($arr as $id => $sub_arr)
				{
					if($se->detailAuth($id) || $se->detailAuth('all'))
					{
						$info = new page_icons_class;
						$info->loadByName($name_array[$id]);
						$onclick ='';
						if($info->id>0)
						{
							$onclick = $info->link;
							if($info->isLink==1)
								//$onclick ="loadIconMenu('".$info->link."','".$info->name."','".$info->width."','".$info->height."');";
								$onclick =$info->link;
						}
						$out .= "<li id=\"$id\" onclick=\"$onclick\" ><a target='_blank' href=\"$onclick\">".$name_array[$id]."</a>";
						$tmp = menu_class::fetch_array($sub_arr,$name_array,$se);
						if($tmp!==FALSE)
						{
							$out .= "<ul>\n";
							$out .= $tmp;
							$out .= "</ul>\n";
						}
						$out .= "</li>\n";
					}
				}
			}
			return($out);
		}
	}
?>
