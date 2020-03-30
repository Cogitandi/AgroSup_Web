<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;
// Include Dompdf required namespaces
use Dompdf\Dompdf;
use Dompdf\Options;

class PDFController extends Controller
{
    /**
     * @Route("/generatePDF", name="generatePDF")
     */
    public function index()
    {
        // Configure Dompdf according to your needs
        $pdfOptions = new Options();
        $pdfOptions->set('defaultFont', 'Arial');
        
        // Instantiate Dompdf with our options
        $dompdf = new Dompdf($pdfOptions);
        
        //$code = "";
        // Retrieve the HTML generated in our twig file
        $html = $this->renderView('pdf.twig');
        
        // Load HTML to Dompdf
        //$dompdf->loadHtml($html);
        $dompdf->loadHtml($html);
        
        // (Optional) Setup the paper size and orientation 'portrait' or 'portrait'
        $dompdf->setPaper('A4', 'landscape');

        // Render the HTML as PDF
        $dompdf->render();

        // Output the generated PDF to Browser (force download)
        $dompdf->stream("mypdf.pdf", [
            "Attachment" => true
        ]);
    }
}