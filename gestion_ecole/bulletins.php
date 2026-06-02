<?php
require_once 'config/database.php';
require_once 'classes/Auth.php';
require_once 'tcpdf/tcpdf.php'; // <- C'est ça qui charge TCPDF

Auth::checkAuth();
$database = new Database();
$db = $database->getConnection();

// SI ON CLIQUE SUR "PDF S1" OU "PDF S2" -> ÇA GÉNÈRE LE PDF
if(isset($_GET['generate']) && isset($_GET['id_etudiant'])){
    $id_etudiant = $_GET['id_etudiant'];
    $semestre = $_GET['semestre'] ?? 1;
    $annee = '2024-2025';

    // 1. INFOS ÉTUDIANT
    $q_et = $db->prepare("SELECT e.*, c.nom_classe FROM etudiants e 
                          JOIN classes c ON e.id_classe = c.id 
                          WHERE e.id = ?");
    $q_et->execute([$id_etudiant]);
    $etudiant = $q_et->fetch(PDO::FETCH_ASSOC);

    // 2. RÉCUPÈRE LES MATIÈRES OÙ IL A DES NOTES CE SEMESTRE
    $q_matieres = $db->prepare("SELECT DISTINCT m.id, m.nom_matiere, m.coefficient 
                                FROM matieres m 
                                JOIN notes n ON m.id = n.id_matiere 
                                WHERE n.id_etudiant = ? AND n.semestre = ? AND n.annee_scolaire = ?
                                ORDER BY m.nom_matiere");
    $q_matieres->execute([$id_etudiant, $semestre, $annee]);
    $matieres = $q_matieres->fetchAll(PDO::FETCH_ASSOC);

    $notes_finales = [];
    $total_points = 0;
    $total_coeff = 0;

    // 3. CALCULE LA MOYENNE PAR MATIÈRE
    foreach($matieres as $mat){
        $q_notes = $db->prepare("SELECT note, coefficient FROM notes 
                                 WHERE id_etudiant = ? AND id_matiere = ? AND semestre = ? AND annee_scolaire = ?");
        $q_notes->execute([$id_etudiant, $mat['id'], $semestre, $annee]);
        $notes_mat = $q_notes->fetchAll(PDO::FETCH_ASSOC);
        
        $somme = 0; $somme_coeff = 0;
        foreach($notes_mat as $n){
            $somme += $n['note'] * $n['coefficient'];
            $somme_coeff += $n['coefficient'];
        }
        $moyenne_mat = $somme_coeff > 0 ? round($somme / $somme_coeff, 2) : null;
        
        if($moyenne_mat !== null){
            $notes_finales[] = [
                'matiere' => $mat['nom_matiere'],
                'coeff' => $mat['coefficient'],
                'moyenne' => $moyenne_mat,
                'points' => $moyenne_mat * $mat['coefficient']
            ];
            $total_points += $moyenne_mat * $mat['coefficient'];
            $total_coeff += $mat['coefficient'];
        }
    }

    $moyenne_generale = $total_coeff > 0 ? round($total_points / $total_coeff, 2) : 0;

    // 4. CALCUL DU RANG DANS LA CLASSE
    $q_rang = $db->prepare("SELECT e.id, 
                            SUM(n.note * n.coefficient * m.coefficient) / SUM(n.coefficient * m.coefficient) as mg
                            FROM etudiants e
                            JOIN notes n ON e.id = n.id_etudiant
                            JOIN matieres m ON n.id_matiere = m.id
                            WHERE e.id_classe = ? AND n.semestre = ? AND n.annee_scolaire = ?
                            GROUP BY e.id
                            ORDER BY mg DESC");
    $q_rang->execute([$etudiant['id_classe'], $semestre, $annee]);
    $classement = $q_rang->fetchAll(PDO::FETCH_ASSOC);
    $rang = 1;
    foreach($classement as $index => $cl){
        if($cl['id'] == $id_etudiant){
            $rang = $index + 1;
            break;
        }
    }
    $effectif = count($classement);

    // 5. CRÉATION DU PDF
    $pdf = new TCPDF('P', 'mm', 'A4', true, 'UTF-8', false);
    $pdf->SetCreator('ESSECT POINCARE');
    $pdf->SetAuthor('ESSECT POINCARE DE BOUAKE');
    $pdf->SetTitle('Bulletin S'.$semestre.' - '.$etudiant['matricule']);
    $pdf->setPrintHeader(false);
    $pdf->setPrintFooter(false);
    $pdf->AddPage();

    // LOGO TEXTE ESSECT POINCARE
    $pdf->SetXY(10, 10);
    $pdf->SetFont('helvetica', 'B', 14);
    $pdf->SetTextColor(0, 51, 102);
    $pdf->Cell(50, 8, 'ESSECT POINCARE', 1, 1, 'C', false);
    $pdf->SetFont('helvetica', '', 9);
    $pdf->SetTextColor(0, 0, 0);
    $pdf->Cell(50, 5, 'BOUAKE', 0, 1, 'C');

    // ENTÊTE
    $pdf->SetXY(70, 12);
    $pdf->SetFont('helvetica', 'B', 15);
    $pdf->Cell(0, 8, 'ESSECT POINCARE DE BOUAKE', 0, 1, 'C');
    $pdf->SetFont('helvetica', '', 11);
    $pdf->Cell(0, 6, 'BULLETIN DE NOTES - SEMESTRE '.$semestre, 0, 1, 'C');
    $pdf->Cell(0, 6, 'Année Scolaire : '.$annee, 0, 1, 'C');
    $pdf->Ln(8);

    // INFOS ÉTUDIANT
    $pdf->SetFont('helvetica', 'B', 11);
    $pdf->Cell(40, 6, 'Nom & Prénoms :', 0, 0);
    $pdf->SetFont('helvetica', '', 11);
    $pdf->Cell(0, 6, strtoupper($etudiant['nom']).' '.$etudiant['prenom'], 0, 1);
    
    $pdf->SetFont('helvetica', 'B', 11);
    $pdf->Cell(40, 6, 'Matricule :', 0, 0);
    $pdf->SetFont('helvetica', '', 11);
    $pdf->Cell(60, 6, $etudiant['matricule'], 0, 0);
    
    $pdf->SetFont('helvetica', 'B', 11);
    $pdf->Cell(20, 6, 'Classe :', 0, 0);
    $pdf->SetFont('helvetica', '', 11);
    $pdf->Cell(0, 6, $etudiant['nom_classe'], 0, 1);
    $pdf->Ln(3);

    // TABLEAU DES NOTES
    $pdf->SetFont('helvetica', 'B', 10);
    $pdf->SetFillColor(220, 220, 220);
    $pdf->Cell(80, 7, 'Matières', 1, 0, 'C', true);
    $pdf->Cell(20, 7, 'Coeff', 1, 0, 'C', true);
    $pdf->Cell(30, 7, 'Moyenne/20', 1, 0, 'C', true);
    $pdf->Cell(30, 7, 'Points', 1, 0, 'C', true);
    $pdf->Cell(30, 7, 'Appréciation', 1, 1, 'C', true);

    $pdf->SetFont('helvetica', '', 9);
    foreach($notes_finales as $nf){
        $app = '';
        if($nf['moyenne'] >= 16) $app = 'T.Bien';
        elseif($nf['moyenne'] >= 14) $app = 'Bien';
        elseif($nf['moyenne'] >= 12) $app = 'A.Bien';
        elseif($nf['moyenne'] >= 10) $app = 'Passable';
        else $app = 'Insuffisant';

        $pdf->Cell(80, 6, $nf['matiere'], 1);
        $pdf->Cell(20, 6, $nf['coeff'], 1, 0, 'C');
        $pdf->Cell(30, 6, $nf['moyenne'], 1, 0, 'C');
        $pdf->Cell(30, 6, number_format($nf['points'], 2), 1, 0, 'C');
        $pdf->Cell(30, 6, $app, 1, 1, 'C');
    }

    // TOTAUX
    $pdf->SetFont('helvetica', 'B', 10);
    $pdf->Cell(80, 7, 'TOTAUX', 1, 0, 'R');
    $pdf->Cell(20, 7, $total_coeff, 1, 0, 'C');
    $pdf->Cell(30, 7, '', 1, 0, 'C');
    $pdf->Cell(30, 7, number_format($total_points, 2), 1, 0, 'C');
    $pdf->Cell(30, 7, '', 1, 1, 'C');
    $pdf->Ln(5);

    // BILAN
    $pdf->SetFont('helvetica', 'B', 12);
    $pdf->Cell(60, 8, 'MOYENNE GENERALE :', 0, 0);
    $pdf->SetFont('helvetica', 'B', 14);
    $pdf->Cell(30, 8, $moyenne_generale.'/20', 0, 1);

    $pdf->SetFont('helvetica', 'B', 12);
    $pdf->Cell(60, 8, 'RANG :', 0, 0);
    $pdf->SetFont('helvetica', '', 12);
    $pdf->Cell(0, 8, $rang.'e sur '.$effectif.' étudiants', 0, 1);

    $decision = $moyenne_generale >= 10 ? 'ADMIS(E) EN CLASSE SUPERIEURE' : 'REDOUBLE';
    $pdf->SetFont('helvetica', 'B', 12);
    $pdf->Cell(60, 8, 'DECISION DU CONSEIL :', 0, 0);
    $pdf->SetFont('helvetica', 'B', 12);
    $pdf->Cell(0, 8, $decision, 0, 1);

    $pdf->Ln(15);
    $pdf->SetFont('helvetica', '', 10);
    $pdf->Cell(0, 6, 'Fait à Bouaké, le '.date('d/m/Y'), 0, 1, 'R');
    $pdf->Ln(10);
    $pdf->Cell(0, 6, 'Le Directeur', 0, 1, 'R');

    $pdf->Output('Bulletin_'.$etudiant['matricule'].'_S'.$semestre.'.pdf', 'I');
    exit();
}

// PAGE LISTE DES ÉTUDIANTS
$stmt_liste = $db->prepare("SELECT e.*, c.nom_classe FROM etudiants e 
                            JOIN classes c ON e.id_classe = c.id 
                            ORDER BY c.nom_classe, e.nom");
$stmt_liste->execute();
$liste = $stmt_liste->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Génération Bulletins - ESSECT POINCARE</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>
<body>
    <nav class="navbar navbar-dark bg-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="dashboard.php">ESSECT POINCARE DE BOUAKE</a>
            <a href="dashboard.php" class="btn btn-outline-light btn-sm">Retour Dashboard</a>
        </div>
    </nav>

    <div class="container mt-4">
        <h2><i class="bi bi-file-earmark-pdf"></i> Génération des Bulletins</h2>
        
        <div class="card">
            <div class="card-body">
                <table class="table table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th>Matricule</th>
                            <th>Nom & Prénoms</th>
                            <th>Classe</th>
                            <th class="text-center">Semestre 1</th>
                            <th class="text-center">Semestre 2</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($liste as $et): ?>
                        <tr>
                            <td><?php echo $et['matricule']; ?></td>
                            <td><?php echo strtoupper($et['nom']).' '.$et['prenom']; ?></td>
                            <td><?php echo $et['nom_classe']; ?></td>
                            <td class="text-center">
                                <a href="?generate=1&id_etudiant=<?php echo $et['id']; ?>&semestre=1" 
                                   class="btn btn-danger btn-sm" target="_blank">
                                    <i class="bi bi-file-pdf"></i> PDF S1
                                </a>
                            </td>
                            <td class="text-center">
                                <a href="?generate=1&id_etudiant=<?php echo $et['id']; ?>&semestre=2" 
                                   class="btn btn-danger btn-sm" target="_blank">
                                    <i class="bi bi-file-pdf"></i> PDF S2
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>