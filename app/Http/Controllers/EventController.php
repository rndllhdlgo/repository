<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\PdfToText\Pdf;
use thiagoalessio\TesseractOCR\TesseractOCR;
use App\Models\SalesInvoice;
use App\Models\DeliveryReceipt;
use App\Models\UserLogs;
use App\Models\Role;
use Spatie\PdfToImage\Pdf as Jpg;

class EventController extends Controller
{
    public function __construct()
    {
       $this->middleware('auth');
    }

    public function save_sales_invoice(Request $request){
        if(SalesInvoice::where('sales_invoice', $request->sales_invoice)->count() > 0) {
            return 'SALES INVOICE Already Exist';
        }

        $file = $request->file('pdf_file');
        if($file->getClientOriginalExtension() === 'pdf'){
            $fileExtension = $file->getClientOriginalExtension();
            $imagick = new \Imagick();
            $imagick->readImage($file->getPathname() . '[0]');
            $imagick->setImageFormat('jpeg');

            $imagePath = storage_path("app/public/$request->sales_invoice.jpeg");
            $imagick->writeImage($imagePath);
            $text = (new TesseractOCR($imagePath))->run();

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
                $filename = $request->sales_invoice.'.'.$fileExtension;
                $file->storeAs('public/sales_invoice',$filename);

                SalesInvoice::create([
                    'sales_invoice' => $request->sales_invoice,
                    'company' => $request->company,
                    'client_name' => $request->client_name,
                    'branch_name' => $request->branch_name,
                    'date_created' => $request->date_created,
                    'date_received' => $request->date_received,
                    'purchase_order' => $request->purchase_order,
                    'sales_order' => $request->sales_order,
                    'delivery_receipt' => $request->delivery_receipt,
                    'pdf_file' => $filename
                ]);

                $userlogs = new UserLogs;
                $userlogs->username = auth()->user()->name;
                $userlogs->role = Role::where('id', auth()->user()->userlevel)->first()->name;
                $userlogs->activity = "USER SUCCESSFULLY ADDED SALES INVOICE ($request->sales_invoice).";
                $userlogs->save();

                return 'success';
            }
        }
        else{
            return 'Invalid file format';
        }
    }

    public function save_delivery_receipt(Request $request){
        if(DeliveryReceipt::where('delivery_receipt', $request->delivery_receipt)->count() > 0) {
            return 'DELIVERY RECEIPT Already Exist';
        }

        $file = $request->file('pdf_file');
        if($file->getClientOriginalExtension() === 'pdf'){
            $fileExtension = $file->getClientOriginalExtension();
            $pdfPath = $file->getPathname();
            $text = strtolower(Pdf::getText($pdfPath));
            if(stripos($text, $request->delivery_receipt) === false){
                return 'DELIVERY RECEIPT not found';
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
            else{
                $filename = $request->delivery_receipt.'.'.$fileExtension;
                $file->storeAs('public/delivery_receipt',$filename);

                DeliveryReceipt::create([
                    'delivery_receipt' => $request->delivery_receipt,
                    'client_name' => $request->client_name,
                    'branch_name' => $request->branch_name,
                    'date_created' => $request->date_created,
                    'date_received' => $request->date_received,
                    'purchase_order' => $request->purchase_order,
                    'sales_order' => $request->sales_order,
                    'pdf_file' => $filename
                ]);

                $userlogs = new UserLogs;
                $userlogs->username = auth()->user()->name;
                $userlogs->role = Role::where('id', auth()->user()->userlevel)->first()->name;
                $userlogs->activity = "USER SUCCESSFULLY ADDED DELIVERY RECEIPT ($request->delivery_receipt).";
                $userlogs->save();

                return 'success';
            }
        }
        else{
            return 'Invalid file format';
        }
    }
}
