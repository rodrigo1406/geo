<?php
$uploaddir = '/var/www/html/geo/uploads/';
//$uploaddir = '/home/biodiversus/www/geo/uploads/';
$uploadfile = $uploaddir . basename($_FILES['userfile']['name']);

$tab = '<pre>';
if (move_uploaded_file($_FILES['userfile']['tmp_name'],$uploadfile)) {
	$tab .= "Arquivo é válido e foi recebido com sucesso [$uploadfile].\n";
	$f = file($uploadfile);
	$tab .= 'Número de linhas: '.count($f).'<br>';
	$tab .= '<table>';
	$row = 0;
	$form = "Ligar <select id='lnk0A' onchange='enableVerify()'>";
	foreach ($f as $l) {
		$col = 0;
		$ll = explode("\t",$l);
		$tab .= '<tr>';
		$ll2 = $ll;
		foreach ($ll as $v) {
			$v2 = trim($v);
			$p1 = strpos($v2,'[');
			// remove [ tudo que estiver entre colchetes (incluindo os colchetes) ]
			while ($p1 !== false) {
				$p2 = strpos($v2,']');
				if ($p2 !== false) {
					$v2 = substr($v2,0,$p1).substr($v2,$p2+1);
				} else {
					$v2 = substr($v2,0,$p1);
				}
				$p1 = strpos($v2,'[');
			}
			if ($v2 != $v) {
				$v = $v2;
				$ll2[$col] = $v2;
			}
			$tab .= "<td>$v</td>";
			if ($row == 0) {
				$form .= "<option value='$col'>$v</option>";
			}
			$col++;
		}
		if ($ll2 != $ll) {
			$f[$row] = implode("\t",$ll2);
		}
		$tab .= '</tr>';
		$row++;
	}
	$tab .= '</table>';
	$json = json_encode($f);
	$tname = pathinfo($_FILES['userfile']['name'])['filename'];
	$form .= "</select> com <select id='lnk0B' onchange='enableVerify()'>
	<option value='iso2'>iso2</option>
	<option value='iso3'>iso3</option>
	<option value='ison'>ison</option>
	<option value='en' selected>en</option>
	<option value='fr'>fr</option>
	<option value='pt'>pt</option>
	<option value='zh'>zh</option>
	</select>
	<div id='divLinkFields'></div>
	<button type='button' onclick='addLinkField()'>+</button><button id='btnVerify' type='button' onclick='importa(\"$tname\")'>Verificar</button>";
} else {
	$tab .= "Possible file upload attack!\n";
}
$tab .= '</pre>';
$arr = [];
$arr[] = $tab;
$arr[] = $form;
$arr[] = $json;
echo json_encode($arr);
?>
