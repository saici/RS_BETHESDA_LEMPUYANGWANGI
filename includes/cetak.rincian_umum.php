<?php
	// sfdn, 24-12-2006
session_start();

require_once("../lib/setting.php");
require_once("../lib/terbilang.php");

require_once("../lib/dbconn.php");
require_once("../lib/form.php");
require_once("../lib/class.PgTable.php");
require_once("../lib/functions.php");

$ROWS_PER_PAGE     = 999999;
//$RS_NAME           = $set_header[0]."<br>".$set_header[1];
//$RS_ALAMAT         = $set_header[2]."<br>".$set_header[3].$set_header[4];

?>

<HTML>


<HEAD>
<TITLE>::: Sistem Informasi <?php echo $RS_NAME; ?> :::</TITLE>
<LINK rel='styleSheet' type='text/css' href='../invoice.css'>
<SCRIPT LANGUAGE="JavaScript">
<!-- Begin
function printWindow() {
bV = parseInt(navigator.appVersion);
if (bV >= 4) window.print();
}
//  End -->
</script>

</HEAD>

<BODY TOPMARGIN=1 LEFTMARGIN=5 MARGINWIDTH=0 MARGINHEIGHT=0 />

<?


$reg = $_GET["rg"];

$rt = pg_query($con,
        "select code,mr,nama,umur, case when sex='W' then 'Wanita' else 'Laki-laki' end as jk from apotik_umum where code::text= '$reg'  ");     

    $nt = pg_num_rows($rt);
    $dt = pg_fetch_object($rt);
    pg_free_result($rt);

if ($reg > 0) {
    if (getFromTable("select code as id ".
                     "from apotik_umum ".
                     "where code = '$reg' ".
                     " ") ==0) {
                     //"and status = 'A'") == 0) {
        $reg = 0;
        $msg = "Nomor registrasi tidak ditemukan. Masukkan kembali nomor registrasi.";
    }
}


//include("335.inc_.php");
?>


    <table cellpadding="0" cellspacing="0" class="items"><tbody><tr>
                <td width="40%">
                    <div class="addressbox">
                        <div><img src="../images/logo_kotakab3.png" title="MWN"></div>
                        <strong>RS. SITI KHADIJAH</strong><br>
                        Jl. Bandung No. 39-47<br>
                        Sugih Waras - Pekalongan Timur <br>
                        Telp. Hunting (0285) 422845<br>
                        Indonesia
                    </div>
                </td>

                </tr></tbody></table>
    <br>
    <table style="width: 100% !important;">
        <tr>
            <td align="center"><font class="paid">Rincian Pembayaran Farmasi</font></td>
        </tr>
    </table>

	<br>
<table border="0" width=100%>
	<tr>
        <td valign=top width=30% class="TITLE_SIM3"><b>SUDAH TERIMA DARI </b></td>
		<td valign=top class="TITLE_SIM3"><b>:</b></td>
		<td valign=top class="TITLE_SIM3"><b> Tn/Ny/Sdr. <?= $dt->nama ?></b></td>
        
	</tr>
<?
$rrs = pg_query($con,
        "select * from rs00005 ".
		"where kasir in ('BYG','BYC','BYS','BYA') and ".
		"	reg = '$reg' "); //and ".
		//"	referensi IN ('KASIR')");

while ($dds = pg_fetch_object($rrs)) {
?>

	<tr>
        <td valign=top width=30% class="TITLE_SIM3"><b>UANG SEJUMLAH</b></td>
		<td valign=top class="TITLE_SIM3"><b>:</b></td>
		<td valign=top  class="TITLE_SIM3"><b>Rp. <?= number_format($dds->jumlah,2) ?></b></td>
        
	</tr><tr>
		<td valign=top class="TITLE_SIM3"><b>&nbsp;</b></td>
		<td valign=top class="TITLE_SIM3"><b>&nbsp;</b></td>
        <td valign=top class="TITLE_SIM3"><b><i><?php $y=terbilang($dds->jumlah);
		echo strtoupper($y);?> RUPIAH</i></b></td>
	</tr>

<?
}
pg_free_result($rrs);


?>

