<?php
/*
 * http://data.worldbank.org/indicator
 * 
 . inserindo bancos várias vezes
 . adicionar país em cty quando opção for -- Inserir --
 . não está encontrando país para sugerir a partir de cty1
 . mostrar tabelas adicionadas no painel verde (link no painel esquerdo ou na tabela Tabelas?)
 . ligar outras colunas além do nome ao inserir novos países (ex: Name + Code iso3)
 . mostrar tabelas em div abre/fecha abaixo das tabelas principais (ou direto na lista?)
 . preencher nome da tabela com o nome do csv
 . salvar como metadados: nomes originais das colunas (só se diferente do nome da coluna)
 . Excluir [tudo-que-estiver-entre-colchetes] nos nomes dos países ou qualquer outra coluna
 . mostrar lista completa dos países possíveis
 . não mostrar números real como notação científica
 . mostrar comentários antes e depois da tabela
 . tirar os [] em upload.php
 . botão Adicionar não habilita enquanto não mexer no nome da tabela
 . & virando &amp;
 . aumentar o tamanho da textarea
 . Desabilitar botão Baixar se nenhum Checklist estiver selecionado
 . salvar como metadados: url/referência; etc.
 . acrescentar texto padrão antes de colunas com título numérico (1960, 1961, 1962... -> ano1960, ano1961, ano1962...)
 . não permitir nomes de tabelas repetidos (deixar label em vermelho e desabilitar botão Adicionar)
 . mostrar nomes alternativos junto aos nomes oficiais
 . não aceitar nome 'c' para coluna (reservado para id do país)
 . escape aspas em nomes de país como "Cote d' Ivore". E aspas duplas??
 . criar backup0 só com as 3 tabelas fundamentais
 . módulo de unir e exportar tabelas (salvar como csv)
 . escolher o nome do arquivo a ser salvo
 . Mostrar toda a informação disponível para cada país de forma organizada e compacta
 . Erro ao linkar pelo iso3
 * Não tentar acrescentar comentários se não houver nenhum a ser acrescentado
 * Barra de rolagem horizontal em divMain, e não na janela toda
 * Opção de selecionar todas as tabelas/nenhuma para fazer o download
 * Como salvar arquivo sem abrir (e fechar rápido) outra aba, como faz o site do Banco Mundial?
 * verificar ' e " nos nomes das colunas (e onde mais? comentários, nomes das tabelas, valores texto, valores numéricos)
 * reconhecer Niger como "Niger (the)" e não como "Nigeria" (olhar primeira palavra idêntica)
 * encontrar "Gambia (the)" a partir de "Gambia, The" (olhar só primeira palavra)
 * "Sudan" bater com "Sudan (the)" antes de "South Sudan" (primeira palavra)
 * criar tipo char(n)
 * aceitar (auto-identificar?) outros separadores além de \t (no csv)
 ? marcar cada coluna para importar ou não (checkbox)
 x barra de rolagem horizontal (e/ou <pre>) na tabela verde? e na vermelha? [nas 2 juntas, a barra da tela]
 x formato data (ou usar texto?) -> texto mais flexível para usos posteriores variados
 x países de cty1 estão no select de opções de países?
 * 
 */
include('../../inc/db_geo.php');
function post($var) {
	$ret = '';
	if (isset($_POST[$var]) && (!empty($_POST[$var]) || $_POST[$var] == '0')) {
		$ret = $_POST[$var];
	}
	return $ret;
}
$debug = false;
$url = str_replace("'","''",post('url'));
$etc = str_replace("'","''",post('etc'));
$lnk0A = post('lnk0A');
$lnk0B = post('lnk0B');
$lang = post('lang');
$tname = post('tname');
$tab = json_decode(post('tab'));
$cols = [];
$tipos = [];
$newnames = [];
$ids = [];
$linksA = [];
$linksB = [];
$toInsert = 0;
$toInsertKey = [];
$toIgnore = 0;
foreach ($_POST as $k => $v) {
	$v = htmlspecialchars_decode($v); // volta &amp; para &
	// cor = Coluna ORiginal
	if (substr($k,0,3) == 'cor') { // 0, 1, 2... nomes das colunas no csv
		$colsOrig[substr($k,3)] = $v;
	} else
	// cbd = Coluna no Banco de Dados
	if (substr($k,0,3) == 'cbd') { // 0, 1, 2... nomes das colunas para criar no psql
		$cols[substr($k,3)] = $v;
	} else
	// typ = TYPe of column
	if (substr($k,0,3) == 'typ') { // 0, 1, 2... tipos das colunas para criar no psql
		if ($v == 'int') {
			$tipos[substr($k,3)] = 'bigint';
		} else {
			$tipos[substr($k,3)] = $v;
		}
	} else
	// por = País ORiginal
	if (substr($k,0,3) == 'por') { // 0, 1, 2... nomes|siglas dos países no csv
		$newnames[substr($k,3)] = $v;
	} else
	if (substr($k,0,3) == 'lnk') { // links adicionais entre csv e cty (ao inserir país em cty)
		if (substr($k,3,1) == 'A') {
			$linksA[substr($k,4)] = $v;
		} else
		if (substr($k,3,1) == 'B') {
			$linksB[substr($k,4)] = $v;
		}
	} else
	// pbd = País no Banco de Dados
	if (substr($k,0,3) == 'pbd') { // 0, 1, 2... ids dos países para ligar no psql (ou insert|ignore)
		$ids[substr($k,3)] = $v;
		if ($v == 'insert') {
			$toInsert++;
			$toInsertKey[] = substr($k,3);
		} else
		if ($v == 'ignore') {
			$toIgnore++;
		}
	}
}
if ($debug) {
	echo "Idioma: $lang<br>";
	echo "TableName: $tname<br><br>";
	if (count($cols) > 0) {
		echo 'Cols:<br>';
		print_r($cols);
		echo '<br><br>';
	}
	if (count($colsOrig) > 0) {
		echo 'ColsOrig:<br>';
		print_r($colsOrig);
		echo '<br><br>';
	}
	if (count($tipos) > 0) {
		echo 'Tipos:<br>';
		print_r($tipos);
		echo '<br><br>';
	}
	if (count($newnames) > 0) {
		echo 'New Names:<br>';
		print_r($newnames);
		echo '<br><br>';
	}
	if (count($linksA) > 0) {
		echo 'LinksA:<br>';
		print_r($linksA);
		echo '<br>';
	}
	if (count($linksB) > 0) {
		echo 'LinksB:<br>';
		print_r($linksB);
		echo '<br><br>';
	}
}

