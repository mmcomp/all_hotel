<?php
/**
*       This is a MultiFunctional Ajax Aware AutoConnect MySql Grid.
*	@param  	Boolean		$canAdd		True means end user have the ability to Edit Cells if he has the access.
*
*/

	class jshowGrid_new{
/**
*	True means end user have the ability to Add a new Row.
*       @var          Boolean
*/
		public $canAdd = TRUE;
/**
*       True means end user have the ability to Edit Cells if he has the access.
*       @var          Boolean
*/
		public $canEdit = TRUE;
/**
*       True means end user have the ability to Delete a Row.
*       @var          Boolean
*/
		public $canDelete = TRUE;
/**
*       This is the Name of the Function that should fire after the Add Event happend instead of regular
*	add function of the Class.
*       @var          String
*/
		public $addFunction = null;
/**
*       This is the Name of the Function that should fire after the Edit Event happend instead of regular
*       edit function of the Class.
*       @var          String
*/
		public $editFunction = null;
/**
*       This is the Name of the Function that should fire after the Delete Event happend instead of regular
*       delete function of the Class.
*       @var          String
*/
		public $deleteFunction = null;
/**
*       This a query for using the Grid as a view only grid .
*       @var          String
*/
		public $query = "";
/**
*       This is the Header of the Index Column.
*       @var          String
*/
		public $indexHeader = 'ردیف';
/**
*       This is the dataSet that will hold all of the Records which we will use in the Grid.
*       @var          DataSet
*/
		public $dataSet = null;
/**
*       This is an Array which holds all of column headers of all fields in fieldList. In order to hide a column , the header
*	corresponding to that field should be set to "null".
*	@see	$filedList	
*       @var          Array
*/
		public $columnHeaders = array();
/**             
*       This is an Array which holds all of column Functions of all fields in fieldList. In order to assign a function to a column
*       , the corresponding array parameter of this should be set to the Name of the Function and the Function should have one input
*	and one output like this:
*	function foo($inp)
*	{
*		.....
*		....
*		..
*		.
*		return($out)
*	}
*	This Function runs when the grid is creating the Interface (output) and will affect the column`s output.
*       @see    $filedList      
*       @var          Array
*/
		public $columnFunctions = array();
/**             
*       This is an Array which holds all of column Functions of all fields in fieldList. In order to assign a function to a column
*       , the corresponding array parameter of this should be set to the Name of the Function and the Function should have one input
*       and one output like this:
*       function foo($inp)
*       {
*               .....
*               ....
*               ..
*               .
*               return($out)
*       }
*	This Function runs when the grid is posting back the Edited data (if end user has the access to edit) and will affect on post
*	backed Data.
*       @see    $filedList      
*       @var          Array
*/
		public $columnCallBackFunctions = array();
/**             
*       This is an Array which holds all of column CallBackFunctions of all fields in fieldList. In order to assign a function to a column
*       , the corresponding array parameter of this should be set to the Name of the Function and the Function should have one input
*       and one output like this:
*       function foo($inp)
*       {
*               .....
*               ....
*               ..
*               .
*               return($out)
*       }
*       This Function runs when the grid is loading Edited data (if end user has the access to edit) and will affect on post
*       backed Data.
*       @see    $filedList      
*       @var          Array
*/

		public $columnLists = array();
/**             
*       This is an Array which holds all of column Lists of all fields in fieldList. In order to assign a list to a column
*       , the corresponding array parameter of this should be set to the array with structure like below:
*	$list["Name"] = ID
*       Name is a String which will be the Text in the ComboBox and id will be the Value of that.
*       @see    $filedList      
*       @var          Array
*/
		public $columnRLists = array();
/**
*	This is just like the columnList but it is loaded by refrence, and its structure is like below:
*	
*/
		public $columnFilters = array();
		public $columnJavaScript = array();
		public $newFieldRefresh = array();
		public $columnAccesses = array();
		public $width = "80%";
		public $cssClass = "showgrid";
		private $rowCount = 0;
		public $footer = "";
		public $fields = array();
		private $class_enabled = FALSE;
		public $checkbox_width = "15px";
		public $index_width = "150px";
		public $error_message = "کلاس گرید به درستی معرفی نشده است";
		public $error_message_ajax = "خطا در بروزرسانی";
		public $askDelete = "آیا حذف انجام شود ؟";
		public $askGroupDelete = "آیا حذف گروهی انجام شود؟";
		public $nextButton = "بعدی";
		public $previousButton = "قبلی";
		public $deleteButton = "حذف";
		public $newTitle = "ثبت جدید";
		public $newButton = "ثبت";
		public $ajaxGoing = "در حال بروزرسانی";
		private $eRequest = null;
		public $fieldList = array();
		public $whereClause = "1=1";
		public $extraClause = "";
		public $tableName = "";
		public $gridName = null;
		public $pageCount = 10;
		public $sums = array();
		public $enableAjax = 0;
		public $ajaxAnimation = "../class/wait.gif";
		private $pageNumber = 0;
		public $mysql_class = null;
		public $pageIndexMode = 1;
		public $groupDelete = FALSE;
		public $group = "";
		public $echoQuery = FALSE;
		public $gotoLast = FALSE;
		public $loadQueryField = FALSE;
		public $showIndex = TRUE;
		public $divProperty = 'style="width:100%;height:100%;overflow:auto;"';
		public $submitOnBlur = FALSE;
		public $attachEnterKey = '';
		public $orderFeild = '';
		public $sortEnabled = FALSE;
		public $loadArray = FALSE;
		public $dataArray = array();
		public $hideIndex = 999;
		public $moreButton = '>>';
		public $lessButton = '<<';
		public $moreShown = FALSE;
		public $showAddDefault = TRUE;
		public $enableComboAjax = FALSE;
		public $addButtonScript = '';
		public $columnLists2 = array();
		public $list2 = FALSE;
		public function __construct($tableName,$gridName,$dataArray=null){
			$this->mysql_class = new mysql_class;
			$this->tableName = $tableName;
			if($tableName=='')
				$this->loadArray = TRUE;
			if( $dataArray!=null && is_array($dataArray))
				$this->dataArray = $dataArray;
			$this->gridName = $gridName;
			$this->class_enabled = TRUE;
			if($this->class_enabled){
				if($this->query == "" && !$this->loadArray){
					$this->mysql_class->ex_sql("select * from $tableName where 0=1",$q);
				}else{
					if(!$this->loadArray)
						$this->mysql_class->ex_sql($this->query,$q);
					$this->canEdit = FALSE;
					$this->canAdd = FALSE;
					$this->canDelete = FALSE;
				}
				if(!$this->loadArray)
					while($r=mysql_fetch_field($q)){
						$this->fields[] = $r;
						$this->columnHeaders[] = $r->name;
						$this->columnFunctions[] = null;
						$this->columnLists[] = null;
						$this->columnRLists[] = null;
						$this->columnFilters[] = FALSE;
						$this->columnAccesses[] = 1;
						$this->columnJavaScript[] = null;
						$this->columnCallBackFunctions[] = null;
						$this->fieldList[] = $r->name;
						switch($r->type){
							case "string":
								$defVal = "";
								break;
							case "int":
								$defVal = -1;
								break;
						}
						$this->add($r->name,$defVal);
					}
				else
				{
					if(isset($this->dataArray[0]))
					{
						$fi = $this->dataArray[0];
						foreach($fi as $key => $value)
						{
							$this->fields[] = $fi;
	                                                $this->columnHeaders[] = $key;
        	                                        $this->columnFunctions[] = null;
                	                                $this->columnLists[] = null;
                        	                        $this->columnRLists[] = null;
                                	                $this->columnFilters[] = FALSE;
                                        	        $this->columnAccesses[] = 1;
                                                	$this->columnJavaScript[] = null;
	                                                $this->columnCallBackFunctions[] = null;
        	                                        $this->add($key,'');
                	                                $this->fieldList[] = $key;
						}
					}
				}
			}
		}
		public function field_is_string($field){
			for($i=0;$i<count($this->fields);$i++){
				if($this->fields[$i]->name==$field && $this->fields[$i]->type!='int'){
					return TRUE;
				}
			}
			return FALSE;
		}
		public function fieldId($fieldname){
			$out = -1;
			for($i = 0;$i < count($this->fieldList);$i++){
				if($this->fieldList[$i]==$fieldname){
					$out = $i;
				}
			}
			return $out;
		}
		public function feildIndex($feild)
		{
			$out = -1;
			for($i = 0;$i < count($this->fieldList);$i++)
				if($this->fieldList[$i] == $feild)
					$out = $i;
			return($out);
		}
		public function intial(){
			if(isset($_REQUEST["group_".$this->gridName]))
			{
				$this->group = trim($_REQUEST["group_".$this->gridName]);				
			}
			$this->orderFeild = (isset($_REQUEST['orderFeild_'.$this->gridName]))?$_REQUEST['orderFeild_'.$this->gridName]:'';
			if(isset($_REQUEST["ajax_post_back_".$this->gridName]) && (int)$_REQUEST["ajax_post_back_".$this->gridName]==1 && !isset($_REQUEST['mod_'.$this->gridName]))
			{
				if(isset($_REQUEST["search_frase_".$this->gridName]))
				{
					$search_frase = $_REQUEST["search_frase_".$this->gridName];
					$object_id = $_REQUEST["object_id_".$this->gridName];
					$feild = explode("_",$object_id);
					$o = array();
					for($i = 1;$i < count($feild);$i++)
						$o[] = $feild[$i];
					$feild = implode('_',$o);
					$feildIndex = $this->feildIndex($feild);
					$columnList = $this->columnLists[$feildIndex];
					$outPut = '';
					if(is_array($columnList))
						foreach($columnList as $name => $id)
							if(strpos($name,$search_frase)!==FALSE)
								$outPut .= "<span onmouseover=\"this.style.backgroundColor='#eee';\" onmouseout=\"this.style.backgroundColor='#fff';\" style=\"color:blue;cursor:pointer;\" onclick=\"document.getElementById('new_$feild"."_back').value='$id';document.getElementById('new_$feild').value='$name';clear_ajax_result_".$this->gridName."();\">$name</span><br/>\n";
					$outPut .= '';
					echo $outPut;
				}
				else
				{
					$tmp = explode("_",$_REQUEST["fieldName"]);
					$field = "";
					for($i=2;$i<count($tmp)-1;$i++)
	                	                $field .= $tmp[$i]."_";
	                                $field .= $tmp[$i];
					$valu = $_REQUEST["value"];
					$selectedId = $_REQUEST["id_"];
					$this->mysql_class->ex_sqlx("update `".$this->tableName."` set `$field`='$valu' where id=$selectedId");
					echo 58;
				}
				exit();
			}			
			elseif(isset($_POST['selectedField_'.$this->gridName])){
				$mod = $_POST['mod_'.$this->gridName];
				$tmp = explode("_",$_POST['selectedField_'.$this->gridName]);
				$this->pageNumber = $_POST["pageNumber_".$this->gridName];
				$this->rowCount = $_POST["rowCount_".$this->gridName];
				$this->moreShown = (($_POST["moreShown_".$this->gridName]==1)?TRUE:FALSE);
                                if(count($tmp)>=3){
                                        $selectedId = $_POST[$tmp[0]."_".$tmp[1]."_id"];
                                }
				switch($mod){
					case "edit":
						$field = "";
						for($i=2;$i<count($tmp)-1;$i++){
							$field .= $tmp[$i]."_";
						}
						$field .= $tmp[$i];
						$tmp[2] = $field;		
						$fieldId = $this->fieldId($field);				
						if($this->editFunction!=null){
							$editF=$this->editFunction;
							$editF($selectedId,$tmp[2],$_POST[$_POST['selectedField_'.$this->gridName]]);
						}else{
							$valu = $_POST[$_POST['selectedField_'.$this->gridName]];
							if(isset($this->columnCallBackFunctions[$fieldId])){
								$fn = $this->columnCallBackFunctions[$fieldId];
								$valu  = $fn($_POST[$_POST['selectedField_'.$this->gridName]]);
							}
							
							if($this->echoQuery)
								echo "update `".$this->tableName."` set `".$tmp[2]."`=".(($this->field_is_string($tmp[2]))?"'":"").$valu.(($this->field_is_string($tmp[2]))?"'":"")." where `".$this->tableName."`.`id`=$selectedId";
							$this->mysql_class->ex_sqlx("update `".$this->tableName."` set `".$tmp[2]."`=".(($this->field_is_string($tmp[2]))?"'":"").$valu.(($this->field_is_string($tmp[2]))?"'":"")." where `".$this->tableName."`.`id`='$selectedId'");
						}
                                                for($i=0;$i<count($this->fieldList);$i++){
                                                        if($this->columnFilters[$i]!==FALSE)
                                                        {
                                                                if(isset($this->columnCallBackFunctions[$i]))
                                                                {
                                                                        $fn = $this->columnCallBackFunctions[$i];
                                                                        $this->columnFilters[$i] = $fn($_REQUEST[$this->gridName."_filter_$i"]);
                                                                }
                                                                else
                                                                {
                                                                        $this->columnFilters[$i] = $_REQUEST[$this->gridName."_filter_$i"];
                                                                }
                                                        }
                                                }
						break;
					case "add":
						if($this->addFunction!=null){
							$addF=$this->addFunction;
							$addF($this->loadNewFeilds($_REQUEST));
						}else{
							//adding
							$addFields = array();
							$addValues = array();
							for($i=0;$i<count($this->fieldList);$i++){								
								if($this->columnHeaders[$i]!=null){
									$addFields[] = $this->fieldList[$i];
									$addValues[] = $_POST["new_".$this->fieldList[$i]];
								}
							}
							$qur = "(";
							for($i=0;$i<count($addFields)-1;$i++){
								$qur .= '`'.$addFields[$i]."`,";
							}
							$qur .= $addFields[$i].") values (";
							for($i=0;$i<count($addValues)-1;$i++){
								$valu = $addValues[$i];

								if(isset($this->columnCallBackFunctions[$this->fieldId($addFields[$i])])){
									$fn = $this->columnCallBackFunctions[$this->fieldId($addFields[$i])];
									$valu  = $fn($addValues[$i]);
								}

								$qur .= (($this->field_is_string($addFields[$i]))?"'":"").$valu.(($this->field_is_string($addFields[$i]))?"'":"").",";
							}
							$valu = $addValues[$i];

							if(isset($this->columnCallBackFunctions[$this->fieldId($addFields[$i])])){
								$fn = $this->columnCallBackFunctions[$this->fieldId($addFields[$i])];
								$valu  = $fn($addValues[$i]);
							}

							$qur .= (($this->field_is_string($addFields[$i]))?"'":"").$valu.(($this->field_is_string($addFields[$i]))?"'":"").")";
							if($this->echoQuery)
								echo "insert into ".$this->tableName.$qur;
							$this->mysql_class->ex_sqlx("insert into `".$this->tableName."` ".$qur);
						}
                                                for($i=0;$i<count($this->fieldList);$i++){
                                                        if($this->columnFilters[$i]!==FALSE)
                                                        {
                                                                if(isset($this->columnCallBackFunctions[$i]))
                                                                {
                                                                        $fn = $this->columnCallBackFunctions[$i];
                                                                        $this->columnFilters[$i] = $fn($_REQUEST[$this->gridName."_filter_$i"]);
                                                                }
                                                                else
                                                                {
                                                                        $this->columnFilters[$i] = $_REQUEST[$this->gridName."_filter_$i"];
                                                                }
                                                        }
                                                }
						break;
					case "delete":
						if($this->deleteFunction!=null){
							$deleteF=$this->deleteFunction;
							$deleteF($selectedId);
						}else{
							if($this->echoQuery)
								echo"delete from `".$this->tableName."` where `".$this->tableName."`.`id`=$selectedId";
							$this->mysql_class->ex_sqlx("delete from `".$this->tableName."` where `".$this->tableName."`.`id`=$selectedId");
						}
                                                for($i=0;$i<count($this->fieldList);$i++){
                                                        if($this->columnFilters[$i]!==FALSE)
                                                        {
                                                                if(isset($this->columnCallBackFunctions[$i]))
                                                                {
                                                                        $fn = $this->columnCallBackFunctions[$i];
                                                                        $this->columnFilters[$i] = $fn($_REQUEST[$this->gridName."_filter_$i"]);
                                                                }
                                                                else
                                                                {
                                                                        $this->columnFilters[$i] = $_REQUEST[$this->gridName."_filter_$i"];
                                                                }
                                                        }
                                                }
						break;
					case "next":
						if($this->pageNumber+$this->pageCount<=$this->rowCount){
							$this->pageNumber += $this->pageCount;
						}else{
							$this->pageNumber = 0;
						}
                                                for($i=0;$i<count($this->fieldList);$i++){
                                                        if($this->columnFilters[$i]!==FALSE)
                                                        {
                                                                if(isset($this->columnCallBackFunctions[$i]))
                                                                {
                                                                        $fn = $this->columnCallBackFunctions[$i];
                                                                        $this->columnFilters[$i] = $fn($_REQUEST[$this->gridName."_filter_$i"]);
                                                                }
                                                                else
                                                                {
                                                                        $this->columnFilters[$i] = $_REQUEST[$this->gridName."_filter_$i"];
                                                                }
                                                        }
                                                }
						break;
					case "prev":
						if($this->pageNumber-$this->pageCount>=0){
							$this->pageNumber -= $this->pageCount;
						}else{
							$this->pageNumber = $this->rowCount - $this->pageCount;
						}
                                                for($i=0;$i<count($this->fieldList);$i++){
                                                        if($this->columnFilters[$i]!==FALSE)
                                                        {
                                                                if(isset($this->columnCallBackFunctions[$i]))
                                                                {
                                                                        $fn = $this->columnCallBackFunctions[$i];
                                                                        $this->columnFilters[$i] = $fn($_REQUEST[$this->gridName."_filter_$i"]);
                                                                }
                                                                else
                                                                {
                                                                        $this->columnFilters[$i] = $_REQUEST[$this->gridName."_filter_$i"];
                                                                }
                                                        }
                                                }
						break;
					case "group":
						$ids = explode(",",$this->group);
						if($this->deleteFunction!=null)
						{
							$deleteF=$this->deleteFunction;
							for($index_i = 0;$index_i < count($ids);$index_i++)
							{
								if((int)$ids[$index_i]>0)
								{
									$deleteF((int)$ids[$index_i]);
								}
							}
						}
						else
						{
							$tmp = "";
							for($index_i = 0;$index_i < count($ids);$index_i++)
							{
								if((int)$ids[$index_i]>0)
								{
									$tmp .= " `".$this->tableName."`.`id`='".(int)$ids[$index_i]."' or";
								}
							}
							if($tmp!="")
							{
								$tmp = substr($tmp,0,strlen($tmp)-3);
								$tmp = " where $tmp";
								$this->mysql_class->ex_sqlx("delete from `".$this->tableName."` $tmp");
							}
						}
						break;
					case "refreshcombo":
						$this->newFieldRefresh = array();
                                                for($i=0;$i<count($this->fieldList);$i++){                                                            
							$this->newFieldRefresh[$this->fieldList[$i]] = $_POST["new_".$this->fieldList[$i]];
                                                }
						break;
					case "filter":
                                                for($i=0;$i<count($this->fieldList);$i++){
                                                        if($this->columnFilters[$i]!==FALSE)
							{
								if(isset($this->columnCallBackFunctions[$i]))
								{
									$fn = $this->columnCallBackFunctions[$i];
									$this->columnFilters[$i] = $fn($_REQUEST[$this->gridName."_filter_$i"]);
								}
								else
								{
									$this->columnFilters[$i] = $_REQUEST[$this->gridName."_filter_$i"];
								}
                                                        }
                                                }
						break;
					default:
						$test = explode(',',$mod);
						if(count($test)==2 && $test[0] ==  'sort')
						{
							$test2 = explode(',',$this->orderFeild);
							if($test2[0] == $test[1] && $test2[1] == 'asc')
								$this->orderFeild = $test[1].',desc';
							else if($test2[0] == $test[1] && $test2[1] == 'desc')
								$this->orderFeild = $test[1].',asc';
							else if($test2[0] != $test[1])
								$this->orderFeild = $test[1].',asc';
						}
						for($i=0;$i<count($this->fieldList);$i++){
                                                        if($this->columnFilters[$i]!==FALSE)
                                                        {
                                                                if(isset($this->columnCallBackFunctions[$i]))
                                                                {
                                                                        $fn = $this->columnCallBackFunctions[$i];
                                                                        $this->columnFilters[$i] = $fn($_REQUEST[$this->gridName."_filter_$i"]);
                                                                }
                                                                else
                                                                {
                                                                        $this->columnFilters[$i] = $_REQUEST[$this->gridName."_filter_$i"];
                                                                }
                                                        }
                                                }
						break;
				}
			}
		}
		private function add( /*string*/ $name = null, /*int*/ $enum = null ) {
			if( isset($enum) ){
				$this->$name = $enum;
			}
			else{
				$this->$name = end($this) + 1;
			}
		}
		public function testSort(){
			$out = trim($this->query);			
			return($out);
		}
		private function arrayToString($inp){
			$out = "";
			for($i=0;$i<count($inp);$i++){
				$out .= "`".$inp[$i]."`,";
			}
			$out = substr($out,0,-1);
			return $out;
		}
		public function columnListToCombo($columnList,$sel){
			if(isset($this->list2) && $this->list2)
			{
				$out = "<option value=\"\">\n&nbsp\n</option>\n";
                                foreach($columnList as $value => $text){
                                        $out .= "<option value=\"$value\" ".(($value==$sel)?"selected=\"selected\"":"")." >\n";
                                        $out .= $text."\n";
                                        $out .= "</option>\n";
                                }
			}
			else
			{
				$out = "<option value=\"\">\n&nbsp\n</option>\n";
				foreach($columnList as $text => $value){
					$out .= "<option value=\"$value\" ".(($value==$sel)?"selected=\"selected\"":"")." >\n";
					$out .= $text."\n";
					$out .= "</option>\n";
				}
			}
			return $out;
		}
		public function loadNewFeilds($req=null)
		{
			$fields = null;
			if($req!=null && is_array($req))
		                foreach($req as $key => $value)
                		        if(substr($key,0,4)=="new_")
		                        	$fields[substr($key,4)] = $value;
				
			return($fields);
		}
		public function createAddQuery($feilds)
		{
			$fi = "(";
                        $valu="(";
                        foreach ($feilds as $field => $value)
                        {
        	                $fi.="`$field`,";
	                        $valu .="'$value',";
                        }
                        $fi=substr($fi,0,-1);
                        $valu=substr($valu,0,-1);
                        $fi.=")";
                        $valu.=")";
			$out = array('fi'=>$fi,'valu'=>$valu);
			return($out);
		}
		public function columnRListToCombo($columnRList,$sel,$ref)
		{
			$out = "<option value=\"\">\n&nbsp\n</option>\n";
			$columnList = $columnRList["list"];
                        foreach($columnList as $cell){
				if($ref == $cell["ref"])
				{
					$value = $cell["value"];
					$text = $cell["text"];
                	                $out .= "<option value=\"$value\" ".(($value==$sel)?"selected=\"selected\"":"")." >\n";
        	                        $out .= $text."\n";
	                                $out .= "</option>\n";
				}
                        }
                        return $out;
		}
		private function createPageNumbers(){
			$out = "";
			if($this->pageCount<=0)
				return($out);
			$count = 0;
			if($this->rowCount % $this->pageCount>0){
				$count = 1;
			}
			$count += (($this->rowCount-($this->rowCount % $this->pageCount))/$this->pageCount);
			if($this->pageIndexMode == 0)
			{			
				$out = "&nbsp;";
				for($i = 1;$i<=$count;$i++){
					$out .= ((($this->pageNumber/$this->pageCount)+1==$i)?$i."&nbsp;":"<a href=\"#\" onclick=\"gotoPage_".$this->gridName."($i);\" />$i</a>&nbsp;");
				}
			}
			else
			{
				if($count>1)
				{
					$out = "<select id=\"pageSelector\" name=\"pageSelector\" onchange=\"gotoPage_".$this->gridName."(this.selectedIndex+1);\">\n";
					for($i = 1;$i<=$count;$i++)
					{
						$out .= "<option".((($this->pageNumber/$this->pageCount)+1==$i)?" selected=\"selected\"":"")." value=\"$i\">\n";
						$out .= "$i\n";
						$out .= "</option>\n";
					}
					$out .= "</select>\n";	
				}
			}
			return($out);
		}
		public function executeQuery(){
			if($this->class_enabled){


                                if($this->query != "" && $this->loadQueryField){
                                        $this->mysql_class->ex_sql($this->query,$q);
                                        $this->canEdit = FALSE;
                                        $this->canAdd = FALSE;
                                        $this->canDelete = FALSE;
                                        $this->fields = array();
/*
                                        $this->columnHeaders = array();
                                        $this->columnFunctions = array();
                                        $this->columnLists = array();
                                        $this->columnRLists = array();
                                        $this->columnFilters = array();
                                        $this->columnAccesses = array();
                                        $this->columnJavaScript = array();
                                        $this->columnCallBackFunctions = array();
*/
                                        $this->fieldList = array();
	                                while($r=mysql_fetch_field($q)){
                                        	$this->fields[] = $r;
/*
                                	        $this->columnHeaders[] = $r->name;
                        	                $this->columnFunctions[] = null;
                	                        $this->columnLists[] = null;
        	                                $this->columnRLists[] = null;
	                                        $this->columnFilters[] = FALSE;
                                        	$this->columnAccesses[] = 1;
                                	        $this->columnJavaScript[] = null;
                        	                $this->columnCallBackFunctions[] = null;
*/
                	                        $this->fieldList[] = $r->name;
	                                }
				}
				

				$filter = "";
				//$columnListVar = (($this->list2)?$this->columnLists2:$this->columnLists);
				$columnListVar = $this->columnLists;
				for($i = 0;$i < count($this->columnFilters);$i++)
				{
					if($this->columnFilters[$i] !==FALSE && $this->columnFilters[$i] !==TRUE && $this->columnFilters[$i] != "")
					{
						if($columnListVar[$i] == null && $this->columnRLists[$i] == null)
						{
							$callbackfn = $this->columnCallBackFunctions[$i];
//							if($callbackfn == null)
								$filter .= " `".$this->fieldList[$i]."` like '%".$this->columnFilters[$i]."%' and";
//							else
//								$filter .= " `".$this->fieldList[$i]."` like '%".$callbackfn($this->columnFilters[$i])."%' and";
						}
						else
							$filter .= " `".$this->fieldList[$i]."` = '".$this->columnFilters[$i]."' and";
					}
				}
				if($filter != "")
				{
					if(trim($this->whereClause)!='')
						 $filter .= ' and ';
					$filter = substr($filter,0,-4);
				}
				if($this->orderFeild != '' && $this->sortEnabled)
				{
					$of = explode(',',$this->orderFeild);
					$ds = $of[1];
					$of = $of[0];
					$oc = '';
					$wc0 = '';
					$wc = explode('order',strtolower($this->whereClause));
                                       	$oc = ' order by `'.$of.'` ';
					$wc0 = $wc[0];
					if(count($wc)>1)
					{
						$wc = $wc[1];
						$wc = explode('by',$wc);
						$wc = $wc[1];
						$wc0 .= $oc . ',' . $wc;
						$wc = explode('group',$wc0);
						$wc0 = $wc[0] . $ds . ((isset($wc[1]))?$wc[1]:'');
					}
					else
					{
						$wc = $wc0;
						$wc = explode('group',$wc);
						$wc0 = $wc[0];
						$wc0 .= $oc . $ds . ((count($wc)>1)?' group '.$wc[1]:'');
					}
					$this->whereClause = $wc0;
				}
				if($this->query == "" && !$this->loadArray){
					$this->mysql_class->ex_sql('select '.$this->arrayToString($this->fieldList).' from `'.$this->tableName.'` '.$this->extraClause.' where '.$filter.$this->whereClause,$this->dataSet);
				}else if(!$this->loadArray){
					$this->mysql_class->ex_sql($this->query,$this->dataSet);
				}
				if(!$this->loadArray){
					$temp_dataset = $this->dataSet;
					while($r = mysql_fetch_array($temp_dataset)){
						if($this->sums != null){
							foreach($this->sums as $sum_key => $sum_value){
								if(isset($r[$sum_key])){
									$this->sums[$sum_key] = $r[$sum_key];
								}
							}
						}
					}
					$this->rowCount = mysql_num_rows($this->dataSet);
				}
				else
					$this->rowCount = count($this->dataArray);
				$this->dataSet = null;
				$mod = ((isset($_POST['mod_'.$this->gridName]))?$_POST['mod_'.$this->gridName]:"");
				if($this->gotoLast && $mod == "add")
				{
					$this->pageNumber = $this->rowCount - ($this->rowCount % $this->pageCount);
					if($this->pageNumber == $this->rowCount)
						$this->pageNumber-=$this->pageCount;
				}
				if($this->query == "" && !$this->loadArray){
					$this->mysql_class->ex_sql("select ".$this->arrayToString($this->fieldList)." from ".$this->tableName." ".$this->extraClause." where $filter ".$this->whereClause.(($this->pageCount>0)?" limit ".$this->pageNumber.",".$this->pageCount:''),$this->dataSet);
				}else if(!$this->loadArray){
					$this->mysql_class->ex_sql($this->query.(($this->pageCount>0)?" limit ".$this->pageNumber.",".$this->pageCount:''),$this->dataSet);
				}
			}	
		}
		public function hasRList()
		{
			$out = FALSE;
			foreach($this->columnRLists as $rlist)
			{
				if($rlist != null)
					$out = TRUE;
			}
			return($out);
		}		
		public function getGrid(){
			$this->hideIndex = ($this->hideIndex > 2 && $this->hideIndex<count($this->columnHeaders)-1) ? $this->hideIndex : 999;
/*
			function $gridname($inp){
                        	return($inp);
                	}
*/
			$khonsa = create_function('$inp','return($inp);');
/*
			function isOddj($inp)
			{
				$out = TRUE;
				if((int)$inp % 2 == 0 ){
					$out = FALSE;
				}
				return ($out);
			}
*/
			//$columnListVar = (($this->list2)?$this->columnLists2:$this->columnLists);
			$columnListVar = $this->columnLists;
			$isOddj = create_function('$inp','$out = TRUE;if((int)$inp % 2 == 0 ) $out = FALSE;return ($out);');
			$out = $this->error_message;
			if($this->class_enabled){
			$current_filename = $_SERVER['REQUEST_URI'];
			$current_filename = explode("?",$current_filename);
			$current_filename = $current_filename[0];
			$group = $this->group;
			$moreShown = (($this->moreShown)?'1':'0');
			$out = <<<Holly
			<div id="$this->gridName" $this->divProperty >
				<form id="frm_$this->gridName" method="post">
				<input type="hidden" id="selectedField_$this->gridName" name="selectedField_$this->gridName" value="" />
				<input type="hidden" id="mod_$this->gridName" name="mod_$this->gridName" value=""/>
				<input type="hidden" id="pageNumber_$this->gridName" name="pageNumber_$this->gridName" value="$this->pageNumber" />
				<input type="hidden" id="pageCount_$this->gridName" name="pageCount_$this->gridName" value="$this->pageCount" />
				<input type="hidden" id="rowCount_$this->gridName" name="rowCount_$this->gridName" value="$this->rowCount" />
				<input type="hidden" id="ajax_post_back_$this->gridName" name="ajax_post_back_$this->gridName" value = "$this->enableAjax" />
				<input type="hidden" id="group_$this->gridName" name="group_$this->gridName" value="$group" />
				<input type="hidden" id="orderFeild_$this->gridName" name="orderFeild_$this->gridName" value="$this->orderFeild" />
				<input type="hidden" id="moreShown_$this->gridName" name="moreShown_$this->gridName" value="$moreShown" />
				<script language="javascript">
		                        function showHint_$this->gridName(obj,hint)
                		        {
                                		if(obj && obj.value=='')
		                                {
                		                        obj.value = hint;
                                		        obj.style.color='gray';
		                                }
                		        }
		                        function hideHint_$this->gridName(obj,hint)
                		        {
		                                if(obj && obj.value == hint && obj.style.color == 'gray')
                		                {
                                		        obj.value = '';
		                                        obj.style.color = 'black';
                		                }
		                        }
					function readyToEdit_$this->gridName(gridName,fieldName){ 
						var is_ajax = document.getElementById("ajax_post_back_$this->gridName").value;
						if(document.getElementById(fieldName)){
							//if(document.getElementById(fieldName).value){
								document.getElementById(fieldName).style.display='block';
								if(document.getElementById(fieldName+'_back')){document.getElementById(fieldName+'_back').style.display='none';}
								document.getElementById(fieldName).focus();
							//}
							document.getElementById('selectedField_$this->gridName').value = fieldName;
							document.getElementById('mod_$this->gridName').value="edit";
						} 
					}
					function readyToDelete_$this->gridName(fieldName){
						document.getElementById('mod_$this->gridName').value="delete";
						document.getElementById('selectedField_$this->gridName').value = fieldName;
						extra_sendObj_$this->gridName("frm_$this->gridName");
					}
					function readyToAdd_$this->gridName(){
						$this->addButtonScript
						document.getElementById('mod_$this->gridName').value="add";
                                                extra_sendObj_$this->gridName("frm_$this->gridName");
					}


					var xmlhttp;
					if (window.XMLHttpRequest)
					{// code for IE7+, Firefox, Chrome, Opera, Safari
						xmlhttp=new XMLHttpRequest();
					}
					else
					{// code for IE6, IE5
						xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
					}

					function stopProg(fieldname)
					{
						var obj = document.getElementById(fieldname+"_img");
						obj.style.display="none";
					}
					function startProg(fieldname)
					{
						var obj = document.getElementById(fieldname+"_img");
						obj.style.display="block";
					}

					function extra_sendObj_$this->gridName(frm){
						//Creating Ajax OBJECT
	                                        var xmlhttp;
        	                                if (window.XMLHttpRequest)
                	                        {// code for IE7+, Firefox, Chrome, Opera, Safari
                        	                        xmlhttp=new XMLHttpRequest();
                                	        }
                                        	else
	                                        {// code for IE6, IE5
        	                                        xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
                	                        }
						var is_ajax = document.getElementById("ajax_post_back_$this->gridName").value;
						var fieldName = document.getElementById('selectedField_$this->gridName').value;
						if(is_ajax==0 || document.getElementById('mod_$this->gridName').value!='edit')
						{
							document.getElementById(frm).submit();
						}
						if(is_ajax==1 && document.getElementById('mod_$this->gridName').value=='edit')
						{
							//Send By Ajax
							if(document.getElementById(fieldName).tagName.toLowerCase() == 'select')
                                                        {
                                                                        //it is SELECT OBJECT
                                                        }
                                                        else
                                                        {
                                                                   //it is NOT SELECT
	                                                        document.getElementById(fieldName+'_back').innerHTML = document.getElementById(fieldName).value;
                                                        }
							if(document.getElementById(fieldName+'_back')){document.getElementById(fieldName+'_back').style.display='block';}
							xmlhttp.onreadystatechange=function()
								{
									if (xmlhttp.readyState==4 && xmlhttp.status==200)
									{
										//alert('"'+xmlhttp.responseText+'"');
										var res_tmp = xmlhttp.responseText;
										if(parseInt(res_tmp,10)==58)
										{
											stopProg(fieldName);
										}
										else
										{
											//alert("خطا در بروزرسانی");
											alert("$this->error_message_ajax");
											stopProg(fieldName);
										}
									}
}
							var val = document.getElementById(fieldName).value;
							var tmp = String(fieldName).split("_");
							var rownum = tmp[1];
							var gridname = String("$this->gridName");
							var idd = document.getElementById(gridname+"_"+rownum+"_id").value;
							//alert("$current_filename?ajax_post_back_$this->gridName=1&fieldName="+fieldName+"&value="+val+"&id ="+idd+"&r="+Math.random()+"&");
							xmlhttp.open("GET","$current_filename?ajax_post_back_$this->gridName=1&fieldName="+fieldName+"&value="+val+"&id ="+idd+"&r="+Math.random()+"&",true);
							xmlhttp.send();
							startProg(fieldName);
						}
					}
					function mover_$this->gridName(j){
						document.getElementById('delete_$this->gridName'+'_'+j).style.display="block";
					}
                                        function mout_$this->gridName(j){
                                                document.getElementById('delete_$this->gridName'+'_'+j).style.display="none";
                                        }
					function ifEnter_$this->gridName(e){
						var out = false;
					        var keycode;                  
					        if (window.event) keycode = window.event.keyCode;
					        else if (e) keycode = e.which;
					        if(parseInt(keycode,10)==13){
					                out = true;
					        }
						return(out);				
					}
					function nextPage_$this->gridName(){
                        			document.getElementById('mod_$this->gridName').value="next";
                                                extra_sendObj_$this->gridName("frm_$this->gridName");						
					}
					function prevPage_$this->gridName(){
                        			document.getElementById('mod_$this->gridName').value="prev";
                                                extra_sendObj_$this->gridName("frm_$this->gridName");						
					}
					function gotoPage_$this->gridName(pageindex){
						var pageCount = parseInt(document.getElementById('pageCount_$this->gridName').value,10);
						document.getElementById('pageNumber_$this->gridName').value=(pageindex-1)*pageCount;
                        			document.getElementById('mod_$this->gridName').value="goto";
                                                extra_sendObj_$this->gridName("frm_$this->gridName");						
					}
					function checkall_$this->gridName(obj)
					{
						var checks = document.getElementsByTagName('input');
						for(var i = 0;i<checks.length;i++)
						{
							if(checks[i].type == 'checkbox' && checks[i].id.substring(0,6) == 'check_')
							{
								checks[i].checked = !obj.checked;
								checks[i].click();
							}
						}
					}
					function check_$this->gridName(obj,rowNumber)
					{
						if(obj)
						{
							var id = '$this->gridName'+'_'+String(rowNumber)+'_id';
							id = document.getElementById(id).value;
							var group = document.getElementById('group_$this->gridName');
							var ids = group.value.split(',');
							group.value = '';
							for(var i=0;i<ids.length;i++)
							{
								if(parseInt(ids[i],10)!=parseInt(id,10) && ids[i]!='')
								{
									group.value += ids[i]+',';
								}
							}
							if(obj.checked)
								group.value += id;
							else
								group.value = group.value.substring(0,group.value.length-1);
						}
					}
					function groupDelete_$this->gridName()
					{
						var group = document.getElementById('group_$this->gridName');
						if(group.value != '' && group.value != ' ')
						{
							document.getElementById('mod_$this->gridName').value="group";
        	                                        extra_sendObj_$this->gridName("frm_$this->gridName");
							
						}
					}
					function refresh_combos_$this->gridName(ref)
					{
						document.getElementById('mod_$this->gridName').value="refreshcombo";
						extra_sendObj_$this->gridName("frm_$this->gridName");
					}
					function filter_$this->gridName()
					{
						document.getElementById('mod_$this->gridName').value="filter";
						extra_sendObj_$this->gridName("frm_$this->gridName");
					} 
					function sort_$this->gridName(feild)
					{
						document.getElementById('mod_$this->gridName').value="sort,"+feild;
						extra_sendObj_$this->gridName("frm_$this->gridName");
					}
					function showMore_$this->gridName(hideIndex,kol_ch)
					{
						var ths = document.getElementsByTagName('th');
						var tds = document.getElementsByTagName('td');
						var tmp;
						var isHide = true;
						for(var i=0;i<ths.length;i++)
						{
							tmp = ths[i].id.split('_');
							if(tmp.length == 3 && tmp[0] == 'hide' && parseInt(tmp[2],10)>=hideIndex)
							{
								if(ths[i].style.display == 'none')
								{
									ths[i].style.display = '';
									isHide = false;
								}
								else
									ths[i].style.display = 'none';
							}
						}
                                                for(i=0;i<tds.length;i++)
                                                {
                                                        tmp = tds[i].id.split('_');
                                                        if(tmp.length == 3 && tmp[0] == 'hide' && parseInt(tmp[2],10)>=hideIndex)
                                                        {
                                                                if(tds[i].style.display == 'none')
                                                                        tds[i].style.display = '';
                                                                else
                                                                        tds[i].style.display = 'none';
                                                        }
                                                }
						if(document.getElementById('add_footer'))
						{
							if(isHide)
							{
								document.getElementById('add_footer').colSpan = String(parseInt(document.getElementById('add_footer').colSpan,10)-kol_ch);
							}
							else
							{
								document.getElementById('add_footer').colSpan = String(parseInt(document.getElementById('add_footer').colSpan,10)+kol_ch);
							}
						}
                                                if(document.getElementById('delete_footer'))
                                                {
                                                        if(isHide)
                                                        {
                                                                document.getElementById('delete_footer').colSpan = String(parseInt(document.getElementById('delete_footer').colSpan,10)-kol_ch);
                                                        }
                                                        else
                                                        {
                                                                document.getElementById('delete_footer').colSpan = String(parseInt(document.getElementById('delete_footer').colSpan,10)+kol_ch);
                                                        }
                                                }
						if(document.getElementById('more_button'))
						{
							if(isHide)
								document.getElementById('more_button').value = '$this->moreButton';
							else
								document.getElementById('more_button').value = '$this->lessButton';
						}
						if(!isHide && document.getElementById('moreShown_$this->gridName'))
							document.getElementById('moreShown_$this->gridName').value = '1';
						else if(document.getElementById('moreShown_$this->gridName'))
							document.getElementById('moreShown_$this->gridName').value = '0';
					}
		                        function mehrdad_ajaxFunction_$this->gridName(func,command,obj){
                		                var ajaxRequest;  // The variable that makes Ajax possible!
                                		try{
		                                        // Opera 8.0+, Firefox, Safari
                		                        ajaxRequest = new XMLHttpRequest();
                                		} catch (e){
		                                        // Internet Explorer Browsers
                		                        try{
                                		                ajaxRequest = new ActiveXObject("Msxml2.XMLHTTP");
		                                        } catch (e) {
                		                                try{
                                		                        ajaxRequest = new ActiveXObject("Microsoft.XMLHTTP");
                                                		} catch (e){
		                                                        // Something went wrong
                		                                        alert("ﻡﺭﻭﺭگﺭ ﺶﻣﺍ ﻕﺎﺒﻟیﺕ ﺁژﺍکﺱ ﺭﺍ ﻥﺩﺍﺭﺩ ﻞﻄﻓﺍً ﺍﺯ ﻡﺭﻭﺭگﺭ ﺝﺩیﺪﺗﺭ ﻭ پیﺵﺮﻔﺘﻫ ﺕﺭی ﻡﺎﻨﻧﺩ ﻑﺍیﺮﻓﺍکﺱ ﺎﺴﺘﻓﺍﺪﻫ کﻥیﺩ");
                                		                        return false;
                                                		}
		                                        }
                		                }
                                		// Create a function that will receive data sent from the server
		                                var ser = obj.value;
                		                ajaxRequest.onreadystatechange = function(){
                                		        if(ajaxRequest.readyState == 4){
                                                		var ar = ajaxRequest.responseText;
		                                                func(command,ar,obj);
                		                        }
                                		};
                                		ajaxRequest.open("POST", "$current_filename?ajax_post_back_$this->gridName=1&search_frase_$this->gridName="+ser+"&object_id_$this->gridName="+obj.id+"&r="+Math.random()+"&", true);
		                                ajaxRequest.send(null); 
                		        }
		                        function search_back_$this->gridName(command,ar,obj)
                		        {
                                		var div = document.getElementById('ajax_result_$this->gridName');
		                                div.style.position = 'absolute';
                		                div.style.borderStyle = 'solid';
                                		div.style.borderWidth = '2px';
						div.style.borderColor = 'gray';
						div.style.textAlign = 'right';
						div.style.backgroundColor = '#fff';
						var pos=findPos_$this->gridName(obj);
		                                div.style.width = String(obj.clientWidth+6)+'px';
                		                div.style.left = String(pos[0])+'px';
                                		div.style.top = String(pos[1]+obj.clientHeight+3)+'px';
		                                div.innerHTML =ar;
                		        }
		                        function search_$this->gridName(obj)
                		        {
                                		if(obj.value)
                		                        mehrdad_ajaxFunction_$this->gridName(search_back_$this->gridName,'search',obj);
		                        }
					function findPos_$this->gridName(obj)
					{
						var curleft = curtop = 0;
						if (obj.offsetParent) {
							do
							{
								curleft += obj.offsetLeft;
								curtop += obj.offsetTop;
							}while (obj = obj.offsetParent);
						}
						return [curleft,curtop];
					}
					function clear_ajax_result_$this->gridName()
					{
						document.getElementById('ajax_result_$this->gridName').innerHTML = '';
						document.getElementById('ajax_result_$this->gridName').style.width = 0;
						document.getElementById('ajax_result_$this->gridName').style.left = 0;
						document.getElementById('ajax_result_$this->gridName').style.top = 0;
						document.getElementById('ajax_result_$this->gridName').style.borderStyle = 'none';
					}
					function intial_body_$this->gridName()
					{
						document.body.onclick = clear_ajax_result_$this->gridName;
					}
					setTimeout("intial_body_$this->gridName()",2000);
				</script>
				<div id="ajax_result_$this->gridName"></div>
Holly;
			$sums = null;
			if(is_array($this->eRequest)){
				foreach($this->eRequest as $key => $value){
					$out .= "<input type=\"hidden\" id=\"$key\" name=\"$key\" value=\"$value\" />\n";
				}
			}
			$checkbox_width = $this->checkbox_width;
			$index_width = $this->index_width;
			$out .= "<table cellpadding=\"0\" cellspacing=\"0\" width=\"" . $this->width . "\" style=\"border-style:solid;border-width:1px;border-color:Black;\">\n";
			$out .= "<tr class=\"" . $this->cssClass . "_header\" >\n<th class='".$this->cssClass."_gHead' style=\"width:$index_width;display:".(($this->showIndex)?"":"none").";\">\n";
			$out .= $this->indexHeader."\n";
			$out .= "<input type=\"hidden\" id=\"row_number\" value=\"0\" />";
			$out .= "</th>\n";
			if($this->canDelete)
			{
				$out .= "<th class='".$this->cssClass."_gHead' style=\"width:$checkbox_width;\" >\n";
				$out .= " <input type=\"checkbox\" id=\"checkboxall_".$this->gridName."\" onclick=\"checkall_".$this->gridName."(this);\" />\n";
				$out .= "</th>\n";
			}
			for($i=0;$i<sizeof($this->columnHeaders);$i++){
				$out .= "<th ".(($i>=$this->hideIndex)?"id=\"hide_head_$i\"":'')." class='".$this->cssClass."_gHead' style=\"".(($this->columnHeaders[$i]!=null && $i<$this->hideIndex)?"":"display:none;")."\">\n";
                                if($this->columnFunctions[$i]==null){
	                                $fn = $khonsa;
                                }else{
                                        $fn = $this->columnFunctions[$i];
                                }
				if($this->columnFilters[$i] !== FALSE)
				{
					$value = (($this->columnFilters[$i]===TRUE)?"":$this->columnFilters[$i]);
					if($columnListVar[$i]==null && $this->columnRLists[$i]==null)
					{
						$out .= "<span ".(($this->sortEnabled)?"class=\"".$this->cssClass."_columnHeader\" onclick=\"sort_".$this->gridName."('".$this->fieldList[$i]."');\"":"").">".$this->columnHeaders[$i] . "</span><br/>\n<input type=\"text\" class=\"".$this->cssClass."_filterBox\" value=\"".$fn($value)."\" id=\"".$this->gridName."_filter_$i\" name=\"".$this->gridName."_filter_$i\" onkeypress=\"if(ifEnter_$this->gridName(event)){filter_$this->gridName();}\"/>";
					}else if($columnListVar[$i]!=null && $this->columnRLists[$i]==null){
                                                $out .= "<span ".(($this->sortEnabled)?"class=\"".$this->cssClass."_columnHeader\" onclick=\"sort_".$this->gridName."('".$this->fieldList[$i]."');\"":"").">".$this->columnHeaders[$i] . "</span><select class=\"".$this->cssClass."_inp\"  id=\"".$this->gridName."_filter_$i\" name=\"".$this->gridName."_filter_$i\" onchange=\"filter_$this->gridName();\" onkeypress=\"if(ifEnter_$this->gridName(event)){filter_$this->gridName();}\" >\n" . $this->columnListToCombo($columnListVar[$i],$value)."</select>\n";
                                        }else
                                        {
                                                $ref = $r[$this->columnRLists[$i]["ref"]];
                                                $out .= "<span ".(($this->sortEnabled)?"class=\"".$this->cssClass."_columnHeader\" onclick=\"sort_".$this->gridName."('".$this->fieldList[$i]."');\"":"").">".$this->columnHeaders[$i] . "</span><select class=\"".$this->cssClass."_inp\"  id=\"".$this->gridName."_filter_$i\" name=\"".$this->gridName."_filter_$i\" onchange=\"filter_$this->gridName();\" onkeypress=\"if(ifEnter_$this->gridName(event)){filter_$this->gridName();}\" >\n" . $this->columnRListToCombo($this->columnRLists[$i],$value,$ref)."</select>\n";
                                        }
				}
				else
				{
					$out .= "<span ".(($this->sortEnabled)?"class=\"".$this->cssClass."_columnHeader\" onclick=\"sort_".$this->gridName."('".$this->fieldList[$i]."');\"":"").">".$this->columnHeaders[$i] . "</span>\n";
				}
				$out .= "</th>\n";
			}
			$out .= "</tr>\n";
			$j=1;
			if(!$this->loadArray)
			{
				$r_ok = ($r = mysql_fetch_array($this->dataSet,MYSQL_ASSOC));
			}
			else
			{
				$r_ok = ($j-1 < count($this->dataArray));
				$r = (isset($this->dataArray[$j-1])) ? $this->dataArray[$j-1] : array();
			}
			while($r_ok){
				if($isOddj($j)){
					$out .= "<tr class=\"" . $this->cssClass . "_row_odd\">\n";				
				}else{
					$out .= "<tr class=\"" . $this->cssClass . "_row_even\">\n";
				}
				$i = 0;
				$out .= "<td ".(($this->canDelete)?"onmouseover=\"mover_".$this->gridName."('$j');\" onmouseout=\"mout_".$this->gridName."('$j');\"":"")." class=\"" . $this->cssClass . "_row_td\" style=\"display:".(($this->showIndex)?"":"none").";\">\n";
				$out .= ($j+$this->pageNumber) . "\n";
/*				if($this->canEdit){
					$out .= " <u id=\"edit_" . "$j\" style=\"display:none;\"><span style=\"color:Blue;cursor:pointer;\" onclick=\"document.getElementById('row_number').value='$j';" . $this->editFunction . "\">اصلاح</span></u> ";					
				}
*/				if($this->canDelete){
					$out .= " <u id=\"delete_".$this->gridName."_$j\" style=\"display:none;\"><span style=\"color:Blue;cursor:pointer;\" onclick=\"if(confirm('".$this->askDelete."')){readyToDelete_".$this->gridName."('".$this->gridName."_$j" . "_id');}\">حذف</span></u> ";
				}
				$out .= "</td>\n";
				if($this->canDelete)
				{
					$out .= "<td class=\"" . $this->cssClass . "_row_td\">\n";
					$out .= " <input type=\"checkbox\" id=\"check_$j\" onclick=\"check_".$this->gridName."(this,$j);\" />\n";
					$out .= "</td>\n";
				}
				if($j==1){
					$sums =$r;
					if($sums!=null)
					{
						foreach($sums as $key=>$value){
							$sums[$key]=0;
						}
					}
				}
				for($hasan=0;$hasan<count($this->fieldList);$hasan++){
					$key = $this->fieldList[$hasan];
					$value = $r[$key];
					if($this->columnFunctions[$i]==null){
						$fn = $khonsa;
					}else{
						$fn = $this->columnFunctions[$i];
					}
					$mehrdad_fn = $fn($value);
					if($this->columnHeaders[$i]!=null){
						$out .= "<td ".(($i>=$this->hideIndex)?"id=\"hide_$j"."_$i\"":'')." class=\"" . $this->cssClass . "_row_td\" ".(($hasan>=$this->hideIndex)?' style="display:none;" ':'')." ".(($this->columnAccesses[$i]==1 && $this->canEdit && $columnListVar[$i]==null)?"onclick=\"readyToEdit_".$this->gridName."('".$this->gridName."','".$this->gridName."_$j" . "_$key');\"":"").">\n";
						if($columnListVar[$i]==null && $this->columnRLists[$i]==null){
							$out .= "<span id=\"".$this->gridName."_$j" . "_$key"."_back\" style=\"display:block;\">".(($mehrdad_fn!='')?$mehrdad_fn:'&nbsp;') . "\n</span><input class=\"".$this->cssClass."_inp\" ".(($this->columnAccesses[$i]==1 && $this->canEdit)?"":"readonly=\"readonly\"")." type=\"text\" id=\"".$this->gridName."_$j" . "_$key\" name=\"".$this->gridName."_$j" . "_$key\" value=\"".(($this->columnCallBackFunctions[$i]!=null)?$mehrdad_fn:$value)."\" style=\"display:none;\" onblur=\"this.style.display='none';document.getElementById('".$this->gridName."_$j" . "_$key"."_back').style.display='block';".(($this->submitOnBlur)?"extra_sendObj_".$this->gridName."('frm_".$this->gridName."');":"")."\"  onkeypress=\"if(ifEnter_$this->gridName(event)){this.style.display='none';extra_sendObj_".$this->gridName."('frm_".$this->gridName."');".$this->attachEnterKey."}\" ".(($this->columnJavaScript[$i]!=null)?$this->columnJavaScript[$i]:"").">";
						}else if($columnListVar[$i]!=null && $this->columnRLists[$i]==null){
							$out .= "<select ".(($this->columnJavaScript[$i]!=null)?$this->columnJavaScript[$i]:"")." class=\"".$this->cssClass."_inp\" ".(($this->columnAccesses[$i]==1 && $this->canEdit)?"":"disabled=\"disabled\"")." id=\"".$this->gridName."_$j" . "_$key\" name=\"".$this->gridName."_$j" . "_$key\" onchange=\"readyToEdit_".$this->gridName."('".$this->gridName."','".$this->gridName."_$j" . "_$key');extra_sendObj_".$this->gridName."('frm_".$this->gridName."');\" onkeypress=\"if(ifEnter_$this->gridName(event)){this.onchange();}\" >\n" . $this->columnListToCombo($columnListVar[$i],$value)."</select>\n";
						}else
						{
							$ref = $r[$this->columnRLists[$i]["ref"]];
							$out .= "<select ".(($this->columnJavaScript[$i]!=null)?$this->columnJavaScript[$i]:"")." class=\"".$this->cssClass."_inp\" ".(($this->columnAccesses[$i]==1 && $this->canEdit)?"":"disabled=\"disabled\"")." id=\"".$this->gridName."_$j" . "_$key\" name=\"".$this->gridName."_$j" . "_$key\" onchange=\"readyToEdit_".$this->gridName."('".$this->gridName."','".$this->gridName."_$j" . "_$key');extra_sendObj_".$this->gridName."('frm_".$this->gridName."');\" onkeypress=\"if(ifEnter_$this->gridName(event)){this.onchange();}\" >\n" . $this->columnRListToCombo($this->columnRLists[$i],$value,$ref)."</select>\n";	
						}
						$out .= "<img src=\"".$this->ajaxAnimation."\" title=\"".$this->ajaxGoing."\" alt=\"".$this->ajaxGoing."\" style=\"width:20px;display:none;\"  id=\"".$this->gridName."_$j" . "_$key"."_img\" /></td>\n";
						if($key=='Total'){
							$sums[$key] = (int)$sums[$key] + (($mehrdad_fn!='')?(int)$value:0);
						}else{
							$sums[$key] = (int)$sums[$key] + (($mehrdad_fn!='')?(int)$mehrdad_fn:0);						
						}
					}else{
						$out .= "<td style=\"display:none;border-style:solid;border-width:1px;border-color:Black;text-align:center;font-family:Tahoma,tahoma;font-size:small;\">\n";
						$out .= (($mehrdad_fn!='')?$mehrdad_fn:'&nbsp;') . "<input type=\"text\" id=\"".$this->gridName."_$j" . "_$key\" name=\"".$this->gridName."_$j" . "_$key\" value=\"$value\">\n";				
						$out .= "</td>\n";
						$sums[$key] = (int)$sums[$key] + (($mehrdad_fn!='')?(int)$mehrdad_fn:0);
					}
					$i++;
				}
				$out .= "</tr>\n";
				$j++;
        	                if(!$this->loadArray)
				{
	                                $r_ok = ($r = mysql_fetch_array($this->dataSet,MYSQL_ASSOC));
				}
                	        else
				{
                        	        $r_ok = ($j-1 < count($this->dataArray));			
					$r = (isset($this->dataArray[$j-1])) ? $this->dataArray[$j-1] : array();
				}
			}
			$ch = 0;
			$kol_ch = 0;
			for($indx_ch = 0;$indx_ch < count($this->columnHeaders);$indx_ch++)
			{
				if($this->columnHeaders[$indx_ch] != null && $indx_ch<$this->hideIndex)
					$ch ++;
				else if($this->columnHeaders[$indx_ch] != null)
					$kol_ch ++;
			}
			$out .= "<tr class=\"" . $this->cssClass . "_insert_row\">\n";
			if(!$this->showIndex)
				$ch--;
			if(!$this->canDelete)
				$ch--;
			$out .= "<td id=\"delete_footer\" colspan=\"" . ($ch+1) . "\" style=\"border-style:solid;border-left-style:none;border-width:1px;border-color:Black;text-align:right;font-family:Tahoma,tahoma;font-size:small;\">\n";
			$out .= "<input type=\"button\" class=\"" . $this->cssClass . "_insert_button\" value=\"".$this->nextButton."\" onclick=\"nextPage_".$this->gridName."();\" ".(($this->pageCount>0 && ($this->rowCount/$this->pageCount)<=1)?"disabled=\"disabled\"":"")."/>";
			$out .= $this->createPageNumbers();
			$out .= "<input type=\"button\" class=\"" . $this->cssClass . "_insert_button\" value=\"".$this->previousButton."\" onclick=\"prevPage_".$this->gridName."();\" ".(($this->pageNumber<=0)?"disabled=\"disabled\"":"")."/>";
			$out .= "</td>\n";
			$out .= "<td style=\"border-style:solid;border-right-style:none;border-width:1px;border-color:Black;text-align:left;font-family:Tahoma,tahoma;font-size:small;\">\n";
			if($this->canDelete)
			{
				$out .= "<input type=\"button\" class=\"" . $this->cssClass . "_insert_button\" value=\"".$this->deleteButton."\" onclick=\"if(confirm('".$this->askGroupDelete."')){groupDelete_".$this->gridName."();}\" ".((!$this->canDelete)?"disabled=\"disabled\"":"")."/>";
			}
			else
			{
				$out .= "&nbsp;";
			}
			$out .= "</td>";
			$out .= "</tr>\n";
			$out .= $this->footer;
			$out .= (($this->canAdd)?"<tr class=\"" . $this->cssClass . "_row_even\"><td colspan=\"".(($this->canDelete)?"2":"1")."\" class=\"" . $this->cssClass . "_row_td\">".$this->newTitle."</td>\n":"");
                        for($i=0;$i<sizeof($this->fieldList) && $this->canAdd;$i++)
			{
				$dis = 'display:none;';
				if($this->columnHeaders[$i]!=null && $i<$this->hideIndex)
					$dis = '';
                                $out .= "<td class=\"" . $this->cssClass . "_row_td\" ".(($i>=$this->hideIndex && $this->columnHeaders[$i]!=null)?"id=\"hide_add_$i\"":'')." style=\"$dis\">\n";
				if($columnListVar[$i]==null && $this->columnRLists[$i]==null){
					$ttmp = ((isset($this->newFieldRefresh[$this->fieldList[$i]]))?$this->newFieldRefresh[$this->fieldList[$i]]:'');
					if($this->showAddDefault)
	                                	$out .= "<input  class=\"".$this->cssClass."_inp\" style=\"color:gray;\" type=\"text\" id=\"new_".$this->fieldList[$i]."\" name=\"new_".$this->fieldList[$i]."\" value=\"".(($ttmp!='')?$ttmp:$this->columnHeaders[$i])."\" onblur=\"showHint_".$this->gridName."(this,'".$this->columnHeaders[$i]."');\" onfocus=\"hideHint_".$this->gridName."(this,'".$this->columnHeaders[$i]."');\" ".(($this->columnJavaScript[$i]!=null)?$this->columnJavaScript[$i]:"")." />\n";
					else
						$out .= "<input  class=\"".$this->cssClass."_inp\" style=\"color:gray;\" type=\"text\" id=\"new_".$this->fieldList[$i]."\" name=\"new_".$this->fieldList[$i]."\" value=\"$ttmp\" ".(($this->columnJavaScript[$i]!=null)?$this->columnJavaScript[$i]:"")." />\n";
				}else if($columnListVar[$i]!=null && $this->columnRLists[$i]==null){
					if($this->enableComboAjax)
					{
						$ttmp = ((isset($this->newFieldRefresh[$this->fieldList[$i]]))?$this->newFieldRefresh[$this->fieldList[$i]]:'');
						$out .= "<input type=\"hidden\"  name=\"new_".$this->fieldList[$i]."\" id=\"new_".$this->fieldList[$i]."_back\"  value=\"$ttmp\" /><input  class=\"".$this->cssClass."_inp\" style=\"color:gray;\" type=\"text\" value=\"$ttmp\" id=\"new_".$this->fieldList[$i]."\"   onkeyup=\"search_".$this->gridName."(this);\" autocomplete=\"off\" />\n";
					}
					else
					{
						$ttmp = ((isset($this->newFieldRefresh[$this->fieldList[$i]]))?$this->newFieldRefresh[$this->fieldList[$i]]:'');
						$out .= "<select ".(($this->hasRList())?"onchange=\"refresh_combos_".$this->gridName."('".$this->fieldList[$i]."');\"":"")." id=\"new_".$this->fieldList[$i]."\" name=\"new_".$this->fieldList[$i]."\" ".(($this->columnJavaScript[$i]!=null)?$this->columnJavaScript[$i]:"")." >\n" . $this->columnListToCombo($columnListVar[$i],$ttmp)."</select>\n";
					}
				}else
				{
					$ref = $this->columnRLists[$i]["ref"];
					$ttmp = ((isset($this->newFieldRefresh[$this->fieldList[$i]]))?$this->newFieldRefresh[$this->fieldList[$i]]:'');
					$tref = ((isset($this->newFieldRefresh[$ref]))?$this->newFieldRefresh[$ref]:'');
					$out .= "<select onchange=\"refresh_combos_".$this->gridName."('".$this->fieldList[$i]."');\" id=\"new_".$this->fieldList[$i]."\" name=\"new_".$this->fieldList[$i]."\" ".(($this->columnJavaScript[$i]!=null)?$this->columnJavaScript[$i]:"")." >\n" . $this->columnRListToCombo($this->columnRLists[$i],$ttmp,$tref)."</select>\n";
				}
                                $out .= "</td>\n";
                        }
			$out .= (($this->canAdd)?"</tr>\n":"");
			$out .= "<tr class=\"" . $this->cssClass . "_insert_row\">\n";
			$out .= "<td id=\"add_footer\" colspan=\"" . ($ch+2) . "\" style=\"border-style:solid;border-width:1px;border-color:Black;text-align:left;font-family:Tahoma,tahoma;font-size:small;\">\n";
			if($this->canAdd)
				$out .= "<input type=\"button\" class=\"" . $this->cssClass . "_insert_button\" value=\"".$this->newButton."\" onclick=\"readyToAdd_".$this->gridName."();\" />";
			if($this->hideIndex <= count($this->columnHeaders))
				$out .= "<input type=\"button\" class=\"" . $this->cssClass . "_insert_button\" id=\"more_button\" value=\"".$this->moreButton."\" onclick=\"showMore_".$this->gridName."(".$this->hideIndex.",$kol_ch);\" />";
			$out .= "</td>\n";
			$out .= "</tr>\n";
//			if($r = mysql_fetch_array($dataSet,MYSQL_ASSOC)){
				$out .= "<tr>\n";
				if($sums != null){
					foreach($sums as $key=>$value){
						$out .= "<td id=\"sum_$key\" style=\"display:none;border-style:solid;border-width:1px;border-color:Black;text-align:center;font-family:B Titr,b titr,B titr,b Titr,Tahoma,tahoma;font-size:medium;\">\n";
						$out .= $value . "\n";
						$out .= "</td>\n";
					}
				}
				$out .= "</tr>\n";
//			}
			$out .= "</table>";
			$out .= "</form></div>";
			if($this->moreShown)
				$out .= "<script language=\"javascript\">showMore_".$this->gridName."(".$this->hideIndex.",$kol_ch);</script>";
			}
			$this->sums = $sums;
			return($out);

		}
		public function setERequest($inp){
			$this->eRequest = $inp;
		}
                public function addFeild($feildName,$indx = -1)
                {
			if($indx > -1)
			{
				//$columnListVar = (($this->list2)?$this->columnLists2:$this->columnLists);				
				$columnListVar = $this->columnLists;
				$fieldList_tmp = $this->fieldList;
				$columnHeaders_tmp= $this->columnHeaders;
				$columnLists_tmp= $columnListVar;
				$columnRLists_tmp= $this->columnRLists;
				$columnFilters_tmp= $this->columnFilters;
				$columnFunctions_tmp= $this->columnFunctions;
				$columnAccesses_tmp= $this->columnAccesses;
				$columnCallBackFunctions_tmp= $this->columnCallBackFunctions;
				$columnJavaScript_tmp= $this->columnJavaScript;
				$this->fieldList = array();
				$this->columnHeaders = array();
				$this->columnLists = array();
				$this->columnLists2 = array();
				$this->columnRLists = array();
				$this->columnFilters = array();
 				$this->columnFunctions = array();
				$this->columnAccesses = array();
 				$this->columnCallBackFunctions = array();
				$this->columnJavaScript[] = array();
				$j = 0;
				for($i = 0;$i <= count($fieldList_tmp);$i++)
				{
					if($indx != $i)
					{
						$this->fieldList[] = $fieldList_tmp[$j];
						$this->columnHeaders[] = $columnHeaders_tmp[$j];
		                                $this->columnLists[] = $columnLists_tmp[$j];
						$this->columnLists2[] = $columnLists_tmp[$j];
                		                $this->columnRLists[] = $columnRLists_tmp[$j];
                                		$this->columnFilters[] = $columnFilters_tmp[$j];
		                                $this->columnFunctions[] = $columnFunctions_tmp[$j];
                		                $this->columnAccesses[] = $columnAccesses_tmp[$j];
                                		$this->columnCallBackFunctions[] = $columnCallBackFunctions_tmp[$j];
		                                $this->columnJavaScript[] = $columnJavaScript_tmp[$j];
						$j++;
					}
					else
					{
						$this->fieldList[] = $feildName;
		                                $this->columnHeaders[] = $feildName;
                		                $this->columnLists[] = null;
						$this->columnLists2[] = null;
                                		$this->columnRLists[] = null;
		                                $this->columnFilters[] = FALSE;
                		                $this->columnFunctions[] = null;
                                		$this->columnAccesses[] = null;
		                                $this->columnCallBackFunctions[] = null;
                		                $this->columnJavaScript[] = null;
					}
				}
			}
			else
			{
	                        $this->fieldList[] = $feildName;
                        	$this->columnHeaders[] = $feildName;
                	        $this->columnLists[] = null;
				$this->columnLists2[] = null;
				$this->columnRLists[] = null;
				$this->columnFilters[] = FALSE;
                        	$this->columnFunctions[] = null;
                	        $this->columnAccesses[] = null;
        	                $this->columnCallBackFunctions[] = null;
				$this->columnJavaScript[] = null;
			}
                }
		public function getRowCount()
		{
			return($this->rowCount);
		}
	}
?>