<tr>
		<td valign=top class="TITLE_SIM3"><b>&nbsp;</b></td>
		<td valign=top class="TITLE_SIM3"><b>&nbsp;</b></td>
        <td valign=top class="TITLE_SIM3"><b>Untuk Pembayaran Obat Kesehatan </b></td>
   </tr>
</table>
<?
//include("335.inc_2.php");

  // title("Pembayaran");

    if ($_GET["kas"] == "igd") {
       $loket = "IGD";
       $kasir = "IGD";
       $lyn = "layanan = '100'";
   
           
       
    } elseif ($_GET["kas"] == "rj") {
       $loket = "RJL";
       $kasir = "RJL";
       $lyn = "layanan not in ('100','99996','99997','12651','13111')";

    } else {
       $loket = "RIN";
       $kasir = "RIN";
       $lyn = "(layanan not in ('99996','99997','12651','13111'))";
       $d->poli = 0;
    }




$tgl_skrg=date('d-m-Y',time());

$sql="SELECT a.obat as nama,b.harga,b.qty, (b.tagihan-(b.harga*b.qty)) as jasa, b.tagihan   
	from rs00015 a, rs00008 b 
	where a.id::text=b.item_id and b.no_reg='".$_GET["rg"]."' and trans_type='OB1' and trans_form in ('320RJ_IGDU','320RJ_SWDU','320RJ_CDMU','320RJ_ASKU')";

$sql2 = "SELECT 'Racikan Obat' as nama,sum(b.harga) as harga, '' as qty , sum((b.tagihan-(b.harga*b.qty))) as jasa, sum(b.tagihan) as tagihan
		from rs00015 a, rs00008 b 
		where a.id::text=b.item_id and b.no_reg='".$_GET["rg"]."' and trans_type='RCK' and trans_form in ('320RJ_IGDU','320RJ_SWDU','320RJ_CDMU','320RJ_ASKU') ";
		
@$r1 = pg_query($con,$sql);
@$n1 = pg_num_rows($r1);

@$r2 = pg_query($con,$sql2);
@$n2 = pg_num_rows($r2);

	$max_row= 30 ;
	$mulai = $HTTP_GET_VARS["rec"] ;	
	if (!$mulai){$mulai=1;}  
	
	if ($_GET[tt] == "igd") {
      $loket = "IGD";
	  $PID1 = "320RJ_IGD";
   } elseif ($_GET[tt] == "swd") {
      $loket = "SWADAYA";
	  $PID1 = "320RJ_SWD";
   } elseif ($_GET[tt] == "cdm") {
      $loket = "CINDUO MATO";
	  $PID1 = "320RJ_CDM";
   } else {
      $loket = "AKSES";
	  $PID1 = "320RJ_ASK";
   }
   
   
   //========== cek bayar/blm
	$blm_byr=getFromTable("select sum(jumlah) from rs00005 where is_obat='Y' and kasir in ('BYG','BYC','BYS','BYA') and is_bayar='N' and reg='".$_GET["rg"]."'");
	$sdh_byr=getFromTable("select sum(jumlah) from rs00005 where is_obat='Y' and kasir in ('BYG','BYC','BYS','BYA') and is_bayar='Y' and reg='".$_GET["rg"]."'");
	$pot_byr=getFromTable("select sum(jumlah) from rs00005 where is_obat='Y' and kasir in ('POT') and is_bayar='Y' and reg='".$_GET["rg"]."'");
	$sisa_tgh=$blm_byr - ($sdh_byr + $pot_byr);
	//=========================
		
		
?>

<br>
<table class="items" align="center" border="0" WIDTH='100%'>
	<tr valign="top" class="title textcenter">
		<td align="center" valign="middle"><font size=1 ><b>Nama Obat</b></font></td>
		<td align="center" width="15%" valign="middle"><font size=1 ><b>Harga</b></font></td>
		<td align="center" width="15%" valign="middle"><font size=1 ><b>Qty</b></font></td>
		<td align="center" width="15%" valign="middle"><font size=1 ><b>Jasa<br>Resep/Racikan</b></font></td>
		<td align="center" width="15%" valign="middle"><font size=1 ><b>Jumlah Harga</b></font></td>
	</tr>