// vê se tem algum país a inserir
if ($toInsert > 0) {
	foreach ($toInsertKey as $key) {
		$q = "insert into cty ($lnk0B,";
		foreach ($linksB as $lB) {
			$q .= "$lB,";
		}
		$q = substr($q,0,-1); // tira a última vírgula
		$q .= ") values ('$newnames[$key]',";
		foreach ($linksA as $lA) {
			$key1 = $key+1; // por causa da linha de cabeçalho
			$q .= "'{$tab[$key1][$lA]}',";
		}
		$q = substr($q,0,-1); // tira a última vírgula
		$q .= ") returning c";
		if ($debug) {
			echo "$q<br><br>";
		}
		$res = pg_query($conn,$q);
		if ($res) {
			$newID = pg_fetch_array($res,NULL,PGSQL_NUM)[0];
			echo "<span style='color:green'>País '$newnames[$key]' inserido com sucesso. ID = $newID.</span><br>";
			$ids[$key] = $newID;
		} else {
			pg_send_query($conn,$q);
			$res = pg_get_result($conn);
			$resErr = pg_result_error($res);
			echo "<span style='color:red'>Erro ao inserir país '$newnames[$key]': $resErr</span>";
		}
	}
}

if ($debug) {
	if (count($ids) > 0) {
		print_r($ids);
		echo '<br><br>';
	}
}

// primeiro insere os nomes alternativos
if (count($newnames) > 0) {
	$q = 'insert into cty1 (c,n,l) values ';
	foreach ($newnames as $k => $name) {
		if ($ids[$k] != 'ignore' && !in_array($k,$toInsertKey)) { // se não for para ignorar && se não tiver sido inserido em cty
			$name = str_replace("'","''",$name);
			$q .= "($ids[$k],'$name',$lang),";
		}
	}
	$q = substr($q,0,-1);
	if ($debug) {
		echo "<br><br>$q<br><br>";
	}
	$res = pg_query($conn,$q);
	if ($res) {
		echo "<span style='color:green'>Nomes alternativos inseridos com sucesso.</span><br>";
	} else {
		pg_send_query($conn,$q);
		$res = pg_get_result($conn);
		$resErr = pg_result_error($res);
		echo "<span style='color:red'>Erro ao inserir nomes alternativos: $resErr</span>";
	}
} else {
	echo "<span style='color:green'>Não há nomes alternativos a inserir.</span><br>";
}

// depois cria a nova tabela
$tabelaExiste = false;
$q = "select exists (select 1 from information_schema.tables where table_schema = 'public' and table_name = '$tname');";
$res = pg_query($conn,$q);
if ($res) {
	if ($row = pg_fetch_array($res,NULL,PGSQL_NUM)) {
		if ($row[0] == 't') { // tabela já existe
			$tabelaExiste = true;
			echo "<span style='color:green'>Tabela '$tname' já existe.</span><br>";
		}
	}
}
if (!$tabelaExiste) {
	$q = "create table \"$tname\" (
		id serial primary key,
		c int references cty, ";
	$colCty = 0;
	foreach ($cols as $k => $col) {
		$q .= "\"$col\" $tipos[$k], ";
		if ($debug) {
			echo "$col,$lnk0A,$k,$colCty.<br>";
		}
		if (trim($colsOrig[$k]) == trim($lnk0A)) {
			$colCty = $k;
		}
	}
	$q = substr($q,0,-2).');'; // tira a última vírgula e espaço, e termina
	if ($debug) {
		echo "<br>$q<br><br>";
	}
	$res = pg_query($conn,$q);
	if ($res) {
		echo "<span style='color:green'>Tabela '$tname' criada com sucesso.</span><br>";
	} else {
		pg_send_query($conn,$q);
		$res = pg_get_result($conn);
		$resErr = pg_result_error($res);
		echo "<span style='color:red'>Erro ao criar tabela '$tname': $resErr</span>";
	}
}

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

