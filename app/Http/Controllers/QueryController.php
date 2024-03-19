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

class QueryController extends Controller
{
    public function check_dr(Request $request){
        $file_urls = array();
        $file_refs = explode(', ',$request->file_refs);
        if(count($file_refs)){
            foreach($file_refs as $file_ref){
                $select = DeliveryReceipt::where('delivery_receipt', $file_ref)
                                            ->where('status', 'VALID');
                                            if($request->file_company != 'NONE'){
                                                $select->where('company', $request->file_company);
                                            }
                                            else{
                                                $select->whereDate('updated_at', '>', $request->start_date);
                                            }
                                            $result = $select->orderBy('updated_at', 'DESC')
                                                ->first();
                if(!$result){
                    array_push($file_urls, "$request->file_company-$file_ref = NOT FOUND");
                }
                else{
                    $file = $result->pdf_file;
                    $created = substr($result->created_at, 0, 10);
                    if(strpos($file, 'storage/') === false){
                        $file = "/storage/delivery_receipt/$created/$file";
                    }
                    array_push($file_urls, "$file_ref = $file");
                }
            }
            return implode(', ', $file_urls);
        }
        return 'UNASSIGNED';
    }

    public function sample_dr(Request $request){
        $data = DeliveryReceipt::where('company', $request->company)
            ->where('status', 'VALID')
            ->take($request->qty)
            ->get()
            ->pluck('delivery_receipt')
            ->toArray();
        return implode(',',$data);
    }

    public function pluck_function(Request $request){
        if($request->file_refs){
            return DeliveryReceipt::whereIn('delivery_receipt', explode(', ',$request->file_refs))
                ->where('status', 'VALID')
                ->whereDate('updated_at', '>', $request->start_date)
                ->get()
                ->pluck('pdf_file');
        }
    }
}
