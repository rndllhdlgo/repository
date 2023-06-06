<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\PdfToText\Pdf;
use thiagoalessio\TesseractOCR\TesseractOCR;

class PdfController extends Controller
{
    public function index()
    {
        return view('pdf.upload');
    }

    public function extractText(Request $request)
    {
        $file = $request->file('pdf_file');

        if ($file->getClientOriginalExtension() === 'pdf') {
            $pdfPath = $file->getPathname();
            $text = Pdf::getText($pdfPath);
        } elseif (in_array(strtolower($file->getClientOriginalExtension()), ['jpg', 'jpeg', 'png', 'gif'])) {

            $imagePath = $file->getPathname();
            $text = (new TesseractOCR($imagePath))->run();
        } else {
            return redirect()->back()->with('error', 'Invalid file format. Please upload a PDF or an image.');
        }

        if (stripos($text, 'grace') !== false) {
            return 'meron';
        }

        return view('pdf.extracted', compact('text'));
    }
}