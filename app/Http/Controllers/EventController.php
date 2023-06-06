<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\PdfToText\Pdf;
use thiagoalessio\TesseractOCR\TesseractOCR;
use App\Models\SalesInvoice;

class EventController extends Controller
{
    public function __construct()
    {
       $this->middleware('auth');
    }

    public function save_sales_invoice(Request $request){
        $file = $request->file('pdf_file');
        if($file->getClientOriginalExtension() === 'pdf'){
            $fileExtension = $file->getClientOriginalExtension();
            $pdfPath = $file->getPathname();
            $text = strtolower(Pdf::getText($pdfPath));
            if(stripos($text, $request->sales_invoice) === false){
                return 'SALES INVOICE not found';
            }
            else if(stripos($text, $request->client_name) === false){
                return 'CLIENT NAME not found';
            }
            else if(stripos($text, $request->branch_name) === false){
                return 'BRANCH NAME not found';
            }
            else if(stripos($text, $request->purchase_order) === false){
                return 'PURCHASE ORDER not found';
            }
            else if(stripos($text, $request->sales_order) === false){
                return 'SALES ORDER not found';
            }
            else if(stripos($text, $request->delivery_receipt) === false){
                return 'DELIVERY RECEIPT not found';
            }
            else{
                return 'success';
            }

            $filename = time().rand(1,100).'_pdf_file.'.$fileExtension;
            $file->storeAs('public/sales_invoice',$filename);
            return $filename;
        }
        else{
            return 'Invalid file format';
        }
    }
}
