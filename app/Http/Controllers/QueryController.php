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
    public function check_dr(){
        return 'check_dr';
    }
}
