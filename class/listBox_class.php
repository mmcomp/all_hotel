<?php
	class listBox_class
	{
		public $dir = '../icon';
		public $imageWidth = '';
		public $imageHeight = '';
		public $width = '200px';
		public $height = '80px';
		public $onClick = "";
		public $input = '';
		public $vertical = TRUE;
		public $objStyle = "border-style:solid;border-width:1px;";
		public $selected = '';
	        public function loadFiles($dir)
        	{
                	$out = null;
	                if ($handle = opendir($dir))
        	        {
                	        while (false !== ($entry = readdir($handle)))
                        	        $out[$entry] = $entry;
	                        closedir($handle);
        	        }
                	return($out);
	        }
		public function getOutput()
		{	
			$dir = $this->dir;
			$onClick = $this->onClick;
			$imageWidth = $this->imageWidth;
			$imageHeight = $this->imageHeight;
			$width = $this->width;
			$height = $this->height;
			$input = $this->input;
			$objStyle = $this->objStyle;
			$selected = $this->selected;
			$tmp = str_split($dir);
			$dir_sep = DIRECTORY_SEPARATOR;
			if($tmp[count($tmp)-1] != $dir_sep)
				$dir .= $dir_sep;
			$imgs = $this->loadFiles($dir);
			$i = 0;
			$out = '<div style="width:'.$width.';height:'.$height.';'.$objStyle.'overflow:auto;">'."\n<table width='100%'>".(($this->vertical)?'':"<tr>\n");
			if(isset($imgs[$selected]))
				$out .= (($this->vertical)?"<tr>\n":'')."<td style='border-style:solid;border-width:1px;border-color:blue;'><img style=\"cursor:pointer;\" onclick=\"$onClick('$selected','$input');\" ".(($imageWidth!='')?" width=\"$imageWidth\" ":'').(($imageHeight!='')?" height=\"$imageHeight\" ":'')." src=\"$dir$selected\" /></td>".(($this->vertical)?"</tr>\n":'');
			foreach($imgs as $key=>$value)
			{
				if(file_exists($dir.$value) && $value != '.' && $value != '..' && $value != $selected)
					$out .= (($this->vertical)?"<tr>\n":'')."<td><img style=\"cursor:pointer;\" onclick=\"$onClick('$value','$input');\" ".(($imageWidth!='')?" width=\"$imageWidth\" ":'').(($imageHeight!='')?" height=\"$imageHeight\" ":'')." src=\"$dir$value\" /></td>".(($this->vertical)?"</tr>\n":'');
				$i++;
			}
			$out .= (($this->vertical)?'':"</tr>\n").'</table></div>'."\n";
			return($out);
		}
		
	}
?>
