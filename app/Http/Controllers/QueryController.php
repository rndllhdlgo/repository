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
                                            ->where('status', 'VALID')
                                            ->whereDate('updated_at', '>', $request->start_date)
                                            ->orderBy('updated_at', 'DESC')
                                            ->first();
                $file = $select->pdf_file;
                $created = substr($select->created_at, 0, 10);
                    if(strpos($file, 'storage/') === false){
                        $file = "/storage/delivery_receipt/$created/$file";
                    }
                    array_push($file_urls, $file ? "$file_ref = $file" : "$file_ref = NOT FOUND");
            }
            return implode(', ', $file_urls);
        }
        return 'UNASSIGNED';
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
