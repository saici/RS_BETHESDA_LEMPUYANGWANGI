<?php
session_start();
require_once("../lib/dbconn.php");
?>

<HTML>

<HEAD>
<TITLE></TITLE>
<SCRIPT LANGUAGE="JavaScript">
<!-- Begin
function printWindow() {
bV = parseInt(navigator.appVersion);
if (bV >= 4) window.print();
}
//  End -->
</script>


</HEAD>

<BODY TOPMARGIN=5 LEFTMARGIN=5 MARGINWIDTH=0 MARGINHEIGHT=0>

<?
$reg = $_GET["rg"];

$r = pg_query($con,"select b.mr_no, b.nama, b.tgl_lahir, b.jenis_kelamin, b.umur, b.alm_tetap ".
				   "from  rs00002 as b ".	 
				   "where b.mr_no = '$reg' ");
$n = pg_num_rows($r);
if($n > 0) $d = pg_fetch_object($r);
pg_free_result($r);

?>

<div style='margin-left:20px;'>

<table cellpadding="0" cellspacing="0" border="0" width="325" style="font-family: tahoma; font-size: 12px;">
    <tr>
        <td height="0" width="230" colspan="2">&nbsp;</td>
    </tr>
    <tr>
        <td height="0" width="230" colspan="2">&nbsp;</td>
    </tr> 
    <tr>
        <td height="0" width="230" colspan="2">&nbsp;</td>
    </tr>
    <tr>
        <td height="0" width="230" colspan="2">&nbsp;</td>
    </tr> 
         <td height="0" width="230" colspan="2">&nbsp;</td>
    </tr>
    <tr>
        <td height="0" width="230" colspan="2"><font size="2"><b><?php echo $d->nama;?></b></font></td>
    </tr>
	
	<?php
		$mrNo = str_split($d->mr_no, 2);
		//echo '<pre>';
		//var_dump($mrNo);
	?>
	
    <tr>
        <td height="0" width="230" colspan="2"><font size="2"><b><?php echo $mrNo[0].'-'.$mrNo[1].'-'.$mrNo[2]?></b></font></td>
    </tr>
	<tr>
        <td height="0" width="230" colspan="2"><font size="2"><b><?php echo $d->umur;?> Th</b></font></td>
    </tr>
    <tr>
        <td height="0" width="230" colspan="2"><font size="2"><b><?php echo $d->alm_tetap;?></b></font></td>
    </tr>
    <tr>
        <td height="0" width="230"><font size="5">&nbsp;</td>
        <td height="0" width="230" align="center"><img src="<?php echo 'cetak.kartu_pasien.php?rg='.$reg?>" style = 'margin-left:25px; margin-top:12px;'/></td>
    </tr>
</table>

</div>

<SCRIPT LANGUAGE="JavaScript">
<!-- Begin
printWindow();
//  End -->
</script>

</body>
</html>
