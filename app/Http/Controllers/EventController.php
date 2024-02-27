<?php

namespace App\Http\Controllers;

use DB;
use Str;
use Carbon\Carbon;
use Spatie\PdfToText\Pdf;
use Spatie\PdfToImage\Pdf as Jpg;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Exceptions\PostTooLargeException;
use thiagoalessio\TesseractOCR\TesseractOCR;
use App\Models\Company;
use App\Models\SalesInvoice;
use App\Models\CollectionReceipt;
use App\Models\BillingStatement;
use App\Models\OfficialReceipt;
use App\Models\DeliveryReceipt;
use App\Models\UserLogs;
use App\Models\Role;
use App\Models\RemarkLogs;
use App\Models\User;
use App\Events\{
    NewCr,
    NewSi,
    NewBs,
    NewOr,
    NewDr
};

class EventController extends Controller
{
    public function __construct()
    {
       $this->middleware('auth');
    }

    private function get_count($type){
        $current_role = Role::where('id', auth()->user()->userlevel)->first()->name;
        $datas = DB::table($type)->whereIn('company', auth()->user()->companies->pluck('company'))->get();
        $count = 0;

        foreach($datas as $data){
            $status = $data->status;
            $stage = $data->stage;
            if($status == 'FOR VALIDATION'){
                if(($stage == '1' && $current_role == 'ENCODER') || ($stage == '1' && $current_role == 'ADMIN')){
                    $count++;
                }
                else if($stage == '0' && $current_role == 'ENCODER'){
                    $count++;
                }
                else if($stage == '0' && $current_role == 'ADMIN'){
                    $count++;
                }
            }
            else if($status == 'INVALID'){
                if($current_role == 'ENCODER'){
                    $count++;
                }
            }
            else if($status == 'FOR CORRECTION' && $current_role == 'ADMIN'){
                $count++;
            }
        }

        return $count;
    }

    public function save_si(Request $request){
        try{
            $maxSize = 2500 * 1024;
            $ext_array = ['pdf','jpg','jpeg','png'];
            foreach($request->file('pdf_file') as $file){
                $extension = strtolower($file->getClientOriginalExtension());
                if(!in_array($extension, $ext_array)){
                    return 'FILE EXTENSION';
                }
                else if($file->getSize() > $maxSize){
                    return 'MAX SIZE';
                }
            }
        }
        catch(PostTooLargeException $th){
            return 'MAX SIZE';
        }

        if(SalesInvoice::where('sales_invoice', $request->sales_invoice)->where('company', $request->company)->count() > 0) {
            return 'DUPLICATE';
        }

        $file = $request->file('pdf_file');
        $str = Str::random(5);
        $cache = "public/sales_invoice/".Carbon::now()->format('Y-m-d').'/';
        $dir = "storage/sales_invoice/".Carbon::now()->format('Y-m-d').'/';
        $document = $request->sales_invoice;
        if(count($file) >= 1){
            $count = 0;
            foreach($file as $files){
                if($files->getClientOriginalExtension() === 'pdf'){
                    $filename = $request->company.'-'.$document.'-'.$str.'.'.$files->getClientOriginalExtension();
                    $files->storeAs($cache, $filename);
                }
                else if($files->getClientOriginalExtension() === 'jpg'){
                    if($count == 0){
                        $imagick = new \Imagick();
                        $count++;
                    }
                    if($files->getClientOriginalExtension() === 'jpg'){
                        $imagick->readImage($files->getPathname());
                    }
                }
                else{
                    return 'INVALID';
                }
            }
            if($count > 0){
                $filename = $request->company.'-'.$document.'-'.$str.'.pdf';
                $files->storeAs($cache, $filename);
                $imagick->setImageFormat('pdf');
                $imagePath = public_path($dir.$filename);
                $imagick->writeImages($imagePath, true);
            }
        }

        $sql = SalesInvoice::create([
            'sales_invoice' => strtoupper($request->sales_invoice),
            'company' => $request->company,
            'client_name' => strtoupper($request->client_name),
            'business_name' => strtoupper($request->business_name),
            'branch_name' => strtoupper($request->branch_name),
            'uploaded_by' => auth()->user()->name,
            'purchase_order' => strtoupper($request->purchase_order),
            'sales_order' => strtoupper($request->sales_order),
            'delivery_receipt' => strtoupper($request->delivery_receipt),
            'pdf_file' => $dir.$filename,
            'status' => 'FOR VALIDATION'
        ]);

        if($sql){
            $count = $this->get_count('sales_invoices');
            event(new NewSi($count));
            $userlogs = new UserLogs;
            $userlogs->username = auth()->user()->name;
            $userlogs->role = auth()->user()->department.' - '.Role::where('id', auth()->user()->userlevel)->first()->name;
            $userlogs->activity = "USER SUCCESSFULLY ADDED SALES INVOICE ($request->sales_invoice) - $request->company.";
            $userlogs->save();

            return 'FOR VALIDATION';
        }
        else{
            return 'false';
        }
    }

    public function save_cr(Request $request){
        try{
            $maxSize = 2500 * 1024;
            $ext_array = ['pdf','jpg','jpeg','png'];
            foreach($request->file('pdf_file') as $file){
                $extension = strtolower($file->getClientOriginalExtension());
                if(!in_array($extension, $ext_array)){
                    return 'FILE EXTENSION';
                }
                else if($file->getSize() > $maxSize){
                    return 'MAX SIZE';
                }
            }
        }
        catch(PostTooLargeException $th){
            return 'MAX SIZE';
        }

        if(CollectionReceipt::where('collection_receipt', $request->collection_receipt)->where('company', $request->company)->count() > 0) {
            return 'DUPLICATE';
        }

        $file = $request->file('pdf_file');
        $str = Str::random(5);
        $cache = "public/collection_receipt/".Carbon::now()->format('Y-m-d').'/';
        $dir = "storage/collection_receipt/".Carbon::now()->format('Y-m-d').'/';
        $document = $request->collection_receipt;
        if(count($file) >= 1){
            $count = 0;
            foreach($file as $files){
                if($files->getClientOriginalExtension() === 'pdf'){
                    $filename = $request->company.'-'.$document.'-'.$str.'.'.$files->getClientOriginalExtension();
                    $files->storeAs($cache, $filename);
                }
                else if($files->getClientOriginalExtension() === 'jpg'){
                    if($count == 0){
                        $imagick = new \Imagick();
                        $count++;
                    }
                    if($files->getClientOriginalExtension() === 'jpg'){
                        $imagick->readImage($files->getPathname());
                    }
                }
                else{
                    return 'INVALID';
                }
            }
            if($count > 0){
                $filename = $request->company.'-'.$document.'-'.$str.'.pdf';
                $files->storeAs($cache, $filename);
                $imagick->setImageFormat('pdf');
                $imagePath = public_path($dir.$filename);
                $imagick->writeImages($imagePath, true);
            }
        }

        $sql = CollectionReceipt::create([
            'collection_receipt' => strtoupper($request->collection_receipt),
            'company' => $request->company,
            'client_name' => strtoupper($request->client_name),
            'branch_name' => strtoupper($request->branch_name),
            'uploaded_by' => auth()->user()->name,
            'sales_order' => strtoupper($request->sales_order),
            'sales_invoice' => strtoupper($request->sales_invoice),
            'pdf_file' => $dir.$filename,
            'status' => 'FOR VALIDATION'
        ]);

        if($sql){
            $count = $this->get_count('collection_receipts');

            event(new NewCr($count));
            $userlogs = new UserLogs;
            $userlogs->username = auth()->user()->name;
            $userlogs->role = auth()->user()->department.' - '.Role::where('id', auth()->user()->userlevel)->first()->name;
            $userlogs->activity = "USER SUCCESSFULLY ADDED COLLECTION RECEIPT ($request->collection_receipt) - $request->company.";
            $userlogs->save();

            return 'FOR VALIDATION';
        }
        else{
            return 'false';
        }
    }

