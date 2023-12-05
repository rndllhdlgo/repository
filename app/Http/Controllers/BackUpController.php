<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\{
    SalesInvoice,
    OfficialReceipt,
    BillingStatement,
    DeliveryReceipt,
    CollectionReceipt
};
class BackUpController extends Controller
{
    public function backup(){
        $sales_invoices = SalesInvoice::all();
        if(!$sales_invoices->isEmpty()){
            $sql_sales_invoices = '';
            foreach($sales_invoices as $sales_invoice){
                $client_name   = str_replace('"', "'", $sales_invoice->client_name);
                $business_name = str_replace('"', "'", $sales_invoice->business_name);
                $branch_name   = str_replace('"', "'", $sales_invoice->branch_name);
                $sql_sales_invoices .= "REPLACE INTO `sales_invoices_copy`
                (
                    `id`,
                    `sales_invoice`,
                    `company`,
                    `client_name`,
                    `business_name`,
                    `branch_name`,
                    `uploaded_by`,
                    `purchase_order`,
                    `sales_order`,
                    `delivery_receipt`,
                    `pdf_file`,
                    `remarks`,
                    `status`,
                    `stage`,
                    `created_at`,
                    `updated_at`
                )
                VALUES
                (
                    '$sales_invoice->id',
                    '$sales_invoice->sales_invoice',
                    '$sales_invoice->company',
                    \"$client_name\",
                    \"$business_name\",
                    \"$branch_name\",
                    '$sales_invoice->uploaded_by',
                    '$sales_invoice->purchase_order',
                    '$sales_invoice->sales_order',
                    '$sales_invoice->delivery_receipt',
                    '$sales_invoice->pdf_file',
                    '$sales_invoice->remarks',
                    '$sales_invoice->status',
                    '$sales_invoice->stage',
                    '$sales_invoice->created_at',
                    '$sales_invoice->updated_at'
                );\n";
            }

            $sales_invoiceBackUpPath = storage_path('app/public/backupsql/sales_invoices.sql');
            file_put_contents($sales_invoiceBackUpPath, $sql_sales_invoices);
        }
        else{
            return 'empty';
            $sales_invoiceBackUpPath = null;
        }

        $official_receipts = OfficialReceipt::all();
        if(!$official_receipts->isEmpty()){
            $sql_official_receipts = '';
            foreach($official_receipts as $official_receipt){
                $client_name   = str_replace('"', "'", $official_receipt->client_name);
                $branch_name   = str_replace('"', "'", $official_receipt->branch_name);
                $sql_official_receipts .= "REPLACE INTO `official_receipts_copy`
                (
                    `id`,
                    `official_receipt`,
                    `company`,
                    `client_name`,
                    `branch_name`,
                    `uploaded_by`,
                    `sales_order`,
                    `pdf_file`,
                    `remarks`,
                    `status`,
                    `stage`,
                    `created_at`,
                    `updated_at`
                )
                VALUES
                (
                    '$official_receipt->id',
                    '$official_receipt->official_receipt',
                    '$official_receipt->company',
                    \"$client_name\",
                    \"$branch_name\",
                    '$official_receipt->uploaded_by',
                    '$official_receipt->sales_order',
                    '$official_receipt->pdf_file',
                    '$official_receipt->remarks',
                    '$official_receipt->status',
                    '$official_receipt->stage',
                    '$official_receipt->created_at',
                    '$official_receipt->updated_at'
                );\n";
            }

            $official_receiptBackUpPath = storage_path('app/public/backupsql/official_receipts.sql');
            file_put_contents($official_receiptBackUpPath, $sql_official_receipts);
        }
        else{
            return 'empty';
            $official_receiptBackUpPath = null;
        }

        $delivery_receipts = DeliveryReceipt::all();
        if(!$delivery_receipts->isEmpty()){
            $sql_delivery_receipts = '';
            foreach($delivery_receipts as $delivery_receipt){
                $client_name   = str_replace('"', "'", $delivery_receipt->client_name);
                $business_name = str_replace('"', "'", $delivery_receipt->business_name);
                $branch_name   = str_replace('"', "'", $delivery_receipt->branch_name);
                $sql_delivery_receipts .= "REPLACE INTO `delivery_receipts_copy`
                (
                    `id`,
                    `delivery_receipt`,
                    `company`,
                    `client_name`,
                    `business_name`,
                    `branch_name`,
                    `uploaded_by`,
                    `purchase_order`,
                    `sales_order`,
                    `pdf_file`,
                    `remarks`,
                    `status`,
                    `stage`,
                    `created_at`,
                    `updated_at`
                )
                VALUES
                (
                    '$delivery_receipt->id',
                    '$delivery_receipt->delivery_receipt',
                    '$delivery_receipt->company',
                    \"$client_name\",
                    \"$business_name\",
                    \"$branch_name\",
                    '$delivery_receipt->purchase_order',
                    '$delivery_receipt->uploaded_by',
                    '$delivery_receipt->sales_order',
                    '$delivery_receipt->pdf_file',
                    '$delivery_receipt->remarks',
                    '$delivery_receipt->status',
                    '$delivery_receipt->stage',
                    '$delivery_receipt->created_at',
                    '$delivery_receipt->updated_at'
                );\n";
            }

            $delivery_receiptBackUpPath = storage_path('app/public/backupsql/delivery_receipts.sql');
            file_put_contents($delivery_receiptBackUpPath, $sql_delivery_receipts);
        }
        else{
            return 'empty';
            $delivery_receiptBackUpPath = null;
        }

        $billing_statements = BillingStatement::all();
        if(!$billing_statements->isEmpty()){
            $sql_billing_statements = '';
            foreach($billing_statements as $billing_statement){
                $client_name   = str_replace('"', "'", $billing_statement->client_name);
                $business_name = str_replace('"', "'", $billing_statement->business_name);
                $branch_name   = str_replace('"', "'", $billing_statement->branch_name);
                $sql_billing_statements .= "REPLACE INTO `billing_statements_copy`
                (
                    `id`,
                    `billing_statement`,
                    `company`,
                    `client_name`,
                    `business_name`,
                    `branch_name`,
                    `uploaded_by`,
                    `purchase_order`,
                    `sales_order`,
                    `pdf_file`,
                    `remarks`,
                    `status`,
                    `stage`,
                    `created_at`,
                    `updated_at`
                )
                VALUES
                (
                    '$billing_statement->id',
                    '$billing_statement->billing_statement',
                    '$billing_statement->company',
                    \"$client_name\",
                    \"$business_name\",
                    \"$branch_name\",
                    '$billing_statement->purchase_order',
                    '$billing_statement->uploaded_by',
                    '$billing_statement->sales_order',
                    '$billing_statement->pdf_file',
                    '$billing_statement->remarks',
                    '$billing_statement->status',
                    '$billing_statement->stage',
                    '$billing_statement->created_at',
                    '$billing_statement->updated_at'
                );\n";
            }

            $billing_statementBackUpPath = storage_path('app/public/backupsql/billing_statements.sql');
            file_put_contents($billing_statementBackUpPath, $sql_billing_statements);
        }
        else{
            return 'empty';
            $billing_statementBackUpPath = null;
        }

        $collection_receipts = CollectionReceipt::all();
        if(!$collection_receipts->isEmpty()){
            $sql_collection_receipts = '';
            foreach($collection_receipts as $collection_receipt){
                $client_name   = str_replace('"', "'", $collection_receipt->client_name);
                $branch_name   = str_replace('"', "'", $collection_receipt->branch_name);
                $sql_collection_receipts .= "REPLACE INTO `collection_receipts_copy`
                (
                    `id`,
                    `collection_receipt`,
                    `company`,
                    `client_name`,
                    `branch_name`,
                    `uploaded_by`,
                    `sales_order`,
                    `sales_invoice`,
                    `pdf_file`,
                    `remarks`,
                    `status`,
                    `stage`,
                    `created_at`,
                    `updated_at`
                )
                VALUES
                (
                    '$collection_receipt->id',
                    '$collection_receipt->collection_receipt',
                    '$collection_receipt->company',
                    \"$client_name\",
                    \"$branch_name\",
                    '$collection_receipt->uploaded_by',
                    '$collection_receipt->sales_order',
                    '$collection_receipt->sales_invoice',
                    '$collection_receipt->pdf_file',
                    '$collection_receipt->remarks',
                    '$collection_receipt->status',
                    '$collection_receipt->stage',
                    '$collection_receipt->created_at',
                    '$collection_receipt->updated_at'
                );\n";
            }

            $collection_receiptBackUpPath = storage_path('app/public/backupsql/collection_receipts.sql');
            file_put_contents($collection_receiptBackUpPath, $sql_collection_receipts);
        }
        else{
            return 'empty';
            $collection_receiptBackUpPath = null;
        }

        return 'completed';
    }
}