<?	
			$totbaru= 0;
			$totulang= 0;
			$row1=0;
			$i= 1 ;
			$j= 1 ;
			$last_id=1;			
			while (@$row1 = pg_fetch_array($r1)){
				if (($j<=$max_row) AND ($i >= $mulai)){
					$no=$i 	
					?>		
				 	<tr > 
			        	<td class="TBL_BODY" align="left"><font size=2 ><?=$row1["nama"] ?> </font></td>
						<td class="TBL_BODY" align="right"><font size=2 ><?=number_format($row1["harga"] ,2,",",".") ?> </font></td>
						<td class="TBL_BODY" align="center"><font size=2 ><?=$row1["qty"] ?> </font></td>
						<td class="TBL_BODY" align="right"><font size=2 ><?=number_format($row1["jasa"] ,2,",",".")?></font></td>
						<td class="TBL_BODY" align="right"><font size=2 ><?=number_format($row1["tagihan"] ,2,",",".")?></font></td>
					</tr>	
					<?
					$totulang=$totulang+$row1["tagihan"] ;
					?>
					<?;$j++;					
				}
				$i++;
				if ($last_id < $row1->no_reg){$last_id=$row1->no_reg;}		
			} 
			?>

<?	
			$totbaru2= 0;
			$totulang2= 0;
			$row2=0;
			$i2= 1 ;
			$j2= 1 ;
			$last_id2=1;			
			while (@$row2 = pg_fetch_array($r2)){
				if (($j2<=$max_row) AND ($i2 >= $mulai)){
					$no=$i 	
					?>		
				 	<tr > 
			        <td class="TBL_BODY" align="left" colspan=3><font size=2 ><?=$row2["nama"] ?> </font></td>
					<td class="TBL_BODY" align="right"><font size=2 ><?=number_format($row2["jasa"] ,2,",",".")?></font></td>
					<td class="TBL_BODY" align="right"><font size=2 ><?=number_format($row2["tagihan"] ,2,",",".")?></font></td>
					</tr>	
					<?
					$totulang2=$totulang2+$row2["tagihan"] ;
					?>
					<?;$j2++;					
				}
				$i2++;
				if ($last_id2 < $row2->no_reg){$last_id2=$row2->no_reg;}		
			} 
			
			$total=$totulang+$totulang2;
			?>
		
					<tr valign="top" class="title add-b" >  
			        	<td align="right" colspan="4" height="25" valign="middle"><font size=1 > TOTAL TAGIHAN</font></td>
						<td align="right" valign="middle"><font size=1 >Rp. <?=number_format($total,2,",",".")?></font></td>
					</tr>
					<tr valign="top" class="title add-b">  
			        	<td align="right" colspan="4" height="25" valign="middle"><font size=1 > POTONGAN </font></td>
						<td align="right" valign="middle"><font size=1 >Rp. <?=number_format($pot_byr,2,",",".")?></font></td>
					</tr>
					<tr valign="top" class="title add-b" >  
			        	<td align="right" colspan="4" height="25" valign="middle"><font size=1 > BAYAR </font></td>
						<td align="right" valign="middle"><font size=1 >Rp. <?=number_format($sdh_byr,2,",",".")?></font></td>
					</tr>
					<tr valign="top" class="title add-b" >  
						<td align="left" colspan="5" valign="middle"><font size=1 >TERBILANG :  <i><?php $y=terbilang($sdh_byr);
		echo strtoupper($y);?> RUPIAH</i></font></td>
					</tr>
					
</table>
<br>


<?
$tgl_sekarang = date("d M Y", time());
?>
<table border="0" align="right" width="50%">
  <tr>
        <td align="center" class="TITLE_SIM3"><b>Pekalongan, <?echo $tgl_sekarang;?></b></td>
      
  </tr>
  <tr>
    <td align="center" class="TITLE_SIM3"><b>&nbsp;</b></td>
</tr>
<tr>
    <td align="center" class="TITLE_SIM3"><b>&nbsp;</b></td>
</tr>
<tr>
    <td align="center" class="TITLE_SIM3"><u><b><? echo $_SESSION["nama_usr"];?></b></u></td>
</tr>
</table>

<SCRIPT LANGUAGE="JavaScript">
<!-- Begin
printWindow();
//  End -->
</script>

</body>
</html>