    public function save_bs(Request $request){
        try{
            $maxSize = 2500 * 1024;
            $ext_array = ['pdf','jpg','jpeg','png'];
            foreach($request->file('pdf_file') as $file){
                $extension = strtolower($file->getClientOriginalExtension());
                if(!in_array($extension, $ext_array)){
                    return 'FILE EXTENSION';
                }
                else if($file->getSize() > $maxSize){
                    return 'MAX SIZE';
                }
            }
        }
        catch(PostTooLargeException $th){
            return 'MAX SIZE';
        }

        if(BillingStatement::where('billing_statement', $request->billing_statement)->where('company', $request->company)->count() > 0) {
            return 'DUPLICATE';
        }

        $file = $request->file('pdf_file');
        $str = Str::random(5);
        $cache = "public/billing_statement/".Carbon::now()->format('Y-m-d').'/';
        $dir = "storage/billing_statement/".Carbon::now()->format('Y-m-d').'/';
        $document = $request->billing_statement;
        if(count($file) >= 1){
            $count = 0;
            foreach($file as $files){
                if($files->getClientOriginalExtension() === 'pdf'){
                    $filename = $request->company.'-'.$document.'-'.$str.'.'.$files->getClientOriginalExtension();
                    $files->storeAs($cache, $filename);
                }
                else if($files->getClientOriginalExtension() === 'jpg'){
                    if($count == 0){
                        $imagick = new \Imagick();
                        $count++;
                    }
                    if($files->getClientOriginalExtension() === 'jpg'){
                        $imagick->readImage($files->getPathname());
                    }
                }
                else{
                    return 'INVALID';
                }
            }
            if($count > 0){
                $filename = $request->company.'-'.$document.'-'.$str.'.pdf';
                $files->storeAs($cache, $filename);
                $imagick->setImageFormat('pdf');
                $imagePath = public_path($dir.$filename);
                $imagick->writeImages($imagePath, true);
            }
        }

        $sql = BillingStatement::create([
            'billing_statement' => strtoupper($request->billing_statement),
            'company' => $request->company,
            'client_name' => strtoupper($request->client_name),
            'business_name' => strtoupper($request->business_name),
            'branch_name' => strtoupper($request->branch_name),
            'uploaded_by' => auth()->user()->name,
            'sales_order' => strtoupper($request->sales_order),
            'purchase_order' => strtoupper($request->purchase_order),
            'pdf_file' => $dir.$filename,
            'status' => 'FOR VALIDATION'
        ]);

        if($sql){
            $count = $this->get_count('billing_statements');

            event(new NewBs($count));
            $userlogs = new UserLogs;
            $userlogs->username = auth()->user()->name;
            $userlogs->role = auth()->user()->department.' - '.Role::where('id', auth()->user()->userlevel)->first()->name;
            $userlogs->activity = "USER SUCCESSFULLY ADDED BILLING STATEMENT ($request->billing_statement) - $request->company.";
            $userlogs->save();

            return 'FOR VALIDATION';
        }
        else{
            return 'false';
        }
    }

    public function save_or(Request $request){
        try{
            $maxSize = 2500 * 1024;
            $ext_array = ['pdf','jpg','jpeg','png'];
            foreach($request->file('pdf_file') as $file){
                $extension = strtolower($file->getClientOriginalExtension());
                if(!in_array($extension, $ext_array)){
                    return 'FILE EXTENSION';
                }
                else if($file->getSize() > $maxSize){
                    return 'MAX SIZE';
                }
            }
        }
        catch(PostTooLargeException $th){
            return 'MAX SIZE';
        }

        if(OfficialReceipt::where('official_receipt', $request->official_receipt)->where('company', $request->company)->count() > 0) {
            return 'DUPLICATE';
        }

        $file = $request->file('pdf_file');
        $str = Str::random(5);
        $cache = "public/official_receipt/".Carbon::now()->format('Y-m-d').'/';
        $dir = "storage/official_receipt/".Carbon::now()->format('Y-m-d').'/';
        $document = $request->official_receipt;
        if(count($file) >= 1){
            $count = 0;
            foreach($file as $files){
                if($files->getClientOriginalExtension() === 'pdf'){
                    $filename = $request->company.'-'.$document.'-'.$str.'.'.$files->getClientOriginalExtension();
                    $files->storeAs($cache, $filename);
                }
                else if($files->getClientOriginalExtension() === 'jpg'){
                    if($count == 0){
                        $imagick = new \Imagick();
                        $count++;
                    }
                    if($files->getClientOriginalExtension() === 'jpg'){
                        $imagick->readImage($files->getPathname());
                    }
                }
                else{
                    return 'INVALID';
                }
            }
            if($count > 0){
                $filename = $request->company.'-'.$document.'-'.$str.'.pdf';
                $files->storeAs($cache, $filename);
                $imagick->setImageFormat('pdf');
                $imagePath = public_path($dir.$filename);
                $imagick->writeImages($imagePath, true);
            }
        }

        $sql = OfficialReceipt::create([
            'official_receipt' => strtoupper($request->official_receipt),
            'company' => $request->company,
            'client_name' => strtoupper($request->client_name),
            'branch_name' => strtoupper($request->branch_name),
            'uploaded_by' => auth()->user()->name,
            'sales_order' => $request->sales_order,
            'pdf_file' => $dir.$filename,
            'status' => 'FOR VALIDATION'
        ]);
        if($sql){
            $count = $this->get_count('official_receipts');

            event(new NewOr($count));
            $userlogs = new UserLogs;
            $userlogs->username = auth()->user()->name;
            $userlogs->role = auth()->user()->department.' - '.Role::where('id', auth()->user()->userlevel)->first()->name;
            $userlogs->activity = "USER SUCCESSFULLY ADDED OFFICIAL RECEIPT ($request->official_receipt) - $request->company.";
            $userlogs->save();

            return 'FOR VALIDATION';
        }
        else{
            return 'false';
        }
    }

    public function save_dr(Request $request){
        try{
            $maxSize = 2500 * 1024;
            $ext_array = ['pdf','jpg','jpeg','png'];
            foreach($request->file('pdf_file') as $file){
                $extension = strtolower($file->getClientOriginalExtension());
                if(!in_array($extension, $ext_array)){
                    return 'FILE EXTENSION';
                }
                else if($file->getSize() > $maxSize){
                    return 'MAX SIZE';
                }
            }
        }
        catch(PostTooLargeException $th){
            return 'MAX SIZE';
        }

        if(DeliveryReceipt::where('delivery_receipt', $request->delivery_receipt)->where('company', $request->company)->count() > 0) {
            return 'DUPLICATE';
        }

        $file = $request->file('pdf_file');
        $str = Str::random(5);
        $cache = "public/delivery_receipt/".Carbon::now()->format('Y-m-d').'/';
        $dir = "storage/delivery_receipt/".Carbon::now()->format('Y-m-d').'/';
        $document = $request->delivery_receipt;
        if(count($file) >= 1){
            $count = 0;
            foreach($file as $files){
                if($files->getClientOriginalExtension() === 'pdf'){
                    $filename = $request->company.'-'.$document.'-'.$str.'.'.$files->getClientOriginalExtension();
                    $files->storeAs($cache, $filename);
                }
                else if($files->getClientOriginalExtension() === 'jpg'){
                    if($count == 0){
                        $imagick = new \Imagick();
                        $count++;
                    }
                    if($files->getClientOriginalExtension() === 'jpg'){
                        $imagick->readImage($files->getPathname());
                    }
                }
                else{
                    return 'INVALID';
                }
            }
            if($count > 0){
                $filename = $request->company.'-'.$document.'-'.$str.'.pdf';
                $files->storeAs($cache, $filename);
                $imagick->setImageFormat('pdf');
                $imagePath = public_path($dir.$filename);
                $imagick->writeImages($imagePath, true);
            }
        }

        $sql = DeliveryReceipt::create([
            'delivery_receipt' => strtoupper($request->delivery_receipt),
            'company' => $request->company,
            'client_name' => strtoupper($request->client_name),
            'business_name' => strtoupper($request->business_name),
            'branch_name' => strtoupper($request->branch_name),
            'uploaded_by' => auth()->user()->name,
            'purchase_order' => strtoupper($request->purchase_order),
            'sales_order' => strtoupper($request->sales_order),
            'pdf_file' => $dir.$filename,
            'status' => 'FOR VALIDATION'
        ]);

        if($sql){
            $count = $this->get_count('delivery_receipts');

            event(new NewDr($count));
            $userlogs = new UserLogs;
            $userlogs->username = auth()->user()->name;
            $userlogs->role = auth()->user()->department.' - '.Role::where('id', auth()->user()->userlevel)->first()->name;
            $userlogs->activity = "USER SUCCESSFULLY ADDED DELIVERY RECEIPT ($request->delivery_receipt) - $request->company.";
            $userlogs->save();

            return 'FOR VALIDATION';
        }
        else{
            return 'false';
        }
    }

