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
            <div data-column="" class="value">{{ $postgrad_array['student_name'] }}</div>
        </div>
        <div class="collection">
            <div class="header">Department Code</div>
            <div data-column="" class="value">{{ $postgrad_array['department'] }}</div>
        </div>
        <div class="collection">
            <div class="header">Academic Year</div>
            <div data-column="" class="value">{{ $postgrad_array['academic_year'] }}</div>
        </div>
        <div class="collection">
            <div class="header">Gender</div>
            <div data-column="" class="value">{{ $postgrad_array['gender'] }}</div>
        </div>
        <div class="collection">
            <div class="header">Nationality</div>
            <div data-column="" class="value">{{ $postgrad_array['nationality'] }}</div>
        </div>
        <div class="collection">
            <div class="header">Registration Date</div>
            <div data-column="" class="value">{{ $postgrad_array['registration_date'] }}</div>
        </div>
        <div class="collection">
            <div class="header">Credit Hours</div>
            <div data-column="" class="value">{{ $postgrad_array['credit_hours'] }}</div>
        </div>
        <div class="collection">
            <div class="header">Preliminary Date</div>
            <div data-column="" class="value">{{ $postgrad_array['preliminary_date'] }}</div>
        </div>
        <div class="collection">
            <div class="header">Telephone Number</div>
            <div data-column="" class="value">{{ $postgrad_array['telephone_number'] }}</div>
        </div>
        <div class="collection">
            <div class="header">Phone Number</div>
            <div data-column="" class="value">{{ $postgrad_array['phone_number'] }}</div>
        </div>
        <div class="collection">
            <div class="header">Employer</div>
            <div data-column="" class="value">{{ $postgrad_array['employer'] }}</div>
        </div>
        <div class="collection">
            <div class="header">Employer Address</div>
            <div data-column="" class="value">{{ $postgrad_array['employer_address'] }}</div>
        </div>
        <div class="collection">
            <div class="header">Grade</div>
            <div data-column="" class="value">{{ $postgrad_array['grade'] }}</div>
        </div>
        <div class="collection">
            <div class="header">Faculty Name</div>
            <div data-column="" class="value">{{ $postgrad_array['faculty_name'] }}</div>
        </div>
        <div class="collection">
            <div class="header">Graduation Date</div>
            <div data-column="" class="value">{{ $postgrad_array['graduation_date'] }}</div>
        </div>
        <div class="collection">
            <div class="header">University Name</div>
            <div data-column="" class="value">{{ $postgrad_array['university_name'] }}</div>
        </div>
        <div class="collection">
            <div class="header">Research Topic</div>
            <div data-column="" class="value">{{ $postgrad_array['research_topic_EN'] }}</div>
        </div>
        <div class="collection">
            <div class="header">Field of Interest</div>
            <div data-column="" class="value">{{ $postgrad_array['research_interest'] }}</div>
        </div>
        <div class="collection">
            <div class="header">Research Target</div>
            <div data-column="" class="value">{{ $postgrad_array['target'] }}</div>
        </div>
        <div class="collection">
            <div class="header">Research Specialization</div>
            <div data-column="" class="value">{{ $postgrad_array['specialization'] }}</div>
        </div>
        <div class="collection">
            <div class="header">Field of Research</div>
            <div data-column="" class="value">{{ $postgrad_array['field_of_research'] }}</div>
        </div>

        <div class="collection">
            <div class="header">Bachelor Certificate Link</div>
            <div data-column="" class="value">{{ $postgrad_array['bachelor_certificate_path'] }}</div>
        </div>
        <div class="collection">
            <div class="header">Attachment Link</div>
            <div data-column="" class="value">{{ $postgrad_array['attachmentPath'] }}</div>
        </div>
    </div>
</body>
</html>
