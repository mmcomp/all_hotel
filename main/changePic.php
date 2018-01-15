<?php
	$target_path = '';
	$new_name = '';
	$out = '';
	$scr = '';
                if(isset($_FILES['uploadedfile']))
                {
                        $tmp_target_path = "../upload/";
                        $ext = explode('.',basename( $_FILES['uploadedfile']['name']));
                        $ext = $ext[count($ext)-1];
                        $target_path =$_FILES['uploadedfile']['name'];
                        if(move_uploaded_file($_FILES['uploadedfile']['tmp_name'], $target_path))
                        {
				
				$new_name = $target_path;				
                                $out = "فایل با موفقیت ذخیره گردید"; 
                        }
                        else
                        {
                                $out =  "در ذخیره فایل مشکل پیش آمده است لطفا مجددا سعی نمایید .";
echo $out;
                        }
             }

	
?>
<html>
        <head>
                <link rel="SHORTCUT ICON" href="img/icon.ico">
                <meta name="keywords" content="" />
                <meta name="description" content="" />
                <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
                <meta name="language" content="en" />
                <link href="../css/style2.css" rel="stylesheet" type="text/css" />
		
			<script>
				function change_path(path)
				{
					document.getElementById("path_name").value = path;
				}
			</script>
        </head>
        <body style="width:100%;height:100%;">
		<br/>
		<br/>
	<?php
		if(isset($_FILES['uploadedfile']))
			$file_name = basename($_FILES['uploadedfile']['name']);
		else
			$file_name = "";
	?>
	<div class="main_th3" id="content" align="center">
			<form method="post" id="frm1"  enctype="multipart/form-data" >	
				<input type="hidden" name="MAX_FILE_SIZE" value="1000000000" />
				<lable class="cabinet"> 
    					<input type="file" class="file_1" name="uploadedfile" id="uploadedfile" onchange="change_path(this.value);"/>
				</lable>
				<input type="hidden" id="path_name"/>
				<button >درج فایل</button>
			</form>
	</div>
	<script> 
			var mess = "<?php echo $out;?>";
			if (mess != "")
				alert(mess);
	</script>
	<?php echo $scr; ?>
        </body>
</html>

