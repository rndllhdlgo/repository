<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\PdfToText\Pdf;
use thiagoalessio\TesseractOCR\TesseractOCR;
use App\Models\SalesInvoice;
use App\Models\CollectionReceipt;
use App\Models\BillingStatement;
use App\Models\OfficialReceipt;
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
            $imagick->modulateImage(120, 100, 100);
            $imagePath = storage_path("app/public/$request->sales_invoice.jpeg");
            $imagick->writeImage($imagePath);
            $text = (new TesseractOCR($imagePath))->run();

            if(stripos($text, $request->sales_invoice) === false){
                return 'SALES INVOICE not found';
            }
            // else if(stripos($text, $request->client_name) === false){
            //     return 'CLIENT NAME not found';
            // }
            // else if(stripos($text, $request->branch_name) === false){
            //     return 'BRANCH NAME not found';
            // }
            // else if(stripos($text, $request->purchase_order) === false){
            //     return 'PURCHASE ORDER not found';
            // }
            // else if(stripos($text, $request->sales_order) === false){
            //     return 'SALES ORDER not found';
            // }
            // else if(stripos($text, $request->delivery_receipt) === false){
            //     return 'DELIVERY RECEIPT not found';
            // }
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

    public function save_cr(Request $request){
        if(CollectionReceipt::where('collection_receipt', $request->collection_receipt)->count() > 0) {
            return 'COLLECTION RECEIPT Already Exist';
        }

        $file = $request->file('pdf_file');
        if($file->getClientOriginalExtension() === 'pdf'){
            $fileExtension = $file->getClientOriginalExtension();
            $imagick = new \Imagick();
            $imagick->readImage($file->getPathname() . '[0]');
            $imagick->setImageFormat('jpeg');
            $imagick->modulateImage(120, 100, 100);
            $imagePath = storage_path("app/public/$request->collection_receipt.jpeg");
            $imagick->writeImage($imagePath);
            $text = (new TesseractOCR($imagePath))->run();

            if(stripos($text, $request->collection_receipt) === false){
                return 'COLLECTION RECEIPT not found';
            }
            // else if(stripos($text, $request->client_name) === false){
            //     return 'CLIENT NAME not found';
            // }
            // else if(stripos($text, $request->branch_name) === false){
            //     return 'BRANCH NAME not found';
            // }
            // else if(stripos($text, $request->purchase_order) === false){
            //     return 'PURCHASE ORDER not found';
            // }
            // else if(stripos($text, $request->sales_order) === false){
            //     return 'SALES ORDER not found';
            // }
            else{
                $filename = $request->collection_receipt.'.'.$fileExtension;
                $file->storeAs('public/collection_receipt',$filename);

                CollectionReceipt::create([
                    'collection_receipt' => $request->collection_receipt,
                    'company' => $request->company,
                    'client_name' => $request->client_name,
                    'branch_name' => $request->branch_name,
                    'date_created' => $request->date_created,
                    'sales_order' => $request->sales_order,
                    'sales_invoice' => $request->sales_invoice,
                    'pdf_file' => $filename
                ]);

                $userlogs = new UserLogs;
                $userlogs->username = auth()->user()->name;
                $userlogs->role = Role::where('id', auth()->user()->userlevel)->first()->name;
                $userlogs->activity = "USER SUCCESSFULLY ADDED COLLECTION RECEIPT ($request->collection_receipt).";
                $userlogs->save();

                return 'success';
            }
        }
        else{
            return 'Invalid file format';
        }
    }

    public function save_bs(Request $request){
        if(BillingStatement::where('billing_statement', $request->billing_statement)->count() > 0) {
            return 'BILLING STATEMENT Already Exist';
        }

        $file = $request->file('pdf_file');
        if($file->getClientOriginalExtension() === 'pdf'){
            $fileExtension = $file->getClientOriginalExtension();
            $imagick = new \Imagick();
            $imagick->readImage($file->getPathname() . '[0]');
            $imagick->setImageFormat('jpeg');
            $imagick->modulateImage(120, 100, 100);
            $imagePath = storage_path("app/public/$request->billing_statement.jpeg");
            $imagick->writeImage($imagePath);
            $text = (new TesseractOCR($imagePath))->run();

            if(stripos($text, $request->billing_statement) === false){
                // Storage::delete('public/documents/'.$employee_details->empno.'_'.$employee_details->last_name.'_'.$employee_details->first_name.'/'.$document_orig->barangay_clearance_file);
                return 'BILLING STATEMENT not found';
            }
            else{
                $filename = $request->billing_statement.'.'.$fileExtension;
                $file->storeAs('public/billing_statement',$filename);

                BillingStatement::create([
                    'billing_statement' => $request->billing_statement,
                    'company' => $request->company,
                    'client_name' => $request->client_name,
                    'branch_name' => $request->branch_name,
                    'date_created' => $request->date_created,
                    'sales_order' => $request->sales_order,
                    'purchase_order' => $request->purchase_order,
                    'pdf_file' => $filename
                ]);

                $userlogs = new UserLogs;
                $userlogs->username = auth()->user()->name;
                $userlogs->role = Role::where('id', auth()->user()->userlevel)->first()->name;
                $userlogs->activity = "USER SUCCESSFULLY ADDED BILLING STATEMENT ($request->billing_statement).";
                $userlogs->save();

                return 'success';
            }
        }
        else{
            return 'Invalid file format';
        }
    }

    public function save_or(Request $request){
        if(OfficialReceipt::where('official_receipt', $request->official_receipt)->count() > 0) {
            return 'OFFICIAL RECEIPT NO. Already Exist';
        }

        $file = $request->file('pdf_file');
        if($file->getClientOriginalExtension() === 'pdf'){
            $fileExtension = $file->getClientOriginalExtension();
            $imagick = new \Imagick();
            $imagick->readImage($file->getPathname() . '[0]');
            $imagick->setImageFormat('jpeg');
            $imagePath = storage_path("app/public/$request->official_receipt.jpeg");
            $imagick->writeImage($imagePath);
            $text = (new TesseractOCR($imagePath))->run();

            if(stripos($text, $request->official_receipt) === false){
                return 'OFFICIAL RECEIPT NO. not found';
            }
            else{
                $filename = $request->official_receipt.'.'.$fileExtension;
                $file->storeAs('public/official_receipt',$filename);

                OfficialReceipt::create([
                    'official_receipt' => $request->official_receipt,
                    'company' => $request->company,
                    'client_name' => $request->client_name,
                    'branch_name' => $request->branch_name,
                    'date_created' => $request->date_created,
                    'sales_order' => $request->sales_order,
                    'pdf_file' => $filename
                ]);

                $userlogs = new UserLogs;
                $userlogs->username = auth()->user()->name;
                $userlogs->role = Role::where('id', auth()->user()->userlevel)->first()->name;
                $userlogs->activity = "USER SUCCESSFULLY ADDED OFFICIAL RECEIPT ($request->official_receipt).";
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
            $imagick = new \Imagick();
            $imagick->readImage($file->getPathname() . '[0]');
            $imagick->setImageFormat('jpeg');
            $imagePath = storage_path("app/public/$request->delivery_receipt.jpeg");
            $imagick->writeImage($imagePath);
            $text = (new TesseractOCR($imagePath))->run();

            if(stripos($text, $request->delivery_receipt) === false){
                return 'DELIVERY RECEIPT not found';
            }
            // else if(stripos($text, $request->client_name) === false){
            //     return 'CLIENT NAME not found';
            // }
            // else if(stripos($text, $request->branch_name) === false){
            //     return 'BRANCH NAME not found';
            // }
            // else if(stripos($text, $request->purchase_order) === false){
            //     return 'PURCHASE ORDER not found';
            // }
            // else if(stripos($text, $request->sales_order) === false){
            //     return 'SALES ORDER not found';
            // }
            else{
                $filename = $request->delivery_receipt.'.'.$fileExtension;
                $file->storeAs('public/delivery_receipt',$filename);

                DeliveryReceipt::create([
                    'delivery_receipt' => $request->delivery_receipt,
                    'company' => $request->company,
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

    public function edit(Request $request){
        if($request->current_page == 'si'){
            $sql = SalesInvoice::where('id', $request->entry_id)
                        ->update([
                        'client_name' => $request->client_name,
                        'branch_name' => $request->branch_name
            ]);
        }

        if($request->current_page == 'cr'){
            $sql = CollectionReceipt::where('id', $request->entry_id)
                        ->update([
                        'client_name' => $request->client_name,
                        'branch_name' => $request->branch_name
            ]);
        }

        if($request->current_page == 'bs'){
            $sql = BillingStatement::where('id', $request->entry_id)
                        ->update([
                        'client_name' => $request->client_name,
                        'branch_name' => $request->branch_name
            ]);
        }

        if($request->current_page == 'or'){
            $sql = OfficialReceipt::where('id', $request->entry_id)
                        ->update([
                        'client_name' => $request->client_name,
                        'branch_name' => $request->branch_name
            ]);
        }

        if($request->current_page == 'dr'){
            $sql = DeliveryReceipt::where('id', $request->entry_id)
                        ->update([
                        'client_name' => $request->client_name,
                        'branch_name' => $request->branch_name
            ]);
        }

        return $sql ? 'true' : 'false';
    }
}
