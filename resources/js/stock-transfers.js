// Assuming you're using Laravel Echo
Echo.private('stock-transfers')
    .listen('StockTransferEvent', (e) => {
        if (e.action === 'approved' || e.action === 'rejected') {
            // Refresh the page or update the specific row
            location.reload();

            // Or use a toast notification
            toastr.info(`Stock transfer #${e.id} has been ${e.action}`);
        }
    });