    public function edit_si(Request $request){
        try{
            $maxSize = 2500 * 1024;
            $ext_array = ['pdf','jpg','jpeg','png'];
            foreach($request->file('pdf_file') as $file){
                $extension = strtolower($file->getClientOriginalExtension());
                if(!in_array($extension, $ext_array)){
                    return 'FILE EXTENSION';
                }
                else if($file->getSize() > $maxSize){
                    return 'MAX SIZE';
                }
            }
        }
        catch(PostTooLargeException $th){
            return 'MAX SIZE';
        }

        $file = $request->file('pdf_file');
        $str = Str::random(5);
        $cache = "public/sales_invoice/".Carbon::now()->format('Y-m-d').'/';
        $dir = "storage/sales_invoice/".Carbon::now()->format('Y-m-d').'/';
        $document = $request->sales_invoice;
        if(count($file) >= 1){
            $count = 0;
            foreach($file as $files){
                if($files->getClientOriginalExtension() === 'pdf'){
                    $filename = $request->company.'-'.$document.'-'.$str.'.'.$files->getClientOriginalExtension();
                    $files->storeAs($cache, $filename);
                }
                else if($files->getClientOriginalExtension() === 'jpg'){
                    if($count == 0){
                        $imagick = new \Imagick();
                        $count++;
                    }
                    if($files->getClientOriginalExtension() === 'jpg'){
                        $imagick->readImage($files->getPathname());
                    }
                }
                else{
                    return 'INVALID';
                }
            }
            if($count > 0){
                $filename = $request->company.'-'.$document.'-'.$str.'.pdf';
                $files->storeAs($cache, $filename);
                $imagick->setImageFormat('pdf');
                $imagePath = public_path($dir.$filename);
                $imagick->writeImages($imagePath, true);
            }
        }

        $current_page = 'SALES INVOICE';
        $reference_number = SalesInvoice::where('id', $request->entry_id)->first()->sales_invoice;
        $company_orig = SalesInvoice::where('id', $request->entry_id)->first()->company;
        $client_name_orig = SalesInvoice::where('id', $request->entry_id)->first()->client_name;
        $business_name_orig = SalesInvoice::where('id', $request->entry_id)->first()->business_name;
        $branch_name_orig = SalesInvoice::where('id', $request->entry_id)->first()->branch_name;
        $purchase_order_orig = SalesInvoice::where('id', $request->entry_id)->first()->purchase_order;
        $sales_order_orig = SalesInvoice::where('id', $request->entry_id)->first()->sales_order;
        $delivery_receipt_orig = SalesInvoice::where('id', $request->entry_id)->first()->delivery_receipt;
        $stage_orig = SalesInvoice::where('id', $request->entry_id)->first()->stage;

        if($stage_orig == '1'){
            $edited = 'CORRECTED';
        }
        else{
            $edited = 'UPDATED';
        }

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
            $client_name_change = "【SOLD TO: FROM '$client_name_orig' TO '$client_name_new'】";
        }
        else{
            $client_name_change = NULL;
        }

        if(strtoupper($request->business_name) != $business_name_orig){
            $business_name_new = strtoupper($request->business_name);
            $business_name_change = "【BUSINESS NAME: FROM '$business_name_orig' TO '$business_name_new'】";
        }
        else{
            $business_name_change = NULL;
        }

        if(strtoupper($request->branch_name) != $branch_name_orig){
            $branch_name_new = strtoupper($request->branch_name);
            $branch_name_change = "【BRANCH NAME: FROM '$branch_name_orig' TO '$branch_name_new'】";
        }
        else{
            $branch_name_change = NULL;
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

        if($request->hasFile('pdf_file')){
            $pdf_change = " 【ATTACHMENT HAS BEEN CHANGED】";
        }
        else{
            $pdf_change = NULL;
        }

        $sql = SalesInvoice::where('id', $request->entry_id)->update([
            'sales_invoice' => strtoupper($request->sales_invoice),
            'company' => $request->company,
            'client_name' => strtoupper($request->client_name),
            'business_name' => strtoupper($request->business_name),
            'branch_name' => strtoupper($request->branch_name),
            'uploaded_by' => strtoupper($request->uploaded_by),
            'purchase_order' => strtoupper($request->purchase_order),
            'sales_order' => strtoupper($request->sales_order),
            'delivery_receipt' => strtoupper($request->delivery_receipt),
            'pdf_file' => $dir.$filename,
            'remarks' => '',
            'status' => 'FOR VALIDATION',
            'stage' => '0'
        ]);

        if($sql){
            $userlogs = new UserLogs;
            $userlogs->username = auth()->user()->name;
            $userlogs->role = auth()->user()->department.' - '.Role::where('id', auth()->user()->userlevel)->first()->name;
            $userlogs->activity = "USER SUCCESSFULLY $edited $current_page ($reference_number) - $request->company with the following changes: $sales_invoice_change $company_change $client_name_change $branch_name_change $purchase_order_change $sales_order_change $delivery_receipt_change $pdf_change.";
            $userlogs->save();

            return 'FOR VALIDATION';
        }
        else{
            return 'false';
        }
    }

    public function edit_cr(Request $request){
        try{
            $maxSize = 2500 * 1024;
            $ext_array = ['pdf','jpg','jpeg','png'];
            foreach($request->file('pdf_file') as $file){
                $extension = strtolower($file->getClientOriginalExtension());
                if(!in_array($extension, $ext_array)){
                    return 'FILE EXTENSION';
                }
                else if($file->getSize() > $maxSize){
                    return 'MAX SIZE';
                }
            }
        }
        catch(PostTooLargeException $th){
            return 'MAX SIZE';
        }

        $file = $request->file('pdf_file');
        $str = Str::random(5);
        $cache = "public/collection_receipt/".Carbon::now()->format('Y-m-d').'/';
        $dir = "storage/collection_receipt/".Carbon::now()->format('Y-m-d').'/';
        $document = $request->collection_receipt;
        if(count($file) >= 1){
            $count = 0;
            foreach($file as $files){
                if($files->getClientOriginalExtension() === 'pdf'){
                    $filename = $request->company.'-'.$document.'-'.$str.'.'.$files->getClientOriginalExtension();
                    $files->storeAs($cache, $filename);
                }
                else if($files->getClientOriginalExtension() === 'jpg'){
                    if($count == 0){
                        $imagick = new \Imagick();
                        $count++;
                    }
                    if($files->getClientOriginalExtension() === 'jpg'){
                        $imagick->readImage($files->getPathname());
                    }
                }
                else{
                    return 'INVALID';
                }
            }
            if($count > 0){
                $filename = $request->company.'-'.$document.'-'.$str.'.pdf';
                $files->storeAs($cache, $filename);
                $imagick->setImageFormat('pdf');
                $imagePath = public_path($dir.$filename);
                $imagick->writeImages($imagePath, true);
            }
        }

        $current_page = 'COLLECTION RECEIPT';
        $reference_number = CollectionReceipt::where('id', $request->entry_id)->first()->collection_receipt;
        $company_orig = CollectionReceipt::where('id', $request->entry_id)->first()->company;
        $client_name_orig = CollectionReceipt::where('id', $request->entry_id)->first()->client_name;
        $branch_name_orig = CollectionReceipt::where('id', $request->entry_id)->first()->branch_name;
        $sales_order_orig = CollectionReceipt::where('id', $request->entry_id)->first()->sales_order;
        $sales_invoice_orig = CollectionReceipt::where('id', $request->entry_id)->first()->sales_invoice;
        $stage_orig = CollectionReceipt::where('id', $request->entry_id)->first()->stage;

        if($stage_orig == '1'){
            $edited = 'CORRECTED';
        }
        else{
            $edited = 'UPDATED';
        }

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
            $client_name_change = "【RECEIVED FROM: FROM '$client_name_orig' TO '$client_name_new'】";
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

        if($request->hasFile('pdf_file')){
            $pdf_change = " 【ATTACHMENT HAS BEEN CHANGED】";
        }
        else{
            $pdf_change = NULL;
        }

        $sql = CollectionReceipt::where('id', $request->entry_id)->update([
                'collection_receipt' => strtoupper($request->collection_receipt),
                'company' => $request->company,
                'client_name' => strtoupper($request->client_name),
                'branch_name' => strtoupper($request->branch_name),
                'uploaded_by' => strtoupper($request->uploaded_by),
                'sales_order' => strtoupper($request->sales_order),
                'sales_invoice' => strtoupper($request->sales_invoice),
                'pdf_file' => $dir.$filename,
                'remarks' => '',
                'status' => 'FOR VALIDATION',
                'stage' => '0'
        ]);

        if($sql){
            $userlogs = new UserLogs;
            $userlogs->username = auth()->user()->name;
            $userlogs->role = auth()->user()->department.' - '.Role::where('id', auth()->user()->userlevel)->first()->name;
            $userlogs->activity = "USER SUCCESSFULLY $edited $current_page ($reference_number) - $request->company with the following changes: $collection_receipt_change $company_change $client_name_change $branch_name_change $sales_order_change $pdf_change.";
            $userlogs->save();

            return 'FOR VALIDATION';
        }
        else{
            return 'false';
        }
    }

