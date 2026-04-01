<?php

namespace App\ESolutions\Helpers;

use Exception;
use mikehaertl\wkhtmlto\Pdf;
use Throwable;

class PdfHelper
{
//    static function create(string $format, array $htmlBody, string $htmlFooter = ''): array
//    {
//        try {
//            $template = self::getTemplate();
//            $pdf = new Pdf([
//                'no-outline',
//                'disable-smart-shrinking',
//                'dpi' => 96,
//                'encoding' => 'UTF-8',
//                'print-media-type',
//                'zoom' => 1,
//                'viewport-size' => '1280x1024',
//                'footer-html' => $htmlFooter,
//                'user-style-sheet' => public_path('css/template.css'),
//                'enable-local-file-access',
//            ]);
//
//            if (isWindows()) {
//                $pdf->binary = public_path('vendor/wkhtmltopdf.exe');
//            }
//
//            foreach ($htmlBody as $item) {
//                $pageConfig = array_merge(self::getFormatConfig($format), $item['config'] ?? []);
//                $html = self::renderTemplate($item['html'], $pageConfig, $template);
//                dd($pageConfig);
//
//                // Añadir página con configuración individual
//                $pdf->addPage($html, [
//                    'margin-top' => $pageConfig['marginTop'],
//                    'margin-bottom' => $pageConfig['marginBottom'],
//                    'margin-right' => $pageConfig['marginRight'],
//                    'margin-left' => $pageConfig['marginLeft'],
//                    'page-height' => $pageConfig['pageHeight'],
//                    'page-width' => $pageConfig['pageWidth'],
//                ]);
//            }
//
//            $pdfContent = $pdf->toString();
//
//            if (!$pdfContent) {
//                throw new Exception('PDF generation error: ' . $pdf->getError());
//            }
//
//            return [
//                'success' => true,
//                'pdf_content' => $pdfContent,
//            ];
//        } catch (Throwable $e) {
//            return [
//                'success' => false,
//                'message' => $e->getMessage(),
//            ];
//        }
//    }


    static function create(string $format, array $htmlBody, string $htmlFooter = ''): array
    {
        try {
            $config = self::getFormatConfig($format);
            $template = self::getTemplate();

            $pdf = new Pdf([
                'no-outline',
                'disable-smart-shrinking',
                'dpi' => 96,
                'encoding' => 'UTF-8',
                'margin-top' => $config['marginTop'],
                'margin-bottom' => $config['marginBottom'],
                'margin-right' => $config['marginRight'],
                'margin-left' => $config['marginLeft'],
                'print-media-type',
                'zoom' => 1,
                'viewport-size' => '1280x1024',
                'page-width' => $config['pageWidth'],
                'page-height' => $config['pageHeight'],
                'footer-html' => $htmlFooter,
                'user-style-sheet' => public_path('css/template.css'),
                'enable-local-file-access',
            ]);

            // Ejecutable wkhtmltopdf en Windows
            if (PHP_OS_FAMILY === 'Windows') {
                $pdf->binary = public_path('vendor/wkhtmltopdf.exe');
            }
            foreach ($htmlBody as $hb) {
                $html = self::renderTemplate($hb, $config, $template);
                $pdf->addPage($html);
            }

            $pdfContent = $pdf->toString();

            if (!$pdfContent) {
                throw new Exception('PDF generation error: ' . $pdf->getError());
            }

            return [
                'success' => true,
                'pdf_content' => $pdfContent,
            ];
        } catch (Throwable $e) {
            return [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }
    }

    static function getFormatConfig(string $format): array
    {
        if ($format === 'ticket') {
            return [
                'pageWidth' => '7cm',
                'pageHeight' => '15cm',
                'marginTop' => '0cm',
                'marginBottom' => '2cm',
                'marginRight' => '0.125cm',
                'marginLeft' => '0.125cm',
            ];
        }

        return [
            'pageWidth' => '21cm',
            'pageHeight' => '29.7cm',
            'marginTop' => '1cm',
            'marginBottom' => '2cm',
            'marginRight' => '1cm',
            'marginLeft' => '1cm',
        ];
    }

    static function getTemplate(): array
    {
        return [
            'font_family' => "'Roboto Mono', monospace",
            'color_line' => '#ffffff',
            'color_head_bg' => '#ffffff',
            'color_head_text' => '#ffffff',
            'watermark_image_url' => '#ffffff',
            'watermark_image_width' => '#ffffff',
            'watermark_image_height' => '#ffffff'
        ];
    }

    static function renderTemplate(string $htmlBody, array $config, array $template): string
    {
        return view('template_main', [
            'html_body' => $htmlBody,
            'config' => $config,
            'template' => $template,
        ])->render();
    }
}
