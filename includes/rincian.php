<?php
$_GET["rg"] = $_GET[rg];
$rg = isset($_GET["rg"])
        ? $_GET["rg"]
        : $_POST["rg"];
$mr = isset($_GET["mr"])
        ? $_GET["mr"]
        : $_POST["mr"];

?>
<table width="100%">
    <tr>
        <td align="center" class="TBL_HEAD" width="10%">TANGGAL</td>
        <td align="center" class="TBL_HEAD">DESCRIPTION</td>
		<td align="center" class="TBL_HEAD">DOKTER</td>
        <td align="center" class="TBL_HEAD" width="10%">JUMLAH</td>
        <td align="center" class="TBL_HEAD" width="10%">CITO</td>
        <td align="center" class="TBL_HEAD" width="10%">DISCOUNT</td>
        <td align="center" class="TBL_HEAD" width="10%">TAGIHAN</td>
        <td align="center" class="TBL_HEAD" width="10%">PENJAMIN</td>
        <td align="center" class="TBL_HEAD" width="10%">SELISIH</td>
        <td align="center" class="TBL_HEAD" width="10%">BATAL</td>
        <td align="center" class="TBL_HEAD" width="10%">CETAK</td>
    </tr>

    <?
    $rec = getFromTable("select count(id) from rs00008 " .
            "where trans_type = 'LTM' and to_number(no_reg,'999999999999') = '".(string)$_GET['rg']."' and referensi = 'P'");

    if ($rec > 0) {
        $sqla = "select distinct a.trans_form,a.is_bayar,a.id,to_char(a.tanggal_trans,'dd-mm-yyyy') as tanggal_trans,a.no_reg,b.id as item_id, upper(b.description) as description, a.qty, a.diskon, a.tagihan
from rs00008 a
left join rs99996 b on to_number(a.item_id,'9999999')=b.id
where a.referensi ='P' and no_reg= '".(string)$_GET['rg']."' order by a.id ";
        
        @$r1 = pg_query($con,
                $sqla);
        @$n1 = pg_num_rows($r1);

        $max_row1 = 200;
        $mulai1 = $HTTP_GET_VARS["rec"];
        if (!$mulai1) {
            $mulai1 = 1;
        }
        ?>
        <tr>
            <td bgcolor="#8ADFD3" align="center">&nbsp;</td>
            <td bgcolor="#8ADFD3" align="left"><b><u>RINCIAN LAYANAN PAKET</u></b></td>
            <td bgcolor="#8ADFD3" align="center">&nbsp;</td>
            <td bgcolor="#8ADFD3" align="center">&nbsp;</td>
            <td bgcolor="#8ADFD3" align="center">&nbsp;</td>
            <td bgcolor="#8ADFD3" align="right">&nbsp;</td>
            <td bgcolor="#8ADFD3" align="right">&nbsp;</td>
            <td bgcolor="#8ADFD3" align="right">&nbsp;</td>
            <td bgcolor="#8ADFD3" align="right">&nbsp;</td>
            <td bgcolor="#8ADFD3" align="right">&nbsp;</td>
            <td bgcolor="#8ADFD3" align="right">&nbsp;</td>
        </tr>
        <?
        // Line 1 Grup layanan paket
        $row1 = 0;
        $tagihan = 0;
        $i = 1;
        $j = 1;
        $last_id = 1;
        while (@$row1 = pg_fetch_array($r1)) {
            if (($j <= $max_row1) AND ($i >= $mulai1)) {
                $no = $i;
        ?>
                <tr>
                    <td class="TBL_BODY" align="center"><b><?= $row1["tanggal_trans"] ?></b></td>
                    <td class="TBL_BODY" align="left"><b>PAKET LAYANAN <?= $row1["description"] ?></b></td>
                    <td class="TBL_BODY" align="left"><b><?= $row1["qty"] ?></b></td>
                    <td class="TBL_BODY" align="left"><b>&nbsp;</b></td>
                    <td class="TBL_BODY" align="right"><b>&nbsp;</b></td>
                    <td class="TBL_BODY" align="right"><?=number_format($row1["diskon"],2,",",".") ?></td>
                    <td class="TBL_BODY" align="right"><b><?= number_format($row1["tagihan"], 2, ",", ".") ?></b></td>
                    <td class="TBL_BODY" align="right"><b>&nbsp;</b></td>
                    <td class="TBL_BODY" align="right"><b>&nbsp;</b></td>
                    <td class="TBL_BODY" align="center">
                        <?
                     //   if ($row1["is_bayar"] == "N" and $row1["trans_form"] == $_GET["p"]) {
                            echo "<a href='actions/$PID.delete.php?rg=$rg&mr=$mr&tbl=del_paket&del=$row1[id]&poli=" . $_GET["poli"] . "&ri=" . $_GET["ri"] . "&sub2=" . $_GET["sub2"] . "'>" . icon("del-left",
                                    "Hapus") . "</a>";
                    //    } else {
                    //        echo "&nbsp;";
                    //    }
                        ?>
                    </td>
                    <td class="TBL_BODY" align="center">&nbsp;</td>
                </tr>
                <?
                // line 2 Rincian oaket Layanan
                $sqlb = "select a.id as id_lay, f.id,z.preset_id, a.layanan, 
				z.qty ||' '|| g.tdesc as qty, f.tagihan,  f.tanggal_trans, f.trans_group 
				from rs00034 a 
				left join rs99997 z on z.item_id=a.id and z.trans_type='LYN'
				left join rs00008 f on to_number(f.item_id,'999999999999') = z.preset_id and f.trans_type = 'LTM' and f.referensi='P'
				left join rs00001 g on a.satuan_id = g.tc and g.tt = 'SAT' 
				where z.preset_id = $row1[item_id] AND f.no_reg = '".$_GET['rg']."'
				order by  a.id ";
		 @$n2 = pg_num_rows($r2);
                $max_row2 = 200;
                $mulai2 = $HTTP_GET_VARS["rec"];
                if (!$mulai2) {
                    $mulai2 = 1;
                }
                $row2 = 0;
                $i2 = 1;
                $j2 = 1;
                $last_id2 = 1;
                if($n2>0){
                ?>
                <tr>
                    <td class="TBL_BODY" align="center">&nbsp;</td>
                    <td class="TBL_BODY" align="left">&nbsp;&nbsp;&nbsp;&nbsp; RINCIAN LAYANAN <?= $row1["description"] ?></td>
                    <td class="TBL_BODY" align="center">&nbsp;</td>
                    <td class="TBL_BODY" align="right">&nbsp;</td>
                    <td class="TBL_BODY" align="right">&nbsp;</td>
                    <td class="TBL_BODY" align="right">&nbsp;</td>
                    <td class="TBL_BODY" align="right">&nbsp;</td>
                    <td class="TBL_BODY" align="right">&nbsp;</td>
                    <td class="TBL_BODY" align="right">&nbsp;</td>
                    <td class="TBL_BODY" align="right">&nbsp;</td>
                    <td class="TBL_BODY" align="right">&nbsp;</td>
                </tr>
                <?
                }
                while (@$row2 = pg_fetch_array($r2)) {
                    if (($j2 <= $max_row2) AND ($i2 >= $mulai2)) {
                        $no2 = $i2;
                        ?>

                        <tr>
                            <td class="TBL_BODY" align="center"><? ?></td>
                            <td class="TBL_BODY" align="left">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;- <?= $row2["layanan"] ?></td>
                            <td class="TBL_BODY" align="right">&nbsp;</td>
                            <td class="TBL_BODY" align="left"><?= $row2["qty"] ?></td>
                            <td class="TBL_BODY" align="right">&nbsp;</td>
                            <td class="TBL_BODY" align="right">&nbsp;</td>
                            <td class="TBL_BODY" align="right">&nbsp;</td>
                            <td class="TBL_BODY" align="right">&nbsp;</td>
                            <td class="TBL_BODY" align="right">&nbsp;</td>
                            <td class="TBL_BODY" align="right">&nbsp;</td>
                        </tr>
                        <?
                        ;
                        $j2++;
                    }
                    $i2++;
                }
                // Batas Untuk Line 2
                // line 2 Rincian paket obat
                $sqlc = "select z.item_id,z.preset_id,to_char(tanggal_trans,'DD-MM-YYYY') as tanggal_trans,  
				b.obat, z.qty ||' '|| c.tdesc as qty, sum(tagihan) as tagihan, a.pembayaran
				from rs00008 a
				left join rs99997 z on z.preset_id=to_number(a.item_id,'999999999999') and z.trans_type='OBI'
				left join rs00015 b on z.item_id = b.id  
				left join rs00001 c on b.satuan_id = c.tc and c.tt = 'SAT' 
				left join rs00001 d on b.kategori_id = d.tc and d.tt = 'GOB' 
				where to_number(a.no_reg,'999999999999')= '".(string)$_GET['rg']."' and a.referensi = 'P' and z.preset_id = $row1[item_id]
				group by  z.preset_id,z.item_id,d.tdesc, a.tanggal_trans, a.id, b.obat, z.qty, a.pembayaran, a.trans_group, c.tdesc, a.trans_form 
				ORDER BY a.tanggal_trans, b.obat ASC";

                @$r3 = pg_query($con,
                        $sqlc);
                @$n3 = pg_num_rows($r3);
		if($n3>0){
                ?>
                <tr>
                    <td class="TBL_BODY" align="center">&nbsp;</td>
                    <td class="TBL_BODY" align="left">&nbsp;&nbsp;&nbsp;&nbsp;  RINCIAN OBAT <?= $row1["description"] ?></td>
                    <td class="TBL_BODY" align="left">&nbsp;</td>
                    <td class="TBL_BODY" align="right">&nbsp;</td>
                    <td class="TBL_BODY" align="right">&nbsp;</td>
                    <td class="TBL_BODY" align="right">&nbsp;</td>
                    <td class="TBL_BODY" align="right">&nbsp;</td>
                    <td class="TBL_BODY" align="right">&nbsp;</td>
                    <td class="TBL_BODY" align="right">&nbsp;</td>
                    <td class="TBL_BODY" align="right">&nbsp;</td>
                    <td class="TBL_BODY" align="right">&nbsp;</td>
                </tr>
                <?
		}
                $max_row3 = 200;
                $mulai3 = $HTTP_GET_VARS["rec"];
                if (!$mulai3) {
                    $mulai3 = 1;
                }

                $row3 = 0;
                $i3 = 1;
                $j3 = 1;
                $last_id3 = 1;
                while (@$row3 = pg_fetch_array($r3)) {
                    if (($j3 <= $max_row3) AND ($i3 >= $mulai3)) {
                        $no3 = $i3;
                        ?>

                        <tr>
                            <td class="TBL_BODY" align="center"><? ?></td>
                            <td class="TBL_BODY" align="left">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;- <?= $row3["obat"] ?></td>
                            <td class="TBL_BODY" align="right">&nbsp;</td>
                            <td class="TBL_BODY" align="left"><?= $row3["qty"] ?></td>
                            <td class="TBL_BODY" align="right">&nbsp;</td>
                            <td class="TBL_BODY" align="right">&nbsp;</td>
                            <td class="TBL_BODY" align="right">&nbsp;</td>
                            <td class="TBL_BODY" align="right">&nbsp;</td>
                            <td class="TBL_BODY" align="right">&nbsp;</td>
                            <td class="TBL_BODY" align="right">&nbsp;</td>
                        </tr>
                        <?
                        $j3++;
                    }
                    $i3++;
                }
                // Batas Untuk Line 3

                $tagihan = $tagihan + $row1["tagihan"];
                $j++;
            }

            $i++;
        }

        // Batas Untuk Line 1
    }
    // Rincian Layanan Non Paket

    $rec1 = getFromTable("select count(id) from rs00008 " .
            "where trans_type = 'LTM' and to_number(no_reg,'999999999999') = '".(string)$_GET['rg']."' and referensi != 'P'");

    if ($rec1 > 0) {
		//febri 21112012	
        $sqle = "select f.trans_form,f.id,f.item_id, a.layanan, f.diskon,f.referensi, 
				f.qty ||' '|| g.tdesc as qty, f.tagihan,  to_char(f.tanggal_trans,'dd-mm-yyyy') as tanggal_trans, f.trans_group,f.is_bayar,h.nama 
				from rs00034 a 
				left join rs00008 f on to_number(f.item_id,'999999999999') = a.id and f.trans_type = 'LTM' and f.referensi != 'P'
				left join rs00001 g on a.satuan_id = g.tc and g.tt = 'SAT' 
				left join rs00017 h on f.no_kwitansi::numeric = h.id::numeric 
				where f.no_reg = '".(string)$_GET['rg']."' 
				order by  f.tanggal_trans, a.layanan ASC ";
        
        @$r4 = pg_query($con,
                $sqle);
        @$n4 = pg_num_rows($r4);
        $max_row4 = 200;
        $mulai4 = $HTTP_GET_VARS["rec"];
        if (!$mulai4) {
            $mulai4 = 1;
        }
        ?>
        <tr>
            <td bgcolor="#8ADFD3" align="center">&nbsp;</td>
            <td bgcolor="#8ADFD3" align="left"><b><u>RINCIAN LAYANAN NON PAKET</u></b></td>
            <td bgcolor="#8ADFD3" align="center">&nbsp;</td>
            <td bgcolor="#8ADFD3" align="center">&nbsp;</td>
            <td bgcolor="#8ADFD3" align="right">&nbsp;</td>
            <td bgcolor="#8ADFD3" align="right">&nbsp;</td>
            <td bgcolor="#8ADFD3" align="right">&nbsp;</td>
            <td bgcolor="#8ADFD3" align="right">&nbsp;</td>
            <td bgcolor="#8ADFD3" align="right">&nbsp;</td>
            <td bgcolor="#8ADFD3" align="right">&nbsp;</td>
            <td bgcolor="#8ADFD3" align="right">&nbsp;</td>
        </tr>
            <?
            $row4 = 0;
            $tagihan2 = 0;
            $i4 = 1;
            $j4 = 1;
            $last_id4 = 1;
            while (@$row4 = pg_fetch_array($r4)) {
                if (($j4 <= $max_row4) AND ($i4 >= $mulai4)) {
                    $no4 = $i4;
                    ?>
                <tr>
                    <td class="TBL_BODY" align="center"><b><?= $row4["tanggal_trans"] ?></b></td>
                    <td class="TBL_BODY" align="left"><?= $row4["layanan"] ?></td> 
                    <td class="TBL_BODY" align="left"><?= $row4["nama"] ?></td>
                    <td class="TBL_BODY" align="left"><?= $row4["qty"] ?></td>
                    <td class="TBL_BODY" align="right"><?= number_format($row4["referensi"],2) ?></td>
                    <td class="TBL_BODY" align="right"><?=number_format($row4["diskon"],2,",",".") ?></td>
                    <td class="TBL_BODY" align="right"><b><?= number_format($row4["tagihan"], 2, ",",".") ?></b></td>
                    <td class="TBL_BODY" align="right"><b>&nbsp;</b></td>
                    <td class="TBL_BODY" align="right"><b>&nbsp;</b></td>
                <?
                ?>
                    <td class="TBL_BODY" align="center">
                <?
//                if ($row4["is_bayar"] == "N" and $row4["trans_form"] == $_GET["p"]) {
                    echo "<a href='actions/$PID.delete.php?rg=" . $_GET["rg"] . "&mr=" . $_GET["mr"] . "&sub=" . $_GET["sub"] . "&tbl=tindakan&del=$row4[id]&ri=" . $_GET["ri"] . "&sub2=" . $_GET["sub2"] . "'>" . icon("del-left",
                            "Hapus") . "</a>";
//                } else {
//                    echo "&nbsp;";
//                }
                ?>
                    </td>
                    <td class="TBL_BODY" align="center">&nbsp;</td>
                </tr>
                <?
                $tagihan2 = $tagihan2 + $row4["tagihan"];
                $j4++;
            }
            $i4++;
        }
    }
    //Batas Layanan Non Paket
    // Penambahan BHP
    $rec7 = getFromTable("select count(id) from rs00008 " .
            "where trans_type = 'BHP' and to_number(no_reg,'999999999999') = '".(string)$_GET['rg']."' and referensi != 'F'");

    if ($rec7 > 0) {
        $sql7 = "select a.is_bayar, a.id, to_char(tanggal_trans,'DD-MM-YYYY') as tanggal_trans,  
		obat, qty ||' '|| c.tdesc as qty, sum(tagihan) as tagihan, sum(dibayar_penjamin) as dibayar_penjamin, pembayaran, trans_group, d.tdesc as kategori, a.trans_form 
		from rs00008 a, rs00015 b, rs00001 c, rs00001 d 
		where to_number(a.item_id,'999999999999') = b.id  
		and b.satuan_id = c.tc and a.trans_type = 'BHP' 
		and c.tt = 'SAT' 
		and b.kategori_id = d.tc and d.tt = 'GOB' 
		and to_number(a.no_reg,'999999999999')= '".(string)$_GET['rg']."'  and referensi != 'F'
		group by  a.is_bayar, d.tdesc, a.tanggal_trans, a.id, b.obat, a.qty, a.pembayaran, a.trans_group, c.tdesc, a.trans_form
		ORDER BY a.tanggal_trans, b.obat ASC";
        @$r7 = pg_query($con,
                $sql7);
        @$n7 = pg_num_rows($r7);

        $max_row7 = 200;
        $mulai7 = $HTTP_GET_VARS["rec"];
        if (!$mulai7) {
            $mulai7 = 1;
        }
        ?>
        <tr>
            <td bgcolor="#8ADFD3" align="center">&nbsp;</td>
            <td bgcolor="#8ADFD3" align="left"><b><u>RINCIAN BHP</u></b></td>
            <td bgcolor="#8ADFD3" align="center">&nbsp;</td>
            <td bgcolor="#8ADFD3" align="center">&nbsp;</td>
            <td bgcolor="#8ADFD3" align="right">&nbsp;</td>
            <td bgcolor="#8ADFD3" align="right">&nbsp;</td>
            <td bgcolor="#8ADFD3" align="right">&nbsp;</td>
            <td bgcolor="#8ADFD3" align="right">&nbsp;</td>
            <td bgcolor="#8ADFD3" align="right">&nbsp;</td>
            <td bgcolor="#8ADFD3" align="right">&nbsp;</td>
	    <td bgcolor="#8ADFD3" align="right">&nbsp;</td>
        </tr>
        <form id="form_bhp" action="includes/cetak.rincian_bhp.php" target="_blank">
            <input type="hidden" name="rg" value="<?php echo $_GET['rg']?>">
            <input type="hidden" name="print_selected" value="true">
                <?
                $row7 = 0;
                $tagihan7 = 0;
                $i7 = 1;
                $j7 = 1;
                $last_id7 = 1;
                while (@$row7 = pg_fetch_array($r7)) {
                    if (($j7 <= $max_row7) AND ($i7 >= $mulai7)) {
                        $no7 = $i7;
                ?>
                <tr>
                    <td class="TBL_BODY" align="center"><b><?= $row7["tanggal_trans"] ?></b></td>
                    <td class="TBL_BODY" align="left"><?= $row7["obat"] ?></td>
                    <td class="TBL_BODY" align="center">&nbsp;</td>
                    <td class="TBL_BODY" align="left"><?= $row7["qty"] ?></td>
		    <td class="TBL_BODY" align="center">&nbsp;</td>
                    <td class="TBL_BODY" align="right"><?=number_format($row7["diskon"],2,",",".") ?></td>
                    <td class="TBL_BODY" align="right"><b><?= number_format($row7["tagihan"], 2, ",", ".") ?></b></td>
                    <td class="TBL_BODY" align="right"><b><?= number_format($row7["dibayar_penjamin"], 2, ",", ".") ?></b></td>
                    <td class="TBL_BODY" align="right"><b><?= number_format(($row7["tagihan"]-$row7["dibayar_penjamin"]), 2, ",", ".") ?></b></td>
                    <td class="TBL_BODY" align="center">
                <?
                if ($row7["is_bayar"] == "N" and $row7["trans_form"] == $_GET["p"]) {
                    echo "<a href='actions/$PID.delete.php?rg=" . (string)$_GET["rg"] . "&mr=" . $_GET["mr"] . "&sub=layanan&list=layanan&tbl=bhp&del=$row7[id]&ri=" . $_GET["ri"] . "&sub2=bhp'>" . icon("del-left",
                            "Hapus") . "</a>";
                } else {
                    echo "&nbsp;";
                }
                ?>
                    </td>
                    <td class="TBL_BODY" align="center">&nbsp;<input type="checkbox" name="bhp_<?php echo $row7['id']?>" valu="<?php echo $row7['id']?>"></td>
                </tr>
                <?
                $tagihan7 = $tagihan7 + $row7["tagihan"];
                $j7++;
            }
            $i7++;
        }
        echo '</form>';
    }


    ///Batas Pembelian Obat
    // Pembelian Obat
    $rec3 = getFromTable("select count(id) from rs00008 " .
            "where trans_type = 'OB1' and to_number(no_reg,'999999999999') = '".(string)$_GET['rg']."' and referensi != 'F'");


    if ($rec3 > 0) {
        $sqlf = "select a.id, to_char(tanggal_trans,'DD-MM-YYYY') as tanggal_trans,  
		obat, qty ||' '|| c.tdesc as qty, a.diskon, sum(tagihan) as tagihan, pembayaran, trans_group, d.tdesc as kategori, a.trans_form 
		from rs00008 a, rs00015 b, rs00001 c, rs00001 d 
		where to_number(a.item_id,'999999999999') = b.id  
		and b.satuan_id = c.tc and a.trans_type = 'OB1' 
		and c.tt = 'SAT' 
		and b.kategori_id = d.tc and d.tt = 'GOB' 
		and to_number(a.no_reg,'999999999999')= '".(string)$_GET['rg']."'  and referensi != 'F'
		group by  d.tdesc, a.tanggal_trans, a.id, b.obat, a.qty, a.pembayaran, a.trans_group, c.tdesc, a.trans_form ";
        @$r5 = pg_query($con,
                $sqlf);
        @$n5 = pg_num_rows($r5);

        $max_row5 = 200;
        $mulai5 = $HTTP_GET_VARS["rec"];
        if (!$mulai5) {
            $mulai5 = 1;
        }
        ?>
        <tr>
            <td bgcolor="#8ADFD3" align="center">&nbsp;</td>
            <td bgcolor="#8ADFD3" align="left"><b><u>RINCIAN OBAT APOTEK</u></b></td>
            <td bgcolor="#8ADFD3" align="center">&nbsp;</td>
            <td bgcolor="#8ADFD3" align="center">&nbsp;</td>
            <td bgcolor="#8ADFD3" align="right">&nbsp;</td>
            <td bgcolor="#8ADFD3" align="right">&nbsp;</td>
            <td bgcolor="#8ADFD3" align="right">&nbsp;</td>
            <td bgcolor="#8ADFD3" align="right">&nbsp;</td>
            <td bgcolor="#8ADFD3" align="right">&nbsp;</td>
            <td bgcolor="#8ADFD3" align="right">&nbsp;</td>
	    <td bgcolor="#8ADFD3" align="right">&nbsp;</td>
        </tr>
        <?
        $row5 = 0;
        $tagihan5 = 0;
        $i5 = 1;
        $j5 = 1;
        $last_id5 = 1;
        while (@$row5 = pg_fetch_array($r5)) {
            if (($j5 <= $max_row5) AND ($i5 >= $mulai5)) {
                $no5 = $i5;
                ?>
                <tr>
                    <td class="TBL_BODY" align="center"><b><?= $row5["tanggal_trans"] ?></b></td>
                    <td class="TBL_BODY" align="left"><?= $row5["obat"] ?></td>
                    <td class="TBL_BODY" align="center">&nbsp;</td>
                    <td class="TBL_BODY" align="left"><?= $row5["qty"] ?></td>
<td class="TBL_BODY" align="center">&nbsp;</td>
                    <td class="TBL_BODY" align="right"><?=number_format($row5["diskon"],2,",",".") ?></td>
                    <td class="TBL_BODY" align="right"><b><?= number_format($row5["tagihan"], 2, ",", ".") ?></b></td>
                    <td class="TBL_BODY" align="right"><b>&nbsp;</b></td>
                    <td class="TBL_BODY" align="right"><b>&nbsp;</b></td>
                    <td class="TBL_BODY" align="center">&nbsp;</td>
                    <td class="TBL_BODY" align="center">&nbsp;</td>
                </tr>
                <?
                $tagihan5 = $tagihan5 + $row5["tagihan"];
                $j5++;
            }
            $i5++;
        }
    }

    ///Batas Pembelian Obat
