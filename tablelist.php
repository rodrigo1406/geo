<?php
include('../../inc/db_geo.php');
$q = "select table_name from information_schema.tables where table_schema='public' and table_type='BASE TABLE' order by lower(table_name)";
$res = pg_query($conn,$q);
if ($res) {
	while ($row = pg_fetch_array($res,NULL,PGSQL_NUM)) {
		if (!in_array($row[0],['cty','lang'])) {
			echo "<li><a href='javascript:show(\"$row[0]\")'>$row[0]</a></li>";
		}
	}
}
?>