// finalmente insere os dados da nova tabela
$nvalues = 0;
$q = "insert into \"$tname\" (c,";
foreach ($cols as $k => $col) {
	$q .= "\"$col\",";
}
$q = substr($q,0,-1).') values '; // tira a última vírgula e continua
for ($i=1; $i<count($tab); $i++) { // CONSIDERANDO QUE TEM CABEÇALHO ($i=1) !!
	// procura o id do país
	$erro = false;
	$nomePais = str_replace("'","''",$tab[$i][$colCty]);
	$q1 = "select * from cty where $lnk0B = '$nomePais'";
	if ($debug) {
		echo "Q1: $q1<br><br>";
	}
	$res1 = pg_query($conn,$q1);
	if ($res1) {
		if ($row1 = pg_fetch_array($res1,NULL,PGSQL_ASSOC)) { // já encontrou o país na tabela principal (cty)
			$ctyid = $row1['c'];
		} else {
			$erro = true;
		}
	} else {
		$erro = true;
	}
	if ($erro) { // ainda não achou o país
		$erro = false; // tenta de novo na tabela secundária (cty1)
		$q2 = "select * from cty1 where n = '$nomePais' and l = $lang";
		if ($debug) {
			echo "Q2: $q2<br><br>";
		}
		$res2 = pg_query($conn,$q2);
		if ($res2) {
			if ($row2 = pg_fetch_array($res2,NULL,PGSQL_ASSOC)) { // encontrou o país na tabela secundária (cty1)
				$ctyid = $row2['c'];
			} else {
				$erro = true;
			}
		} else {
			$erro = true;
		}
	}
	$q3 = "select c from \"$tname\" where c = $ctyid";
	$res3 = pg_query($conn,$q3);
	if ($res3) {
		if ($row3 = pg_fetch_array($res3,NULL,PGSQL_ASSOC)) { // país já existe em 'tname'
			$erro = true;
		}
	}
	if (!$erro) {
		$q .= "($ctyid,";
		foreach ($tab[$i] as $v) {
			if ($v == '') {
				$q .= "NULL,";
			} else {
				$v = str_replace("'","''",$v);
				$q .= "'$v',";
			}
		}
		$q = substr($q,0,-1).'),'; // tira a última vírgula e continua
		$nvalues++;
	}
}
$q = substr($q,0,-1).';'; // tira a última vírgula e conclui

if ($nvalues > 0) {
	if ($debug) {
		echo "<br><br>$q<br><br>";
	}
	$res = pg_query($conn,$q);
	if ($res) {
		echo "<span style='color:green'>Tabela '$tname' preenchida com sucesso.</span><br>";
	} else {
		pg_send_query($conn,$q);
		$res = pg_get_result($conn);
		$resErr = pg_result_error($res);
		echo "<span style='color:red'>Erro ao preencher tabela '$tname': $resErr</span>";
	}
} else {
	echo "<span style='color:green'>Tabela '$tname' já estava preenchida.</span><br>";
}

// adiciona os comentários/metadados das colunas
$q = '';
foreach ($cols as $k => $col) {
	$colOrig = str_replace("'","''",$colsOrig[$k]);
	if ($colOrig != $col) {
		$q .= "comment on column \"$tname\".\"$col\" is '$colOrig'; ";
	}
}
if ($debug) {
	echo "$q<br>";
}
$res = pg_query($conn,$q);
if ($res) {
	echo "<span style='color:green'>Comentários adicionados com sucesso nas colunas de '$tname'.</span><br>";
} else {
	pg_send_query($conn,$q);
	$res = pg_get_result($conn);
	$resErr = pg_result_error($res);
	echo "<span style='color:red'>Erro ao adicionar comentários nas colunas de '$tname': $resErr</span>";
}

// adiciona os comentários/metadados da tabela
$q = "comment on table \"$tname\" is '$url\n\n$etc';";
if ($debug) {
	echo "$q<br>";
}
$res = pg_query($conn,$q);
if ($res) {
	echo "<span style='color:green'>Comentários adicionados com sucesso na tabela '$tname'.</span><br>";
} else {
	pg_send_query($conn,$q);
	$res = pg_get_result($conn);
	$resErr = pg_result_error($res);
	echo "<span style='color:red'>Erro ao adicionar comentários na tabela '$tname': $resErr</span>";
}
?>