    public function edit_bs(Request $request){
        try{
            $maxSize = 2500 * 1024;
            $ext_array = ['pdf','jpg','jpeg','png'];
            foreach($request->file('pdf_file') as $file){
                $extension = strtolower($file->getClientOriginalExtension());
                if(!in_array($extension, $ext_array)){
                    return 'FILE EXTENSION';
                }
                else if($file->getSize() > $maxSize){
                    return 'MAX SIZE';
                }
            }
        }
        catch(PostTooLargeException $th){
            return 'MAX SIZE';
        }

        $file = $request->file('pdf_file');
        $str = Str::random(5);
        $cache = "public/billing_statement/".Carbon::now()->format('Y-m-d').'/';
        $dir = "storage/billing_statement/".Carbon::now()->format('Y-m-d').'/';
        $document = $request->billing_statement;
        if(count($file) >= 1){
            $count = 0;
            foreach($file as $files){
                if($files->getClientOriginalExtension() === 'pdf'){
                    $filename = $request->company.'-'.$document.'-'.$str.'.'.$files->getClientOriginalExtension();
                    $files->storeAs($cache, $filename);
                }
                else if($files->getClientOriginalExtension() === 'jpg'){
                    if($count == 0){
                        $imagick = new \Imagick();
                        $count++;
                    }
                    if($files->getClientOriginalExtension() === 'jpg'){
                        $imagick->readImage($files->getPathname());
                    }
                }
                else{
                    return 'INVALID';
                }
            }
            if($count > 0){
                $filename = $request->company.'-'.$document.'-'.$str.'.pdf';
                $files->storeAs($cache, $filename);
                $imagick->setImageFormat('pdf');
                $imagePath = public_path($dir.$filename);
                $imagick->writeImages($imagePath, true);
            }
        }

        $current_page = 'BILLING STATEMENT';
        $reference_number = BillingStatement::where('id', $request->entry_id)->first()->billing_statement;
        $company_orig = BillingStatement::where('id', $request->entry_id)->first()->company;
        $client_name_orig = BillingStatement::where('id', $request->entry_id)->first()->client_name;
        $business_name_orig = BillingStatement::where('id', $request->entry_id)->first()->business_name;
        $branch_name_orig = BillingStatement::where('id', $request->entry_id)->first()->branch_name;
        $sales_order_orig = BillingStatement::where('id', $request->entry_id)->first()->sales_order;
        $purchase_order_orig = BillingStatement::where('id', $request->entry_id)->first()->purchase_order;
        $stage_orig = BillingStatement::where('id', $request->entry_id)->first()->stage;

        if($stage_orig == '1'){
            $edited = 'CORRECTED';
        }
        else{
            $edited = 'UPDATED';
        }

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
            $client_name_change = "【BILLED TO: FROM '$client_name_orig' TO '$client_name_new'】";
        }
        else{
            $client_name_change = NULL;
        }

        if(strtoupper($request->business_name) != $business_name_orig){
            $business_name_new = strtoupper($request->business_name);
            $business_name_change = "【BUSINESS NAME: FROM '$business_name_orig' TO '$business_name_new'】";
        }
        else{
            $business_name_change = NULL;
        }

        if(strtoupper($request->branch_name) != $branch_name_orig){
            $branch_name_new = strtoupper($request->branch_name);
            $branch_name_change = "【BRANCH NAME: FROM '$branch_name_orig' TO '$branch_name_new'】";
        }
        else{
            $branch_name_change = NULL;
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

        if($request->hasFile('pdf_file')){
            $pdf_change = " 【ATTACHMENT HAS BEEN CHANGED】";
        }
        else{
            $pdf_change = NULL;
        }

        $sql = BillingStatement::where('id', $request->entry_id)->update([
            'billing_statement' => strtoupper($request->billing_statement),
            'company' => $request->company,
            'client_name' => strtoupper($request->client_name),
            'business_name' => strtoupper($request->business_name),
            'branch_name' => strtoupper($request->branch_name),
            'uploaded_by' => strtoupper($request->uploaded_by),
            'sales_order' => $request->sales_order,
            'purchase_order' => $request->purchase_order,
            'pdf_file' => $dir.$filename,
            'remarks' => '',
            'status' => 'FOR VALIDATION',
            'stage' => '0'
        ]);

        if($sql){
            $userlogs = new UserLogs;
            $userlogs->username = auth()->user()->name;
            $userlogs->role = auth()->user()->department.' - '.Role::where('id', auth()->user()->userlevel)->first()->name;
            $userlogs->activity = "USER SUCCESSFULLY $edited $current_page ($reference_number) - $request->company with the following changes: $billing_statement_change $company_change $client_name_change $branch_name_change $sales_order_change $purchase_order_change $pdf_change.";
            $userlogs->save();

            return 'FOR VALIDATION';
        }
        else{
            return 'false';
        }
    }

    public function edit_or(Request $request){
        try{
            $maxSize = 2500 * 1024;
            $ext_array = ['pdf','jpg','jpeg','png'];
            foreach($request->file('pdf_file') as $file){
                $extension = strtolower($file->getClientOriginalExtension());
                if(!in_array($extension, $ext_array)){
                    return 'FILE EXTENSION';
                }
                else if($file->getSize() > $maxSize){
                    return 'MAX SIZE';
                }
            }
        }
        catch(PostTooLargeException $th){
            return 'MAX SIZE';
        }

        $file = $request->file('pdf_file');
        $str = Str::random(5);
        $cache = "public/official_receipt/".Carbon::now()->format('Y-m-d').'/';
        $dir = "storage/official_receipt/".Carbon::now()->format('Y-m-d').'/';
        $document = $request->official_receipt;
        if(count($file) >= 1){
            $count = 0;
            foreach($file as $files){
                if($files->getClientOriginalExtension() === 'pdf'){
                    $filename = $request->company.'-'.$document.'-'.$str.'.'.$files->getClientOriginalExtension();
                    $files->storeAs($cache, $filename);
                }
                else if($files->getClientOriginalExtension() === 'jpg'){
                    if($count == 0){
                        $imagick = new \Imagick();
                        $count++;
                    }
                    if($files->getClientOriginalExtension() === 'jpg'){
                        $imagick->readImage($files->getPathname());
                    }
                }
                else{
                    return 'INVALID';
                }
            }
            if($count > 0){
                $filename = $request->company.'-'.$document.'-'.$str.'.pdf';
                $files->storeAs($cache, $filename);
                $imagick->setImageFormat('pdf');
                $imagePath = public_path($dir.$filename);
                $imagick->writeImages($imagePath, true);
            }
        }

        $current_page = 'OFFICIAL RECEIPT';
        $reference_number = OfficialReceipt::where('id', $request->entry_id)->first()->official_receipt;
        $company_orig = OfficialReceipt::where('id', $request->entry_id)->first()->company;
        $client_name_orig = OfficialReceipt::where('id', $request->entry_id)->first()->client_name;
        $branch_name_orig = OfficialReceipt::where('id', $request->entry_id)->first()->branch_name;
        $sales_order_orig = OfficialReceipt::where('id', $request->entry_id)->first()->sales_order;
        $stage_orig = OfficialReceipt::where('id', $request->entry_id)->first()->stage;

        if($stage_orig == '1'){
            $edited = 'CORRECTED';
        }
        else{
            $edited = 'UPDATED';
        }

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
            $client_name_change = "【RECEIVED FROM: FROM '$client_name_orig' TO '$client_name_new'】";
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

        if($request->sales_order != $sales_order_orig){
            $sales_order_new = $request->sales_order;
            $sales_order_change = "【SALES ORDER: FROM '$sales_order_orig' TO '$sales_order_new'】";
        }
        else{
            $sales_order_change = NULL;
        }

        if($request->hasFile('pdf_file')){
            $pdf_change = " 【ATTACHMENT HAS BEEN CHANGED】";
        }
        else{
            $pdf_change = NULL;
        }

        $sql = OfficialReceipt::where('id', $request->entry_id)
                ->update([
                'official_receipt' => strtoupper($request->official_receipt),
                'company' => $request->company,
                'client_name' => strtoupper($request->client_name),
                'branch_name' => strtoupper($request->branch_name),
                'uploaded_by' => strtoupper($request->uploaded_by),
                'sales_order' => $request->sales_order,
                'pdf_file' => $dir.$filename,
                'remarks' => '',
                'status' => 'FOR VALIDATION',
                'stage' => '0'
        ]);

        if($sql){
            $userlogs = new UserLogs;
            $userlogs->username = auth()->user()->name;
            $userlogs->role = auth()->user()->department.' - '.Role::where('id', auth()->user()->userlevel)->first()->name;
            $userlogs->activity = "USER SUCCESSFULLY $edited $current_page ($reference_number) - $request->company with the following changes: $official_receipt_change $company_change $client_name_change $branch_name_change $sales_order_change $pdf_change.";
            $userlogs->save();

            return 'FOR VALIDATION';
        }
        else{
            return 'false';
        }
    }

