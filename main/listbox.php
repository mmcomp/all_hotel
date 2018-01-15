<?php
	include_once("../kernel.php");
	$ls = new listBox_class;
	$ls->selected = 'icon3.jpg';
	$ls->onClick = 'f1';
	$ls->vertical = FALSE;
	$ls->height = '60px';
	$ls->width = '80px';
	$ls->imageWidth = '';
	$ls->imageHeight = '39px';
	$out = $ls->getOutput();
?>
<html>
	<head>
		<script language="javascript" >
			function f1(img)
			{
				alert(img);
			}
		</script>
	</head>
	<body dir="rtl">
		<?php echo $out; ?>
	</body>
</html>
