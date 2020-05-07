<?php
include('../../inc/db_geo.php');
function get($var) {
	$ret = '';
	if (!empty($_GET[$var])) {
		$ret = $_GET[$var];
	}
	return $ret;
}
$sql = get('sql');
$tab = get('tab');
$res = pg_query($conn,$sql);
if ($res) {
	// mostra comentários
	if ($tab != 'c') {
		$q = "select description from pg_description where objoid = '\"$tab\"'::regclass order by objsubid";
		$res1 = pg_query($conn,$q);
		if ($res1) {
			$n = 0;
			while ($row = pg_fetch_array($res1,NULL,PGSQL_NUM)) {
				if ($n == 0) {
					echo str_replace("\n","<br>",$row[0]).'<br><hr>';
				} else {
					echo pg_field_name($res,$n+1).': '.str_replace("\n","<br>",$row[0]).'<br>';
				}
				$n++;
			}
		}
	}
	echo '<table>';
	// CABEÇALHO
	$nf = pg_num_fields($res);
	echo '<tr>';
	for ($i=0; $i<$nf; $i++) {
		echo '<th>'.pg_field_name($res,$i).'</th>';
	}
	echo '</tr>';
	// LINHAS
	if ($sql == "select table_name from information_schema.tables where table_schema='public' and table_type='BASE TABLE' order by table_name") {
		while ($row = pg_fetch_array($res,NULL,PGSQL_ASSOC)) {
			echo '<tr>';
			foreach ($row as $col) {
				echo "<td><a href='javascript:show(\"$col\")'>$col</a></td>";
			}
			echo '</tr>';
		}
	} else {
		while ($row = pg_fetch_array($res,NULL,PGSQL_ASSOC)) {
			echo '<tr>';
			foreach ($row as $v) {
				if (is_numeric($v)) {
					/*if (round($v) != $v) { // tem casas decimais
						//$v = rtrim(rtrim(sprintf('%.3F',$v),'0'),'.');
					} else { // inteiro
						//$v = number_format($v,0,'.','');
					}*/
					echo "<td>$v</td>";
				} else {
					echo "<td style='text-align:left'>$v</td>";
				}
			}
			echo '</tr>';
		}
	}
	// RODAPÉ
	echo '<tr>';
	for ($i=0; $i<$nf; $i++) {
		echo '<th>'.pg_field_name($res,$i).'</th>';
	}
	echo '</tr></table>';
	if ($tab != 'c') {
		$q = "select description from pg_description where objoid = '\"$tab\"'::regclass order by objsubid desc";
		$res1 = pg_query($conn,$q);
		if ($res1) {
			//$n = 0;
			while ($row = pg_fetch_array($res1,NULL,PGSQL_NUM)) {
				$n--;
				if ($n == 0) {
					echo str_replace("\n","<br>",$row[0]).'<br><br>';
				} else {
					echo pg_field_name($res,$n+1).': '.str_replace("\n","<br>",$row[0]).'<br>';
					if ($n == 1) {
						echo '<hr>';
					}
				}
			}
		}
	}
}
?>
