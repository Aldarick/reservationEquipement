<?php
require('fpdf/fpdf.php');
require_once "defaultincludes.inc";
require_once "mrbs_sql.inc";




class PDF extends FPDF
{
// En-tête
function Header()
{
	// Logo
	$this->Image('logo.png',10,6,45);
	// Police Arial gras 15
	$this->SetFont('Arial','B',15);
	// Décalage à droite
	$this->Cell(60);
	// Titre
	$this->Cell(70,10,utf8_decode('Information équipement'),1,0,'C');
	// Saut de ligne
	$this->Ln(20);
}

// Pied de page
function Footer()
{
	// Set position to 1.7cm from bottom
	$this->SetY(-17);
	// Font Arial italique 8
	$this->SetFont('Arial','I',8);
    $this->Cell(0,2,'', T,1,'C');
    
	$this->Cell(0,5,utf8_decode('III-V Lab - Document généré automatiquement'),0,1,'C');
    $this->Cell(0,5,utf8_decode('En cas de problème avec la réservation d\'équipements, contacter l\'administrateur à : hugues.rieublandou@3-5lab.fr'),0,1,'C');
}
}

$equipment = get_form_var('equipment', 'string');

//error_log("Equip : " . $equipment, 3, "C:/xampp/htdocs/reservation/log2.log");

$sql = "SELECT R.room_name, A.area_name AS area_name, U1.prenom AS admin_firstname, U1.nom AS admin_lastname, U2.prenom AS coadmin_firstname, U2.nom AS coadmin_lastname" 
    . " FROM $tbl_room R, $tbl_users U1, $tbl_users U2, $tbl_area A" 
    . " WHERE R.id = $equipment"
    . " AND R.admin_id = U1.id"
    . " AND R.coadmin_id = U2.id"
    . " AND A.id = R.area_id";
//error_log("Requete : " . $sql, 3, "C:/xampp/htdocs/reservation/log2.log");


$res = sql_query($sql);
  if (! $res)
  {
    trigger_error(sql_error(), E_USER_WARNING);
    fatal_error(FALSE, get_vocab("fatal_db_error"));

  }
  if (sql_count($res) == 1)
  {
    $row = sql_row_keyed($res, 0);
    $equipment_name = $row['room_name'];
    $admin = $row['admin_firstname'] . ' ' . $row['admin_lastname'];
    $coadmin = $row['coadmin_firstname'] . ' ' . $row['coadmin_lastname'];
    $area_name = $row['area_name'];
  }
  sql_free($res);


list($admin_mail, $coadmin_mail) = roomadmin_adress($equipment);


// Instanciation de la classe dérivée
$pdf = new PDF();
$pdf->AddPage();

$pdf->Cell(50,10,'', 0,1,'L');

$pdf->SetFont('Arial','',25 );
$pdf->Cell(0,10,$equipment_name, B,1,'C');
$pdf->SetFont('Arial','',15 );
$pdf->Cell(0,10,$area_name, 0,1,'C');
$pdf->Cell(50,10,'', 0,1,'L');

// ADMIN
$pdf->SetFont('Arial','b',20 );
$pdf->Write(5, 'Responsable: ' . "\n\n");
$pdf->SetFont('Arial','',15 );
$pdf->Write(5, '     Nom : ' . $admin . "\n\n");
$pdf->Write(5, '      Mail : ' . $admin_mail . "\n\n");

//space between lines
$pdf->SetFont('Arial','',5 );
$pdf->Write(5, "\n");

// COADMIN
$pdf->SetFont('Arial','b',20 );
$pdf->Write(5, 'Co-Responsable: ' . "\n\n");
$pdf->SetFont('Arial','',15 );
$pdf->Write(5, '     Nom : ' . $coadmin . "\n\n");
$pdf->Write(5, '      Mail : ' . $coadmin_mail . "\n\n");






	//$pdf->Cell(0,10,'Impression de la ligne numéro '.$i,0,1);
$pdf->Output();
?>