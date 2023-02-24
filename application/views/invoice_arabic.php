<!DOCTYPE html>
<html lang="ar" dir="rtl">
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
                   <img src="https://www.circuitstore.qa/Ecommerce_api/uploads/logo.jpg" style="margin: auto; float: right; width: 20%; height: 10%;">
               
                </div>
            </div>
        </div>

        <div class="body-section">
            <div class="row">
                   <table style="border:0px !important;">
                     <thead>
                        <tr style="border:0px !important;">
                            <td style="text-align:right !important;">
                                الأسم الكامل *  :<?=$data[0]['user_name']?>   
                            </td>
                            <td style="text-align:left !important;">
                             رقم الطلب :  <?=$data[0]['order_id']?> 
                            </td>
                        </tr>
                        <tr style="border:0px !important;">
                            <td style="text-align:right !important;">
                               رقم الهاتف * :<?=$data[0]['contact_no']?> 
                            </td>
                            <td style="text-align:left !important;">
                              تاريخ الطلب :  <?=$data[0]['order_date_time']?>
                            </td>
                        </tr>
                        <tr style="border:0px !important;">
                            <td style="text-align:right !important;">
                                عناوين التوصيل  
                                :<?=$data[0]['building'].", ".$data[0]['street'].", ".$data[0]['zone']?>
                            </td>
                        </tr>

                     </thead>
                   
                </table>
            </div>
        </div>

        <div class="body-section">
            <h3 class="heading">العناصر المطلوبة</h3>
            <br>
            <table class="table-bordered">
                <thead>
                    <tr>
                        <th>الرقم</th>
                        <th>اسم المنتج</th>
                        <th class="w-20">السعر</th>
                        <th class="w-20">الكمية</th>
                        <th class="w-20">المجموع</th>
                    </tr>
                </thead>
                <tbody>
                     <?php $i=0; foreach($data as $data_key => $data_row) { 
                        ?>
                     
                    <tr>
                        <td><?= ++$i;?></td>
                        <td><?=$data_row['product_name_ar']?></td>
                        <td><?=$data_row['unit_price']." "."ر.ق"?></td>
                        <td><?=$data_row['quantity']?></td>
                        <td><?=$data_row['total']." "."ر.ق"?></td>
                    </tr>
               <?php } ?>
                    <tr>
                        <td colspan="4" class="text-right"><strong>المجموع الفرعي</strong></td>
                        <td><?=$data[0]['sub_total'] ." "."ر.ق"?></td>
                    </tr>
                    <tr>
                        <td colspan="4" class="text-right"><strong>رسوم التوصيل   </strong></td>
                        <td> <?=$data[0]['tax']." "."ر.ق"?></td>
                    </tr>
                    <tr>
                        <td colspan="4" class="text-right"><strong>إجمالي المبلغ</strong></td>
                        <td> <?=$data[0]['grand_total']." "."ر.ق"?></td>
                    </tr>
                </tbody>
            </table>
            <br>
            <h5 class="heading" style="float:right;"> طريقة الدفع  :<?=$data[0]['payment_type']?> </h5>
        </div>  
    </div>      

</body>
</html>

