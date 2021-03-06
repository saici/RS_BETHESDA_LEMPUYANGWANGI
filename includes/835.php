<?php // Nugraha, Fri May  7 14:24:46 WIT 2004

$PID = "835";
$SC = $_SERVER["SCRIPT_NAME"];

require_once("lib/dbconn.php");
require_once("lib/form.php");
require_once("lib/class.PgTable.php");
require_once("lib/functions.php");

function getLevel($hcode)
{
    if (strlen($hcode) != 9) return 0;
    if (substr($hcode,  4,  6) == str_repeat("0", 6)) return 1;
    if (substr($hcode,  7,  3) == str_repeat("0", 3)) return 2;
    return 3;
}


title("<img src='icon/informasi-2.gif' align='absmiddle' >  DATA MASTER BANGSAL");
echo "<br>";

if ($_GET["action"] == "new") {
	
echo "<div align=right><a href='".
        "$SC?p=$PID&parent=".$_GET["parent"]."&grp=".$_GET["grp"].
        "'>".icon("back", "Kembali")."</a></div>";
        	
    $f = new Form("actions/835.insert.php", "POST");
    $f->PgConn = $con;
    $f->hidden("parent", $_GET["parent"]);
    $f->hidden("f_is_group", $_GET["grp"] == "B" || $_GET["grp"] == "R" ? "Y" : "N");
    if ($_GET["grp"] == "B") {
        $f->text("f_bangsal","Nama Bangsal",50,255,$d->bangsal);
    } elseif ($_GET["grp"] == "R") {
        $f->text("f_bangsal","Nama Ruangan",50,255,$d->bangsal);
        $f->selectSQL("f_klasifikasi_tarif_id", "Klasifikasi Tarif",
            "select '' as tt, '' as tdesc union ".
            "select tc, tdesc ".
            "from rs00001 ".
            "where tt = 'KTR' ".
            "order by tdesc", $d->klasifikasi_tarif_id);
        $f->text("f_harga","Harga PerHari",12,12,0,"style='text-align:right'");
	//$f->text("f_hargaj","Harga Per6Jam",12,12,0,"style='text-align:right'");
	 $f->selectSQL("f_askep_rs34", "Tarif Jasa Perawatan",
            "select '' as id, '' as layanan union ".
            "SELECT id::character varying, layanan FROM rs00034 WHERE layanan ILIKE '%Jasa Perawatan%' AND is_group = 'N'
            ORDER BY layanan ASC");
    } elseif ($_GET["grp"] == "D") {
        $f->text("f_bangsal","Nama Bed",50,255,$d->bangsal);
    }
    $f->submit(" Simpan ");
    $f->execute();
} elseif ($_GET["action"] == "edit") {
    $r = pg_query($con, "select * from rs00012 where id = '".$_GET["e"]."'");
    $n = pg_num_rows($r);
    if($n > 0) $d = pg_fetch_object($r);
    pg_free_result($r);

    $f = new Form("actions/835.update.php", "POST");
    $f->PgConn = $con;
    $f->hidden("id", $_GET["e"]);
    $f->hidden("parent", $_GET["parent"]);
    if ($_GET["grp"] == "B") {
        $f->text("f_bangsal","Nama Bangsal",50,255,$d->bangsal);
    } elseif ($_GET["grp"] == "R") {
        $f->text("f_bangsal","Nama Ruangan",50,255,$d->bangsal);
        $f->selectSQL("f_klasifikasi_tarif_id", "Klasifikasi Tarif",
            "select '' as tt, '' as tdesc union ".
            "select tc, tdesc ".
            "from rs00001 ".
            "where tt = 'KTR' ".
            "order by tdesc", $d->klasifikasi_tarif_id);
        $f->text("f_harga","Harga PerHari",12,12,$d->harga,"style='text-align:right'");
        $f->selectSQL("f_askep_rs34", "Tarif Jasa Perawatan",
            "select '' as id, '' as layanan union ".
            "SELECT id::character varying, layanan FROM rs00034 WHERE layanan ILIKE '%Jasa Perawatan%' AND is_group = 'N'
            ORDER BY layanan ASC", 
            $d->askep_rs34);
            /**
        $f->selectSQL("f_akomodasi_rs34", "Tarif Akomodasi",
            "select '' as id, '' as layanan union ".
            "SELECT id::character varying, layanan FROM rs00034 WHERE layanan ILIKE '%akomodasi%' AND is_group = 'N'
            ORDER BY layanan ASC", 
            $d->akomodasi_rs34);
            */ 
	//$f->text("f_hargaj","Harga Per6Jam",12,12,$d->hargaj,"style='text-align:right'");

    } elseif ($_GET["grp"] == "D") {
        $f->text("f_bangsal","Nama Bed",50,255,$d->bangsal);
    }
    $f->submit(" Simpan ");
    $f->execute();
}elseif ($_GET["action"] == "delete"){
	
	$f = new Form("actions/835.delete.php", "POST");
    $f->PgConn = $con;
    $f->hidden("id", $_GET["e"]);
    $f->hidden("parent", $_GET["parent"]);
    $f->hidden("L1", $_GET["L1"]);
    $f->execute();	   
}else {

    $ext = "OnChange = 'Form1.submit();'";
    $level = 0;
    $f = new Form($SC, "GET", "NAME=Form1");
    $f->PgConn = $con;
    $f->hidden("p", $PID);
    $f->selectSQL("L1", "Keperawatan",
        "select '' as hierarchy, '' as bangsal union " .
        "select hierarchy, bangsal ".
        "from rs00012 ".
        "where substr(hierarchy,4,12) = '000000000000' ".
        "and is_group = 'Y' ".
        "order by bangsal", $_GET["L1"],
        $ext);
    if (strlen($_GET["L1"]) > 0) $level = 1;
    if (getFromTable(
            "select hierarchy, bangsal ".
            "from rs00012 ".
            "where substr(hierarchy,7,9) = '000000000' ".
            "and substr(hierarchy,1,3) = '".substr($_GET["L1"],0,3)."' ".
            "and hierarchy != '".$_GET["L1"]."' ".
            "and is_group = 'Y'")
        && strlen($_GET["L1"]) > 0) {
        $f->selectSQL("L2", "Nama Bangsal",
            "select '' as hierarchy, '' as bangsal union " .
            "select a.hierarchy as hierarchy, a.bangsal || '  ' || b.tdesc as bangsal ".
            "from rs00012 a, rs00001 b ".
            "where substr(hierarchy,7,9) = '000000000' ".
            "and substr(hierarchy,1,3) = '".substr($_GET["L1"],0,3)."' ".
            "and a.klasifikasi_tarif_id = b.tc and b.tt='KTR' ".
            "and hierarchy != '".$_GET["L1"]."' ".
            "and is_group = 'Y' ".
            "order by hierarchy", $_GET["L2"],
            $ext);
        if (strlen($_GET["L2"]) > 0) $level = 2;
        //tambahan sfdn
        if (getFromTable(
                "select hierarchy, bangsal ".
                "from rs00012 ".
                "where substr(hierarchy,10,6) = '000000' ".
                "and substr(hierarchy,1,6) = '".substr($_GET["L2"],0,6)."' ".
                "and hierarchy != '".$_GET["L2"]."' ".
                "and is_group = 'Y'")
            && strlen($_GET["L1"]) > 0
            && strlen($_GET["L2"]) > 0) {
            $f->selectSQL("L3", "",
                "select '' as hierarchy, '' as bangsal union " .
                "select hierarchy, bangsal ".
                "from rs00012 ".
                "where substr(hierarchy,10,6) = '000000' ".
                "and substr(hierarchy,1,6) = '".substr($_GET["L2"],0,6)."' ".
                "and hierarchy != '".$_GET["L2"]."' ".
                "and is_group = 'Y' ".
                "order by layanan", $_GET["L3"],
                $ext);
            if (strlen($_GET["L3"]) > 0) $level = 3;
            if (getFromTable(
                    "select hierarchy, bangsal ".
                    "from rs00012 ".
                    "where substr(hierarchy,13,3) = '000' ".
                    "and substr(hierarchy,1,9) = '".substr($_GET["L3"],0,9)."' ".
                    "and hierarchy != '".$_GET["L3"]."' ".
                    "and is_group = 'Y'")
                && strlen($_GET["L1"]) > 0
                && strlen($_GET["L2"]) > 0
                && strlen($_GET["L3"]) > 0) {
                $f->selectSQL("L4", "",
                    "select '' as hierarchy, '' as bangsal union " .
                    "select hierarchy, bangsal ".
                    "from rs00012 ".
                    "where substr(hierarchy,13,3) = '000' ".
                    "and substr(hierarchy,1,9) = '".substr($_GET["L3"],0,9)."' ".
                    "and hierarchy != '".$_GET["L3"]."' ".
                    "and is_group = 'Y' ".
                    "order by layanan", $_GET["L4"],
                    $ext);
                    if (strlen($_GET["L4"]) > 0) $level = 4;
                }
            }
        }
        //akhir tambahan

    $f->execute();

    $SQL1 = "select a.bangsal, a.id ".
            "from rs00012 as a ".
            "left join rs00001 as c on a.klasifikasi_tarif_id = c.tc and c.tt = 'KTR' ".
            "where substr(a.hierarchy,1,".($level*3).") = '".substr($_GET["L$level"],0,($level*3))."' ".
            "and a.hierarchy <> '".$_GET["L$level"]."' ".
            "and substr(a.hierarchy,".(($level*3)+4).",".(9-(($level*3)+3)).") = '".
            str_repeat("0",9-(($level*3)+3))."'".
             "order by a.bangsal ASC" ;
            
    $SQL2 = "select a.bangsal,  c.tdesc as klasifikasi_tarif, a.harga, f.layanan, a.id ".
            "from rs00012 as a ".
            "left join rs00001 as c on a.klasifikasi_tarif_id = c.tc and c.tt = 'KTR' ".
            "left join rs00034 as f on a.askep_rs34 = f.id ".
            "where substr(a.hierarchy,1,".($level*3).") = '".substr($_GET["L$level"],0,($level*3))."' ".
            "and a.hierarchy <> '".$_GET["L$level"]."' ".
            "and substr(a.hierarchy,".(($level*3)+4).",".(9-(($level*3)+3)).") = '".
            str_repeat("0",9-(($level*3)+3))."'";
    $SQL3 = "select is_group ".
            "from rs00012 ".
            "where substr(hierarchy,1,".($level*3).") = '".substr($_GET["L$level"],0,($level*3))."' ".
            "and hierarchy <> '".$_GET["L$level"]."' ".
            "and substr(hierarchy,".(($level*3)+4).",".(9-(($level*3)+3)).") = '".
            str_repeat("0",9-(($level*3)+3))."'";
    $SQL4 = "select a.bangsal, a.id ".
            "from rs00012 as a ".
            "left join rs00001 as c on a.klasifikasi_tarif_id = c.tc and c.tt = 'KTR' ".
            "where substr(a.hierarchy,1,".($level*3).") = '".substr($_GET["L$level"],0,($level*3))."' ".
            "and a.hierarchy <> '".$_GET["L$level"]."' ".
            "and substr(a.hierarchy,".(($level*3)+4).",".(9-(($level*3)+3)).") = '".
            str_repeat("0",9-(($level*3)+3))."'";

    $isGroup = getFromTable($SQL3);

 
    if ($level == 0) {
        $t = new PgTable($con, "100%");
        $t->SQL = $SQL1;
        $t->setlocale("id_ID");
        $t->ShowRowNumber = true;
        $t->RowsPerPage = 10;

        $t->ColFormatHtml[1] =
            "<A CLASS=TBL_HREF HREF='".
            "$SC?p=$PID&action=edit&parent=".$_GET["L$level"]."&grp=B&e=<#1#>".
            "'>".icon("edit","Edit")."</A> ".
            "<A CLASS=TBL_HREF HREF='actions/835.delete.php?p=$PID&action=delete&parent=".$_GET["L$level"]."&grp=B&e=<#1#>".
            "'>".icon("delete","Hapus")."</A>".
            "</nobr>";
        $t->ColHeader = Array("KEPERAWATAN", "&nbsp;");
        $t->ColAlign[1] = "CENTER";
        $t->execute();
    } elseif ($level == 1) {
        $t = new PgTable($con, "100%");
        $t->SQL = $SQL2;
        $t->setlocale("id_ID");
        $t->ShowRowNumber = true;
        $t->RowsPerPage = 10;
        //$t->ColFormatMoney[2] = "%!+#2n";
        $t->ColFormatHtml[4] =
            "<A CLASS=TBL_HREF HREF='".
            "$SC?p=$PID&action=edit&parent=".$_GET["L$level"]."&grp=R&e=<#4#>".
            "'>".icon("edit","Edit")."</A> ".
            "<A CLASS=TBL_HREF HREF='actions/835.delete.php?p=$PID&action=delete&parent=".$_GET["L$level"]."&grp=R&e=<#4#>".
            "'>".icon("delete","Hapus")."</A>".
            "</nobr>";
        $t->ColHeader = Array("BANGSAL","KLASIFIKASI TARIF","HARGA PERHARI","ASUHAN KEPERAWATAN","&nbsp;");
        $t->ColAlign[3] = "LEFT";
        $t->execute();
    } elseif ($level == 2) {
        $t = new PgTable($con, "100%");
        $t->SQL = $SQL4;
        $t->setlocale("id_ID");
        $t->ShowRowNumber = true;
        $t->RowsPerPage = 10;
        $t->ColFormatHtml[1] =
            "<A CLASS=TBL_HREF HREF='".
            "$SC?p=$PID&action=edit&parent=".$_GET["L$level"]."&grp=D&e=<#1#>".
            "'>".icon("edit","Edit")."</A> ".
            "<A CLASS=TBL_HREF HREF='actions/835.delete.php?p=$PID&action=delete&parent=".$_GET["L$level"]."&grp=D&e=<#1#>".
            "'>".icon("delete","Hapus")."</A>".
            "</nobr>";
            
        $t->ColHeader = Array("BED", "&nbsp;");
        $t->ColAlign[1] = "CENTER";
        $t->execute();
    }
    
       echo "<br>";
    echo "<DIV ALIGN=LEFT><img src=\"icon/rawat-inap.gif\" align=absmiddle >";
    if ($level == 0) {
        echo "<A HREF='$SC?p=$PID&action=new&parent=".$_GET["L$level"]."&grp=B'>Tambah Kategori Keperawatan</A><br>";
    } elseif ($level == 1) {
        echo "<A HREF='$SC?p=$PID&action=new&parent=".$_GET["L$level"]."&grp=R'>Tambah Bangsal</A><br>";
    } elseif ($level == 2) {
        echo "<A HREF='$SC?p=$PID&action=new&parent=".$_GET["L$level"]."&grp=D'>Tambah Bed</A><br>";
    }
    echo "</DIV>";

}


?>
