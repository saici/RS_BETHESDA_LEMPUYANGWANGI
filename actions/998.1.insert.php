<?php // Nugraha, Wed Jun  2 16:19:34 WIT 2004

session_start();

$PID = "998";

require_once("../lib/dbconn.php");

if (strlen($_POST["description"]) > 0 ) {
 /*   $SQL = "insert into rs99996 " .
           "(id, trans_type, description) ".
           "values".
           "(nextval('rs99996_seq'),'LYN','".$_POST["description"]."')";
   */
    $SQL = "insert into rs99996 " .
           "(id, trans_type, description,harga_paket) ".
           "values".
           "(nextval('rs99996_seq'),'LYN','".$_POST["description"]."','".$_POST["harga"]."')";
                      
    pg_query($con, $SQL);
    $curr = getFromTable("select currval('rs99996_seq')");
}

if (strlen($_POST["layanan"]) > 0 && strlen($_POST["jumlah"]) > 0) {
    $SQL = "insert into rs99997 " .
           "(id, preset_id, item_id, qty,trans_type) ".
           "values".
           "(nextval('rs99997_seq'),'".$_POST["id"]."','".$_POST["layanan"]."','".$_POST["jumlah"]."','LYN')";
    pg_query($con, $SQL);
    $curr = $_POST["id"];
    unset($_SESSION["SELECT_LAYANAN"]);
}

if (strlen($_POST["obat"]) > 0 && strlen($_POST["jumlah"]) > 0) {
    $SQL = "insert into rs99997 " .
           "(id, preset_id, item_id, qty,trans_type) ".
           "values".
           "(nextval('rs99997_seq'),'".$_POST["id"]."','".$_POST["obat"]."','".$_POST["jumlah"]."','OBI')";
    pg_query($con, $SQL);
    $curr = $_POST["id"];
    unset($_SESSION["SELECT_OBAT"]);
}

header("Location: ../index2.php?p=$PID&id=$curr");
exit;

print_r($_POST);

?>
