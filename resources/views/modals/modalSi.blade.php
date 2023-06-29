<div id="siModal" class="modal fade">
    <div class="modal-dialog" style="margin-top: 150px;">
        <div class="modal-content">
            <div class="modal-header bg-default">
                <h5 class="modal-title w-100 text-center" id="siTitle"></h5>
                <button type="button" class="btn-close btn-close-white close btnClose" data-bs-dismiss="modal"></button>
            </div>
        <div class="modal-body">
            <input type="hidden" id="entry_id">
            <form id="form_reset">
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
                            <select class="form-select forminput form-control requiredField" id="company">
                                <option value="" disabled selected>SELECT COMPANY</option>
                                <option value="APSOFT">APSOFT</option>
                                <option value="IDSI">IDSI</option>
                                <option value="PLSI">PLSI</option>
                            </select>
                            <label for="company" class="formlabel form-label">COMPANY</label>
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
                            <input class="forminput form-control spChar bg-white text-uppercase" type="search" id="purchase_order" name="purchase_order" placeholder=" ">
                            <label for="purchase_order" class="formlabel form-label">PURCHASE ORDER NO. <span style="font-size: 13px !important;">(OPTIONAL)</label>
                        </div>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col">
                        <div class="f-outline">
                            <input class="forminput form-control spChar bg-white text-uppercase" type="search" id="sales_order" name="sales_order" placeholder=" ">
                            <label for="sales_order" class="formlabel form-label">SALES ORDER NO. <span style="font-size: 13px !important;">(OPTIONAL)</label>
                        </div>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col">
                        <div class="f-outline">
                            <input class="forminput form-control spChar bg-white text-uppercase" type="search" id="delivery_receipt" name="delivery_receipt" placeholder=" ">
                            <label for="delivery_receipt" class="formlabel form-label">DELIVERY RECEIPT NO. <span style="font-size: 13px !important;">(OPTIONAL)</label>
                        </div>
                    </div>
                </div>
                <div class="row" id="file_div">
                    <div class="col-7">
                        <input type="file" id="pdf_file" name="pdf_file" class="form-control requiredField" accept=".pdf"/>
                    </div>
                    <div class="col mt-2">
                        <span class="pdf_file"></span>
                    </div>
                </div>
            </div>
            <div class="modal-footer footer_hide">
                @role('ADMIN')
                    <button type="button" id="btnApprove" class="btn btn-primary mr-auto bp"><i class="fas fa-check"></i> APPROVE</button>
                @endrole
                <button type="reset"  id="btnClear" class="btn btn-primary float-end bp"><i class="fas fa-eraser"></i> CLEAR</button>
                <button type="button" id="btnSave"  class="btn btn-primary float-end bp btnRequired"><i class="fas fa-save"></i> SAVE</button>
                <button type="button" id="btnEdit"  class="btn btn-primary float-end bp btnRequired" style="display:none;"><i class="fas fa-save"></i> UPDATE</button>
            </div>
        </form>
        </div>
    </div>
</div>