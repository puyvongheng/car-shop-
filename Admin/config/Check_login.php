<?php

// Check if the user is an admin
if (!isset($_SESSION['admin_id'])) {
    // Display an error message with Bootstrap styling and an icon
    echo '
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Unauthorized Access</title>
        <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
        <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    </head>
    <body>
        <div class="container mt-5">
            <div class="alert alert-danger d-flex align-items-center" role="alert">
                <i class="fas fa-exclamation-triangle fa-2x mr-3"></i>
                <div>
                    <h4 class="alert-heading">Unauthorized Access!ចូល​ដោយ​គ្មាន​ការ​អនុញ្ញាត!</h4>
                    <p>You do not have permission to access this page. Please contact the system administrator if you believe this is an error.អ្នកមិនមានសិទ្ធិចូលប្រើទំព័រនេះទេ។ សូមទាក់ទងអ្នកគ្រប់គ្រងប្រព័ន្ធ ប្រសិនបើអ្នកជឿថានេះជាកំហុស។</p>
                    <hr>
                    <p class="mb-0"> please <a href="../../src/pages/login.php">ត្រឡប់ទៅក្រោយ</a>.</p>
                </div>
            </div>
        </div>
    </body>
    </html>
    ';
    exit(); // Make sure to stop further execution
}
?>
