<!DOCTYPE html>
<html>
<head>
<meta charset='UTF-8'>
<title>Geo</title>
<style>
table {
	border-collapse:collapse;
}
th {
	border:1px solid gray;
}
td {
	border:1px solid gray;
	text-align:right;
}
#divBar {
	position:fixed;
	top:0;
	bottom:0;
	left:0;
	width:20%;
	overflow-y:auto;
}
#divTableList {
	padding-left:20px;
	display:none;
}
#divMain {
	position:relative;
	width:80%;
	left:20%;
}
#divMainL {
	float:left;
	background-color:#DFD;
	width:50%;
}
#divMainR {
	float:right;
	background-color:#FDD;
	width:50%;
}
</style>
<script>
var formData = null;
function updateGet1() {
	if (HttpReq.readyState == 4) {
		if (HttpReq.status == 200) {
			var div = document.getElementById('divMainL');
			div.innerHTML = HttpReq.responseText;
		} else {
			console.log('Erro: ' + HttpReq.statusText + ' ['+HttpReq.status+']');
		}
	}
}
function updateGet2() {
	if (HttpReq.readyState == 4) {
		if (HttpReq.status == 200) {
			var div = document.getElementById('divTableList');
			div.innerHTML = HttpReq.responseText;
		} else {
			console.log('Erro: ' + HttpReq.statusText + ' ['+HttpReq.status+']');
		}
	}
}
function updateGet3() {
	if (HttpReq.readyState == 4) {
		if (HttpReq.status == 200) {
			var div = document.getElementById('divMainR');
			div.innerHTML = HttpReq.responseText;
		} else {
			console.log('Erro: ' + HttpReq.statusText + ' ['+HttpReq.status+']');
		}
	}
}
function updateGet4() {
	if (HttpReq.readyState == 4) {
		if (HttpReq.status == 200) {
			var div = document.getElementById('divProfile');
			div.innerHTML = HttpReq.responseText;
		} else {
			console.log('Erro: ' + HttpReq.statusText + ' ['+HttpReq.status+']');
		}
	}
}
var tabela = null;
function updatePost1() {
	if (HttpReq.readyState == 4) {
		if (HttpReq.status == 200) {
			var div = document.getElementById('divMainR');
			var data = JSON.parse(HttpReq.responseText);
			div.innerHTML = data[0];
			var imp = document.getElementById('divImport1');
			imp.innerHTML = data[1];
			var tab = JSON.parse(data[2]);
			//console.log(data[3]);
			var i, j, l;
			tabela = [];
			for (i=0; i<tab.length; i++) {
				//l = tab[i].split('\t').map(function(e) { return e.trim(); }); // funciona com " e / no meio do texto
				l = tab[i].split('\t');
				tabela.push(l);
			}
			document.getElementById('divImport2').innerHTML = '';
		} else {
			console.log('Erro: ' + HttpReq.statusText + ' ['+HttpReq.status+']');
		}
	}
}
function updatePost2() {
	if (HttpReq.readyState == 4) {
		if (HttpReq.status == 200) {
			var div = document.getElementById('divImport2');
			div.innerHTML = HttpReq.responseText;
		} else {
			console.log('Erro: ' + HttpReq.statusText + ' ['+HttpReq.status+']');
		}
	}
}
var enablTName = true;
var enablCabCols = true;
var enablPaises = true;
function updatePost3() {
	if (HttpReq.readyState == 4) {
		if (HttpReq.status == 200) {
			var div = document.getElementById('divImport2');
			var data = JSON.parse(HttpReq.responseText);
			div.innerHTML = data[0];
			enablPaises = data[1];
		} else {
			console.log('Erro: ' + HttpReq.statusText + ' ['+HttpReq.status+']');
		}
	}
}
function updatePost4() {
	if (HttpReq.readyState == 4) {
		if (HttpReq.status == 200) {
			var div = document.getElementById('divMainR');
			div.innerHTML = HttpReq.responseText;
		} else {
			console.log('Erro: ' + HttpReq.statusText + ' ['+HttpReq.status+']');
		}
	}
}
function conecta(url,how) { // makes AJAX connection
	if (document.getElementById && window.XMLHttpRequest) { // If Browser supports DHTML, Firefox, etc.
		HttpReq = new XMLHttpRequest();
		switch (how) {
			case 'get1' :
				HttpReq.onreadystatechange = updateGet1;
				HttpReq.open('GET', url, true);
				HttpReq.send(null);
				break;
			case 'get2' :
				HttpReq.onreadystatechange = updateGet2;
				HttpReq.open('GET', url, true);
				HttpReq.send(null);
				break;
			case 'get3' :
				HttpReq.onreadystatechange = updateGet3;
				HttpReq.open('GET', url, true);
				HttpReq.send(null);
				break;
			case 'get4' :
				HttpReq.onreadystatechange = updateGet4;
				HttpReq.open('GET', url, true);
				HttpReq.send(null);
				break;
			case 'post1' :
				HttpReq.onreadystatechange = updatePost1;
				HttpReq.open('POST', url, true);
				HttpReq.send(formData);
				break;
			case 'post2' :
				HttpReq.onreadystatechange = updatePost2;
				HttpReq.open('POST', url, true);
				HttpReq.send(formData);
				break;
			case 'post3' :
				HttpReq.onreadystatechange = updatePost3;
				HttpReq.open('POST', url, true);
				HttpReq.send(formData);
				break;
			case 'post4' :
				HttpReq.onreadystatechange = updatePost4;
				HttpReq.open('POST', url, true);
				HttpReq.send(formData);
				break;
		}
	}
}
function sqlFromTable(tab) {
	var sql = '';
	switch (tab) {
		case 'cty' :
			sql = 'select c,iso2,iso3,ison,en,fr,pt,zh from cty order by en';
			break;
		case 'cty1' :
			sql = 'select cty1.id,c,n as name,lang.name lang from cty1 join lang on (lang.id = cty1.l) order by cty1.id';
			break;
		case 'lang' :
			sql = 'select * from lang order by id';
			break;
		case 'c' :
			sql = "select c,en \"iso english name\",string_agg(n,'; ') \"other names\" from cty full join cty1 using (c) group by c,en order by en";
			break;
		/*case 'tables' :
			sql = "select table_name from information_schema.tables where table_schema='public' and table_type='BASE TABLE' order by table_name";
			break;*/
		default :
			sql = 'select * from "'+tab+'" order by id';
	}
	return sql;
}
function upload(who) {
	var fileInput = document.getElementById('userfile');
	var file = fileInput.files[0];
	formData = new FormData();
	formData.append('userfile', file);
	conecta('upload.php','post1');
}
function show(who) {
	if (who == 'tables') {
		if (document.getElementById('divTableList').style.display != 'block') {
			conecta('tablelist.php','get2');
			document.getElementById('divTableList').style.display = 'block';
		} else {
			document.getElementById('divTableList').style.display = 'none';
		}
	} else {
		conecta('table.php?sql='+sqlFromTable(who)+'&tab='+who,'get1');
	}
}
function importa(tname) {
	formData = new FormData();
	var lnk0A = document.getElementById('lnk0A').value;
	var lnk0B = document.getElementById('lnk0B').value;
	/*var i, p, c1, c2;
	for (i=1; i<tabela.length; i++) {
		p = tabela[i][lnk0A];
		do {
			c1 = p.indexOf('[');
			c2 = p.indexOf(']');
			if (c1 > 0) {
				if (c2 > 0) {
					p = p.substr(0,c1) + p.substr(c2+1);
				} else {
					p = p.substr(0,c1);
				}
			}
		} while (p.indexOf('[') > 0);
		if (tabela[i][lnk0A] != p) {
			//console.log('>>>|'+p+'|');
			tabela[i][lnk0A] = p;
		}
	}*/
	formData.append('tab',JSON.stringify(tabela));
	formData.append('lnk0A',lnk0A);
	formData.append('lnk0B',lnk0B);
	formData.append('tname',tname);
	conecta('import.php','post3');
}
function selAddChange() {
	var i, sel = document.getElementById('divImport2').getElementsByTagName('select');
	enablPaises = true;
	for (i=0; i<sel.length; i++) {
		if (sel[i].value == 'none') {
			enablPaises = false;
			break;
		}
	}
	document.getElementById('btnAdd').disabled = !(enablTName && enablCabCols && enablPaises);
}
function txtTNameChange(who,tabs) {
	//alert(tabs);
	tabs = tabs.split('\t');
	//alert(tabs.length);
	if (who.value == '' || tabs.indexOf(who.value.trim()) > 0) {
		enablTName = false;
		document.getElementById('labTName').style.color = 'red';
	} else {
		enablTName = true;
		document.getElementById('labTName').style.color = 'black';
	}
	document.getElementById('btnAdd').disabled = !(enablTName && enablCabCols && enablPaises);
}
function selTypeChange(who) {
	var input = document.getElementById('nchar'+who.id.substr(3));
	if (who.value == 'char') {
		input.style.display = 'inline';
	} else {
		input.style.display = 'none';
	}
}
function txtCabChange(who) {
	var desc = who.id.substr(3);
	var lab = document.getElementById('cor'+desc);
	if (who.value == '' || who.value == 'c') {
		lab.style.color = 'red';
	} else {
		lab.style.color = 'black';
	}
	var i, inputs = document.getElementById('divImport2').getElementsByTagName('label');
	enablCabCols = true;
	for (i=0; i<inputs.length; i++) {
		if (inputs[i].id.substr(0,3) == 'cor' && inputs[i].style.color == 'red') {
			enablCabCols = false;
			break;
		}
	}
	document.getElementById('btnAdd').disabled = !(enablTName && enablCabCols && enablPaises);
}
function isNumeric(n) {
	n = parseFloat(n);
	return !Number.isNaN(n) && Number.isFinite(n);
}
function prefixChange(who) {
	var i, txt, fs = document.getElementById('divImport2').getElementsByTagName('input');
	for (i=0; i<fs.length; i++) {
		if (fs[i].id.substr(0,3) == 'cbd') {
			txt = document.getElementById('cor'+fs[i].id.substr(3)).innerHTML;
			if (isNumeric(txt)) {
				fs[i].value = who.value+txt;
			}
		}
	}
}
function adiciona() {
	// SQL: COMMENT ON (tabela|coluna)... para inserir metadados
	formData = new FormData();
	formData.append('url',document.getElementById('url').value);
	formData.append('etc',document.getElementById('etc').value);
	if (document.getElementById('lang')) {
		formData.append('lang',document.getElementById('lang').value);
	}
	formData.append('tname',document.getElementById('tname').value);
	formData.append('tab',JSON.stringify(tabela));
	var i, ctrl, col;
	ctrl = document.getElementById('divImport2').getElementsByTagName('label'); // colunas e nomes dos países não encontrados
	col = 0;
	for (i=0; i<ctrl.length; i++) {
		if (ctrl[i].id.substr(0,3) == 'cor') {
			formData.append('cor'+col,ctrl[i].innerHTML); // colunas da tabela csv (Colunas ORiginais)
			col++;
		} else {
			formData.append(ctrl[i].id,ctrl[i].innerHTML); // nomes dos países na tabela csv (Países ORiginais)
		}
	}
	ctrl = document.getElementById('divImport2').getElementsByTagName('input');
	for (i=0; i<ctrl.length; i++) {
		formData.append(ctrl[i].id,ctrl[i].value);
	}
	ctrl = document.getElementById('divImport2').getElementsByTagName('select');
	for (i=0; i<ctrl.length; i++) {
		formData.append(ctrl[i].id,ctrl[i].value);
	}
	formData.append('lnk0A',document.getElementById('lnk0A').options[document.getElementById('lnk0A').selectedIndex].innerHTML);
	formData.append('lnk0B',document.getElementById('lnk0B').options[document.getElementById('lnk0B').selectedIndex].innerHTML);
	var j, divs, sels;
	divs = document.getElementById('divLinkFields').getElementsByTagName('div');
	for (i=0; i<divs.length; i++) {
		sels = divs[i].getElementsByTagName('select'); // lnkAxx e lnkBxx
		for (j=0; j<sels.length; j++) {
			if (sels[j].id.substr(3,1) == 'A') {
				formData.append('lnkA'+sels[j].id.substr(4),sels[j].selectedIndex); // número da coluna
			} else
			if (sels[j].id.substr(3,1) == 'B') {
				formData.append('lnkB'+sels[j].id.substr(4),sels[j].options[sels[j].selectedIndex].innerHTML); // nome do campo html
			}
		}
	}
	conecta('add.php','post2');
}
function enableVerify() {
	//var div0 = document.getElementById('divLinkFields');
	var arr1 = [];
	arr1.push(document.getElementById('lnk0A').options[document.getElementById('lnk0A').selectedIndex].value);
	var arr2 = [];
	arr2.push(document.getElementById('lnk0B').options[document.getElementById('lnk0B').selectedIndex].value);
	var divs = document.getElementById('divLinkFields').getElementsByTagName('div');
	var i, j, sels, repetido = false;
	for (i=0; i<divs.length; i++) {
		sels = divs[i].getElementsByTagName('select');
		for (j=0; j<sels.length; j++) {
			if (sels[j].id.substr(0,7) == 'lnkA') {
				if (arr1.indexOf(sels[j].options[sels[j].selectedIndex].value) >= 0) { // valor repetido
					repetido = true;
					break;
				} else {
					arr1.push(sels[j].options[sels[j].selectedIndex].value);
				}
			} else
			if (sels[j].id.substr(0,7) == 'lnkB') {
				if (arr2.indexOf(sels[j].options[sels[j].selectedIndex].value) >= 0) { // valor repetido
					repetido = true;
					break;
				} else {
					arr2.push(sels[j].options[sels[j].selectedIndex].value);
				}
			}
		}
		if (repetido) {
			break;
		}
	}
	document.getElementById('btnVerify').disabled = repetido;
}
function removeLinkField(who) {
	who.parentNode.remove();
	enableVerify();
}
var linkFieldN = 0;
function addLinkField() {
	var div0 = document.getElementById('divLinkFields');
	var n = linkFieldN;
	// insere uma nova div com os controles
	var div = document.createElement('div');
	div.id = 'divLinkFields'+n;
	div.innerHTML = 'Ligar ';
	
	var selA = document.getElementById('lnk0A').cloneNode(true);
	selA.id = 'lnkA'+n;
	div.appendChild(selA);
	div.innerHTML = div.innerHTML + ' com ';

	var selB = document.getElementById('lnk0B').cloneNode(true);
	selB.id = 'lnkB'+n;
	div.appendChild(selB);
	
	var but = document.createElement('button');
	but.type = 'button';
	but.onclick = function() {removeLinkField(this)};
	but.innerHTML = '-';
	div.appendChild(but);
	
	div0.appendChild(div);
	linkFieldN++;
	enableVerify();
}
function dl() {
	conecta('dl.php','get3');
}
function chkdlclk() {
	var i, chks = document.getElementById('divMainR').getElementsByTagName('input');
	var enabled = false;
	for (i=0; i<chks.length; i++) {
		if (chks[i].id.substr(0,3) == 'chk' && chks[i].checked) {
			enabled = true;
			break;
		}
	}
	document.getElementById('btndl').disabled = !enabled;
}
function download() {
	var i, chks = document.getElementById('divMainR').getElementsByTagName('input');
	var tabs = [];
	for (i=0; i<chks.length; i++) {
		if (chks[i].id.substr(0,3) == 'chk' && chks[i].checked) {
			tabs.push(chks[i].id.substr(3));
		}
	}
	var filename = document.getElementById('dlfilename').value;
	window.open('download.php?filename='+JSON.stringify(filename)+'&tabs='+JSON.stringify(tabs));
}
function pf() {
	var divPf = document.getElementById('divProfile');
	if (divPf.style.display != 'block') { // mostra
		divPf.style.display = 'block';
		conecta('pf.php','get4');
	} else { // esconde
		divPf.style.display = 'none';
		document.getElementById('divMainL').innerHTML = '';
	}
}
function selProfileChange() {
	var c = document.getElementById('selProfile').value;
	if (c != 'none') {
		conecta('profile.php?c='+c,'get1');
	}
}
</script>
</head>
<body>
<div id='divBar'>
	<h1>Geo-politic</h1>
	<nav>
		<h1>Tabelas <a href="javascript:dl()" style='font-size:14px'>Download</a>
		<a href="javascript:pf()" style='font-size:14px'>Profile</a></h1>
		<div id='divProfile'></div>
		<ul>
			<li><a href="javascript:show('cty')">Países</a> (ISO 3166)</li>
			<li><a href="javascript:show('c')">Países (outros nomes)</a></li>
			<li><a href="javascript:show('lang')">Idiomas</a> (ISO 639)</li>
			<li><a href="javascript:show('tables')">Tabelas</a></li>
			<div id='divTableList'>
			</div>
		</ul>
	</nav>
	Importar tabela (tab-separated):<br>
	<form enctype='multipart/form-data' action='upload.php' method='POST'>
		<input type='file' id='userfile' name='userfile' accept='*.csv,*.txt,text/plain' />
		<input type='button' value='Enviar arquivo' onclick='upload(this)' />
	</form>
	<form action='import.php' method='POST'>
		<div id='divImport1' style='background-color:#DDF;'>
		</div>
	</form>
	<form action='add.php' method='POST'>
		<div id='divImport2' style='background-color:#FFD;'>
		</div>
	</form>
</div>
<div id='divMain'>
	<div id='divMainL'>
	</div>
	<div id='divMainR'>
	</div>
</div>
</body>
</html>