// Pembelian Obat Racikan
    $rec4 = getFromTable("select count(id) from rs00008 " .
            "where trans_type = 'RCK' and to_number(no_reg,'999999999999') = '".(string)$_GET['rg']."' and referensi != 'F'");

    if ($rec4 > 0) {
        $sqlf = "select a.id, to_char(tanggal_trans,'DD-MM-YYYY') as tanggal_trans,  
		obat, qty ||' '|| c.tdesc as qty, a.diskon, sum(tagihan) as tagihan, pembayaran, trans_group, d.tdesc as kategori, a.trans_form 
		from rs00008 a, rs00015 b, rs00001 c, rs00001 d 
		where to_number(a.item_id,'999999999999') = b.id  
		and b.satuan_id = c.tc and a.trans_type = 'RCK' 
		and c.tt = 'SAT' 
		and b.kategori_id = d.tc and d.tt = 'GOB' 
		and to_number(a.no_reg,'999999999999')= '".(string)$_GET['rg']."'  and referensi != 'F'
		group by  d.tdesc, a.tanggal_trans, a.id, b.obat, a.qty, a.pembayaran, a.trans_group, c.tdesc, a.trans_form 
		ORDER BY a.tanggal_trans, b.obat ASC";
        @$r6 = pg_query($con,
                $sqlf);
        @$n6 = pg_num_rows($r6);

        $max_row6 = 200;
        $mulai6 = $HTTP_GET_VARS["rec"];
        if (!$mulai6) {
            $mulai6 = 1;
        }
        ?>
        <tr>
            <td bgcolor="#8ADFD3" align="center">&nbsp;</td>
            <td bgcolor="#8ADFD3" align="left"><b><u>RINCIAN RACIKAN OBAT APOTEK</u></b></td>
            <td bgcolor="#8ADFD3" align="center">&nbsp;</td>
            <td bgcolor="#8ADFD3" align="center">&nbsp;</td>
            <td bgcolor="#8ADFD3" align="right">&nbsp;</td>
            <td bgcolor="#8ADFD3" align="right">&nbsp;</td>
            <td bgcolor="#8ADFD3" align="right">&nbsp;</td>
            <td bgcolor="#8ADFD3" align="right">&nbsp;</td>
            <td bgcolor="#8ADFD3" align="right">&nbsp;</td>
            <td bgcolor="#8ADFD3" align="right">&nbsp;</td>
        </tr>
        <?
        $row6 = 0;
        $tagihan6 = 0;
        $i6 = 1;
        $j6 = 1;
        $last_id6 = 1;
        while (@$row6 = pg_fetch_array($r6)) {
            if (($j6 <= $max_row6) AND ($i6 >= $mulai6)) {
                $no6 = $i6;
                ?>
                <tr>
                    <td class="TBL_BODY" align="center"><b><?= $row6["tanggal_trans"] ?></b></td>
                    <td class="TBL_BODY" align="left"><?= $row6["obat"] ?></td>
                    <td class="TBL_BODY" align="center">&nbsp;</td>
                    <td class="TBL_BODY" align="left"><?= $row6["qty"] ?></td>
                    <td class="TBL_BODY" align="right"><?=number_format($row6["diskon"],2,",",".") ?></td>
                    <td class="TBL_BODY" align="right"><b><?= number_format($row6["tagihan"], 2, ",", ".") ?></b></td>
                    <td class="TBL_BODY" align="right"><b>&nbsp;</b></td>
                    <td class="TBL_BODY" align="right"><b>&nbsp;</b></td>
                    <td class="TBL_BODY" align="center">&nbsp;</td>
                    <td class="TBL_BODY" align="center">&nbsp;</td>
                </tr>
            <?
            $tagihan6 = $tagihan6 + $row6["tagihan"];
            $j6++;
        }
        $i6++;
    }
}

