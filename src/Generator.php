<?php

namespace InvoiceGenerator;

use Mpdf\Mpdf;
use Twig\Environment as Twig;
use Twig\Loader\FilesystemLoader as TwigFSLoader;

class Generator
{
    public function __construct(Invoice $invoice, array $config)
    {
        $data['invoice'] = $invoice;
        $data['config'] = $config;
        $twig = new Twig(new TwigFSLoader($config['templates_path']));
        $mpdf = new Mpdf([
            'default_font' => 'Arial',
            'margin_left' => 10,
            'margin_right' => 10,
            'margin_top' => 10,
            'margin_bottom' => 10,
            'margin_header' => 0,
            'margin_footer' => 5,
        ]);
        $mpdf->SetFooter('Page {PAGENO}/{nbpg}');
        $mpdf->WriteHTML($twig->render($config['template'], [
            'invoice' => $invoice,
            'config' => $config
        ]));
        $mpdf->Output($config['output']);
    }
}