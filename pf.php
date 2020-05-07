<?php
include('../../inc/db_geo.php');
$q = 'select c,en from cty order by en';
$res = pg_query($conn,$q);
if ($res) {
	echo "<select id='selProfile' onchange='selProfileChange()'>";
	echo "<option value='none'>-- Escolha um país --</option>";
	while ($row = pg_fetch_array($res,NULL,PGSQL_NUM)) {
		echo "<option value='$row[0]'>$row[1]</option>";
	}
	echo "</select>";
}
?>
