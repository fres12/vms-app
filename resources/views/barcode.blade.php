<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laravel Barcode Example</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">
</head>
<body>
    <div class="container mt-5">
        <div class="card">
            <h3 class="card-header p-3">Laravel Barcode Example</h3>
            <div class="card-body">
                <h4>Code: 0001245259636</h4>
                {!! $barcode !!}
            </div>
        </div>
    </div>
</body>
</html> 