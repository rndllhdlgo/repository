<div id="siModal" class="modal fade">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-default">
                <h5 class="modal-title w-100 text-center">ADD SALES INVOICE</h5>
                <button type="button" class="btn-close btn-close-white close btnClose" data-bs-dismiss="modal"></button>
            </div>
        <div class="modal-body">
            <form>
                <div class="row mb-3">
                    <div class="col-6">
                        <div class="f-outline">
                            <input class="forminput form-control requiredField bg-white text-uppercase" type="search" id="sales_invoice" name="sales_invoice" placeholder=" ">
                            <label for="sales_invoice" class="formlabel form-label">SALES INVOICE NO.</label>
                        </div>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col">
                        <div class="f-outline">
                            <input class="forminput form-control requiredField bg-white text-uppercase" type="search" id="client_name" name="client_name" placeholder=" ">
                            <label for="client_name" class="formlabel form-label">CLIENT NAME</label>
                        </div>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col">
                        <div class="f-outline">
                            <input class="forminput form-control requiredField bg-white text-uppercase" type="search" id="branch_name" name="branch_name" placeholder=" ">
                            <label for="branch_name" class="formlabel form-label">BRANCH NAME</label>
                        </div>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col">
                        <div class="f-outline">
                            <input class="forminput form-control requiredField bg-white text-uppercase" type="date" id="date_created" name="date_created" placeholder=" ">
                            <label for="date_created" class="formlabel form-label">DATE CREATED</label>
                        </div>
                    </div>
                    <div class="col">
                        <div class="f-outline">
                            <input class="forminput form-control requiredField bg-white text-uppercase" type="date" id="date_received" name="date_received" placeholder=" ">
                            <label for="date_received" class="formlabel form-label">DATE RECEIVED</label>
                        </div>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col">
                        <div class="f-outline">
                            <input class="forminput form-control requiredField bg-white text-uppercase" type="search" id="purchase_order" name="purchase_order" placeholder=" ">
                            <label for="purchase_order" class="formlabel form-label">PURCHASE ORDER NO.</label>
                        </div>
                    </div>
                    <div class="col">
                        <div class="f-outline">
                            <input class="forminput form-control requiredField bg-white text-uppercase" type="search" id="delivery_receipt" name="delivery_receipt" placeholder=" ">
                            <label for="delivery_receipt" class="formlabel form-label">DELIVERY RECEIPT NO.</label>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col">
                        <input type="file" id="sales_invoice_file" name="sales_invoice_file" class="form-control requiredField" accept="image/*,.pdf"/>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="reset"  class="btn btn-primary float-end bp"><i class="fas fa-eraser"></i> CLEAR</button>
                <button type="button" id="btnSave" class="btn btn-primary float-end bp btnRequired"><i class="fas fa-save"></i> SAVE</button>
            </div>
        </form>
        </div>
    </div>
</div>