<?php
	
	require_once('../kernel.php');

	$objTreeview = new treeview_class();
	$objTreeview->addDocument('File1', 'alert(\'This is document 1\')');
	$iKey = $objTreeview->addFolder ('Folder1');
	$iKey2 = $objTreeview->getObjFolder($iKey)->addFolder ('Folder1.1');
	$iKey3 = $objTreeview->getObjFolder($iKey)->getObjFolder($iKey2)->addFolder ('Folder1.1.1');
	$objTreeview->getObjFolder($iKey)->getObjFolder($iKey2)->getObjFolder($iKey3)->addDocument('File2', 'alert(\'This is document 2\')');
	$iKey = $objTreeview->addFolder ('Folder2');
	$iKey2 = $objTreeview->getObjFolder($iKey)->addFolder ('Folder2.1');
	$objTreeview->getObjFolder($iKey)->getObjFolder($iKey2)->addFolder ('Folder2.1.1');
	$iKey = $objTreeview->addFolder ('Folder3');
	$iKey2 = $objTreeview->getObjFolder($iKey)->addFolder ('Folder3.1');
	$objTreeview->getObjFolder($iKey)->getObjFolder($iKey2)->addFolder ('Folder3.1.1');
?>
<html>
	<head>
		<title>Treeview example</title>
		<link href="../css/treeview.css" rel="stylesheet" type="text/css" />
		<script type="text/javascript" src="../js/treeview.js"></script>
	</head>
	<body style="background-color:#dedad9;direction:rtl;" >
		<?php echo security_class::blockIfBlocked($se,lang_fa_class::block); ?>
		<?php echo $objTreeview->render('200px', '150px'); ?>
	</body>
</html>
