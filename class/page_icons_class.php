<?php
	class page_icons_class
	{
		public $id=-1;
		public $img="";
		public $name="";
		public $j_id="";
		public $link="";
		public $grop_id=-1;
		public $width = '800';
		public $height = '500';
		public $isLink = TRUE;
		public function __construct($id=-1)
		{
			mysql_class::ex_sql("select * from `page_icons` where `id` = $id",$q);
			if($r = mysql_fetch_array($q))
			{
				$this->id=$r['id'];
				$this->img=$r['img'];
				$this->name=$r['name'];
				$this->j_id=$r['j_id'];
				$this->link=$r['link'];
				$this->grop_id=$r['grop_id'];
				$this->width = $r['width'];
				$this->height = $r['height'];
				$this->isLink = (int)$r['isLink'];
			}
		}
		public function loadByName($name='')
                {
                        mysql_class::ex_sql("select * from `page_icons` where `name` = '$name' limit 1",$q);
                        if($r = mysql_fetch_array($q))
                        {
                                $this->id=$r['id'];
                                $this->img=$r['img'];
                                $this->name=$r['name'];
                                $this->j_id=$r['j_id'];
                                $this->link=$r['link'];
                                $this->grop_id=$r['grop_id'];
                                $this->width = $r['width'];
                                $this->height = $r['height'];
                                $this->isLink = (int)$r['isLink'];
                        }
                }
		public function getIcons($group_id,$user_id,$canSeeAll,$columnCount = 3)
		{
			/*$out = <<<SCR
				<script language="javascript">
					function loadIconMenu(link,name,w,h)
					{
						$.window({
							title : name,
							width : w,
							height : h,
							content: $("#window_block8"),
							containerClass: "my_container",
							headerClass: "my_header",
							frameClass: "my_frame",
							footerClass: "my_footer",
							selectedHeaderClass: "my_selected_header",
							createRandomOffset: {x:0, y:0},
							showFooter: false,
							showRoundCorner: true,
							x: 0,
							y: 0,
							url: link
						});
					}
				</script>
SCR;*/
			$out = "
				<script language='javascript'>
					function open_win(link)
						{
							window.open(link)
						}
				</script>";
			$i = 1;
			$user_sandoghs = user_class::loadSondogh($user_id,$canSeeAll);
			for($k = $i-1;$k < count($user_sandoghs);$k++)
		        {
				if($i == 1)
					$out .= "<tr>\n";
				$tmp_sandogh = new sandogh_class((int)$user_sandoghs[$k]);
				if($tmp_sandogh->icon != '')
				{
					$link = "sandogh_det.php?sandogh_id=".(int)$user_sandoghs[$k]."&";
					$name = "فرانت آفیس";
					$width = 800;
					$height = 500;
					$out .= "<td id=\"sandogh_".(int)$user_sandoghs[$k]."\" onclick=\"open_win('$link');\" align=\"center\" >
	            		                        <table>
                                		                <tr>
                                                		        <td align=\"center\">
                                                                		<img src=\"".$tmp_sandogh->icon."\" width=\"75\" ></img>
		                                                        </td>
                		                                </tr>
                                		                <tr>
                                                		        <th>
                                                                		".$tmp_sandogh->name."
		                                                        </th>
                		                                </tr>
                                		        </table>

		                                </td>";
					if($i % $columnCount == 0 && $i < count($user_sandoghs)  && $i > 1)
	                                        $out .= "</tr>\n<tr>\n";
					$i++;
				}
			}
			mysql_class::ex_sql("select * from `page_icons` where (`grop_id` = $group_id) or (`grop_id` = -1) order by `id`",$q);
			$co = mysql_num_rows($q);
			while($r = mysql_fetch_array($q))
			{
				if((int)$r['isLink'] == 1)
				{
					$add = $r['link']."','".$r['name']."','".$r['width']."','".$r['height'];
					$scr = "open_win('$add');";
					//$scr = "loadIconMenu('".$r['link']."','".$r['name']."',".$r['width'].",".$r['height'].");";
				}
				else
					$scr = $r['link'];
				if($i == 1)
					$out .= "<tr>\n";
				$out .= "<td id=\"".$r['j_id']."\" onclick=\"$scr\" align=\"center\">\n<table>\n<tr>\n<td align=\"center\">\n";
				$out .= "<img src=\"".$r['img']."\" width=\"75\" ></img>\n";
				$out .= "</td>\n</tr>\n<tr>\n<th>\n";
				$out .= $r['name']."\n";
				$out .= "</th>\n</tr>\n</table>\n</td>\n";
				if($i % $columnCount == 0  && $i >1)
					$out .= "</tr>\n<tr>\n";
				$i++;
			}
			return($out);
		}
	}
?>
