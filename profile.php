<?php
include('../../inc/db_geo.php');
function get($var) {
	$ret = '';
	if (!empty($_GET[$var])) {
		$ret = $_GET[$var];
	}
	return $ret;
}
$c = get('c');
// Imprime dados básicos do país
$q = 'select * from cty where c = $1';
$res = pg_query_params($conn,$q,[$c]);
if ($res) {
	if ($row = pg_fetch_array($res,NULL,PGSQL_ASSOC)) {
		$l = '';
		foreach ($row as $k => $v) {
			if ($k != 'c' && (string)$v != '') {
				$l .= "<span style='color:#080'>$k:</span> $v; ";
			}
		}
		$l = substr($l,0,-2).'<br>';
		echo $l;
	}
}

// Imprime os valores de cada tabela
$q = "select table_name from information_schema.tables where table_schema='public' and table_type='BASE TABLE' and table_name not in ('cty','cty1','lang') order by lower(table_name)";
$res = pg_query($conn,$q);
$tabs = [];
if ($res) {
	while ($row = pg_fetch_array($res,NULL,PGSQL_NUM)) {
		$tabs[] = $row[0];
	}
}
foreach ($tabs as $tab) {
	// pega os comentários das colunas
	$col = [];
	$q = "select objsubid,description from pg_description where objoid = '\"$tab\"'::regclass order by objsubid";
	$res = pg_query($conn,$q);
	if ($res) {
		while ($row = pg_fetch_array($res,NULL,PGSQL_NUM)) {
			if ($row[0] > 0) { // pula os comentários da tabela
				$col[$row[0]] = $row[1];
			}
		}
	}
	$t = "<br><span style='font-weight:bold'>$tab:</span> ";
	$q = "select * from \"$tab\" where c = $1";
	$res = pg_query_params($conn,$q,[$c]);
	if ($res) {
		if ($row = pg_fetch_array($res,NULL,PGSQL_ASSOC)) {
			$ncol = 1; // para seguir o índice de $col
			$l = '';
			foreach ($row as $k => $v) {
				if ($ncol > 2) { // pula 'id' e 'c'
					if ((string)$v != '') {
						if (isset($col[$ncol])) {
							$k = $col[$ncol];
						}
						$l .= "<span style='color:#080'>$k:</span> $v; ";
					}
				}
				$ncol++;
			}
			if ($l != '') {
				$l = $t.substr($l,0,-2);
				echo $l;
			}
		}
	}
}
echo '<br><br>';
?>
