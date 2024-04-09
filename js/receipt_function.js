
function generatePdf() {
    var element = document.getElementById('receiptContainer');
    var opt = {
        margin: 0,
        filename: 'receipt.pdf',
        image: { type: 'jpeg', quality: 0.98 },
        html2canvas: { scale: 1 },
        jsPDF: { unit: 'mm', format: 'a7', orientation: 'portrait', autoPrint: true }
    };

    // Use the output option to get the PDF content as a data URL
    html2pdf(element, opt).outputPdf().then(function (pdfDataUrl) {
        // Open the PDF in the current window
        window.location.href = pdfDataUrl;

        element.style.height = 'auto';
    });
}


function printReceipt() {
    // Show the purchase order modal
    var purchaseOrderModal = new bootstrap.Modal(document.getElementById('purchaseOrderModal'));
    purchaseOrderModal.show();
 
    // Add a class to hide the modal-footer during printing
    document.getElementById('purchaseOrderModal').classList.add('no-print-footer');
 
    // Trigger the print action
    window.print();

    // Remove the class after printing to show the modal-footer again
    document.getElementById('purchaseOrderModal').classList.remove('no-print-footer');
 
    // Close the purchase order modal after printing
    purchaseOrderModal.hide();
 }