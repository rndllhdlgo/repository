<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\PdfToText\Pdf;
use thiagoalessio\TesseractOCR\TesseractOCR;

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
            $text = Pdf::getText($pdfPath);
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

    // public function save_sales_invoice(Request $request){
    //     $sales = new SalesInvoice;
    //     $sales->sales_invoice = $request->sales_invoice;
    //     $sales->client_name = $request->client_name;
    //     $sales->branch_name = $request->branch_name;
    //     $sales->date_created = $request->date_created;
    //     $sales->date_received = $request->date_received;
    //     $sales->purchase_order = $request->purchase_order;
    //     $sales->sales_order = $request->sales_order;
    //     $sales->delivery_receipt = $request->delivery_receipt;
    //     $sales->pdf_file = $request->pdf_file;
    //     $save = $sales->save();

    //     if($save){
    //         return 'true';
    //     }
    //     else{
    //         return 'false';
    //     }
    // }
}
