<?php
header('Content-Type: text/html; charset=UTF-8');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
    <style>
        .header{
            display: inline;
        }
        .collection{
            margin-bottom:30px;
        }
        .value{
            display: inline;
            margin-left: 60px;
        }
        .container{
            margin: 50px 80px;
        }
    </style>
</head>
<body>


<div class="head">
        <div class="leftside">
            <div>
                Cairo university
            </div>
            <div>
                Faculty of Computers and Artificial Intelegence
            </div>

        </div>
    </div>
    <div class="container">
        <div class="collection">
            <div class="header">User Name</div>
            <div data-column="" class="value">{{ $secondmentDetails['user'] }}</div>
        </div>
        <div class="collection">
            <div class="header">Description</div>
            <div data-column="" class="value">{{ $secondmentDetails['desc'] }}</div>
        </div>
        <div class="collection">
            <div class="header">Start Date</div>
            <div data-column="" class="value">{{ $secondmentDetails['start'] }}</div>
        </div>
        <div class="collection">
            <div class="header">End Date</div>
            <div data-column="" class="value">{{ $secondmentDetails['end'] }}</div>
        </div>
        <div class="collection">
            <div class="header">Type</div>
            <div data-column="" class="value">{{ $secondmentDetails['type'] }}</div>
        </div>
        <div class="collection">
            <div class="header">Country</div>
            <div data-column="" class="value">{{ $secondmentDetails['country'] }}</div>
        </div>
    </div>
</body>
</html>
