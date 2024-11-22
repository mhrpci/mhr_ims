$(document).ready(function() {
    $('.datatable').DataTable({
        responsive: true,
        language: {
            search: "_INPUT_",
            searchPlaceholder: "Search...",
        },
        // Add any other DataTables options here
    });
});
