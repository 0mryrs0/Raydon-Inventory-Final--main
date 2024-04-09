
            <!--Modal For Purchase Order-->
            <div class="modal" id="purchaseOrderModal" tabindex="-1" aria-labelledby="purchaseOrderLabel">
               <div class="modal-dialog">
                     <div class="modal-content">
                        <div class="modal-header">
                           <h5 class="modal-title" id="">Receipt</h5>
                           <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                           <!-- Purchase Order Container -->
                           <div class="container" id="purchaseOrder">
                                 <div class="purchase-header text-left">
                                    <div class="w-100">
                                       <img src="../img/companyLogo2.svg" class="w-25">
                                       <text class="h-6">Raydon Construction Trading</text>
                                    </div>
                                    <h3 class="text-center">Receipt</h3>
                                    <hr><hr>
                                    <h4 id="supplier-name">Raydon Construction </h4>
                                    <h5 class="mt-4">0966666</h5>
                                    <hr><hr>
                                 </div>
                                 <div class="purchase-body">
                                    <table class="w-100 table">
                                       <thead>
                                             <tr>
                                                <th>Qty</th>
                                                <th>Product</th>
                                                <th>Price</th>
                                                <th>Subtotal</th>
                                             </tr>
                                       </thead>
                                       <tbody id="table-receipt-body">
                                             
                                       </tbody>
                                    </table>
                                    <h6 class="fs-5" id="total">TOTAL: </h6>
                                    <h6 class="fs-5" id="cash">CASH: </h6>
                                    <h6 class="fs-5" id="receipt-change">CHANGE: </h6>
                                 </div>

                                 <hr>
                           </div>
                        </div>
                        <div class="modal-footer">
                           <button type="button" onclick="printPurchaseOrder2() " class="btn btn-success">Print</button>
                           <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        </div>
                     </div>
               </div>
            </div>

            <div class="modal fade" id="printReceiptModal" tabindex="-1" aria-labelledby="printReceiptModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title fs-1" id="printReceiptModalLabel">Printable Receipt</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                
                                <!-- Receipt Container -->
                                <div id="receiptContainer">
                                    <div class="receipt-header text-center">
                                        <img src="../img/companyLogo2.svg" class="w-50">
                                        <h6 class="fs-6">Raydon Construction Trading</h6>
                                        <sub class=" fs-6">632 Kalayaan Street, Di Ka Malaya, Pasig City Metro Manila, 1010</sub><br>
                                        <sub class=" fs-6">Contact: 099999999</sub><br>
                                        <hr>
                                        
                                    </div>
                                    <div class="receipt-body">
                                        <table class="table">
                                            <thead>
                                                <tr>
                                                    <th>Qty</th>
                                                    <th>Name</th>
                                                    <th>Price</th>
                                                    <th>Subtotal</th>
                                                </tr>
                                            </thead>
                                            <tbody id="table-receipt-body">

                                            </tbody>
                                        </table>
                                    </div>
                                    <hr>
                                    <div class="receipt-footer">
                                        <h6 class="fs-5" id="total">TOTAL: </h6>
                                        <sub class="fs-5" id="cash">CASH: </sub><br>
                                        <sub class="fs-5" id="receipt-change">CHANGE: </sub>
                                    </div>
                                    <hr>
                                    <div>
                                        <sub class="fs-5">Cashier: <?php echo $user_data['first_name'] . " " . $user_data['last_name']?></sub>
                                        <br><sub class="fs-5" id="currentDate"></sub>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button onclick="printSalesOrder()" class="btn btn-primary">Generate PDF</button>
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            </div>
                        </div>
                    </div>
</div>


function printSalesOrder() {
    // Show the purchase order modal
    var salesOrderModal = new bootstrap.Modal(document.getElementById('purchaseOrderModal'));
    salesOrderModal.show();

    // Calculate the height of the content inside the modal
    var contentHeight = document.getElementById('receiptContainer').offsetHeight;

    // Set the height of the printable receipt dynamically
    document.getElementById('receiptContainer').style.height = contentHeight + 'px';

    // Add a class to hide the modal-footer during printing
    document.getElementById('printReceiptModal').classList.add('no-print-footer');

    // Trigger the print action
    window.print();

    // Listen for the 'onafterprint' event to handle actions after printing
    window.onafterprint = function () {
        // Remove the height style after printing
        document.getElementById('receiptContainer').style.height = '';

        // Remove the class after printing to show the modal-footer again
        document.getElementById('printReceiptModal').classList.remove('no-print-footer');

        // Close the purchase order modal after printing
        salesOrderModal.hide();

        // Reset the 'onafterprint' event to null to avoid unintended actions
        window.onafterprint = null;
    };
}