    public function edit_dr(Request $request){
        try{
            $maxSize = 2500 * 1024;
            $ext_array = ['pdf','jpg','jpeg','png'];
            foreach($request->file('pdf_file') as $file){
                $extension = strtolower($file->getClientOriginalExtension());
                if(!in_array($extension, $ext_array)){
                    return 'FILE EXTENSION';
                }
                else if($file->getSize() > $maxSize){
                    return 'MAX SIZE';
                }
            }
        }
        catch(PostTooLargeException $th){
            return 'MAX SIZE';
        }

        $file = $request->file('pdf_file');
        $str = Str::random(5);
        $cache = "public/delivery_receipt/".Carbon::now()->format('Y-m-d').'/';
        $dir = "storage/delivery_receipt/".Carbon::now()->format('Y-m-d').'/';
        $document = $request->delivery_receipt;
        if(count($file) >= 1){
            $count = 0;
            foreach($file as $files){
                if($files->getClientOriginalExtension() === 'pdf'){
                    $filename = $request->company.'-'.$document.'-'.$str.'.'.$files->getClientOriginalExtension();
                    $files->storeAs($cache, $filename);
                }
                else if($files->getClientOriginalExtension() === 'jpg'){
                    if($count == 0){
                        $imagick = new \Imagick();
                        $count++;
                    }
                    if($files->getClientOriginalExtension() === 'jpg'){
                        $imagick->readImage($files->getPathname());
                    }
                }
                else{
                    return 'INVALID';
                }
            }
            if($count > 0){
                $filename = $request->company.'-'.$document.'-'.$str.'.pdf';
                $files->storeAs($cache, $filename);
                $imagick->setImageFormat('pdf');
                $imagePath = public_path($dir.$filename);
                $imagick->writeImages($imagePath, true);
            }
        }

        $current_page = 'DELIVERY RECEIPT';
        $reference_number = DeliveryReceipt::where('id', $request->entry_id)->first()->delivery_receipt;
        $company_orig = DeliveryReceipt::where('id', $request->entry_id)->first()->company;
        $client_name_orig = DeliveryReceipt::where('id', $request->entry_id)->first()->client_name;
        $business_name_orig = DeliveryReceipt::where('id', $request->entry_id)->first()->business_name;
        $branch_name_orig = DeliveryReceipt::where('id', $request->entry_id)->first()->branch_name;
        $purchase_order_orig = DeliveryReceipt::where('id', $request->entry_id)->first()->purchase_order;
        $sales_order_orig = DeliveryReceipt::where('id', $request->entry_id)->first()->sales_order;
        $stage_orig = DeliveryReceipt::where('id', $request->entry_id)->first()->stage;

        if($stage_orig == '1'){
            $edited = 'CORRECTED';
        }
        else{
            $edited = 'UPDATED';
        }

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
            $client_name_change = "【DELIVERED TO: FROM '$client_name_orig' TO '$client_name_new'】";
        }
        else{
            $client_name_change = NULL;
        }

        if(strtoupper($request->business_name) != $business_name_orig){
            $business_name_new = strtoupper($request->business_name);
            $business_name_change = "【BUSINESS NAME: FROM '$business_name_orig' TO '$business_name_new'】";
        }
        else{
            $business_name_change = NULL;
        }

        if(strtoupper($request->branch_name) != $branch_name_orig){
            $branch_name_new = strtoupper($request->branch_name);
            $branch_name_change = "【BRANCH NAME: FROM '$branch_name_orig' TO '$branch_name_new'】";
        }
        else{
            $branch_name_change = NULL;
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

        if($request->hasFile('pdf_file')){
            $pdf_change = " 【ATTACHMENT HAS BEEN CHANGED】";
        }
        else{
            $pdf_change = NULL;
        }

        $sql = DeliveryReceipt::where('id', $request->entry_id)
                ->update([
                'delivery_receipt' => strtoupper($request->delivery_receipt),
                'company' => $request->company,
                'client_name' => strtoupper($request->client_name),
                'business_name' => strtoupper($request->business_name),
                'branch_name' => strtoupper($request->branch_name),
                'uploaded_by' => strtoupper($request->uploaded_by),
                'purchase_order' => $request->purchase_order,
                'sales_order' => $request->sales_order,
                'pdf_file' => $dir.$filename,
                'remarks' => '',
                'status' => 'FOR VALIDATION',
                'stage' => '0'
        ]);

        if($sql){
            $userlogs = new UserLogs;
            $userlogs->username = auth()->user()->name;
            $userlogs->role = auth()->user()->department.' - '.Role::where('id', auth()->user()->userlevel)->first()->name;
            $userlogs->activity = "USER SUCCESSFULLY $edited $current_page ($reference_number) - $request->company with the following changes: $delivery_receipt_change $company_change $client_name_change $business_name_change $branch_name_change $sales_order_change $purchase_order_change $pdf_change.";
            $userlogs->save();

            return 'FOR VALIDATION';
        }
        else{
            return 'false';
        }
    }

    public function edit(Request $request){
        if($request->current_page == 'si'){
            $current_page = 'SALES INVOICE';
            $reference_number = SalesInvoice::where('id', $request->entry_id)->first()->sales_invoice;
            $company_orig = SalesInvoice::where('id', $request->entry_id)->first()->company;
            $client_name_orig = SalesInvoice::where('id', $request->entry_id)->first()->client_name;
            $business_name_orig = SalesInvoice::where('id', $request->entry_id)->first()->business_name;
            $branch_name_orig = SalesInvoice::where('id', $request->entry_id)->first()->branch_name;
            $purchase_order_orig = SalesInvoice::where('id', $request->entry_id)->first()->purchase_order;
            $sales_order_orig = SalesInvoice::where('id', $request->entry_id)->first()->sales_order;
            $delivery_receipt_orig = SalesInvoice::where('id', $request->entry_id)->first()->delivery_receipt;
            $stage_orig = SalesInvoice::where('id', $request->entry_id)->first()->stage;

            if($stage_orig == '1'){
                $edited = 'CORRECTED';
            }
            else{
                $edited = 'UPDATED';
            }

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
                $client_name_change = "【SOLD TO: FROM '$client_name_orig' TO '$client_name_new'】";
            }
            else{
                $client_name_change = NULL;
            }

            if(strtoupper($request->business_name) != $business_name_orig){
                $business_name_new = strtoupper($request->business_name);
                $business_name_change = "【BUSINESS NAME: FROM '$business_name_orig' TO '$business_name_new'】";
            }
            else{
                $business_name_change = NULL;
            }

            if(strtoupper($request->branch_name) != $branch_name_orig){
                $branch_name_new = strtoupper($request->branch_name);
                $branch_name_change = "【BRANCH NAME: FROM '$branch_name_orig' TO '$branch_name_new'】";
            }
            else{
                $branch_name_change = NULL;
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
                $delivery_receipt_change = "【DELIVERY RECEIPT: FROM '$delivery_receipt_orig' TO '$delivery_receipt_new'】";
            }
            else{
                $delivery_receipt_change = NULL;
            }

            if($sales_invoice_change == NULL
                && $company_change == NULL
                && $client_name_change == NULL
                && $business_name_change == NULL
                && $branch_name_change == NULL
                && $purchase_order_change == NULL
                && $sales_order_change == NULL
                && $delivery_receipt_change == NULL
                ){
                return 'NO CHANGES';
            }

            $sql = SalesInvoice::where('id', $request->entry_id)
                        ->update([
                        'sales_invoice' => strtoupper($request->sales_invoice),
                        'company' => $request->company,
                        'client_name' => strtoupper($request->client_name),
                        'business_name' => strtoupper($request->business_name),
                        'branch_name' => strtoupper($request->branch_name),
                        'uploaded_by' => strtoupper($request->uploaded_by),
                        'purchase_order' => strtoupper($request->purchase_order),
                        'sales_order' => strtoupper($request->sales_order),
                        'delivery_receipt' => strtoupper($request->delivery_receipt),
                        'remarks' => '',
                        'status' => 'FOR VALIDATION',
                        'stage' => '0'
            ]);
        }

