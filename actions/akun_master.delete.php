<?php 
//Wildan, ST. 17 Feb 2014

$PID = "akun_master";

require_once("../lib/dbconn.php");

if ($_GET[grp] == "N") {

   if (empty($_GET[sure])) {
	header("Location: ../index2.php?p=$PID&grp=N&sort=".$_GET[sort]."&order=".$_GET[order]."&e=".$_GET[e]."&L1=".$_GET[L1]."&L2=".$_GET[L2]."&L3=".$_GET[L3]."&L4=".$_GET[L4]."&L5=".$_GET[L5]."&tblstart=".$_GET[tblstart]."&sure=false");
	exit();
   } elseif ($_GET[sure] == "::YA::") {

   $SQL = "delete from akun_master where ".
            "id = '".$_GET["e"]."'";
   } else {

    header("Location: ../index2.php?p=$PID&sort=".$_GET[sort]."&order=".$_GET[order]."&L1=".$_GET[L1]."&L2=".$_GET[L2]."&L3=".$_GET[L3]."&L4=".$_GET[L4]."&L5=".$_GET[L5]."&tblstart=".$_GET[tblstart]);
    exit();

   }


} else {

   if (empty($_GET[sure])) {
	header("Location: ../index2.php?p=$PID&grp=Y&e=".$_GET[e]."&sure=false");
	exit();
   } elseif ($_GET[sure] == "::YA::") {

   $q = pg_query("select hierarchy from akun_master where id = ".$_GET[e]);
   $qr = pg_fetch_array($q);
   $hierar = $qr[0];


   if (substr($hierar,3,6) == "000") {
      $level = substr($hierar,0,3);
   } elseif (substr($hierar,6,9) == "000") {
      $level = substr($hierar,0,6);
   } elseif (substr($hierar,9,12) == "000") {
      $level = substr($hierar,0,9);
   } else {$level = substr($hierar,0,12);}
  
   $SQL = "delete from akun_master where hierarchy LIKE '$level%'";
 
   } else {

    header("Location: ../index2.php?p=$PID");
    exit();

   }



}

 pg_query($con, $SQL);
  
header("Location: ../index2.php?p=$PID&sort=".$_GET[sort]."&order=".$_GET[order]."&tblstart=".$_GET[tblstart]."&L1=".$_GET[L1]."&L2=".$_GET[L2]."&L3=".$_GET[L3]."&L4=".$_GET[L4]."&L5=".$_GET[L5]."");
exit();

?>
