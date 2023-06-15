<div id="crModal" class="modal fade">
    <div class="modal-dialog" style="margin-top: 150px;">
        <div class="modal-content">
            <div class="modal-header bg-default">
                <h5 class="modal-title w-100 text-center" id="crTitle"></h5>
                <button type="button" class="btn-close btn-close-white close btnClose" data-bs-dismiss="modal"></button>
            </div>
        <div class="modal-body">
            <form id="form_reset">
                <div class="row mb-3">
                    <div class="col-6">
                        <div class="f-outline">
                            <input class="forminput form-control requiredField bg-white text-uppercase disabled" type="search" id="collection_receipt" name="collection_receipt" placeholder=" ">
                            <label for="collection_receipt" class="formlabel form-label">COLLECTION RECEIPT NO.</label>
                        </div>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col">
                        <div class="f-outline">
                            <select class="form-select forminput form-control requiredField disabled" id="company">
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
                            <input class="forminput form-control requiredField bg-white text-uppercase disabled" type="search" id="client_name" name="client_name" placeholder=" ">
                            <label for="client_name" class="formlabel form-label">CLIENT NAME</label>
                        </div>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col">
                        <div class="f-outline">
                            <input class="forminput form-control requiredField bg-white text-uppercase disabled" type="search" id="branch_name" name="branch_name" placeholder=" ">
                            <label for="branch_name" class="formlabel form-label">BRANCH NAME</label>
                        </div>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col">
                        <div class="f-outline">
                            <input class="forminput form-control requiredField bg-white text-uppercase disabled" type="date" id="date_created" name="date_created" placeholder=" ">
                            <label for="date_created" class="formlabel form-label">DATE CREATED</label>
                        </div>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col">
                        <div class="f-outline">
                            <input class="forminput form-control bg-white spChar text-uppercase disabled" type="search" id="sales_order" name="sales_order" placeholder=" ">
                            <label for="sales_order" class="formlabel form-label">SALES ORDER NO.</label>
                        </div>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col">
                        <div class="f-outline">
                            <input class="forminput form-control bg-white spChar text-uppercase disabled" type="search" id="sales_invoice" name="sales_invoice" placeholder=" ">
                            <label for="sales_invoice" class="formlabel form-label">SALES INVOICE NO.</label>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col">
                        <span class="pdf_file"></span>
                        <input type="file" id="pdf_file" name="pdf_file" class="form-control requiredField" accept=".pdf"/>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="reset"  id="btnClear" class="btn btn-primary float-end bp"><i class="fas fa-eraser"></i> CLEAR</button>
                <button type="button" id="btnSave"  class="btn btn-primary float-end bp btnRequired"><i class="fas fa-save"></i> SAVE</button>
            </div>
        </form>
        </div>
    </div>
</div>