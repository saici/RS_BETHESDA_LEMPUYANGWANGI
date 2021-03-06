<?php // Nugraha, Tue Apr 20 15:41:59 WIT 2004
     // sfdn, 05-06-2004

session_start();
unset($_SESSION["SELECT_IBU"]);
if (isset($_GET["e"])) {
    $_SESSION["SELECT_IBU"] = $_GET["e"];
    ?>
    <SCRIPT language="JavaScript">
        window.opener.location = window.opener.location;
        window.close();
    </SCRIPT>
    <?php
    exit;
}
if (isset($_GET["mTipe"])) $_SESSION["mTipe"] = $_GET["mTipe"];
if (isset($_GET["tag"]))  $_SESSION["tag"]  = $_GET["tag"];
?>
<HTML>
<HEAD>
    <TITLE>Pilih Identitas Ibu</TITLE>
    <LINK rel='StyleSheet' type='text/css' href='../default.css'>
</HEAD>
<BODY>
<TABLE border="0" bgcolor="#FFFFFF" width="100%" cellpadding="8"><TR><TD>
<?php
require_once("../lib/dbconn.php");
require_once("../lib/form.php");
require_once("../lib/class.PgTable.php");
require_once("../lib/functions.php");

title("Pilih Ibu");
echo "<br>";
$f = new Form("ibu.php", "GET", "NAME=Form1");
$f->PgConn = $con;
$f->selectSQL("mTipe", "Tipe Pasien Ibu",
    "select '' as tc, '' as tdesc union " .
    "select tc, tdesc ".
    "from rs00001 ".
    "where tt = 'JEP' and tc != '000' ".
    "order by tdesc", $_SESSION["mTipe"],
    "OnChange = 'Form1.submit();'");
    
$f->execute();
$is_selected = getFromTable(
    "select count(mr_no) ".
    "from rs00002 ".
    "where jenis_kelamin='P' and tipe_pasien = '" . $_SESSION["mTipe"] . "'") > 0;

if ($is_selected) {
    // search box
    echo "<DIV ALIGN=RIGHT><TABLE BORDER=0><FORM ACTION='ibu.php'><TR>";
    echo "<INPUT TYPE=HIDDEN NAME=mTipe VALUE='".$_SESSION["mTipe"]."'>";
    //echo "<INPUT TYPE=HIDDEN NAME=mJMF VALUE='".$_GET["mJMF"]."'>";
    echo "<TD><INPUT TYPE=TEXT NAME=search VALUE='".$_GET["search"]."'></TD>";
    echo "<TD><INPUT TYPE=SUBMIT VALUE=' Cari Nama '></TD>";
    echo "</TR></FORM></TABLE></DIV>";

    $t = new PgTable($con, "100%");
    $t->SQL =   
			"select a.mr_no as id,a.mr_no as mr_no, a.nama ".
            "from rs00002 a ".
			"where a.tipe_pasien = '".$_SESSION["mTipe"]."'  and a.jenis_kelamin='P' and a.is_bayi is null and ".
  			"upper(a.nama) LIKE '%".strtoupper($_GET["search"])."%' ".
			"group by a.mr_no, a.nama ".		
            "order by a.nama";
				
				
    $t->setlocale("id_ID");    
    //$t->ShowRowNumber = true;
    $t->RowsPerPage = 1000000000;
    $t->DisableStatusBar = true;
    $t->ColFormatHtml[0] =
        "<A HREF='ibu.php?e=<#0#>'><IMG BORDER=0 SRC='../images/icon-ok.png'></A>";
    $t->ColHeader = Array("&nbsp;","No.MR" ,    "NAMA" );
    $t->ColAlign  = Array("CENTER","center",  "LEFT");
    //$t->showsql=true;
    $t->execute();
}

?>
</TD></TR></TABLE>
</BODY>
</HTML>
