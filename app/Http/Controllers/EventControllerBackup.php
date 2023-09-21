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
use Carbon\Carbon;

class EventController extends Controller
{
    public function __construct()
    {
       $this->middleware('auth');
    }

    public function save_si(Request $request){
        if(SalesInvoice::where('sales_invoice', $request->sales_invoice)->count() > 0) {
            return 'SALES INVOICE Already Exist';
        }

        $file = $request->file('pdf_file');
        if($file->getClientOriginalExtension() === 'pdf'){
            $fileExtension = $file->getClientOriginalExtension();
            $imagick = new \Imagick();
            $imagick->readImage($file->getPathname() . '[0]');
            $imagick->setImageFormat('jpeg');
            $imagick->unsharpMaskImage(0, 0.5, 1, 0.05);
            $imagePath = storage_path("app/public/$request->sales_invoice.jpeg");
            $imagick->writeImage($imagePath);
            $text = (new TesseractOCR($imagePath))->run();

            $status = 'valid';

            if(stripos(str_replace(' ', '', $text), $request->sales_invoice) === false){
                $status = 'invalid';
            }
                $filename = $request->sales_invoice.'.'.$fileExtension;
                $file->storeAs('public/sales_invoice/'.Carbon::now()->format('Y-m-d'), $filename);

                SalesInvoice::create([
                    'sales_invoice' => strtoupper($request->sales_invoice),
                    'company' => $request->company,
                    'client_name' => strtoupper($request->client_name),
                    'branch_name' => strtoupper($request->branch_name),
                    'date_created' => $request->date_created,
                    'date_received' => $request->date_received,
                    'purchase_order' => strtoupper($request->purchase_order),
                    'sales_order' => strtoupper($request->sales_order),
                    'delivery_receipt' => strtoupper($request->delivery_receipt),
                    'pdf_file' => $filename,
                    'status' => $status
                ]);

                $userlogs = new UserLogs;
                $userlogs->username = auth()->user()->name;
                $userlogs->role = Role::where('id', auth()->user()->userlevel)->first()->name;
                $userlogs->activity = "USER SUCCESSFULLY ADDED SALES INVOICE ($request->sales_invoice).";
                $userlogs->save();

                return $status;
        }
        else if($file->getClientOriginalExtension() === 'jpg'){
            $fileExtension = $file->getClientOriginalExtension();
            $imagick = new \Imagick();
            $imagick->readImage($file->getPathname() . '[0]');
            $imagick->setImageFormat('jpeg');
            $imagick->unsharpMaskImage(0, 0.5, 1, 0.05);
            $imagePath = storage_path("app/public/$request->sales_invoice.jpeg");
            $imagick->writeImage($imagePath);
            $text = (new TesseractOCR($imagePath))->run();
            $status = 'valid';

            if(stripos(str_replace(' ', '', $text), $request->sales_invoice) === false){
                $status = 'invalid';
            }
                $filename = $request->sales_invoice.'.'.$fileExtension;
                $file->storeAs('public/sales_invoice/'.Carbon::now()->format('Y-m-d'), $filename);

                SalesInvoice::create([
                    'sales_invoice' => strtoupper($request->sales_invoice),
                    'company' => $request->company,
                    'client_name' => strtoupper($request->client_name),
                    'branch_name' => strtoupper($request->branch_name),
                    'date_created' => $request->date_created,
                    'date_received' => $request->date_received,
                    'purchase_order' => strtoupper($request->purchase_order),
                    'sales_order' => strtoupper($request->sales_order),
                    'delivery_receipt' => strtoupper($request->delivery_receipt),
                    'pdf_file' => $filename,
                    'status' => $status
                ]);

                $userlogs = new UserLogs;
                $userlogs->username = auth()->user()->name;
                $userlogs->role = Role::where('id', auth()->user()->userlevel)->first()->name;
                $userlogs->activity = "USER SUCCESSFULLY ADDED SALES INVOICE ($request->sales_invoice).";
                $userlogs->save();

                return $status;
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
            $imagick->unsharpMaskImage(0, 0.5, 2, 0.05);
            $imagePath = storage_path("app/public/$request->collection_receipt.jpeg");
            $imagick->writeImage($imagePath);
            $text = (new TesseractOCR($imagePath))->run();

            $status = 'valid';
            if(stripos(str_replace(' ', '', $text), $request->collection_receipt) === false){
                $status = 'invalid';
            }
                $filename = $request->collection_receipt.'.'.$fileExtension;
                $file->storeAs('public/collection_receipt/'.Carbon::now()->format('Y-m-d'),$filename);

                CollectionReceipt::create([
                    'collection_receipt' => $request->collection_receipt,
                    'company' => $request->company,
                    'client_name' => strtoupper($request->client_name),
                    'branch_name' => strtoupper($request->branch_name),
                    'date_created' => $request->date_created,
                    'sales_order' => strtoupper($request->sales_order),
                    'sales_invoice' => strtoupper($request->sales_invoice),
                    'pdf_file' => $filename,
                    'status' => $status
                ]);

                $userlogs = new UserLogs;
                $userlogs->username = auth()->user()->name;
                $userlogs->role = Role::where('id', auth()->user()->userlevel)->first()->name;
                $userlogs->activity = "USER SUCCESSFULLY ADDED COLLECTION RECEIPT ($request->collection_receipt).";
                $userlogs->save();

                return $status;
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
            $imagick->unsharpMaskImage(0, 0.5, 2, 0.05);
            $imagePath = storage_path("app/public/$request->billing_statement.jpeg");
            $imagick->writeImage($imagePath);
            $text = (new TesseractOCR($imagePath))->run();

            $status = 'valid';

            if(stripos(str_replace(' ', '', $text), $request->billing_statement) === false){
                $status = 'invalid';
            }
                $filename = $request->billing_statement.'.'.$fileExtension;
                $file->storeAs('public/billing_statement/'.Carbon::now()->format('Y-m-d'), $filename);

                BillingStatement::create([
                    'billing_statement' => $request->billing_statement,
                    'company' => $request->company,
                    'client_name' => strtoupper($request->client_name),
                    'branch_name' => strtoupper($request->branch_name),
                    'date_created' => $request->date_created,
                    'sales_order' => strtoupper($request->sales_order),
                    'purchase_order' => strtoupper($request->purchase_order),
                    'pdf_file' => $filename,
                    'status' => $status
                ]);

                $userlogs = new UserLogs;
                $userlogs->username = auth()->user()->name;
                $userlogs->role = Role::where('id', auth()->user()->userlevel)->first()->name;
                $userlogs->activity = "USER SUCCESSFULLY ADDED BILLING STATEMENT ($request->billing_statement).";
                $userlogs->save();

                return $status;

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
            $imagick->modulateImage(150,100,100);
            $imagick->contrastImage(1);
            $imagePath = storage_path("app/public/$request->official_receipt.jpeg");
            $imagick->writeImage($imagePath);
            $text = (new TesseractOCR($imagePath))->run();

            $status = 'valid';
            if(stripos(str_replace(' ', '', $text), $request->official_receipt) === false){
                $status = 'invalid';
            }
                $filename = $request->official_receipt.'.'.$fileExtension;
                $file->storeAs('public/official_receipt/'.Carbon::now()->format('Y-m-d'),$filename);

                OfficialReceipt::create([
                    'official_receipt' => $request->official_receipt,
                    'company' => $request->company,
                    'client_name' => strtoupper($request->client_name),
                    'branch_name' => strtoupper($request->branch_name),
                    'date_created' => $request->date_created,
                    'sales_order' => $request->sales_order,
                    'pdf_file' => $filename,
                    'status' => $status
                ]);

                $userlogs = new UserLogs;
                $userlogs->username = auth()->user()->name;
                $userlogs->role = Role::where('id', auth()->user()->userlevel)->first()->name;
                $userlogs->activity = "USER SUCCESSFULLY ADDED OFFICIAL RECEIPT ($request->official_receipt).";
                $userlogs->save();

                return $status;
        }
        else{
            return 'Invalid file format';
        }
    }

