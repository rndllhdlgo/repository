<div id="siModal" class="modal fade" data-bs-focus="false" style="height: 100vh;">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header bg-default">
                <h5 class="modal-title w-100 text-center" id="siTitle"></h5>
                <button type="button" class="btn-close btn-close-white close btnClose" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-6">
                        <input type="hidden" id="entry_id">
                        <form id="form_reset">
                            <div class="row mb-3">
                                <div class="col-6">
                                    <div class="f-outline">
                                        <input class="forminput form-control requiredField bg-white text-uppercase form_disable" type="search" id="sales_invoice" name="sales_invoice" placeholder=" ">
                                        <label for="sales_invoice" class="formlabel form-label">SALES INVOICE NO.</label>
                                    </div>
                                </div>
                                <div class="col-6" id="status_div">
                                    <div class="f-outline">
                                        <input class="forminput form-control requiredField bg-white text-uppercase" type="search" id="status" name="status" placeholder=" " readonly>
                                        <label for="status" class="formlabel form-label">STATUS</label>
                                    </div>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col">
                                    <div class="f-outline">
                                        <select class="form-select forminput form-control requiredField form_disable" id="company">
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
                                        <input class="forminput form-control requiredField bg-white text-uppercase form_disable" type="search" id="client_name" name="client_name" placeholder=" ">
                                        <label for="client_name" class="formlabel form-label">SOLD TO</label>
                                    </div>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col">
                                    <div class="f-outline">
                                        <input class="forminput form-control bg-white text-uppercase form_disable" type="search" id="business_name" name="business_name" placeholder=" ">
                                        <label for="business_name" class="formlabel form-label">BUSINESS NAME (Optional)</label>
                                    </div>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col">
                                    <div class="f-outline">
                                        <input class="forminput form-control requiredField bg-white text-uppercase form_disable" type="search" id="branch_name" name="branch_name" placeholder=" ">
                                        <label for="branch_name" class="formlabel form-label">BRANCH NAME</label>
                                    </div>
                                </div>
                            </div>
                            <div class="row mb-3" id="uploaded_by_div" style="display:none;">
                                <div class="col">
                                    <div class="f-outline">
                                        <input class="forminput form-control requiredField bg-white text-uppercase" type="search" id="uploaded_by" name="uploaded_by" placeholder=" ">
                                        <label for="uploaded_by" class="formlabel form-label">UPLOADED BY</label>
                                    </div>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col">
                                    <div class="f-outline">
                                        <input class="forminput form-control spChar bg-white text-uppercase form_disable" type="search" id="purchase_order" name="purchase_order" placeholder=" ">
                                        <label for="purchase_order" class="formlabel form-label">PURCHASE ORDER NO. <span style="font-size: 13px !important;">(OPTIONAL)</label>
                                    </div>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col">
                                    <div class="f-outline">
                                        <input class="forminput form-control spChar bg-white text-uppercase form_disable" type="search" id="sales_order" name="sales_order" placeholder=" ">
                                        <label for="sales_order" class="formlabel form-label">SALES ORDER NO. <span style="font-size: 13px !important;">(OPTIONAL)</label>
                                    </div>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col">
                                    <div class="f-outline">
                                        <input class="forminput form-control spChar bg-white text-uppercase form_disable" type="search" id="delivery_receipt" name="delivery_receipt" placeholder=" ">
                                        <label for="delivery_receipt" class="formlabel form-label">DELIVERY RECEIPT NO. <span style="font-size: 13px !important;">(OPTIONAL)</label>
                                    </div>
                                </div>
                            </div>
                            <div class="row mb-3" id="remarks_div" style="display:none;">
                                <div class="col">
                                    <div class="f-outline">
                                        <label for="remarks_text" class="form-label"><b>REMARKS:</b></label>
                                        <textarea class="form-control" id="remarks_text" rows="3" readonly></textarea>
                                    </div>
                                </div>
                            </div>
                            <div class="row" id="file_div">
                                <div class="col-7">
                                    <input type="file" id="pdf_file" name="pdf_file[]" class="form-control requiredField" accept=".jpg, .pdf" multiple/>
                                </div>
                                <div class="col mt-2">
                                    <span class="pdf_file"></span>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col">
                                    @role('ADMIN')
                                        <div class="mr-auto">
                                            <button type="button" id="btnApprove" class="btn btn-success" style="display:none;"><i class="fas fa-check"></i> <b>VALID</b></button>
                                            <button type="button" id="btnDisapprove" class="btn btn-danger" style="display:none;"><i class="fa-solid fa-xmark"></i> <b>INVALID</b></button>
                                            <button type="button" id="btnReturn" class="btn btn-danger" style="display:none;"><i class="fa-solid fa-share fa-flip-horizontal"></i> <b>RETURN TO ENCODER</b></button>
                                        </div>
                                    @endrole
                                    <button type="reset"  id="btnClear" class="btn btn-primary float-end bp"><i class="fas fa-eraser"></i> CLEAR</button>
                                    <button type="button" id="btnSave"  class="btn btn-primary float-end bp btnRequired"><i class="fas fa-save"></i> SAVE</button>
                                    <button type="button" id="btnEdit"  class="btn btn-primary float-end bp btnRequired"><i class="fas fa-save"></i> UPDATE</button>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="col-6">
                        <div class="row">
                            <div class="col">
                                <ul id="pagi" class="pagination">
                                    <li class="page-item pagi"><a class="page-link"><b>PAGES:</b></a></li>
                                </ul>
                            </div>
                            <div class="col divReplaceFile">
                                <button type="button" id="txtUploadPdf" class="btn btn-primary bp float-end" onclick="$('#pdf_file').click();">
                                    <i class="fa-solid fa-file-arrow-up mr-1"></i>
                                    <span id="txtUploadPdf">REPLACE FILE</span>
                                </button>
                                <span class="d-none">
                                    <input type="file" id="pdf_file" name="pdf_file[]" class="form-control " accept=".jpg,.pdf" multiple/>
                                </span>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col">
                                <div id="displayFile"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>