///Batas Pembelian Obat Racikan
$rec4 = getFromTable("select count(id) from rs00008_return " .
            "where no_reg = '".$_GET['rg']."' and referensi != 'F'");

    if ($rec4 > 0) {
        $sqlf = "select a.id, to_char(tanggal_trans,'DD-MM-YYYY') as tanggal_trans,  
		obat, qty_return ||' '|| c.tdesc as qty, sum(tagihan) as tagihan, pembayaran, trans_group, d.tdesc as kategori, a.trans_form 
		from rs00008_return a, rs00015 b, rs00001 c, rs00001 d 
		where to_number(a.item_id,'999999999999') = b.id  
		and b.satuan_id = c.tc
		and c.tt = 'SAT' 
		and b.kategori_id = d.tc and d.tt = 'GOB' 
		and to_number(a.no_reg,'999999999999')= '".$_GET['rg']."' and referensi != 'F'
		group by  d.tdesc, a.tanggal_trans, a.id, qty_return, b.obat, a.pembayaran, a.trans_group, c.tdesc, a.trans_form";
        @$r6 = pg_query($con, $sqlf);
        @$n6 = pg_num_rows($r6);
        $max_row6 = 200;
        $mulai6 = $HTTP_GET_VARS["rec"];
        if (!$mulai6) {
            $mulai6 = 1;
        }
        ?>
        <tr>
            <td bgcolor="#8ADFD3" align="center">&nbsp;</td>
            <td bgcolor="#8ADFD3" align="left"><b><u>RINCIAN RETUR OBAT APOTEK</u></b></td>
            <td bgcolor="#8ADFD3" align="center">&nbsp;</td>
            <td bgcolor="#8ADFD3" align="center">&nbsp;</td>
            <td bgcolor="#8ADFD3" align="right">&nbsp;</td>
            <td bgcolor="#8ADFD3" align="right">&nbsp;</td>
            <td bgcolor="#8ADFD3" align="right">&nbsp;</td>
            <td bgcolor="#8ADFD3" align="right">&nbsp;</td>
            <td bgcolor="#8ADFD3" align="right">&nbsp;</td>
            <td bgcolor="#8ADFD3" align="right">&nbsp;</td>
            <td bgcolor="#8ADFD3" align="right">&nbsp;</td>
        </tr>
        <?
        $row6 = 0;
        $tagihan_retur = 0;
        $i6 = 1;
        $j6 = 1;
        $last_id6 = 1;
        while (@$row6 = pg_fetch_array($r6)) {
            if (($j6 <= $max_row6) AND ($i6 >= $mulai6)) {
                $no6 = $i6;
                $row6["tagihan"]*=-1;
                ?>
                <tr>
                    <td class="TBL_BODY" align="center"><b><?= $row6["tanggal_trans"] ?></b></td>
                    <td class="TBL_BODY" align="left"><?= $row6["obat"] ?></td>
                    <td class="TBL_BODY" align="center">&nbsp;</td>
                    <td class="TBL_BODY" align="left"><?= $row6["qty"] ?></td>
                    <td class="TBL_BODY" align="center">&nbsp;</td>
                    <td class="TBL_BODY" align="right"><?=number_format($row6["diskon"],2,",",".") ?></td>
                    <td class="TBL_BODY" align="right"><b><?= number_format($row6["tagihan"], 2, ",", ".") ?></b></td>
                    <td class="TBL_BODY" align="right"><b>&nbsp;</b></td>
                    <td class="TBL_BODY" align="right"><b>&nbsp;</b></td>
                    <td class="TBL_BODY" align="center">&nbsp;</td>
                    <td class="TBL_BODY" align="center">&nbsp;</td>
                </tr>
            <?
            $tagihan_retur = $tagihan_retur + $row6["tagihan"];
            $j6++;
        }
        $i6++;
    }
}