        if($request->current_page == 'cr'){
            $current_page = 'COLLECTION RECEIPT';
            $reference_number = CollectionReceipt::where('id', $request->entry_id)->first()->collection_receipt;
            $company_orig = CollectionReceipt::where('id', $request->entry_id)->first()->company;
            $client_name_orig = CollectionReceipt::where('id', $request->entry_id)->first()->client_name;
            $branch_name_orig = CollectionReceipt::where('id', $request->entry_id)->first()->branch_name;
            $sales_order_orig = CollectionReceipt::where('id', $request->entry_id)->first()->sales_order;
            $sales_invoice_orig = CollectionReceipt::where('id', $request->entry_id)->first()->sales_invoice;
            $stage_orig = CollectionReceipt::where('id', $request->entry_id)->first()->stage;

            if($stage_orig == '1'){
                $edited = 'CORRECTED';
            }
            else{
                $edited = 'UPDATED';
            }

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
                $client_name_change = "【RECEIVED FROM: FROM '$client_name_orig' TO '$client_name_new'】";
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
                && $sales_order_change == NULL
                && $sales_invoice_change == NULL
                ){
                return 'NO CHANGES';
            }

            $sql = CollectionReceipt::where('id', $request->entry_id)
                        ->update([
                        'collection_receipt' => $request->collection_receipt,
                        'company' => $request->company,
                        'client_name' => strtoupper($request->client_name),
                        'branch_name' => strtoupper($request->branch_name),
                        'uploaded_by' => strtoupper($request->uploaded_by),
                        'sales_order' => strtoupper($request->sales_order),
                        'sales_invoice' => strtoupper($request->sales_invoice),
                        'remarks' => '',
                        'status' => 'FOR VALIDATION',
                        'stage' => '0'
            ]);
        }

        if($request->current_page == 'bs'){
            $current_page = 'BILLING STATEMENT';
            $reference_number = BillingStatement::where('id', $request->entry_id)->first()->billing_statement;
            $company_orig = BillingStatement::where('id', $request->entry_id)->first()->company;
            $client_name_orig = BillingStatement::where('id', $request->entry_id)->first()->client_name;
            $business_name_orig = BillingStatement::where('id', $request->entry_id)->first()->business_name;
            $branch_name_orig = BillingStatement::where('id', $request->entry_id)->first()->branch_name;
            $sales_order_orig = BillingStatement::where('id', $request->entry_id)->first()->sales_order;
            $purchase_order_orig = BillingStatement::where('id', $request->entry_id)->first()->purchase_order;
            $stage_orig = BillingStatement::where('id', $request->entry_id)->first()->stage;

            if($stage_orig == '1'){
                $edited = 'CORRECTED';
            }
            else{
                $edited = 'UPDATED';
            }

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
                $client_name_change = "【BILLED TO: FROM '$client_name_orig' TO '$client_name_new'】";
            }
            else{
                $client_name_change = NULL;
            }

            if(strtoupper($request->business_name) != $business_name_orig){
                $business_name_new = strtoupper($request->business_name);
                $business_name_change = "【BUSINESS NAME: FROM '$business_name_orig' TO '$business_name_new'】";
            }
            else{
                $business_name_change = NULL;
            }

            if(strtoupper($request->branch_name) != $branch_name_orig){
                $branch_name_new = strtoupper($request->branch_name);
                $branch_name_change = "【BRANCH NAME: FROM '$branch_name_orig' TO '$branch_name_new'】";
            }
            else{
                $branch_name_change = NULL;
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
                && $business_name_change == NULL
                && $branch_name_change == NULL
                && $sales_order_change == NULL
                && $purchase_order_change == NULL
                ){
                return 'NO CHANGES';
            }

            $sql = BillingStatement::where('id', $request->entry_id)
                        ->update([
                        'billing_statement' => $request->billing_statement,
                        'company' => $request->company,
                        'client_name' => strtoupper($request->client_name),
                        'business_name' => strtoupper($request->business_name),
                        'branch_name' => strtoupper($request->branch_name),
                        'sales_order' => $request->sales_order,
                        'purchase_order' => $request->purchase_order,
                        'remarks' => '',
                        'status' => 'FOR VALIDATION',
                        'stage' => '0'
            ]);
        }

        if($request->current_page == 'or'){
            $current_page = 'OFFICIAL RECEIPT';
            $reference_number = OfficialReceipt::where('id', $request->entry_id)->first()->official_receipt;
            $company_orig = OfficialReceipt::where('id', $request->entry_id)->first()->company;
            $client_name_orig = OfficialReceipt::where('id', $request->entry_id)->first()->client_name;
            $branch_name_orig = OfficialReceipt::where('id', $request->entry_id)->first()->branch_name;
            $sales_order_orig = OfficialReceipt::where('id', $request->entry_id)->first()->sales_order;
            $stage_orig = OfficialReceipt::where('id', $request->entry_id)->first()->stage;

            if($stage_orig == '1'){
                $edited = 'CORRECTED';
            }
            else{
                $edited = 'UPDATED';
            }

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
                $client_name_change = "【RECEIVED FROM: FROM '$client_name_orig' TO '$client_name_new'】";
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
                && $sales_order_change == NULL
                ){
                return 'NO CHANGES';
            }

            $sql = OfficialReceipt::where('id', $request->entry_id)
                        ->update([
                        'official_receipt' => $request->official_receipt,
                        'company' => $request->company,
                        'client_name' => strtoupper($request->client_name),
                        'branch_name' => strtoupper($request->branch_name),
                        'uploaded_by' => strtoupper($request->uploaded_by),
                        'sales_order' => $request->sales_order,
                        'remarks' => '',
                        'status' => 'FOR VALIDATION',
                        'stage' => '0'
            ]);
        }

        if($request->current_page == 'dr'){
            $current_page = 'DELIVERY RECEIPT';
            $reference_number = DeliveryReceipt::where('id', $request->entry_id)->first()->delivery_receipt;
            $company_orig = DeliveryReceipt::where('id', $request->entry_id)->first()->company;
            $client_name_orig = DeliveryReceipt::where('id', $request->entry_id)->first()->client_name;
            $business_name_orig = DeliveryReceipt::where('id', $request->entry_id)->first()->business_name;
            $branch_name_orig = DeliveryReceipt::where('id', $request->entry_id)->first()->branch_name;
            $purchase_order_orig = DeliveryReceipt::where('id', $request->entry_id)->first()->purchase_order;
            $sales_order_orig = DeliveryReceipt::where('id', $request->entry_id)->first()->sales_order;
            $stage_orig = DeliveryReceipt::where('id', $request->entry_id)->first()->stage;

            if($stage_orig == '1'){
                $edited = 'CORRECTED';
            }
            else{
                $edited = 'UPDATED';
            }

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
                $client_name_change = "【DELIVERED TO: FROM '$client_name_orig' TO '$client_name_new'】";
            }
            else{
                $client_name_change = NULL;
            }

            if(strtoupper($request->business_name) != $business_name_orig){
                $business_name_new = strtoupper($request->business_name);
                $business_name_change = "【BUSINESS NAME: FROM '$business_name_orig' TO '$business_name_new'】";
            }
            else{
                $business_name_change = NULL;
            }

            if(strtoupper($request->branch_name) != $branch_name_orig){
                $branch_name_new = strtoupper($request->branch_name);
                $branch_name_change = "【BRANCH NAME: FROM '$branch_name_orig' TO '$branch_name_new'】";
            }
            else{
                $branch_name_change = NULL;
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
                && $business_name_change == NULL
                && $branch_name_change == NULL
                && $purchase_order_change == NULL
                && $sales_order_change == NULL
                ){
                return 'NO CHANGES';
            }

            $sql = DeliveryReceipt::where('id', $request->entry_id)
                        ->update([
                        'delivery_receipt' => $request->delivery_receipt,
                        'company' => $request->company,
                        'client_name' => strtoupper($request->client_name),
                        'business_name' => strtoupper($request->business_name),
                        'branch_name' => strtoupper($request->branch_name),
                        'uploaded_by' => strtoupper($request->uploaded_by),
                        'purchase_order' => $request->purchase_order,
                        'sales_order' => $request->sales_order,
                        'remarks' => '',
                        'status' => 'FOR VALIDATION',
                        'stage' => '0'
            ]);
        }

        if($sql){
            $userlogs = new UserLogs;
            $userlogs->username = auth()->user()->name;
            $userlogs->role = auth()->user()->department.' - '.Role::where('id', auth()->user()->userlevel)->first()->name;
            if($request->current_page == 'si'){
                $userlogs->activity = "USER SUCCESSFULLY $edited $current_page ($reference_number) - $request->company with the following changes: $sales_invoice_change $company_change $client_name_change $business_name_change $branch_name_change $purchase_order_change $sales_order_change $delivery_receipt_change.";
            }
            if($request->current_page == 'cr'){
                $userlogs->activity = "USER SUCCESSFULLY $edited $current_page ($reference_number) - $request->company with the following changes: $collection_receipt_change $company_change $client_name_change $branch_name_change $sales_order_change.";
            }
            if($request->current_page == 'bs'){
                $userlogs->activity = "USER SUCCESSFULLY $edited $current_page ($reference_number) - $request->company with the following changes: $billing_statement_change $company_change $client_name_change $business_name_change $branch_name_change $sales_order_change $purchase_order_change.";
            }
            if($request->current_page == 'or'){
                $userlogs->activity = "USER SUCCESSFULLY $edited $current_page ($reference_number) - $request->company with the following changes: $official_receipt_change $company_change $client_name_change $branch_name_change $sales_order_change.";
            }
            if($request->current_page == 'dr'){
                $userlogs->activity = "USER SUCCESSFULLY $edited $current_page ($reference_number) - $request->company with the following changes: $delivery_receipt_change $company_change $client_name_change $business_name_change $branch_name_change $sales_order_change $purchase_order_change.";
            }
            $userlogs->save();
            return 'true';
        }
        else{
            return 'false';
        }
    }

    public function approve(Request $request){
        if($request->current_page == 'si'){
            $current_page = 'SALES INVOICE';
            $reference_number = SalesInvoice::where('id', $request->entry_id)->first()->sales_invoice;
            $company = SalesInvoice::where('id', $request->entry_id)->first()->company;
            $sql = SalesInvoice::where('id', $request->entry_id)->update(['status' => 'VALID', 'stage' => '1']);
        }

        if($request->current_page == 'cr'){
            $current_page = 'COLLECTION RECEIPT';
            $reference_number = CollectionReceipt::where('id', $request->entry_id)->first()->collection_receipt;
            $company = CollectionReceipt::where('id', $request->entry_id)->first()->company;
            $sql = CollectionReceipt::where('id', $request->entry_id)->update(['status' => 'VALID', 'stage' => '1']);
        }

        if($request->current_page == 'bs'){
            $current_page = 'BILLING STATEMENT';
            $reference_number = BillingStatement::where('id', $request->entry_id)->first()->billing_statement;
            $company = BillingStatement::where('id', $request->entry_id)->first()->billing_statement;
            $sql = BillingStatement::where('id', $request->entry_id)->update(['status' => 'VALID', 'stage' => '1']);
        }

        if($request->current_page == 'or'){
            $current_page = 'OFFICIAL RECEIPT';
            $reference_number = OfficialReceipt::where('id', $request->entry_id)->first()->official_receipt;
            $company = OfficialReceipt::where('id', $request->entry_id)->first()->company;
            $sql = OfficialReceipt::where('id', $request->entry_id)->update(['status' => 'VALID', 'stage' => '1']);
        }

        if($request->current_page == 'dr'){
            $current_page = 'DELIVERY RECEIPT';
            $reference_number = DeliveryReceipt::where('id', $request->entry_id)->first()->delivery_receipt;
            $company = DeliveryReceipt::where('id', $request->entry_id)->first()->company;
            $sql = DeliveryReceipt::where('id', $request->entry_id)->update(['status' => 'VALID', 'stage' => '1']);
        }

        if($sql){
            if($request->current_page == 'si'){
                $count = $this->get_count('sales_invoices');
                event(new NewSi($count));
            }

            if($request->current_page == 'cr'){
                $count = $this->get_count('collection_receipts');
                event(new NewCr($count));
            }

            if($request->current_page == 'bs'){
                $count = $this->get_count('billing_statements');
                event(new NewBs($count));
            }

            if($request->current_page == 'or'){
                $count = $this->get_count('official_receipts');
                event(new NewOr($count));
            }

            if($request->current_page == 'dr'){
                $count = $this->get_count('delivery_receipts');
                event(new NewDr($count));
            }

            $remarklogs = new RemarkLogs;
            $remarklogs->username = auth()->user()->name;
            $remarklogs->role = Role::where('id', auth()->user()->userlevel)->first()->name;
            $remarklogs->activity = "USER SUCCESSFULLY MARKED AS VALID $current_page ($reference_number) - $company.";
            $remarklogs->save();

            $userlogs = new UserLogs;
            $userlogs->username = auth()->user()->name;
            $userlogs->role = auth()->user()->department.' - '.Role::where('id', auth()->user()->userlevel)->first()->name;
            $userlogs->activity = "USER SUCCESSFULLY MARKED AS VALID $current_page ($reference_number) - $company.";
            $userlogs->save();
            return 'true';
        }
        else{
            return 'false';
        }
    }

    public function disapprove(Request $request){
        if($request->current_page == 'si'){
            $current_page = 'SALES INVOICE';
            $reference_number = SalesInvoice::where('id', $request->entry_id)->first()->sales_invoice;
            $company = SalesInvoice::where('id', $request->entry_id)->first()->company;
            $sql = SalesInvoice::where('id', $request->entry_id)->update(['remarks' => $request->remarks, 'status' => 'INVALID', 'stage' => '1']);
        }

        if($request->current_page == 'cr'){
            $current_page = 'COLLECTION RECEIPT';
            $reference_number = CollectionReceipt::where('id', $request->entry_id)->first()->collection_receipt;
            $company = CollectionReceipt::where('id', $request->entry_id)->first()->company;
            $sql = CollectionReceipt::where('id', $request->entry_id)->update(['remarks' => $request->remarks, 'status' => 'INVALID', 'stage' => '1']);
        }

        if($request->current_page == 'bs'){
            $current_page = 'BILLING STATEMENT';
            $reference_number = BillingStatement::where('id', $request->entry_id)->first()->billing_statement;
            $company = BillingStatement::where('id', $request->entry_id)->first()->company;
            $sql = BillingStatement::where('id', $request->entry_id)->update(['remarks' => $request->remarks, 'status' => 'INVALID', 'stage' => '1']);
        }

        if($request->current_page == 'or'){
            $current_page = 'OFFICIAL RECEIPT';
            $reference_number = OfficialReceipt::where('id', $request->entry_id)->first()->official_receipt;
            $company = OfficialReceipt::where('id', $request->entry_id)->first()->company;
            $sql = OfficialReceipt::where('id', $request->entry_id)->update(['remarks' => $request->remarks, 'status' => 'INVALID', 'stage' => '1']);
        }

        if($request->current_page == 'dr'){
            $current_page = 'DELIVERY RECEIPT';
            $reference_number = DeliveryReceipt::where('id', $request->entry_id)->first()->delivery_receipt;
            $company = DeliveryReceipt::where('id', $request->entry_id)->first()->company;
            $sql = DeliveryReceipt::where('id', $request->entry_id)->update(['remarks' => $request->remarks, 'status' => 'INVALID', 'stage' => '1']);
        }

        if($sql){
            if($request->current_page == 'si'){
                $count = $this->get_count('sales_invoices');
                event(new NewSi($count));
            }

            if($request->current_page == 'cr'){
                $count = $this->get_count('collection_receipts');
                event(new NewCr($count));
            }

            if($request->current_page == 'bs'){
                $count = $this->get_count('billing_statements');
                event(new NewBs($count));
            }

            if($request->current_page == 'or'){
                $count = $this->get_count('official_receipts');
                event(new NewOr($count));
            }

            if($request->current_page == 'dr'){
                $count = $this->get_count('delivery_receipts');
                event(new NewDr($count));
            }

            $remarklogs = new RemarkLogs;
            $remarklogs->username = auth()->user()->name;
            $remarklogs->role = Role::where('id', auth()->user()->userlevel)->first()->name;
            $remarklogs->activity = "USER SUCCESSFULLY MARKED AS INVALID $current_page ($reference_number) - $company with remarks: '$request->remarks'.";
            $remarklogs->save();

            $userlogs = new UserLogs;
            $userlogs->username = auth()->user()->name;
            $userlogs->role = auth()->user()->department.' - '.Role::where('id', auth()->user()->userlevel)->first()->name;
            $userlogs->activity = "USER SUCCESSFULLY MARKED AS INVALID $current_page ($reference_number) - $company with remarks: '$request->remarks'.";
            $userlogs->save();
            return 'true';
        }
        else{
            return 'false';
        }
    }

    public function return_to_encoder(Request $request){
        if($request->current_page == 'si'){
            $current_page = 'SALES INVOICE';
            $reference_number = SalesInvoice::where('id', $request->entry_id)->first()->sales_invoice;
            $company = SalesInvoice::where('id', $request->entry_id)->first()->company;
            $sql = SalesInvoice::where('id', $request->entry_id)->update(['remarks' => $request->remarks, 'status' => 'FOR VALIDATION']);
        }

        if($request->current_page == 'cr'){
            $current_page = 'COLLECTION RECEIPT';
            $reference_number = CollectionReceipt::where('id', $request->entry_id)->first()->collection_receipt;
            $company = CollectionReceipt::where('id', $request->entry_id)->first()->company;
            $sql = CollectionReceipt::where('id', $request->entry_id)->update(['remarks' => $request->remarks, 'status' => 'FOR VALIDATION']);
        }

        if($request->current_page == 'bs'){
            $current_page = 'BILLING STATEMENT';
            $reference_number = BillingStatement::where('id', $request->entry_id)->first()->billing_statement;
            $company = BillingStatement::where('id', $request->entry_id)->first()->company;
            $sql = BillingStatement::where('id', $request->entry_id)->update(['remarks' => $request->remarks, 'status' => 'FOR VALIDATION']);
        }

        if($request->current_page == 'or'){
            $current_page = 'OFFICIAL RECEIPT';
            $reference_number = OfficialReceipt::where('id', $request->entry_id)->first()->official_receipt;
            $company = OfficialReceipt::where('id', $request->entry_id)->first()->company;
            $sql = OfficialReceipt::where('id', $request->entry_id)->update(['remarks' => $request->remarks, 'status' => 'FOR VALIDATION']);
        }

        if($request->current_page == 'dr'){
            $current_page = 'DELIVERY RECEIPT';
            $reference_number = DeliveryReceipt::where('id', $request->entry_id)->first()->delivery_receipt;
            $company = DeliveryReceipt::where('id', $request->entry_id)->first()->company;
            $sql = DeliveryReceipt::where('id', $request->entry_id)->update(['remarks' => $request->remarks, 'status' => 'FOR VALIDATION']);
        }

        if($sql){
            if($request->current_page == 'si'){
                $count = $this->get_count('sales_invoices');
                event(new NewSi($count));
            }

            if($request->current_page == 'cr'){
                $count = $this->get_count('collection_receipts');
                event(new NewCr($count));
            }

            if($request->current_page == 'bs'){
                $count = $this->get_count('billing_statements');
                event(new NewBs($count));
            }

            if($request->current_page == 'or'){
                $count = $this->get_count('official_receipts');
                event(new NewOr($count));
            }

            if($request->current_page == 'dr'){
                $count = $this->get_count('delivery_receipts');
                event(new NewDr($count));
            }

            $remarklogs = new RemarkLogs;
            $remarklogs->username = auth()->user()->name;
            $remarklogs->role = Role::where('id', auth()->user()->userlevel)->first()->name;
            $remarklogs->activity = "USER SUCCESSFULLY RETURNED TO ENCODER $current_page ($reference_number) - $company with remarks: '$request->remarks'.";
            $remarklogs->save();

            $userlogs = new UserLogs;
            $userlogs->username = auth()->user()->name;
            $userlogs->role = auth()->user()->department.' - '.Role::where('id', auth()->user()->userlevel)->first()->name;
            $userlogs->activity = "USER SUCCESSFULLY RETURNED TO ENCODER $current_page ($reference_number) - $company with remarks: '$request->remarks'.";
            $userlogs->save();
            return 'true';
        }
        else{
            return 'false';
        }
    }

    public function notif_update(Request $request){
        if($request->checking != 'default'){
            $latest = DB::table($request->check_table)
                        ->where('id', $request->check_current_id)
                        ->first()
                        ->updated_at;
            $current = $request->check_updated_at;

            $result = $latest != $current ? 'true' : 'false';
            $changed_id = $request->check_current_id;
        }
        else{
            $result = 'false';
            $changed_id = '0';
        }

        $user_update = !User::count() ? 'NULL' : User::where('id', auth()->user()->id)->first()->updated_at;
        $acc_update = !User::count() ? 'NULL' : User::latest('updated_at')->first()->updated_at;
        $log_update = !UserLogs::count() ? 0 : UserLogs::select()->count();

        return $data = array(
            'result' => $result, 'changed_id' => $changed_id,
            'user_update' => $user_update, 'acc_update' => $acc_update, 'log_update' => $log_update,
        );
    }

    public function checkNext(Request $request){
        $tables = [
            '/si' => 'sales_invoices',
            '/cr' => 'collection_receipts',
            '/bs' => 'billing_statements',
            '/or' => 'official_receipts',
            '/dr' => 'delivery_receipts',
        ];

        $table = $tables[$request->current_location];
        $next = DB::table($table)
                    ->where('status', 'FOR VALIDATION')
                    ->latest('updated_at')
                    ->first()
                    ->id;
        return $next ? $next : '0';
    }

    public function requestEdit(Request $request){
        if($request->current_page == 'si'){
            $current_page = 'SALES INVOICE';
            $reference_number = SalesInvoice::where('id', $request->entry_id)->first()->sales_invoice;
            $company = SalesInvoice::where('id', $request->entry_id)->first()->company;
            $sql = SalesInvoice::where('id', $request->entry_id)->update(['status' => 'FOR CORRECTION']);
        }

        if($request->current_page == 'cr'){
            $current_page = 'COLLECTION RECEIPT';
            $reference_number = CollectionReceipt::where('id', $request->entry_id)->first()->collection_receipt;
            $company = CollectionReceipt::where('id', $request->entry_id)->first()->company;
            $sql = CollectionReceipt::where('id', $request->entry_id)->update(['status' => 'FOR CORRECTION']);
        }

        if($request->current_page == 'bs'){
            $current_page = 'BILLING STATEMENT';
            $reference_number = BillingStatement::where('id', $request->entry_id)->first()->billing_statement;
            $company = BillingStatement::where('id', $request->entry_id)->first()->company;
            $sql = BillingStatement::where('id', $request->entry_id)->update(['status' => 'FOR CORRECTION']);
        }

        if($request->current_page == 'or'){
            $current_page = 'OFFICIAL RECEIPT';
            $reference_number = OfficialReceipt::where('id', $request->entry_id)->first()->official_receipt;
            $company = OfficialReceipt::where('id', $request->entry_id)->first()->company;
            $sql = OfficialReceipt::where('id', $request->entry_id)->update(['status' => 'FOR CORRECTION']);
        }

        if($request->current_page == 'dr'){
            $current_page = 'DELIVERY RECEIPT';
            $reference_number = DeliveryReceipt::where('id', $request->entry_id)->first()->delivery_receipt;
            $company = DeliveryReceipt::where('id', $request->entry_id)->first()->company;
            $sql = DeliveryReceipt::where('id', $request->entry_id)->update(['status' => 'FOR CORRECTION']);
        }

        if($sql){
            $userlogs = new UserLogs;
            $userlogs->username = auth()->user()->name;
            $userlogs->role = auth()->user()->department.' - '.Role::where('id', auth()->user()->userlevel)->first()->name;
            $userlogs->activity = "USER SUCCESSFULLY REQUESTED CORRECTION $current_page ($reference_number) - $company.";
            $userlogs->save();
            return 'true';
        }
        else{
            return 'false';
        }
    }
}
