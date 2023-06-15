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
            $imagick->modulateImage(120, 100, 100);
            $imagePath = storage_path("app/public/$request->sales_invoice.jpeg");
            $imagick->writeImage($imagePath);
            $text = (new TesseractOCR($imagePath))->run();

            if(stripos($text, $request->sales_invoice) === false){
                return 'Input Sales Invoice No. does not match with the uploaded document.';
            }
            else{
                $filename = $request->sales_invoice.'.'.$fileExtension;
                $file->storeAs('public/sales_invoice',$filename);

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
                return 'Input Collection Receipt No. does not match with the uploaded document.';
            }
            else{
                $filename = $request->collection_receipt.'.'.$fileExtension;
                $file->storeAs('public/collection_receipt',$filename);

                CollectionReceipt::create([
                    'collection_receipt' => $request->collection_receipt,
                    'company' => $request->company,
                    'client_name' => strtoupper($request->client_name),
                    'branch_name' => strtoupper($request->branch_name),
                    'date_created' => $request->date_created,
                    'sales_order' => strtoupper($request->sales_order),
                    'sales_invoice' => strtoupper($request->sales_invoice),
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
                return 'Input Billing Statement No. does not match with the uploaded document.';
            }
            else{
                $filename = $request->billing_statement.'.'.$fileExtension;
                $file->storeAs('public/billing_statement',$filename);

                BillingStatement::create([
                    'billing_statement' => $request->billing_statement,
                    'company' => $request->company,
                    'client_name' => strtoupper($request->client_name),
                    'branch_name' => strtoupper($request->branch_name),
                    'date_created' => $request->date_created,
                    'sales_order' => strtoupper($request->sales_order),
                    'purchase_order' => strtoupper($request->purchase_order),
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
                return 'Input Official Receipt No. does not match with the uploaded document.';
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

            if(stripos($text, $request->delivery_receipt) === false){
                return 'Input Delivery Receipt No. does not match with the uploaded document.';
            }
            else{
                $filename = $request->delivery_receipt.'.'.$fileExtension;
                $file->storeAs('public/delivery_receipt',$filename);

                DeliveryReceipt::create([
                    'delivery_receipt' => $request->delivery_receipt,
                    'company' => $request->company,
                    'client_name' => strtoupper($request->client_name),
                    'branch_name' => strtoupper($request->branch_name),
                    'date_created' => $request->date_created,
                    'date_received' => $request->date_received,
                    'purchase_order' => strtoupper($request->purchase_order),
                    'sales_order' => strtoupper($request->sales_order),
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
            $current_page = 'SALES INVOICE';
            $reference_number = SalesInvoice::where('id', $request->entry_id)->first()->sales_invoice;
            $client_name_orig = SalesInvoice::where('id', $request->entry_id)->first()->client_name;
            $branch_name_orig = SalesInvoice::where('id', $request->entry_id)->first()->branch_name;

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

            if($client_name_change == NULL && $branch_name_change == NULL){
                return 'no changes';
            }

            $sql = SalesInvoice::where('id', $request->entry_id)
                        ->update([
                        'client_name' => strtoupper($request->client_name),
                        'branch_name' => strtoupper($request->branch_name)
            ]);
        }

        if($request->current_page == 'cr'){
            $current_page = 'COLLECTION RECEIPT';
            $reference_number = CollectionReceipt::where('id', $request->entry_id)->first()->collection_receipt;
            $client_name_orig = CollectionReceipt::where('id', $request->entry_id)->first()->client_name;
            $branch_name_orig = CollectionReceipt::where('id', $request->entry_id)->first()->branch_name;

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

            if($client_name_change == NULL && $branch_name_change == NULL){
                return 'no changes';
            }

            $sql = CollectionReceipt::where('id', $request->entry_id)
                        ->update([
                        'client_name' => strtoupper($request->client_name),
                        'branch_name' => strtoupper($request->branch_name)
            ]);
        }

        if($request->current_page == 'bs'){
            $current_page = 'BILLING STATEMENT';
            $reference_number = BillingStatement::where('id', $request->entry_id)->first()->billing_statement;
            $client_name_orig = BillingStatement::where('id', $request->entry_id)->first()->client_name;
            $branch_name_orig = BillingStatement::where('id', $request->entry_id)->first()->branch_name;

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

            if($client_name_change == NULL && $branch_name_change == NULL){
                return 'no changes';
            }

            $sql = BillingStatement::where('id', $request->entry_id)
                        ->update([
                        'client_name' => strtoupper($request->client_name),
                        'branch_name' => strtoupper($request->branch_name)
            ]);
        }

        if($request->current_page == 'or'){
            $current_page = 'OFFICIAL RECEIPT';
            $reference_number = OfficialReceipt::where('id', $request->entry_id)->first()->official_receipt;
            $client_name_orig = OfficialReceipt::where('id', $request->entry_id)->first()->client_name;
            $branch_name_orig = OfficialReceipt::where('id', $request->entry_id)->first()->branch_name;

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

            if($client_name_change == NULL && $branch_name_change == NULL){
                return 'no changes';
            }

            $sql = OfficialReceipt::where('id', $request->entry_id)
                        ->update([
                        'client_name' => strtoupper($request->client_name),
                        'branch_name' => strtoupper($request->branch_name)
            ]);
        }

        if($request->current_page == 'dr'){
            $current_page = 'DELIVERY RECEIPT';
            $reference_number = DeliveryReceipt::where('id', $request->entry_id)->first()->delivery_receipt;
            $client_name_orig = DeliveryReceipt::where('id', $request->entry_id)->first()->client_name;
            $branch_name_orig = DeliveryReceipt::where('id', $request->entry_id)->first()->branch_name;

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

            if($client_name_change == NULL && $branch_name_change == NULL){
                return 'no changes';
            }

            $sql = DeliveryReceipt::where('id', $request->entry_id)
                        ->update([
                        'client_name' => strtoupper($request->client_name),
                        'branch_name' => strtoupper($request->branch_name)
            ]);
        }

        $userlogs = new UserLogs;
        $userlogs->username = auth()->user()->name;
        $userlogs->role = Role::where('id', auth()->user()->userlevel)->first()->name;
        $userlogs->activity = "USER SUCCESSFULLY UPDATED $current_page DETAILS ($reference_number): $client_name_change $branch_name_change.";
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