    public function save_dr(Request $request){
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

            $status = 'valid';

            if(stripos(str_replace(' ', '', $text), $request->delivery_receipt) === false){
                $status = 'invalid';
            }
                $filename = $request->delivery_receipt.'.'.$fileExtension;
                $file->storeAs('public/delivery_receipt/'.Carbon::now()->format('Y-m-d'),$filename);

                DeliveryReceipt::create([
                    'delivery_receipt' => $request->delivery_receipt,
                    'company' => $request->company,
                    'client_name' => strtoupper($request->client_name),
                    'branch_name' => strtoupper($request->branch_name),
                    'date_created' => $request->date_created,
                    'date_received' => $request->date_received,
                    'purchase_order' => strtoupper($request->purchase_order),
                    'sales_order' => strtoupper($request->sales_order),
                    'pdf_file' => $filename,
                    'status' => $status
                ]);

                $userlogs = new UserLogs;
                $userlogs->username = auth()->user()->name;
                $userlogs->role = Role::where('id', auth()->user()->userlevel)->first()->name;
                $userlogs->activity = "USER SUCCESSFULLY ADDED DELIVERY RECEIPT ($request->delivery_receipt).";
                $userlogs->save();

                return $status;
        }
        else{
            return 'Invalid file format';
        }
    }

    public function edit_si(Request $request){
        $file = $request->file('pdf_file');
        if($file->getClientOriginalExtension() === 'pdf'){
            $fileExtension = $file->getClientOriginalExtension();
            $imagick = new \Imagick();
            $imagick->readImage($file->getPathname() . '[0]');
            $imagick->setImageFormat('jpeg');
            $imagick->unsharpMaskImage(0, 0.5, 1, 0.05);
            $imagePath = storage_path("app/public/$request->sales_invoice.jpeg");
            $imagick->writeImage($imagePath);
            $text = (new TesseractOCR($imagePath))->run();

            $status = 'valid';

            if(stripos(str_replace(' ', '', $text), $request->sales_invoice) === false){
                $status = 'invalid';
            }
                $filename = $request->sales_invoice.'.'.$fileExtension;
                $file->storeAs('public/sales_invoice/'.Carbon::now()->format('Y-m-d'), $filename);

                $current_page = 'SALES INVOICE';
                $reference_number = SalesInvoice::where('id', $request->entry_id)->first()->sales_invoice;
                $company_orig = SalesInvoice::where('id', $request->entry_id)->first()->company;
                $client_name_orig = SalesInvoice::where('id', $request->entry_id)->first()->client_name;
                $branch_name_orig = SalesInvoice::where('id', $request->entry_id)->first()->branch_name;
                $date_created_orig = SalesInvoice::where('id', $request->entry_id)->first()->date_created;
                $date_received_orig = SalesInvoice::where('id', $request->entry_id)->first()->date_received;
                $purchase_order_orig = SalesInvoice::where('id', $request->entry_id)->first()->purchase_order;
                $sales_order_orig = SalesInvoice::where('id', $request->entry_id)->first()->sales_order;
                $delivery_receipt_orig = SalesInvoice::where('id', $request->entry_id)->first()->delivery_receipt;

                if($request->sales_invoice != $reference_number){
                    $sales_invoice_new = $request->sales_invoice;
                    $sales_invoice_change = "【SALES INVOICE: FROM '$reference_number' TO '$sales_invoice_new'】";
                }
                else{
                    $sales_invoice_change = NULL;
                }

                if($request->company != $company_orig){
                    $company_new = $request->company;
                    $company_change = "【COMPANY: FROM '$company_orig' TO '$company_new'】";
                }
                else{
                    $company_change = NULL;
                }

                if(strtoupper($request->client_name) != $client_name_orig){
                    $client_name_new = strtoupper($request->client_name);
                    $client_name_change = "【CLIENT NAME: FROM '$client_name_orig' TO '$client_name_new'】";
                }
                else{
                    $client_name_change = NULL;
                }

                if(strtoupper($request->branch_name) != $branch_name_orig){
                    $branch_name_new = strtoupper($request->branch_name);
                    $branch_name_change = "【BRANCH NAME: FROM '$branch_name_orig' TO '$branch_name_new'】";
                }
                else{
                    $branch_name_change = NULL;
                }

                if($request->date_created != $date_created_orig){
                    $date_created1 = Carbon::parse($date_created_orig)->format('F d, Y');
                    $date_created2 = Carbon::parse($request->date_created)->format('F d, Y');
                    $date_created_change = "【DATE CREATED: FROM '$date_created1' TO '$date_created2'】";
                }
                else{
                    $date_created_change = NULL;
                }

                if($request->date_received != $date_received_orig){
                    $date_received1 = Carbon::parse($date_received_orig)->format('F d, Y');
                    $date_received2 = Carbon::parse($request->date_received)->format('F d, Y');
                    $date_received_change = "【DATE RECEIVED: FROM '$date_received1' TO '$date_received2'】";
                }
                else{
                    $date_received_change = NULL;
                }

                if($request->purchase_order != $purchase_order_orig){
                    $purchase_order_new = $request->purchase_order;
                    $purchase_order_change = "【PURCHASE ORDER: FROM '$purchase_order_orig' TO '$purchase_order_new'】";
                }
                else{
                    $purchase_order_change = NULL;
                }

                if($request->sales_order != $sales_order_orig){
                    $sales_order_new = $request->sales_order;
                    $sales_order_change = "【SALES ORDER: FROM '$sales_order_orig' TO '$sales_order_new'】";
                }
                else{
                    $sales_order_change = NULL;
                }

                if($request->delivery_receipt != $delivery_receipt_orig){
                    $delivery_receipt_new = $request->delivery_receipt;
                    $delivery_receipt_change = "【DELIVERY RECEIPT: FROM '$delivery_receipt_orig' TO '$sales_order_new'】";
                }
                else{
                    $delivery_receipt_change = NULL;
                }

                if(auth()->user()->userlevel == '1'){
                    $status = 'valid';
                }
                else{
                    if(SalesInvoice::where('id', $request->entry_id)->first()->status == 'valid'){
                        $status = 'valid';
                    }
                    else{
                        $status = 'invalid';
                    }
                }

                $sql = SalesInvoice::where('id', $request->entry_id)
                            ->update([
                            'sales_invoice' => strtoupper($request->sales_invoice),
                            'company' => $request->company,
                            'client_name' => strtoupper($request->client_name),
                            'branch_name' => strtoupper($request->branch_name),
                            'date_created' => $request->date_created,
                            'date_received' => $request->date_received,
                            'purchase_order' => strtoupper($request->purchase_order),
                            'sales_order' => strtoupper($request->sales_order),
                            'delivery_receipt' => strtoupper($request->delivery_receipt),
                            'pdf_file' => $filename,
                            'status' => $status
                ]);

                $userlogs = new UserLogs;
                $userlogs->username = auth()->user()->name;
                $userlogs->role = Role::where('id', auth()->user()->userlevel)->first()->name;
                $userlogs->activity = "USER SUCCESSFULLY UPDATED $current_page ($reference_number) with the following changes: $sales_invoice_change $company_change $client_name_change $branch_name_change $date_created_change $date_received_change $purchase_order_change $sales_order_change $delivery_receipt_change.";
                $userlogs->save();

                return $status;
        }
        else{
            return 'Invalid file format';
        }
    }

    public function edit_cr(Request $request){
        $file = $request->file('pdf_file');
        if($file->getClientOriginalExtension() === 'pdf'){
            $fileExtension = $file->getClientOriginalExtension();
            $imagick = new \Imagick();
            $imagick->readImage($file->getPathname() . '[0]');
            $imagick->setImageFormat('jpeg');
            $imagick->unsharpMaskImage(0, 0.5, 2, 0.05);
            $imagePath = storage_path("app/public/$request->collection_receipt.jpeg");
            $imagick->writeImage($imagePath);
            $text = (new TesseractOCR($imagePath))->run();

            $status = 'valid';
            if(stripos(str_replace(' ', '', $text), $request->collection_receipt) === false){
                $status = 'invalid';
            }
                $filename = $request->collection_receipt.'.'.$fileExtension;
                $file->storeAs('public/collection_receipt/'.Carbon::now()->format('Y-m-d'),$filename);

                $current_page = 'COLLECTION RECEIPT';
                $reference_number = CollectionReceipt::where('id', $request->entry_id)->first()->collection_receipt;
                $company_orig = CollectionReceipt::where('id', $request->entry_id)->first()->company;
                $client_name_orig = CollectionReceipt::where('id', $request->entry_id)->first()->client_name;
                $branch_name_orig = CollectionReceipt::where('id', $request->entry_id)->first()->branch_name;
                $date_created_orig = CollectionReceipt::where('id', $request->entry_id)->first()->date_created;
                $sales_order_orig = CollectionReceipt::where('id', $request->entry_id)->first()->sales_order;
                $sales_invoice_orig = CollectionReceipt::where('id', $request->entry_id)->first()->sales_invoice;

                if($request->collection_receipt != $reference_number){
                    $collection_receipt_new = $request->collection_receipt;
                    $collection_receipt_change = "【COLLECTION RECEIPT: FROM '$reference_number' TO '$collection_receipt_new'】";
                }
                else{
                    $collection_receipt_change = NULL;
                }

                if($request->company != $company_orig){
                    $company_new = $request->company;
                    $company_change = "【COMPANY: FROM '$company_orig' TO '$company_new'】";
                }
                else{
                    $company_change = NULL;
                }

                if(strtoupper($request->client_name) != $client_name_orig){
                    $client_name_new = strtoupper($request->client_name);
                    $client_name_change = "【CLIENT NAME: FROM '$client_name_orig' TO '$client_name_new'】";
                }
                else{
                    $client_name_change = NULL;
                }

                if(strtoupper($request->branch_name) != $branch_name_orig){
                    $branch_name_new = strtoupper($request->branch_name);
                    $branch_name_change = "【BRANCH NAME: FROM '$branch_name_orig' TO '$branch_name_new'】";
                }
                else{
                    $branch_name_change = NULL;
                }

                if($request->date_created != $date_created_orig){
                    $date_created1 = Carbon::parse($date_created_orig)->format('F d, Y');
                    $date_created2 = Carbon::parse($request->date_created)->format('F d, Y');
                    $date_created_change = "【DATE CREATED: FROM '$date_created1' TO '$date_created2'】";
                }
                else{
                    $date_created_change = NULL;
                }

                if($request->sales_order != $sales_order_orig){
                    $sales_order_new = $request->sales_order;
                    $sales_order_change = "【SALES ORDER: FROM '$sales_order_orig' TO '$sales_order_new'】";
                }
                else{
                    $sales_order_change = NULL;
                }

                if($request->sales_invoice != $sales_invoice_orig){
                    $sales_invoice_new = $request->delivery_receipt;
                    $sales_invoice_change = "【SALES INVOICE: FROM '$sales_invoice_orig' TO '$sales_invoice_new'】";
                }
                else{
                    $sales_invoice_change = NULL;
                }

                if(auth()->user()->userlevel == '1'){
                    $status = 'valid';
                }
                else{
                    if(CollectionReceipt::where('id', $request->entry_id)->first()->status == 'valid'){
                        $status = 'valid';
                    }
                    else{
                        $status = 'invalid';
                    }
                }

                $sql = CollectionReceipt::where('id', $request->entry_id)
                        ->update([
                        'collection_receipt' => $request->collection_receipt,
                        'company' => $request->company,
                        'client_name' => strtoupper($request->client_name),
                        'branch_name' => strtoupper($request->branch_name),
                        'date_created' => $request->date_created,
                        'sales_order' => strtoupper($request->sales_order),
                        'sales_invoice' => strtoupper($request->sales_invoice),
                        'pdf_file' => $filename,
                        'status' => $status
                ]);

                $userlogs = new UserLogs;
                $userlogs->username = auth()->user()->name;
                $userlogs->role = Role::where('id', auth()->user()->userlevel)->first()->name;
                $userlogs->activity = "USER SUCCESSFULLY UPDATED $current_page ($reference_number) with the following changes: $collection_receipt_change $company_change $client_name_change $branch_name_change $date_created_change $sales_order_change.";
                $userlogs->save();

                return $status;
        }
        else{
            return 'Invalid file format';
        }
    }

    public function edit_bs(Request $request){
        $file = $request->file('pdf_file');
        if($file->getClientOriginalExtension() === 'pdf'){
            $fileExtension = $file->getClientOriginalExtension();
            $imagick = new \Imagick();
            $imagick->readImage($file->getPathname() . '[0]');
            $imagick->setImageFormat('jpeg');
            $imagick->unsharpMaskImage(0, 0.5, 2, 0.05);
            $imagePath = storage_path("app/public/$request->billing_statement.jpeg");
            $imagick->writeImage($imagePath);
            $text = (new TesseractOCR($imagePath))->run();

            $status = 'valid';

            if(stripos(str_replace(' ', '', $text), $request->billing_statement) === false){
                $status = 'invalid';
            }
                $filename = $request->billing_statement.'.'.$fileExtension;
                $file->storeAs('public/billing_statement/'.Carbon::now()->format('Y-m-d'), $filename);

                $current_page = 'BILLING STATEMENT';
                $reference_number = BillingStatement::where('id', $request->entry_id)->first()->billing_statement;
                $company_orig = BillingStatement::where('id', $request->entry_id)->first()->company;
                $client_name_orig = BillingStatement::where('id', $request->entry_id)->first()->client_name;
                $branch_name_orig = BillingStatement::where('id', $request->entry_id)->first()->branch_name;
                $date_created_orig = BillingStatement::where('id', $request->entry_id)->first()->date_created;
                $sales_order_orig = BillingStatement::where('id', $request->entry_id)->first()->sales_order;
                $purchase_order_orig = BillingStatement::where('id', $request->entry_id)->first()->purchase_order;

                if($request->billing_statement != $reference_number){
                    $billing_statement_new = $request->billing_statement;
                    $billing_statement_change = "【BILLING STATEMENT: FROM '$reference_number' TO '$billing_statement_new'】";
                }
                else{
                    $billing_statement_change = NULL;
                }

                if($request->company != $company_orig){
                    $company_new = $request->company;
                    $company_change = "【COMPANY: FROM '$company_orig' TO '$company_new'】";
                }
                else{
                    $company_change = NULL;
                }

                if(strtoupper($request->client_name) != $client_name_orig){
                    $client_name_new = strtoupper($request->client_name);
                    $client_name_change = "【CLIENT NAME: FROM '$client_name_orig' TO '$client_name_new'】";
                }
                else{
                    $client_name_change = NULL;
                }

                if(strtoupper($request->branch_name) != $branch_name_orig){
                    $branch_name_new = strtoupper($request->branch_name);
                    $branch_name_change = "【BRANCH NAME: FROM '$branch_name_orig' TO '$branch_name_new'】";
                }
                else{
                    $branch_name_change = NULL;
                }

                if($request->date_created != $date_created_orig){
                    $date_created1 = Carbon::parse($date_created_orig)->format('F d, Y');
                    $date_created2 = Carbon::parse($request->date_created)->format('F d, Y');
                    $date_created_change = "【DATE CREATED: FROM '$date_created1' TO '$date_created2'】";
                }
                else{
                    $date_created_change = NULL;
                }

                if($request->sales_order != $sales_order_orig){
                    $sales_order_new = $request->sales_order;
                    $sales_order_change = "【SALES ORDER: FROM '$sales_order_orig' TO '$sales_order_new'】";
                }
                else{
                    $sales_order_change = NULL;
                }

                if($request->purchase_order != $purchase_order_orig){
                    $purchase_order_new = $request->purchase_order;
                    $purchase_order_change = "【PURCHASE ORDER: FROM '$purchase_order_orig' TO '$purchase_order_new'】";
                }
                else{
                    $purchase_order_change = NULL;
                }

                if(auth()->user()->userlevel == '1'){
                    $status = 'valid';
                }
                else{
                    if(BillingStatement::where('id', $request->entry_id)->first()->status == 'valid'){
                        $status = 'valid';
                    }
                    else{
                        $status = 'invalid';
                    }
                }

                $sql = BillingStatement::where('id', $request->entry_id)
                            ->update([
                            'billing_statement' => $request->billing_statement,
                            'company' => $request->company,
                            'client_name' => strtoupper($request->client_name),
                            'branch_name' => strtoupper($request->branch_name),
                            'date_created' => $request->date_created,
                            'sales_order' => $request->sales_order,
                            'purchase_order' => $request->purchase_order,
                            'pdf_file' => $filename,
                            'status' => $status
                ]);

                $userlogs = new UserLogs;
                $userlogs->username = auth()->user()->name;
                $userlogs->role = Role::where('id', auth()->user()->userlevel)->first()->name;
                $userlogs->activity = "USER SUCCESSFULLY UPDATED $current_page ($reference_number) with the following changes: $billing_statement_change $company_change $client_name_change $branch_name_change $date_created_change $sales_order_change $purchase_order_change.";
                $userlogs->save();

                return $status;

        }
        else{
            return 'Invalid file format';
        }
    }

    public function edit_or(Request $request){
        $file = $request->file('pdf_file');
        if($file->getClientOriginalExtension() === 'pdf'){
            $fileExtension = $file->getClientOriginalExtension();
            $imagick = new \Imagick();
            $imagick->readImage($file->getPathname() . '[0]');
            $imagick->setImageFormat('jpeg');
            $imagick->modulateImage(150,100,100);
            $imagick->contrastImage(1);
            $imagePath = storage_path("app/public/$request->official_receipt.jpeg");
            $imagick->writeImage($imagePath);
            $text = (new TesseractOCR($imagePath))->run();

            $status = 'valid';
            if(stripos(str_replace(' ', '', $text), $request->official_receipt) === false){
                $status = 'invalid';
            }
                $filename = $request->official_receipt.'.'.$fileExtension;
                $file->storeAs('public/official_receipt/'.Carbon::now()->format('Y-m-d'),$filename);

                $current_page = 'OFFICIAL RECEIPT';
                $reference_number = OfficialReceipt::where('id', $request->entry_id)->first()->official_receipt;
                $company_orig = OfficialReceipt::where('id', $request->entry_id)->first()->company;
                $client_name_orig = OfficialReceipt::where('id', $request->entry_id)->first()->client_name;
                $branch_name_orig = OfficialReceipt::where('id', $request->entry_id)->first()->branch_name;
                $date_created_orig = OfficialReceipt::where('id', $request->entry_id)->first()->date_created;
                $sales_order_orig = OfficialReceipt::where('id', $request->entry_id)->first()->sales_order;

                if($request->official_receipt != $reference_number){
                    $official_receipt_new = $request->official_receipt;
                    $official_receipt_change = "【OFFICIAL RECEIPT: FROM '$reference_number' TO '$official_receipt_new'】";
                }
                else{
                    $official_receipt_change = NULL;
                }

                if($request->company != $company_orig){
                    $company_new = $request->company;
                    $company_change = "【COMPANY: FROM '$company_orig' TO '$company_new'】";
                }
                else{
                    $company_change = NULL;
                }

                if(strtoupper($request->client_name) != $client_name_orig){
                    $client_name_new = strtoupper($request->client_name);
                    $client_name_change = "【CLIENT NAME: FROM '$client_name_orig' TO '$client_name_new'】";
                }
                else{
                    $client_name_change = NULL;
                }

                if(strtoupper($request->branch_name) != $branch_name_orig){
                    $branch_name_new = strtoupper($request->branch_name);
                    $branch_name_change = "【BRANCH NAME: FROM '$branch_name_orig' TO '$branch_name_new'】";
                }
                else{
                    $branch_name_change = NULL;
                }

                if($request->date_created != $date_created_orig){
                    $date_created1 = Carbon::parse($date_created_orig)->format('F d, Y');
                    $date_created2 = Carbon::parse($request->date_created)->format('F d, Y');
                    $date_created_change = "【DATE CREATED: FROM '$date_created1' TO '$date_created2'】";
                }
                else{
                    $date_created_change = NULL;
                }

                if($request->sales_order != $sales_order_orig){
                    $sales_order_new = $request->sales_order;
                    $sales_order_change = "【SALES ORDER: FROM '$sales_order_orig' TO '$sales_order_new'】";
                }
                else{
                    $sales_order_change = NULL;
                }

                if(auth()->user()->userlevel == '1'){
                    $status = 'valid';
                }
                else{
                    if(OfficialReceipt::where('id', $request->entry_id)->first()->status == 'valid'){
                        $status = 'valid';
                    }
                    else{
                        $status = 'invalid';
                    }
                }

                $sql = OfficialReceipt::where('id', $request->entry_id)
                        ->update([
                        'official_receipt' => $request->official_receipt,
                        'company' => $request->company,
                        'client_name' => strtoupper($request->client_name),
                        'branch_name' => strtoupper($request->branch_name),
                        'date_created' => $request->date_created,
                        'sales_order' => $request->sales_order,
                        'pdf_file' => $filename,
                        'status' => $status
                ]);

                $userlogs = new UserLogs;
                $userlogs->username = auth()->user()->name;
                $userlogs->role = Role::where('id', auth()->user()->userlevel)->first()->name;
                $userlogs->activity = "USER SUCCESSFULLY UPDATED $current_page ($reference_number) with the following changes: $official_receipt_change $company_change $client_name_change $branch_name_change $date_created_change $sales_order_change.";
                $userlogs->save();

                return $status;
        }
        else{
            return 'Invalid file format';
        }
    }

    public function edit_dr(Request $request){
        $file = $request->file('pdf_file');
        if($file->getClientOriginalExtension() === 'pdf'){
            $fileExtension = $file->getClientOriginalExtension();
            $imagick = new \Imagick();
            $imagick->readImage($file->getPathname() . '[0]');
            $imagick->setImageFormat('jpeg');
            $imagePath = storage_path("app/public/$request->delivery_receipt.jpeg");
            $imagick->writeImage($imagePath);
            $text = (new TesseractOCR($imagePath))->run();

            $status = 'valid';

            if(stripos(str_replace(' ', '', $text), $request->delivery_receipt) === false){
                $status = 'invalid';
            }
                $filename = $request->delivery_receipt.'.'.$fileExtension;
                $file->storeAs('public/delivery_receipt/'.Carbon::now()->format('Y-m-d'),$filename);

                $current_page = 'DELIVERY RECEIPT';
                $reference_number = DeliveryReceipt::where('id', $request->entry_id)->first()->delivery_receipt;
                $company_orig = DeliveryReceipt::where('id', $request->entry_id)->first()->company;
                $client_name_orig = DeliveryReceipt::where('id', $request->entry_id)->first()->client_name;
                $branch_name_orig = DeliveryReceipt::where('id', $request->entry_id)->first()->branch_name;
                $date_created_orig = DeliveryReceipt::where('id', $request->entry_id)->first()->date_created;
                $date_received_orig = DeliveryReceipt::where('id', $request->entry_id)->first()->date_received;
                $purchase_order_orig = DeliveryReceipt::where('id', $request->entry_id)->first()->purchase_order;
                $sales_order_orig = DeliveryReceipt::where('id', $request->entry_id)->first()->sales_order;

                if($request->delivery_receipt != $reference_number){
                    $delivery_receipt_new = $request->delivery_receipt;
                    $delivery_receipt_change = "【DELIVERY RECEIPT: FROM '$reference_number' TO '$delivery_receipt_new'】";
                }
                else{
                    $delivery_receipt_change = NULL;
                }

                if($request->company != $company_orig){
                    $company_new = $request->company;
                    $company_change = "【COMPANY: FROM '$company_orig' TO '$company_new'】";
                }
                else{
                    $company_change = NULL;
                }

                if(strtoupper($request->client_name) != $client_name_orig){
                    $client_name_new = strtoupper($request->client_name);
                    $client_name_change = "【CLIENT NAME: FROM '$client_name_orig' TO '$client_name_new'】";
                }
                else{
                    $client_name_change = NULL;
                }

                if(strtoupper($request->branch_name) != $branch_name_orig){
                    $branch_name_new = strtoupper($request->branch_name);
                    $branch_name_change = "【BRANCH NAME: FROM '$branch_name_orig' TO '$branch_name_new'】";
                }
                else{
                    $branch_name_change = NULL;
                }

                if($request->date_created != $date_created_orig){
                    $date_created1 = Carbon::parse($date_created_orig)->format('F d, Y');
                    $date_created2 = Carbon::parse($request->date_created)->format('F d, Y');
                    $date_created_change = "【DATE CREATED: FROM '$date_created1' TO '$date_created2'】";
                }
                else{
                    $date_created_change = NULL;
                }

                if($request->date_received != $date_received_orig){
                    $date_received1 = Carbon::parse($date_received_orig)->format('F d, Y');
                    $date_received2 = Carbon::parse($request->date_received)->format('F d, Y');
                    $date_received_change = "【DATE RECEIVED: FROM '$date_received1' TO '$date_received2'】";
                }
                else{
                    $date_received_change = NULL;
                }

                if($request->purchase_order != $purchase_order_orig){
                    $purchase_order_new = $request->purchase_order;
                    $purchase_order_change = "【PURCHASE ORDER: FROM '$purchase_order_orig' TO '$purchase_order_new'】";
                }
                else{
                    $purchase_order_change = NULL;
                }

                if($request->sales_order != $sales_order_orig){
                    $sales_order_new = $request->sales_order;
                    $sales_order_change = "【SALES ORDER: FROM '$sales_order_orig' TO '$sales_order_new'】";
                }
                else{
                    $sales_order_change = NULL;
                }

                if(auth()->user()->userlevel == '1'){
                    $status = 'valid';
                }
                else{
                    if(DeliveryReceipt::where('id', $request->entry_id)->first()->status == 'valid'){
                        $status = 'valid';
                    }
                    else{
                        $status = 'invalid';
                    }
                }

                $sql = DeliveryReceipt::where('id', $request->entry_id)
                        ->update([
                        'delivery_receipt' => $request->delivery_receipt,
                        'client_name' => strtoupper($request->client_name),
                        'branch_name' => strtoupper($request->branch_name),
                        'date_created' => $request->date_created,
                        'purchase_order' => $request->purchase_order,
                        'sales_order' => $request->sales_order,
                        'pdf_file' => $filename,
                        'status' => $status
                ]);

                $userlogs = new UserLogs;
                $userlogs->username = auth()->user()->name;
                $userlogs->role = Role::where('id', auth()->user()->userlevel)->first()->name;
                $userlogs->activity = "USER SUCCESSFULLY UPDATED $current_page ($reference_number) with the following changes: $delivery_receipt_change $company_change $client_name_change $branch_name_change $date_created_change $sales_order_change $purchase_order_change.";
                $userlogs->save();

                return $status;
        }
        else{
            return 'Invalid file format';
        }
    }

    public function edit(Request $request){
        if($request->current_page == 'si'){
            $current_page = 'SALES INVOICE';
            $reference_number = SalesInvoice::where('id', $request->entry_id)->first()->sales_invoice;
            $company_orig = SalesInvoice::where('id', $request->entry_id)->first()->company;
            $client_name_orig = SalesInvoice::where('id', $request->entry_id)->first()->client_name;
            $branch_name_orig = SalesInvoice::where('id', $request->entry_id)->first()->branch_name;
            $date_created_orig = SalesInvoice::where('id', $request->entry_id)->first()->date_created;
            $date_received_orig = SalesInvoice::where('id', $request->entry_id)->first()->date_received;
            $purchase_order_orig = SalesInvoice::where('id', $request->entry_id)->first()->purchase_order;
            $sales_order_orig = SalesInvoice::where('id', $request->entry_id)->first()->sales_order;
            $delivery_receipt_orig = SalesInvoice::where('id', $request->entry_id)->first()->delivery_receipt;

            if($request->sales_invoice != $reference_number){
                $sales_invoice_new = $request->sales_invoice;
                $sales_invoice_change = "【SALES INVOICE: FROM '$reference_number' TO '$sales_invoice_new'】";
            }
            else{
                $sales_invoice_change = NULL;
            }

            if($request->company != $company_orig){
                $company_new = $request->company;
                $company_change = "【COMPANY: FROM '$company_orig' TO '$company_new'】";
            }
            else{
                $company_change = NULL;
            }

            if(strtoupper($request->client_name) != $client_name_orig){
                $client_name_new = strtoupper($request->client_name);
                $client_name_change = "【CLIENT NAME: FROM '$client_name_orig' TO '$client_name_new'】";
            }
            else{
                $client_name_change = NULL;
            }

            if(strtoupper($request->branch_name) != $branch_name_orig){
                $branch_name_new = strtoupper($request->branch_name);
                $branch_name_change = "【BRANCH NAME: FROM '$branch_name_orig' TO '$branch_name_new'】";
            }
            else{
                $branch_name_change = NULL;
            }

            if($request->date_created != $date_created_orig){
                $date_created1 = Carbon::parse($date_created_orig)->format('F d, Y');
                $date_created2 = Carbon::parse($request->date_created)->format('F d, Y');
                $date_created_change = "【DATE CREATED: FROM '$date_created1' TO '$date_created2'】";
            }
            else{
                $date_created_change = NULL;
            }

            if($request->date_received != $date_received_orig){
                $date_received1 = Carbon::parse($date_received_orig)->format('F d, Y');
                $date_received2 = Carbon::parse($request->date_received)->format('F d, Y');
                $date_received_change = "【DATE RECEIVED: FROM '$date_received1' TO '$date_received2'】";
            }
            else{
                $date_received_change = NULL;
            }

            if($request->purchase_order != $purchase_order_orig){
                $purchase_order_new = $request->purchase_order;
                $purchase_order_change = "【PURCHASE ORDER: FROM '$purchase_order_orig' TO '$purchase_order_new'】";
            }
            else{
                $purchase_order_change = NULL;
            }

            if($request->sales_order != $sales_order_orig){
                $sales_order_new = $request->sales_order;
                $sales_order_change = "【SALES ORDER: FROM '$sales_order_orig' TO '$sales_order_new'】";
            }
            else{
                $sales_order_change = NULL;
            }

            if($request->delivery_receipt != $delivery_receipt_orig){
                $delivery_receipt_new = $request->delivery_receipt;
                $delivery_receipt_change = "【DELIVERY RECEIPT: FROM '$delivery_receipt_orig' TO '$sales_order_new'】";
            }
            else{
                $delivery_receipt_change = NULL;
            }

            if($sales_invoice_change == NULL
                && $company_change == NULL
                && $client_name_change == NULL
                && $branch_name_change == NULL
                && $date_created_change == NULL
                && $date_received_change == NULL
                && $purchase_order_change == NULL
                && $sales_order_change == NULL
                && $delivery_receipt_change == NULL
                ){
                return 'no changes';
            }

            $sql = SalesInvoice::where('id', $request->entry_id)
                        ->update([
                        'sales_invoice' => strtoupper($request->sales_invoice),
                        'company' => $request->company,
                        'client_name' => strtoupper($request->client_name),
                        'branch_name' => strtoupper($request->branch_name),
                        'date_created' => $request->date_created,
                        'date_received' => $request->date_received,
                        'purchase_order' => strtoupper($request->purchase_order),
                        'sales_order' => strtoupper($request->sales_order),
                        'delivery_receipt' => strtoupper($request->delivery_receipt)
            ]);
        }

        if($request->current_page == 'cr'){
            $current_page = 'COLLECTION RECEIPT';
            $reference_number = CollectionReceipt::where('id', $request->entry_id)->first()->collection_receipt;
            $company_orig = CollectionReceipt::where('id', $request->entry_id)->first()->company;
            $client_name_orig = CollectionReceipt::where('id', $request->entry_id)->first()->client_name;
            $branch_name_orig = CollectionReceipt::where('id', $request->entry_id)->first()->branch_name;
            $date_created_orig = CollectionReceipt::where('id', $request->entry_id)->first()->date_created;
            $sales_order_orig = CollectionReceipt::where('id', $request->entry_id)->first()->sales_order;
            $sales_invoice_orig = CollectionReceipt::where('id', $request->entry_id)->first()->sales_invoice;

            if($request->collection_receipt != $reference_number){
                $collection_receipt_new = $request->collection_receipt;
                $collection_receipt_change = "【COLLECTION RECEIPT: FROM '$reference_number' TO '$collection_receipt_new'】";
            }
            else{
                $collection_receipt_change = NULL;
            }

            if($request->company != $company_orig){
                $company_new = $request->company;
                $company_change = "【COMPANY: FROM '$company_orig' TO '$company_new'】";
            }
            else{
                $company_change = NULL;
            }

            if(strtoupper($request->client_name) != $client_name_orig){
                $client_name_new = strtoupper($request->client_name);
                $client_name_change = "【CLIENT NAME: FROM '$client_name_orig' TO '$client_name_new'】";
            }
            else{
                $client_name_change = NULL;
            }

            if(strtoupper($request->branch_name) != $branch_name_orig){
                $branch_name_new = strtoupper($request->branch_name);
                $branch_name_change = "【BRANCH NAME: FROM '$branch_name_orig' TO '$branch_name_new'】";
            }
            else{
                $branch_name_change = NULL;
            }

            if($request->date_created != $date_created_orig){
                $date_created1 = Carbon::parse($date_created_orig)->format('F d, Y');
                $date_created2 = Carbon::parse($request->date_created)->format('F d, Y');
                $date_created_change = "【DATE CREATED: FROM '$date_created1' TO '$date_created2'】";
            }
            else{
                $date_created_change = NULL;
            }

            if($request->sales_order != $sales_order_orig){
                $sales_order_new = $request->sales_order;
                $sales_order_change = "【SALES ORDER: FROM '$sales_order_orig' TO '$sales_order_new'】";
            }
            else{
                $sales_order_change = NULL;
            }

            if($request->sales_invoice != $sales_invoice_orig){
                $sales_invoice_new = $request->delivery_receipt;
                $sales_invoice_change = "【SALES INVOICE: FROM '$sales_invoice_orig' TO '$sales_invoice_new'】";
            }
            else{
                $sales_invoice_change = NULL;
            }

            if($collection_receipt_change == NULL
                && $company_change == NULL
                && $client_name_change == NULL
                && $branch_name_change == NULL
                && $date_created_change == NULL
                && $sales_order_change == NULL
                && $sales_invoice_change == NULL
                ){
                return 'no changes';
            }

            $sql = CollectionReceipt::where('id', $request->entry_id)
                        ->update([
                        'collection_receipt' => $request->collection_receipt,
                        'company' => $request->company,
                        'client_name' => strtoupper($request->client_name),
                        'branch_name' => strtoupper($request->branch_name),
                        'date_created' => $request->date_created,
                        'sales_order' => strtoupper($request->sales_order),
                        'sales_invoice' => strtoupper($request->sales_invoice)
            ]);
        }

        if($request->current_page == 'bs'){
            $current_page = 'BILLING STATEMENT';
            $reference_number = BillingStatement::where('id', $request->entry_id)->first()->billing_statement;
            $company_orig = BillingStatement::where('id', $request->entry_id)->first()->company;
            $client_name_orig = BillingStatement::where('id', $request->entry_id)->first()->client_name;
            $branch_name_orig = BillingStatement::where('id', $request->entry_id)->first()->branch_name;
            $date_created_orig = BillingStatement::where('id', $request->entry_id)->first()->date_created;
            $sales_order_orig = BillingStatement::where('id', $request->entry_id)->first()->sales_order;
            $purchase_order_orig = BillingStatement::where('id', $request->entry_id)->first()->purchase_order;

            if($request->billing_statement != $reference_number){
                $billing_statement_new = $request->billing_statement;
                $billing_statement_change = "【BILLING STATEMENT: FROM '$reference_number' TO '$billing_statement_new'】";
            }
            else{
                $billing_statement_change = NULL;
            }

            if($request->company != $company_orig){
                $company_new = $request->company;
                $company_change = "【COMPANY: FROM '$company_orig' TO '$company_new'】";
            }
            else{
                $company_change = NULL;
            }

            if(strtoupper($request->client_name) != $client_name_orig){
                $client_name_new = strtoupper($request->client_name);
                $client_name_change = "【CLIENT NAME: FROM '$client_name_orig' TO '$client_name_new'】";
            }
            else{
                $client_name_change = NULL;
            }

            if(strtoupper($request->branch_name) != $branch_name_orig){
                $branch_name_new = strtoupper($request->branch_name);
                $branch_name_change = "【BRANCH NAME: FROM '$branch_name_orig' TO '$branch_name_new'】";
            }
            else{
                $branch_name_change = NULL;
            }

            if($request->date_created != $date_created_orig){
                $date_created1 = Carbon::parse($date_created_orig)->format('F d, Y');
                $date_created2 = Carbon::parse($request->date_created)->format('F d, Y');
                $date_created_change = "【DATE CREATED: FROM '$date_created1' TO '$date_created2'】";
            }
            else{
                $date_created_change = NULL;
            }

            if($request->sales_order != $sales_order_orig){
                $sales_order_new = $request->sales_order;
                $sales_order_change = "【SALES ORDER: FROM '$sales_order_orig' TO '$sales_order_new'】";
            }
            else{
                $sales_order_change = NULL;
            }

            if($request->purchase_order != $purchase_order_orig){
                $purchase_order_new = $request->purchase_order;
                $purchase_order_change = "【PURCHASE ORDER: FROM '$purchase_order_orig' TO '$purchase_order_new'】";
            }
            else{
                $purchase_order_change = NULL;
            }

            if($billing_statement_change == NULL
                && $company_change == NULL
                && $client_name_change == NULL
                && $branch_name_change == NULL
                && $date_created_change == NULL
                && $sales_order_change == NULL
                && $purchase_order_change == NULL
                ){
                return 'no changes';
            }

            $sql = BillingStatement::where('id', $request->entry_id)
                        ->update([
                        'billing_statement' => $request->billing_statement,
                        'company' => $request->company,
                        'client_name' => strtoupper($request->client_name),
                        'branch_name' => strtoupper($request->branch_name),
                        'date_created' => $request->date_created,
                        'sales_order' => $request->sales_order,
                        'purchase_order' => $request->purchase_order
            ]);
        }

        if($request->current_page == 'or'){
            $current_page = 'OFFICIAL RECEIPT';
            $reference_number = OfficialReceipt::where('id', $request->entry_id)->first()->official_receipt;
            $company_orig = OfficialReceipt::where('id', $request->entry_id)->first()->company;
            $client_name_orig = OfficialReceipt::where('id', $request->entry_id)->first()->client_name;
            $branch_name_orig = OfficialReceipt::where('id', $request->entry_id)->first()->branch_name;
            $date_created_orig = OfficialReceipt::where('id', $request->entry_id)->first()->date_created;
            $sales_order_orig = OfficialReceipt::where('id', $request->entry_id)->first()->sales_order;

            if($request->official_receipt != $reference_number){
                $official_receipt_new = $request->official_receipt;
                $official_receipt_change = "【OFFICIAL RECEIPT: FROM '$reference_number' TO '$official_receipt_new'】";
            }
            else{
                $official_receipt_change = NULL;
            }

            if($request->company != $company_orig){
                $company_new = $request->company;
                $company_change = "【COMPANY: FROM '$company_orig' TO '$company_new'】";
            }
            else{
                $company_change = NULL;
            }

            if(strtoupper($request->client_name) != $client_name_orig){
                $client_name_new = strtoupper($request->client_name);
                $client_name_change = "【CLIENT NAME: FROM '$client_name_orig' TO '$client_name_new'】";
            }
            else{
                $client_name_change = NULL;
            }

            if(strtoupper($request->branch_name) != $branch_name_orig){
                $branch_name_new = strtoupper($request->branch_name);
                $branch_name_change = "【BRANCH NAME: FROM '$branch_name_orig' TO '$branch_name_new'】";
            }
            else{
                $branch_name_change = NULL;
            }

            if($request->date_created != $date_created_orig){
                $date_created1 = Carbon::parse($date_created_orig)->format('F d, Y');
                $date_created2 = Carbon::parse($request->date_created)->format('F d, Y');
                $date_created_change = "【DATE CREATED: FROM '$date_created1' TO '$date_created2'】";
            }
            else{
                $date_created_change = NULL;
            }

            if($request->sales_order != $sales_order_orig){
                $sales_order_new = $request->sales_order;
                $sales_order_change = "【SALES ORDER: FROM '$sales_order_orig' TO '$sales_order_new'】";
            }
            else{
                $sales_order_change = NULL;
            }

            if($official_receipt_change == NULL
                && $company_change == NULL
                && $client_name_change == NULL
                && $branch_name_change == NULL
                && $date_created_change == NULL
                && $sales_order_change == NULL
                ){
                return 'no changes';
            }

            $sql = OfficialReceipt::where('id', $request->entry_id)
                        ->update([
                        'official_receipt' => $request->official_receipt,
                        'company' => $request->company,
                        'client_name' => strtoupper($request->client_name),
                        'branch_name' => strtoupper($request->branch_name),
                        'date_created' => $request->date_created,
                        'sales_order' => $request->sales_order
            ]);
        }

        if($request->current_page == 'dr'){
            $current_page = 'DELIVERY RECEIPT';
            $reference_number = DeliveryReceipt::where('id', $request->entry_id)->first()->delivery_receipt;
            $company_orig = DeliveryReceipt::where('id', $request->entry_id)->first()->company;
            $client_name_orig = DeliveryReceipt::where('id', $request->entry_id)->first()->client_name;
            $branch_name_orig = DeliveryReceipt::where('id', $request->entry_id)->first()->branch_name;
            $date_created_orig = DeliveryReceipt::where('id', $request->entry_id)->first()->date_created;
            $date_received_orig = DeliveryReceipt::where('id', $request->entry_id)->first()->date_received;
            $purchase_order_orig = DeliveryReceipt::where('id', $request->entry_id)->first()->purchase_order;
            $sales_order_orig = DeliveryReceipt::where('id', $request->entry_id)->first()->sales_order;

            if($request->delivery_receipt != $reference_number){
                $delivery_receipt_new = $request->delivery_receipt;
                $delivery_receipt_change = "【DELIVERY RECEIPT: FROM '$reference_number' TO '$delivery_receipt_new'】";
            }
            else{
                $delivery_receipt_change = NULL;
            }

            if($request->company != $company_orig){
                $company_new = $request->company;
                $company_change = "【COMPANY: FROM '$company_orig' TO '$company_new'】";
            }
            else{
                $company_change = NULL;
            }

            if(strtoupper($request->client_name) != $client_name_orig){
                $client_name_new = strtoupper($request->client_name);
                $client_name_change = "【CLIENT NAME: FROM '$client_name_orig' TO '$client_name_new'】";
            }
            else{
                $client_name_change = NULL;
            }

            if(strtoupper($request->branch_name) != $branch_name_orig){
                $branch_name_new = strtoupper($request->branch_name);
                $branch_name_change = "【BRANCH NAME: FROM '$branch_name_orig' TO '$branch_name_new'】";
            }
            else{
                $branch_name_change = NULL;
            }

            if($request->date_created != $date_created_orig){
                $date_created1 = Carbon::parse($date_created_orig)->format('F d, Y');
                $date_created2 = Carbon::parse($request->date_created)->format('F d, Y');
                $date_created_change = "【DATE CREATED: FROM '$date_created1' TO '$date_created2'】";
            }
            else{
                $date_created_change = NULL;
            }

            if($request->date_received != $date_received_orig){
                $date_received1 = Carbon::parse($date_received_orig)->format('F d, Y');
                $date_received2 = Carbon::parse($request->date_received)->format('F d, Y');
                $date_received_change = "【DATE RECEIVED: FROM '$date_received1' TO '$date_received2'】";
            }
            else{
                $date_received_change = NULL;
            }

            if($request->purchase_order != $purchase_order_orig){
                $purchase_order_new = $request->purchase_order;
                $purchase_order_change = "【PURCHASE ORDER: FROM '$purchase_order_orig' TO '$purchase_order_new'】";
            }
            else{
                $purchase_order_change = NULL;
            }

            if($request->sales_order != $sales_order_orig){
                $sales_order_new = $request->sales_order;
                $sales_order_change = "【SALES ORDER: FROM '$sales_order_orig' TO '$sales_order_new'】";
            }
            else{
                $sales_order_change = NULL;
            }

            if($delivery_receipt_change == NULL
                && $company_change == NULL
                && $client_name_change == NULL
                && $branch_name_change == NULL
                && $date_created_change == NULL
                && $date_received_change == NULL
                && $purchase_order_change == NULL
                && $sales_order_change == NULL
                ){
                return 'no changes';
            }

            $sql = DeliveryReceipt::where('id', $request->entry_id)
                        ->update([
                        'delivery_receipt' => $request->delivery_receipt,
                        'client_name' => strtoupper($request->client_name),
                        'branch_name' => strtoupper($request->branch_name),
                        'date_created' => $request->date_created,
                        'purchase_order' => $request->purchase_order,
                        'sales_order' => $request->sales_order
            ]);
        }

        $userlogs = new UserLogs;
        $userlogs->username = auth()->user()->name;
        $userlogs->role = Role::where('id', auth()->user()->userlevel)->first()->name;
        if($request->current_page == 'si'){
            $userlogs->activity = "USER SUCCESSFULLY UPDATED $current_page ($reference_number) with the following changes: $sales_invoice_change $company_change $client_name_change $branch_name_change $date_created_change $date_received_change $purchase_order_change $sales_order_change $delivery_receipt_change.";
        }
        if($request->current_page == 'cr'){
            $userlogs->activity = "USER SUCCESSFULLY UPDATED $current_page ($reference_number) with the following changes: $collection_receipt_change $company_change $client_name_change $branch_name_change $date_created_change $sales_order_change.";
        }
        if($request->current_page == 'bs'){
            $userlogs->activity = "USER SUCCESSFULLY UPDATED $current_page ($reference_number) with the following changes: $billing_statement_change $company_change $client_name_change $branch_name_change $date_created_change $sales_order_change $purchase_order_change.";
        }
        if($request->current_page == 'or'){
            $userlogs->activity = "USER SUCCESSFULLY UPDATED $current_page ($reference_number) with the following changes: $official_receipt_change $company_change $client_name_change $branch_name_change $date_created_change $sales_order_change.";
        }
        if($request->current_page == 'dr'){
            $userlogs->activity = "USER SUCCESSFULLY UPDATED $current_page ($reference_number) with the following changes: $delivery_receipt_change $company_change $client_name_change $branch_name_change $date_created_change $sales_order_change $purchase_order_change.";
        }
        $userlogs->save();

        return $sql ? 'true' : 'false';
    }

    public function approve(Request $request){
        if($request->current_page == 'bs'){
            $current_page = 'BILLING STATEMENT';
            $reference_number = BillingStatement::where('id', $request->entry_id)->first()->billing_statement;
            $sql = BillingStatement::where('id', $request->entry_id)->update(['status' => 'valid']);
        }

        if($request->current_page == 'si'){
            $current_page = 'SALES INVOICE';
            $reference_number = SalesInvoice::where('id', $request->entry_id)->first()->sales_invoice;
            $sql = SalesInvoice::where('id', $request->entry_id)->update(['status' => 'valid']);
        }

        if($request->current_page == 'cr'){
            $current_page = 'COLLECTION RECEIPT';
            $reference_number = CollectionReceipt::where('id', $request->entry_id)->first()->collection_receipt;
            $sql = CollectionReceipt::where('id', $request->entry_id)->update(['status' => 'valid']);
        }

        if($request->current_page == 'or'){
            $current_page = 'OFFICIAL RECEIPT';
            $reference_number = OfficialReceipt::where('id', $request->entry_id)->first()->official_receipt;
            $sql = OfficialReceipt::where('id', $request->entry_id)->update(['status' => 'valid']);
        }

        if($request->current_page == 'dr'){
            $current_page = 'DELIVERY RECEIPT';
            $reference_number = DeliveryReceipt::where('id', $request->entry_id)->first()->delivery_receipt;
            $sql = DeliveryReceipt::where('id', $request->entry_id)->update(['status' => 'valid']);
        }

        $userlogs = new UserLogs;
        $userlogs->username = auth()->user()->name;
        $userlogs->role = Role::where('id', auth()->user()->userlevel)->first()->name;
        $userlogs->activity = "USER SUCCESSFULLY APPROVED $current_page ($reference_number).";
        $userlogs->save();

        return $sql ? 'true' : 'false';
    }

    public function table_reload(Request $request){
        if($request->current_page == 'si'){
            if(SalesInvoice::count() == 0){
                return 'NULL';
            }
            $data_update = SalesInvoice::latest('updated_at')->first()->updated_at;
        }

        if($request->current_page == 'cr'){
            if(CollectionReceipt::count() == 0){
                return 'NULL';
            }
            $data_update = CollectionReceipt::latest('updated_at')->first()->updated_at;
        }

        if($request->current_page == 'bs'){
            if(BillingStatement::count() == 0){
                return 'NULL';
            }
            $data_update = BillingStatement::latest('updated_at')->first()->updated_at;
        }

        if($request->current_page == 'or'){
            if(OfficialReceipt::count() == 0){
                return 'NULL';
            }
            $data_update = OfficialReceipt::latest('updated_at')->first()->updated_at;
        }

        if($request->current_page == 'dr'){
            if(DeliveryReceipt::count() == 0){
                return 'NULL';
            }
            $data_update = DeliveryReceipt::latest('updated_at')->first()->updated_at;
        }
        return $data_update;
    }
}
