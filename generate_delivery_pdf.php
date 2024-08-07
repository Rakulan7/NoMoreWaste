<?php
require('fpdf186/fpdf.php');

class PDF extends FPDF
{
    // En-tête
    function Header()
    {
        $this->SetFont('Arial', 'B', 12);
        $this->Cell(0, 10, 'Details de la Livraison', 0, 1, 'C');
        $this->Ln(10);
    }

    // Pied de page
    function Footer()
    {
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 8);
        $this->Cell(0, 10, 'Page ' . $this->PageNo(), 0, 0, 'C');
    }
}

function sanitizeString($string)
{
    $search = ['é', 'è', 'ê', 'ë', 'à', 'â', 'ä', 'ç', 'ù', 'û', 'ü', 'ô', 'ö', 'î', 'ï'];
    $replace = ['e', 'e', 'e', 'e', 'a', 'a', 'a', 'c', 'u', 'u', 'u', 'o', 'o', 'i', 'i'];
    return str_replace($search, $replace, $string);
}

function generateDeliveryPDF($delivery_id, $conn, $path)
{
    // Récupérer les détails de la livraison
    $query = "
        SELECT 
            d.id AS delivery_id,
            d.delivery_date,
            d.status AS delivery_status,
            d.beneficiary_id,
            b.name AS beneficiary_name,
            b.address AS beneficiary_address,
            cr.id AS collection_id,
            cr.collection_date,
            cr.collection_time,
            cr.merchant_address,
            sl.name AS storage_name,
            sl.address AS storage_address,
            u.name AS volunteer_name
        FROM deliveries d
        LEFT JOIN collection_requests cr ON d.collection_request_id = cr.id
        LEFT JOIN storage_locations sl ON cr.storage_location_id = sl.id
        LEFT JOIN users u ON d.volunteer_id = u.id
        LEFT JOIN beneficiaries b ON d.beneficiary_id = b.id
        WHERE d.id = ?
    ";

    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $delivery_id);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();

    if (!$result) {
        return false;
    }

    // Récupérer les produits associés à la collecte
    $product_query = "SELECT * FROM products WHERE collection_request_id = ?";
    $product_stmt = $conn->prepare($product_query);
    $product_stmt->bind_param("i", $result['collection_id']);
    $product_stmt->execute();
    $products_result = $product_stmt->get_result();

    // Création du PDF
    $pdf = new PDF();
    $pdf->AddPage();
    $pdf->SetFont('Arial', '', 12);

    // Détails de la livraison
    $pdf->Cell(0, 10, 'ID de la Livraison: ' . sanitizeString($result['delivery_id']), 0, 1);
    $pdf->Cell(0, 10, 'Date de Livraison: ' . sanitizeString($result['delivery_date']), 0, 1);
    $pdf->Cell(0, 10, 'Statut de la Livraison: ' . sanitizeString($result['delivery_status']), 0, 1);
    $pdf->Cell(0, 10, 'Beneficiaire: ' . sanitizeString($result['beneficiary_name']), 0, 1);
    $pdf->Cell(0, 10, 'Adresse du Beneficiaire: ' . sanitizeString($result['beneficiary_address']), 0, 1);

    // Détails de la collecte
    $pdf->Ln(10);
    $pdf->Cell(0, 10, 'Details de la Collecte Associee', 0, 1, 'C');
    $pdf->Ln(5);
    $pdf->Cell(0, 10, 'ID de la Collecte: ' . sanitizeString($result['collection_id']), 0, 1);
    $pdf->Cell(0, 10, 'Date de Collecte: ' . sanitizeString($result['collection_date']), 0, 1);
    $pdf->Cell(0, 10, 'Heure de Collecte: ' . sanitizeString($result['collection_time']), 0, 1);
    $pdf->Cell(0, 10, 'Adresse du Marchand: ' . sanitizeString($result['merchant_address']), 0, 1);
    $pdf->Cell(0, 10, 'Lieu de Stockage: ' . sanitizeString($result['storage_name']) . ', ' . sanitizeString($result['storage_address']), 0, 1);
    $pdf->Cell(0, 10, 'Benevole: ' . sanitizeString($result['volunteer_name'] ?: 'Non attribue'), 0, 1);

    // Produits associés à la collecte
    $pdf->Ln(10);
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(0, 10, 'Produits Donnes', 0, 1);
    $pdf->SetFont('Arial', '', 12);

    if ($products_result->num_rows > 0) {
        while ($product = $products_result->fetch_assoc()) {
            $pdf->Cell(0, 10, 'Nom du Produit: ' . sanitizeString($product['name']), 0, 1);
            $pdf->Cell(0, 10, 'Code-barres: ' . sanitizeString($product['barcode']), 0, 1);
            $pdf->Cell(0, 10, 'Date d\'Expiration: ' . sanitizeString($product['expiry_date']), 0, 1);
            $pdf->Cell(0, 10, 'Quantite: ' . sanitizeString($product['quantity']), 0, 1);
            $pdf->Cell(0, 10, 'Date de Stockage: ' . sanitizeString($product['storage_date']), 0, 1);
            $pdf->Ln(5);
        }
    } else {
        $pdf->Cell(0, 10, 'Aucun produit trouve pour cette collecte.', 0, 1);
    }

    $filePath = '/pdf/delivery_' . $delivery_id . '.pdf';
    $pdf->Output('F', $filePath);

    return $filePath;
}
?>
