<?php
include('../../inc/db_geo.php');
function post($var) {
	$ret = '';
	if (!empty($_POST[$var]) || $_POST[$var] == '0') {
		$ret = $_POST[$var];
	}
	return $ret;
}
$lnk0A = post('lnk0A');
$lnk0B = post('lnk0B');
$tname = post('tname');
$lang = 0;
switch ($lnk0B) {
	case 'en' :
		$lang = 41;
		break;
	case 'fr' :
		$lang = 48;
		break;
	case 'pt' :
		$lang = 129;
		break;
	case 'zh' :
		$lang = 30;
		break;
}
//$tab = json_decode(stripslashes(post('tab')));
$tab = json_decode(post('tab')); // funciona com " e / no meio do texto
// separa o cabeçalho
$cabecalho = $tab[0];
$tab = array_slice($tab,1);
// Metadados: URL e etc
$form = "<label id='labURL' for='url'>URL origem:</label> <input id='url' /><br>";
//$form .= implode('|',$cabecalho).'<br>';
$form .= "<label id='labetc' for='etc'>Outros metadados:</label><br><textarea id='etc' rows='15' cols='45'></textarea><br>";

// monta a lista de idiomas
if ($lang > 0) {
	$form .= "<label for='lang'>Idioma:</label> <select id='lang'>";
	$q = 'select id,name,nat,fam from lang order by name';
	$res = pg_query($conn,$q);
	if ($res) {
		while ($row = pg_fetch_array($res,NULL,PGSQL_ASSOC)) {
			if ($row['id'] == $lang) {
				$form .= "<option value='$row[id]' selected>$row[name] [$row[nat] / $row[fam]]</option>";
			} else {
				$form .= "<option value='$row[id]'>$row[name] [$row[nat] / $row[fam]]</option>";
			}
		}
	}
	$form .= "</select><br>";
}
// tabelas já existentes
$q = "select table_name from information_schema.tables where table_schema='public' and table_type='BASE TABLE' order by table_name";
$res = pg_query($conn,$q);
$tabelas = [];
if ($res) {
	while ($row = pg_fetch_array($res,NULL,PGSQL_NUM)) {
		$tabelas[] = $row[0];
	}
}
$tabelas = implode("\t",$tabelas);
if (trim($tname) != '') {
	$form .= "<label id='labTName' for='tname'>Nome da tabela:</label> <input id='tname' onchange='txtTNameChange(this,\"$tabelas\")' oninput='txtTNameChange(this,\"$tabelas\")' value='$tname' /><br>";
} else {
	$form .= "<label id='labTName' for='tname' style='color:red'>Nome da tabela:</label> <input id='tname' onchange='txtTNameChange(this,\"$tabelas\")' oninput='txtTNameChange(this,\"$tabelas\")' value='$tname' /><br>";
}
// se existirem colunas com título apenas numérico
$tipo = 'text';
foreach ($cabecalho as $cab) {
	if ($cab == '') { // cabeçalho vazio => abortar
		exit('Alguma coluna sem cabeçalho.');
	} else
	if (is_numeric($cab)) {
		$tipo = 'num';
		break;
	}
}
if ($tipo == 'num') { // pelo menos um título apenas numérico
	$form .= "<label id='labPadrao' for='cnu'>Prefixar títulos numéricos:</label> <input id='cnu' size=5 onchange='prefixChange(this)' oninput='prefixChange(this)' /><br>";
}
//$form .= "[lnk0A = $lnk0A, lnk0B = $lnk0B, ".count($tab).' rows, '.count($tab[0])." cols]<br>";
// investiga e pergunta os tipos de cada coluna
$linha1 = 0;
foreach ($cabecalho as $cab) {
	$tipo = 'int'; // começa como int
	for ($i=0; $i<count($tab); $i++) {
		if (!is_numeric($tab[$i][$linha1]) && $tab[$i][$linha1] != '') { // se encontrar algum texto, break, é text
			$tipo = 'text';
			break;
		} else
		if (is_numeric($tab[$i][$linha1]) && $tab[$i][$linha1] != round($tab[$i][$linha1])) { // se encontrar algum float, é real, mas não break (pq ainda pode achar texto pra frente)
			$tipo = 'numeric';
		}
	}
	// cor = Coluna ORiginal
	$form .= "<label id='cor$linha1' for='cbd$linha1' style='font-weight:bold'>$cab</label> = ";
	// cbd = Coluna Banco de Dados
	$form .= "<input id='cbd$linha1' value='$cab' onkeypress='txtCabChange(this)' oninput='txtCabChange(this)' size=8 />";
	$form .= " <select id='typ$linha1' onkeyup='selTypeChange(this)' onchange='selTypeChange(this)'>";
	$form .= "<option value='char'>char</option>";
	if ($tipo == 'int') {
		$form .= "<option value='int' selected>int</option>";
	} else {
		$form .= "<option value='int'>int</option>";
	}
	if ($tipo == 'numeric') {
		$form .= "<option value='numeric' selected>real</option>";
	} else {
		$form .= "<option value='numeric'>real</option>";
	}
	if ($tipo == 'text') {
		$form .= "<option value='text' selected>text</option>";
	} else {
		$form .= "<option value='text'>text</option>";
	}
	$form .= "</select> <input id='nchar$linha1' size=4 style='display:none' /><br>";
	$linha1++;
}
// verifica os nomes já conhecidos e propõe alternativas próximas
$linha1 = 0;
$enablPaises = true;
foreach ($tab as $l) {
	$nomePais = str_replace("'","''",$l[$lnk0A]);
	$q = "select * from cty where $lnk0B = '$nomePais'";
	//$form .= "$q<br>";
	$res = pg_query($conn,$q);
	if ($res) {
		if ($row = pg_fetch_array($res,NULL,PGSQL_ASSOC)) {
			//$form .= "$row[$lnk0B] ($row[iso3])<br>"; // encontrou idêntico -> não mostra nada
		} else {
			// se não encontrou idêntico na tabela principal (cty), procura na tabela auxiliar (cty1)
			$q3 = "select * from cty1 where n = '$nomePais' and l = $lang";
			$res3 = pg_query($conn,$q3);
			if ($res3) {
				if ($row3 = pg_fetch_array($res3,NULL,PGSQL_ASSOC)) {
					// encontrou idêntico -> não mostra nada
				} else {
					// por = País ORiginal
					$form .= "<label id='por$linha1' for='pbd$linha1'>$l[$lnk0A]</label>";
					$q1 = "select * from cty where $lnk0B ilike '%$nomePais%' order by $lnk0B";
					$res1 = pg_query($conn,$q1);
					if ($res1) {
						// pbd = País Banco de Dados
						$form .= " <select id='pbd$linha1' onchange='selAddChange()'>";
						$linha2 = 0;
						$form .= "<option value='insert'>-- Inserir --</option>";
						$form .= "<option value='ignore'>-- Ignorar --</option>";
						while ($row1 = pg_fetch_array($res1,NULL,PGSQL_ASSOC)) {
							if ($linha2 == 0) {
								$form .= "<option value='$row1[c]' selected>$row1[$lnk0B]</option>";
							} else {
								$form .= "<option value='$row1[c]'>$row1[$lnk0B]</option>";
							}
							$linha2++;
						}
						if ($linha2 == 0) { // nada semelhante, seleciona o vazio
							$form .= "<option value='none' selected></option>";
							$enablPaises = false;
						}
						// mostra lista completa
						$q2 = "select * from cty order by $lnk0B";
						$res2 = pg_query($conn,$q2);
						if ($res2) {
							while ($row2 = pg_fetch_array($res2,NULL,PGSQL_ASSOC)) {
								$form .= "<option value='$row2[c]'>$row2[$lnk0B]</option>";
							}
						}
						$form .= "</select><br>";
					}
				}
			}
		}
	}
	$linha1++;
}
if ($enablPaises) {
	$form .= "<br><button id='btnAdd' type='button' onclick='adiciona()'>Adicionar</button>";
} else {
	$form .= "<br><button id='btnAdd' type='button' onclick='adiciona()' disabled>Adicionar</button>";
}
$arr = [];
$arr[] = $form;
$arr[] = $enablPaises;
echo json_encode($arr);
?>
