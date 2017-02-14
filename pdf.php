<?php
	// Connexion à la base de données
	require_once "defaultincludes.inc";
	mysqli_query($co, "set names utf8");

		
	
	// Appelle de la librairie FPDF
	require("fpdf/fpdf.php");
	
	
	// Création de la classe PDF
	class PDF extends FPDF {
		// Header
		function Header() {
			$this->Image('fpdf/images/iut_orsay.gif', 8, 2, 50);
			$this->Ln(20);
		}
		
		// Footer
		function Footer() {
			// Positionnement à 1.5 cm du bas
			$this->SetY(-15);
			
			// Pied
			$this->Cell(196,5,'IUT Orsay RPZ - Aldarick feat M. PHP', 0, 0, 'C');
		}
	}

	// Activation de la classe
	$pdf = new PDF('P', 'mm', 'A4');
	$pdf->AddPage();
	$pdf->SetFont('Arial','','11');
	$pdf->SetTextColor(0);
	
	$largeur = $pdf->w;
	// // // //
	// Corps
	$fond=0;
	$infos = "";
	
	$largeurCol1 = 60;
	$hauteurLigne = 8;
	$alignementTableau = ($largeur - $largeurCol1 * 3) / 2;
	
	// TABLEAU INDENTITE
	if ($_GET['idEleve'] != null){
		$requete = "select Nom, Prenom, c.classe from eleve e, classe c where IDEleve = ".$_GET['idEleve']." and e.Classe = c.code";
		$res = mysqli_query($co, $requete) or die ("Erreur requète ligne ".__LINE__." : $requete");
	}
	while($row=mysqli_fetch_array($res)){
		$pdf->SetXY($alignementTableau,$pdf->GetY()+$hauteurLigne);
		$pdf->cell($largeurCol1,$hauteurLigne,"Prenom : ".$row['Prenom'],1,0,'C',$fond);
		$pdf->cell($largeurCol1,$hauteurLigne,"Nom : ".$row['Nom'],1,0,'C',$fond);
		$pdf->cell($largeurCol1,$hauteurLigne,"En classe de : ".$row['classe'],1,0,'C',$fond);
		$pdf->SetXY($alignementTableau,$pdf->GetY()+$hauteurLigne * 2);
	}
	
	
	
	// TABLEAU NOTES
	
	// Récupération des informations sur les notes
	if ($_GET['idEleve'] != null){
		$requete = "select * from eleve where IDEleve = ".$_GET['idEleve'];
		$res = mysqli_query($co, $requete) or die ("Erreur requète ligne ".__LINE__." : $requete");
	}
	

	// Variables tableau
	$largeurCol1 = 80;
	$largeurCol2 = 40;
	$hauteurLigne = 8;
	$alignementTableau = ($largeur - $largeurCol1 - $largeurCol2) / 2;
	$taillePoliceTitres = '17'; 
	$taillePoliceLignes = '13'; 
	// Titres
	$pdf->SetXY($alignementTableau,$pdf->GetY());
	
	$pdf->SetFont('Arial','',$taillePoliceTitres);
	$pdf->cell($largeurCol1,10,'Matiere',1,0,'C',0);
	$pdf->cell($largeurCol2,10,'Note',1,0,'C',0);
	
	$pdf->SetXY($alignementTableau,$pdf->GetY()+10);
	
	// Tableau
	$pdf->SetFont('Arial','',$taillePoliceLignes = 13 );
		//Notes
	while($row=mysqli_fetch_array($res)){
		$pdf->cell($largeurCol1,$hauteurLigne,'Maths',1,0,'C',$fond);
		$pdf->cell($largeurCol2,$hauteurLigne,$row['Maths'],1,0,'C',$fond);
		$pdf->SetXY($alignementTableau,$pdf->GetY()+$hauteurLigne);
		
		$pdf->cell($largeurCol1,$hauteurLigne,'Physique',1,0,'C',$fond);
		$pdf->cell($largeurCol2,$hauteurLigne,$row['Physique'],1,0,'C',$fond);
		$pdf->SetXY($alignementTableau,$pdf->GetY()+$hauteurLigne);
		
		$pdf->cell($largeurCol1,$hauteurLigne,'Francais',1,0,'C',$fond);
		$pdf->cell($largeurCol2,$hauteurLigne,$row['Francais'],1,0,'C',$fond);
		$pdf->SetXY($alignementTableau,$pdf->GetY()+$hauteurLigne);
		
		$pdf->cell($largeurCol1,$hauteurLigne,'SVT',1,0,'C',$fond);
		$pdf->cell($largeurCol2,$hauteurLigne,$row['SVT'],1,0,'C',$fond);
		$pdf->SetXY($alignementTableau,$pdf->GetY()+$hauteurLigne);
		
		$pdf->cell($largeurCol1,$hauteurLigne,'Anglais',1,0,'C',$fond);
		$pdf->cell($largeurCol2,$hauteurLigne,$row['Anglais'],1,0,'C',$fond);
		$pdf->SetXY($alignementTableau,$pdf->GetY()+$hauteurLigne);
		
		$pdf->cell($largeurCol1,$hauteurLigne,'Sport',1,0,'C',$fond);
		$pdf->cell($largeurCol2,$hauteurLigne,$row['Sport'],1,0,'C',$fond);
		$pdf->SetXY($alignementTableau,$pdf->GetY()+$hauteurLigne);
		
		$pdf->cell($largeurCol1 + $largeurCol2,$hauteurLigne / 3,'',1,0,'C',$fond);
		$pdf->SetXY($alignementTableau,$pdf->GetY()+$hauteurLigne / 3);
		$moy = ($row['Maths']*5+$row['Physique']*4+$row['Francais']*2+$row['SVT']*3+$row['Anglais']*2+$row['Sport']*1)/17;
		$pdf->cell($largeurCol1,$hauteurLigne,'Moyenne',1,0,'C',$fond);
		$pdf->cell($largeurCol2,$hauteurLigne,round($moy, 2),1,0,'C',$fond);
		$pdf->SetXY($alignementTableau,$pdf->GetY()+$hauteurLigne);
		
		$fond=!$fond;
		
		
	}
		
	
	// Affichage
	$pdf->Output();
	
	
	
	
?>