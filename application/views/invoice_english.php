<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Circuit Store</title>
    <style>
        body{
            background-color: #F6F6F6; 
            margin: 0;
            padding: 0;
        }
        h1,h2,h3,h4,h5,h6{
            margin: 0;
            padding: 0;
        }
        p{
            margin: 0;
            padding: 0;
        }
        .container{
            width: 100%;
            margin-right: auto;
            margin-left: auto;
        }
        .brand-section{
           background-color: #d98237;
           padding: 10px 40px;
        }
        /*.logo{
            width: 50%;
        }*/

        .row{
            display: flex;
            flex-wrap: wrap;
        }
        .col-6{
            width: 50%;
            flex: 0 0 auto;
        }
        .text-white{
            color: #fff;
        }
        .company-details{
            float: right;
            text-align: right;
        }
        .user-details{
            /* float: right; */
            text-align: right;
             margin-top: -50px;
        }
        .body-section{
            padding: 16px;
            border: 1px solid gray;
        }
        .heading{
            font-size: 20px;
            margin-bottom: 08px;
        }
        .sub-heading{
            color: #262626;
            margin-bottom: 05px;
        }
        table{
            background-color: #fff;
            width: 100%;
            border-collapse: collapse;
        }
        table thead tr{
            border: 1px solid #111;
            background-color: #f2f2f2;
        }
        table td {
            vertical-align: middle !important;
            text-align: center;
        }
        table th, table td {
            padding-top: 08px;
            padding-bottom: 08px;
        }
        .table-bordered{
            box-shadow: 0px 0px 5px 0.5px gray;
        }
        .table-bordered td, .table-bordered th {
            border: 1px solid #dee2e6;
        }
        .text-right{
            text-align: end;
        }
        .w-20{
            width: 20%;
        }
        .float-right{
            float: right;
        }
    </style>
</head>
<body>

    <div class="container">
        <div class="body-section">
            <div class="row">
                <div class="col-md-6">
                    <h1 class="text-white"><img src="https://www.circuitstore.qa/Ecommerce_api/uploads/logo.jpg" style="margin: auto; width: 20%; height: 10%;"></h1>
                </div>
               <!--  <div class="col-md-6">
                    <div class="company-details">
                        <p class="sub-heading">assdad asd  asda asdad a sd</p>
                        <p class="sub-heading">assdad asd asd</p>
                        <p class="sub-heading"></p>
                    </div>
                </div> -->
            </div>
        </div>

        <div class="body-section">
            <div class="row">
                <div class="col-md-6">
                    <h2 class="heading">Invoice No.: <?=$data[0]['order_id']?></h2>
                    <p class="sub-heading"><strong>Order Date:</strong> <?=$data[0]['order_date_time']?></p>
                </div>
                <div class="col-md-6">
                    <div class="user-details">
                        <p class="sub-heading"><strong>Full Name:  </strong><?=$data[0]['user_name']?></p>
                        <p class="sub-heading"><strong>Phone Number:  </strong><?=$data[0]['contact_no']?></p>
                        <p class="sub-heading"><strong>Address:  </strong><?=$data[0]['building'].", ".$data[0]['street'].", ".$data[0]['zone']?></p>                   
                    <!--  <p class="sub-heading">City,State,Pincode:  </p> -->
                    </div>
    </div>
            </div>
        </div>

        <div class="body-section">
            <h3 class="heading">Ordered Items</h3>
            <br>
            <table class="table-bordered">
                <thead>
                    <tr>
                        <th>Sr.No</th>
                        <th>Product Name</th>
                        <th class="w-20">Price</th>
                        <th class="w-20">Quantity</th>
                        <th class="w-20">Total</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $i=0; foreach($data as $data_key => $data_row) { 
                        ?>
                     
                    <tr>
                        <td><?= ++$i;?></td>
                        <td><?=$data_row['product_name']?></td>
                        <td><?="QAR"." ".$data_row['unit_price']?></td>
                        <td><?=$data_row['quantity']?></td>
                        <td><?="QAR"." ".$data_row['total']?></td>
                    </tr>
               <?php } ?>
                    <tr>
                        <td colspan="4" class="text-right"><strong>Sub Total</strong></td>
                        <td><strong>QAR <?=$data[0]['sub_total']?></strong></td>
                    </tr>
                    <tr>
                        <td colspan="4" class="text-right"><strong>Delivery Rate</strong></td>
                        <td><strong>QAR <?=$data[0]['tax']?></strong></td>
                    </tr>
                    <tr>
                        <td colspan="4" class="text-right"><strong>Grand Total</strong></td>
                        <td><strong> QAR <?=$data[0]['grand_total']?></strong></td>
                    </tr>
                </tbody>
            </table>
            <br>
            <h5 class="heading">Payment Mode: <?=$data[0]['payment_type']?></h5>
        </div>   
    </div>      

</body>
</html>

