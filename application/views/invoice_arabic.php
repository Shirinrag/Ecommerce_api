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
        

        .row{
            display: flex;
            flex-wrap: wrap;
        }
        .col-6{
            width: 100%;
            /*flex: 0 0 auto;*/
        }
        .text-white{
            color: #fff;
        }
        .company-details{
            float: left;
            text-align: left;
        }
         .user-details{
            float: right;
            text-align: right;
            margin-top: -50px;
        }
        .body-section{
            padding: 16px;
            border: 1px solid gray;
        }
        .heading{
            font-size: 18px;
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
                
                <!-- <div class="col-md-6">                    
                        <p class="sub-heading">assdad asd  asda asdad a sd</p>
                        <p class="sub-heading">assdad asd asd</p>
                        <p class="sub-heading"></p>
                    
                </div> -->
                <div class="col-md-12">                    
                   <img src="http://localhost/stzsoft/Ecommerce_api/uploads/logo.jpg" style="margin: auto; float: right; width: 20%; height: 10%;">
               
                </div>
            </div>
        </div>

        <div class="body-section">
            <div class="row">
                <div class="col-md-6">
                    <p class="sub-heading"><strong>الاسم:ا</strong><?=$data[0]['user_name']?></p>
                        <p class="sub-heading"><strong>Phone Number:  </strong><?=$data[0]['contact_no']?></p>
                        <p class="sub-heading"><strong>Address:  </strong><?=$data[0]['roomno'].", ".$data[0]['building'].", ".$data[0]['street'].", ".$data[0]['pincode']?></p> 
                    
                </div>
                <div class="col-md-6">
                    <div class="user-details">
                    <h4 ><?=$data[0]['order_id']?> :رقم الفاتورة:</h4>
                    <p ><?=$data[0]['date']?><strong>: تاريخ الطلب</strong> </p>            
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
                        <th>Product</th>
                        <th class="w-20">Price</th>
                        <th class="w-20">Quantity</th>
                        <th class="w-20">Total</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>1</td>
                        <td>Product Name</td>
                        <td>10</td>
                        <td>1</td>
                        <td>10</td>
                    </tr>
                    <tr>
                        <td colspan="4" class="text-right"><strong>Sub Total</strong></td>
                        <td><?=$data[0]['sub_total']?></td>
                    </tr>
                    <tr>
                        <td colspan="4" class="text-right"><strong>Delivery Rate</strong></td>
                        <td> <?=$data[0]['tax']?></td>
                    </tr>
                    <tr>
                        <td colspan="4" class="text-right"><strong>Grand Total</strong></td>
                        <td> <?=$data[0]['grand_total']?></td>
                    </tr>
                </tbody>
            </table>
            <br>
            <h5 class="heading" style="float:right;">Payment Mode: <?=$data[0]['payment_type']?></h5>
        </div>

        <div class="body-section">
            <p>&copy; Copyright <?= date('Y')?> - Circuit Store. All rights reserved. 
               
            </p>
        </div>      
    </div>      

</body>
</html>

