<?php
include('../../inc/db_geo.php');
function get($var) {
	$ret = '';
	if (!empty($_GET[$var])) {
		$ret = $_GET[$var];
	}
	return $ret;
}
$filename = trim(json_decode(get('filename')));
if ($filename == '') {
	$filename = 'data.csv';
} else {
	$filename = str_replace('"','',$filename); // remove todas as " aspas duplas "
	if (substr($filename,-4) != '.csv') { // e acrescenta extensão .csv se não tiver
		$filename .= '.csv';
	}
}
$tabs = json_decode(get('tabs'));
$ncols = [];
$fields = [];
// colunas da tabela cty
$q = "select * from cty limit 1";
$res = pg_query($conn,$q);
if ($res) {
	$nf = pg_num_fields($res);
	for ($i=0; $i<$nf; $i++) {
		$fields[] = 'cty.'.pg_field_name($res,$i);
	}
	$ncols[] = $nf;
}
// colunas das outras tabelas, e monta a query final
$q1 = 'select * from cty right join (select * from ';
$itab = 0;
foreach ($tabs as $tab) {
	if ($itab == 0) {
		$q1 .= "\"$tab\" ";
	} else {
		$q1 .= "full join \"$tab\" using (c) ";
	}
	$itab++;
	$q = "select * from \"$tab\" limit 1";
	$res = pg_query($conn,$q);
	if ($res) {
		$nf = pg_num_fields($res);
		for ($i=1; $i<$nf; $i++) { // $i=1 pula o id
			if (pg_field_name($res,$i) != 'c') {
				$fields[] = "$tab.".pg_field_name($res,$i);
			}
		}
		$ncols[] = $nf-1; // -1 pelo c retirado no full join
	}
}
$q1 .= ') fj using (c) order by c';
$res = pg_query($conn,$q1);
header('Content-Type: text/csv;charset=UTF-8;');
header("Content-Disposition: attachment;filename=\"$filename\";");
$out = fopen('php://output','w') or die ("Erro ao criar o arquivo $filename!");
if ($res) {
	fputs($out,implode("\t",$fields)."\n");
	while ($row = pg_fetch_array($res,NULL,PGSQL_NUM)) {
		$n = 0;
		foreach ($ncols as $ncol) {
			if ($n > 0) {
				array_splice($row,$n+1,1);
			}
			$n += $ncol-1; // -1 por causa do id removido
		}
		fputs($out,implode("\t",$row)."\n");
	}
}
fclose($out);
?>
