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

    public function uploadFile(Request $request){
        if($request->action == 'RECEIVE'){
            $files = Requests::where('request_number', $request->reqnum)->first()->receipt_upload;
            if($files != NULL){
                $files = str_replace(']','',(str_replace('[','',(explode(',',$files)))));
                foreach($files as $file){
                    $file = str_replace('"','',$file);
                    if(file_exists(public_path('uploads/'.$file))){
                        unlink(public_path('uploads/'.$file));
                    }
                }
            }

            $x = 1;
            $receipt_upload = array();
            foreach($request->reference_upload as $upload){
                $datetime = Carbon::now()->isoformat('YYYYMMDDHHmmss');
                $extension = $upload->getClientOriginalExtension();
                $filename = $datetime.'_'.$request->reqnum.'-'.$x.'.'.$extension;
                array_push($receipt_upload, $filename);
                $x++;
            }

            Requests::where('request_number', $request->reqnum)
                ->update(['receipt_upload' => $receipt_upload]);

            for($i=0; $i < count($receipt_upload); $i++){
                $request->reference_upload[$i]->move(public_path('/uploads'), $receipt_upload[$i]);
            }

            $reference_delete = array();
            for($c=0; $c < count($receipt_upload); $c++){
                if(str_contains($receipt_upload[$c], '.pdf') == true){
                    $pdf = new Pdf(public_path('uploads/'.$receipt_upload[$c]));
                    $pdfcount = $pdf->getNumberOfPages();
                    $datetime = Carbon::now()->isoformat('YYYYMMDDHHmmss');
                    for($a=1; $a < $pdfcount+1; $a++){
                        $filename = $datetime.'_'.$request->reqnum.'-'.$a.'-'.Str::random(5).'.jpg';
                        $pdf->setPage($a)
                            ->setOutputFormat('jpg')
                            ->saveImage(public_path('uploads/'.$filename));
                        array_push($receipt_upload, $filename);
                    }
                    unlink(public_path('uploads/'.$receipt_upload[$c]));
                    array_push($reference_delete, $receipt_upload[$c]);
                }
            }
            $receipt_upload = json_encode($receipt_upload);
            for($d=0; $d < count($reference_delete); $d++){
                $receipt_upload = str_replace('"'.$reference_delete[$d].'",', "", $receipt_upload);
                $receipt_upload = str_replace('"'.$reference_delete[$d].'"', "", $receipt_upload);
                $receipt_upload = str_replace($reference_delete[$d], "", $receipt_upload);
            }

            Requests::where('request_number', $request->reqnum)
                ->update(['receipt_upload' => $receipt_upload]);
        }
        else{
            $files = Requests::where('request_number', $request->reqnum)->first()->reference_upload;
            if($files != NULL){
                $files = str_replace(']','',(str_replace('[','',(explode(',',$files)))));
                foreach($files as $file){
                    $file = str_replace('"','',$file);
                    if(file_exists(public_path('uploads/'.$file))){
                        unlink(public_path('uploads/'.$file));
                    }
                }
            }

            $x = 1;
            $reference_upload = array();
            foreach($request->reference_upload as $upload){
                $datetime = Carbon::now()->isoformat('YYYYMMDDHHmmss');
                $extension = $upload->getClientOriginalExtension();
                $filename = $datetime.'_'.$request->reqnum.'-'.$x.'.'.$extension;
                array_push($reference_upload, $filename);
                $x++;
            }

            Requests::where('request_number', $request->reqnum)
                ->update(['reference_upload' => $reference_upload]);

            for($i=0; $i < count($reference_upload); $i++){
                $request->reference_upload[$i]->move(public_path('/uploads'), $reference_upload[$i]);
            }

            $reference_delete = array();
            for($c=0; $c < count($reference_upload); $c++){
                if(str_contains($reference_upload[$c], '.pdf') == true){
                    $pdf = new Pdf(public_path('uploads/'.$reference_upload[$c]));
                    $pdfcount = $pdf->getNumberOfPages();
                    $datetime = Carbon::now()->isoformat('YYYYMMDDHHmmss');
                    for($a=1; $a < $pdfcount+1; $a++){
                        $filename = $datetime.'_'.$request->reqnum.'-'.$a.'-'.Str::random(5).'.jpg';
                        $pdf->setPage($a)
                            ->setOutputFormat('jpg')
                            ->saveImage(public_path('uploads/'.$filename));
                        array_push($reference_upload, $filename);
                    }
                    unlink(public_path('uploads/'.$reference_upload[$c]));
                    array_push($reference_delete, $reference_upload[$c]);
                }
            }
            $reference_upload = json_encode($reference_upload);
            for($d=0; $d < count($reference_delete); $d++){
                $reference_upload = str_replace('"'.$reference_delete[$d].'",', "", $reference_upload);
                $reference_upload = str_replace('"'.$reference_delete[$d].'"', "", $reference_upload);
                $reference_upload = str_replace($reference_delete[$d], "", $reference_upload);
            }

            Requests::where('request_number', $request->reqnum)
                ->update(['reference_upload' => $reference_upload]);
        }

        if($request->action == 'SUBMIT'){
            return redirect()->to('/stockrequest?submit='.$request->reqnum);
        }
        else if($request->action == 'ASSET'){
            return redirect()->to('/stockrequest?asset='.$request->reqnum);
        }
        else if($request->action == 'RECEIVE'){
            return redirect()->to("/stockrequest?receive=$request->reqnum&reqtype=$request->reqtypeid&status=$request->statusid&inctype=$request->inctype");
        }
        else if($request->action == 'EDIT'){
            $reqtype = Requests::where('request_number', $request->reqnum)->first()->request_type;
            return redirect()->to("/stockrequest?reqtype=$reqtype&status=7&edit=$request->reqnum");
        }
        else{
            return redirect()->to('/stockrequest?sale='.$request->reqnum);
        }
    }
}
