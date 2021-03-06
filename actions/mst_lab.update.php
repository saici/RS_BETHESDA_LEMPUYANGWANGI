<?php // Nugraha, Fri Apr 30 15:47:37 WIT 2004
	 // Ian, custom Lab 01-12-2007

$PID = "mst_lab";

require_once("../lib/dbconn.php");
require_once("../lib/querybuilder.php");

function getLevel($hcode)
{
    if (strlen($hcode) != 15) return 0;
    if (substr($hcode,  4, 12) == str_repeat("0", 12)) return 1;
    if (substr($hcode,  7,  9) == str_repeat("0",  9)) return 2;
    if (substr($hcode, 10,  6) == str_repeat("0",  6)) return 3;
    if (substr($hcode, 13,  3) == str_repeat("0",  3)) return 4;
    return 5;
}

$qb = New UpdateQuery();
$qb->TableName = "c_pemeriksaan_lab";
$qb->HttpAction = "POST";
$qb->VarPrefix = "f_";
$qb->addPrimaryKey("id", "'" . $_POST["id"] . "'");
$SQL = $qb->build();

$level = getLevel($_POST["parent"]);

for ($n = $level; $n > 0; $n--) {
    $href = "&L$n=" . substr($_POST["parent"],0,$n*3) . str_repeat("0", 15 - ($n * 3)) . $href;
}
if($_POST['sort']){
	$sort="&sort=".$_POST[sort]."&order=".$_POST[order];
}
if($_POST['tblstart']){
	$tbl="&tblstart=".$_POST[tblstart];
}
$href = "../index2.php?p=$PID".$href."".$sort."".$tbl;

pg_query($con, $SQL);
header("Location: $href");
exit;

?>
