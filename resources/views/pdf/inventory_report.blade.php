<!DOCTYPE html>
<html>

<head>
    <title>Inventory Report</title>
    <!-- Bootstrap CSS v5.2.1 -->

    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 12px;
            margin: 0;
            padding: 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        table,
        th,
        td {
            border: 1px solid black;
        }

        th,
        td {
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
        }

        .text-right {
            text-align: right;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
            width: 100%;
            border-bottom: 1px solid rgba(182, 177, 177, 0.568);
        }
    </style>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous" />
</head>

<body>
    <div class="header">
        <div class="container d-flex flex-column align-items-center">
            <img class="img-fluid img-logo" src="https://merchant9.com/img/merchant9-logo.png" alt="Merchant9 Logo">
            <h1>Sales Report</h1>
            <h4>Sale period: <strong>{{ $report_generated_at_start }}</strong> to
                <strong>{{ $report_generated_at_end }}</strong>
            </h4>
        </div>
    </div>
    <div class="container">
        @foreach ($inventoryData as $description => $data)
            <h2>Description: {{ $description }}</h2>
            <h6>Category Code: {{ $data['category_code'] }}</h6>
            <h6>Items Sold: {{ $data['items_sold_count'] }}</h6>
            <h6>Total Sales Amount: {{ $data['sales_amount'] }}</h6>

            <table>
                <thead>
                    <tr>
                        <th>Item NO. </th>
                        <th>Inventory Code</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($data['inventory_codes'] as $index => $code)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $code }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endforeach
    </div>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"
        integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous">
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.min.js"
        integrity="sha384-BBtl+eGJRgqQAUMxJ7pMwbEyER4l1g+O15P+16Ep7Q9Q+zqX6gSbd85u4mG4QzX+" crossorigin="anonymous">
    </script>
</body>

</html>