$r2 = pg_query($con,
        "select * from rsv0012 where id = '$reg'");
$d2 = pg_fetch_object($r2);
?>
    <tr>
        <td class="TBL_BODY" align="right" colspan="5"><b>T O T A L &nbsp;&nbsp;&nbsp;&nbsp; T A G I H A N</b></td>
        <td class="TBL_BODY" align="right">&nbsp;</td>
        <td class="TBL_BODY" align="right"><b><?= number_format($tagihan + $tagihan_retur + $tagihan2 + $tagihan5 + $tagihan6 +$tagihan7, 2, ",", ".") ?></b></td>
        <td class="TBL_BODY" align="right">&nbsp;</td>
        <td class="TBL_BODY" align="right">&nbsp;</td>
        <td class="TBL_BODY" align="right">&nbsp;</td>
        <td class="TBL_BODY" align="right">&nbsp;</td>
    </tr>
    <tr>
        <td class="TBL_BODY" align="right" colspan="5"><b>T O T A L &nbsp;&nbsp;&nbsp;&nbsp; P E M B A Y A R A N</b></td>
        <td class="TBL_BODY" align="right">&nbsp;</td>
        <td class="TBL_BODY" align="right"><b><?= number_format($d2->bayar, 2, ",", ".") ?></b></td>
        <td class="TBL_BODY" align="right">&nbsp;</td>
        <td class="TBL_BODY" align="right">&nbsp;</td>
        <td class="TBL_BODY" align="right">&nbsp;</td>
        <td class="TBL_BODY" align="right">&nbsp;</td>
    </tr>
    <tr>
        <td class="TBL_BODY" align="right" colspan="5"><b>S I S A &nbsp;&nbsp;&nbsp;&nbsp; P E M B A Y A R A N</b></td>
        <td class="TBL_BODY" align="right">&nbsp;</td>
        <td class="TBL_BODY" align="right"><b><?= number_format($d2->sisa + $tagihan_retur , 2, ",", ".") ?></b></td>
        <td class="TBL_BODY" align="right">&nbsp;</td>
        <td class="TBL_BODY" align="right">&nbsp;</td>
        <td class="TBL_BODY" align="right">&nbsp;</td>
        <td class="TBL_BODY" align="right">&nbsp;</td>
    </tr>
</table>
<script>
function submitBHP()
{
document.getElementById("form_bhp").submit();
}
</script>
