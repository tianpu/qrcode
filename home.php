<?php
$html = '';
$mode = 'encode';
if(!empty($_GET['mode']) && $_GET['mode']=='decode'){
	$mode = 'decode';
}
$html.= '<p class="navs">';
foreach(['encode','decode'] as $k){
	if($k==$mode){
		$html.= '<a href="?mode='.$k.'"><b>'.$k.'</b></a> ';
	}
	else{
		$html.= '<a href="?mode='.$k.'">'.$k.'</a> ';
	}
}
$html.= '</p>'.PHP_EOL;
if($mode=='encode'){
	if(!empty($_POST['action']) && $_POST['action']=='encode'){
		$d = [];
		if(!empty($_FILES['vault'])){
			$s = $_FILES['vault'];
			if(empty($s['error'])){
				$d['name'] = $s['name'];
				if(empty($d['name'])){
					$d['name'] = 'vault.rar';
				}
				$d['data'] = file_get_contents($s['tmp_name']);
				if(!empty($d['data'])){
					$d['data'] = chunk_split(base64_encode($d['data']));
				}
				else{
					$d = [];
				}
			}
		}
		if(empty($d)){
			$html.= 'file encode fail, <a href="?action=encode">click here</a> try again.';
		}
		else{
			$html.= '<p>base64_decode the text, then save the binary data as '.$d['name'].'<br>'.PHP_EOL;
			$html.= 'Sample script at https://gist.github.com/tianpu/qrcode<br>'.PHP_EOL;
			$html.= '</p>'.PHP_EOL;
			$html.= '<script src="./img/jquery.min.js"></script>'.PHP_EOL;
			$html.= '<script src="./img/qrcode.min.js"></script>'.PHP_EOL;
			$html.= '<div id="qrcode"></div>'.PHP_EOL;
			$html.= '<script type="text/javascript">
var qrtext = '.json_encode($d['data']).';
var qrcode = new QRCode(document.getElementById(\'qrcode\'),{
	width:512,
	height:512,
	useSVG:false
});
qrcode.makeCode(qrtext);
</script>'.PHP_EOL;
		}
	}
	else{
		$html.= '<form method="post" enctype="multipart/form-data">'.PHP_EOL;
		$html.= '<input type="hidden" name="action" value="encode">'.PHP_EOL;
		$html.= '<p>Upload the file for print</p>'.PHP_EOL;
		$html.= '<p><input type="file" name="vault"></p>'.PHP_EOL;
		$html.= '<p><input type="submit" value="Submit"></p>'.PHP_EOL;
		$html.= '</form>'.PHP_EOL;
	}
}
else{
	if(!empty($_POST['action']) && $_POST['action']=='decode'){
		$d = [];
		if(!empty($_POST['data'])){
			if(!empty($_POST['name'])){
				$d['name'] = $_POST['name'];
			}
			else{
				$d['name'] = 'vault.rar';
			}
			$d['data'] = base64_decode($_POST['data']);
			if(empty($d['data'])){
				$d = [];
			}
		}
		if(empty($d)){
			$html.= 'file decode fail, <a href="?action=decode">click here</a> try again.';
		}
		else{
			header('Content-Type: application/octet-stream');
			header('Content-Transfer-Encoding: Binary'); 
			header('Content-disposition: attachment; filename="'.rawurlencode($d['name']).'"'); 
			echo $d['data'];
			exit();
		}
	}
	else{
		$html.= '<form method="post">'.PHP_EOL;
		$html.= '<input type="hidden" name="action" value="decode">'.PHP_EOL;
		$html.= '<p>Input encoded text to decode</p>'.PHP_EOL;
		$html.= '<p>File: <br><input type="text" name="name" value="vault.rar"></p>'.PHP_EOL;
		$html.= '<p>Data: <br><textarea name="data" rows="13" cols="76"></textarea></p>'.PHP_EOL;
		$html.= '<p><input type="submit" value="Submit"></p>'.PHP_EOL;
		$html.= '</form>'.PHP_EOL;
	}
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Vault</title>
<meta name="viewport" content="width=device-width,initial-scale=1.0">
<style>@media print{.navs{display:none;}}span,pre{font-size:17px;font-family:monospace;}</style>
</head>
<body>
<?php echo $html; ?>
</body>
</html>