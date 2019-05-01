<!doctype html>
<html lang="{{ app()->getLocale() }}">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Yourfut</title>

        <!-- Fonts -->
        <link href="https://fonts.googleapis.com/css?family=Raleway:100,600" rel="stylesheet" type="text/css">

        <!-- Styles -->
        <style>
            .card {
                box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2);
                max-width: 1000px;
                margin: auto;
                text-align: center;
              }
              
              .title {
                color: grey;
                font-size: 18px;
              }
              
              button {
                border: none;
                outline: 0;
                display: inline-block;
                padding: 8px;
                color: white;
                background-color: #000;
                text-align: center;
                cursor: pointer;
                width: 100%;
                font-size: 18px;
              }
              
              a {
                text-decoration: none;
                font-size: 22px;
                color: black;
              }
              
              button:hover, a:hover {
                opacity: 0.7;
              }
        </style>
    </head>
    <body>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
        <div class="card">
            <table class="table" style="text-align: left; width:100%;">
                <tr>
                    <td style="padding:20px; background: #f9f9f9; text-align:center;"><img src="{{ $userdata->profile_image }}" /></td>
                </tr>
            </table>
            <table class="table" style="text-align: left; width:960px;margin-top:20px;" align="center">
                <tr>
                    <td>
                        <table class="table" style="text-align: left; width:100%;margin-bottom:20px;border: 1px solid #c1c1c1;"> 
                            @if($userdata->name)   
                            <tr>
                                <th style="font-weight:bold; font-size:16px; padding:10px;">Name :</th>
                                <td style="font-weight:normal; font-size:16px; padding:10px;">{{ $userdata->name }} </td>
                            </tr> 
                            @endif
                            @if($userdata->email)   
                            <tr>
                                <th style="font-weight:bold; font-size:16px; padding:10px;">Email :</th>
                                <td style="font-weight:normal; font-size:16px; padding:10px;">{{ $userdata->email }}</td>
                            </tr>
                            @endif
                            @if($userdata->gender)   
                            <tr>
                                <th style="font-weight:bold; font-size:16px; padding:10px;">Gender :</th>
                                <td style="font-weight:normal; font-size:16px; padding:10px;">{{ $userdata->gender }}</td>
                            </tr>
                            @endif
                            @if($userdata->country)   
                            <tr>
                                <th style="font-weight:bold; font-size:16px; padding:10px; ">Country :</th>
                                <td style="font-weight:normal; font-size:16px; padding:10px; ">{{$userdata->country}}</td>
                            </tr>
                            @endif
                            @if($userdata->city)   
                            <tr>
                                <th style="font-weight:bold; font-size:16px; padding:10px;">City :</th>
                                <td style="font-weight:normal; font-size:16px; padding:10px;">{{$userdata->city}}</td>
                            </tr>
                            @endif

                            @if($userdata->language)   
                            <tr>
                                <th style="font-weight:bold; font-size:16px; padding:10px;">Languages:</th>
                                <td style="font-weight:normal; font-size:16px; padding:10px;">{{$userdata->language}}</td>
                            </tr>
                            @endif

                            @if($userdata->dob)   
                            <tr>
                                <th style="font-weight:bold; font-size:16px; padding:10px;">DOB :</th>
                                <td style="font-weight:normal; font-size:16px; padding:10px;">{{$userdata->dob}}</td>
                            </tr>
                            @endif

                            @if($userdata->phone)   
                            <tr>
                                <th style="font-weight:bold; font-size:16px; padding:10px;">Phone Number :</th>
                                <td style="font-weight:normal; font-size:16px; padding:10px;">{{$userdata->phone}}</td>
                            </tr>
                            @endif

                            @if($userdata->description)   
                            <tr>
                                 <th style="font-weight:bold; font-size:16px; padding:10px;">Description :</th>
                                 <td style="font-weight:normal; font-size:16px; padding:10px;">{{$userdata->description}}</td>
                            </tr>
                            @endif
                            @if($userdata->ref_name)   
                                <tr>
                                    <th style="font-weight:bold; font-size:16px; padding:10px;">Refrence Person :</th>
                                    <td style="font-weight:normal; font-size:16px; padding:10px;">{{$userdata->ref_name}}</td>
                                </tr>
                            @endif
                            @if($userdata->ref_phone)   
                            <tr>
                                <th style="font-weight:bold; font-size:16px; padding:10px;">Refrence Phone :</th>
                                <td style="font-weight:normal; font-size:16px; padding:10px;">{{$userdata->ref_phone}}</td>
                            </tr>
                            @endif
                        </table>
                        <table class="table" style="text-align: left; width:100%;margin-bottom:5px;">     
                            <tr>
                                <th style="font-weight:bold; font-size:18px;">Education Detail :</th>
                            </tr>  
                        </table>
                        @if($userdata['educationDetail'])     
                            <table class="table" style="text-align: left; width:100%;margin-bottom:20px;border: 1px solid #c1c1c1;">  
                                <thead style="background: #f9f9f9;">                 
                                    <tr>
                                        <th style="font-weight:bold; font-size:16px; padding:10px;">Institude</th>    
                                        <th style="font-weight:bold; font-size:16px; padding:10px;">Course</th>
                                        <th style="font-weight:bold; font-size:16px; padding:10px;">Degree</th>    
                                        <th style="font-weight:bold; font-size:16px; padding:10px;">Start date</th>
                                        <th style="font-weight:bold; font-size:16px; padding:10px;">End date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($userdata['educationDetail'] as $val) 
                                    <tr>
                                        <td style="font-weight:normal; font-size:14px; padding:10px;">{{$val->institute}}</td>    
                                        <td style="font-weight:normal; font-size:14px; padding:10px;">{{$val->course}}</td>
                                        <td style="font-weight:normal; font-size:14px; padding:10px;">{{$val->degree}}</td>    
                                        <td style="font-weight:normal; font-size:14px; padding:10px;">{{date("d-m-Y", $val->start_date)}}</td>
                                        <td style="font-weight:normal; font-size:14px; padding:10px;">{{date("d-m-Y", $val->end_date)}}</td>                        
                                    </tr> 
                                    @endforeach  
                                </tbody>             
                            </table>   
                            <table class="table" style="text-align: left; width:100%;margin-bottom:5px;">     
                            <tr>
                                <th style="font-weight:bold; font-size:18px;">Employment Detail :</th>
                            </tr>  
                        </table>
                            <table class="table" style="text-align: left; width:100%;margin-bottom:20px;border: 1px solid #c1c1c1;"> 
                                <thead  style="background: #f9f9f9;">
                                
                                <tr>
                                    <th style="font-weight:bold; font-size:16px; padding:10px;">Company name :</th>    
                                    <th style="font-weight:bold; font-size:16px; padding:10px;">title</th>
                                    <th style="font-weight:bold; font-size:16px; padding:10px;">Country</th>    
                                    <th style="font-weight:bold; font-size:16px; padding:10px;">City</th>
                                    <th style="font-weight:bold; font-size:16px; padding:10px;">Is currently working</th>
                                     @foreach ($userdata['employmentDetail'] as $val)          @if($val->is_currently_working)  
                                        <th style="font-weight:bold; font-size:16px; padding:10px; border-bottom: 1px solid #c1c1c1;">From date :</th>
                                        <th style="font-weight:bold; font-size:16px; padding:10px; border-bottom: 1px solid #c1c1c1;">To date :</th>
                                    @endif
                                    @endforeach 
                                </tr>
                                </thead>
                                <tbody>                                
                                    @foreach ($userdata['employmentDetail'] as $val) 
                                    <tr>
                                        <td style="font-weight:normal; font-size:14px; padding:10px;">{{$val->company_name}}</td>    
                                        <td style="font-weight:normal; font-size:14px; padding:10px;">{{$val->title}}</td>
                                        <td style="font-weight:normal; font-size:14px; padding:10px;">{{$val->country}}</td>    
                                        <td style="font-weight:normal; font-size:14px; padding:10px;">{{$val->city}}</td>
                                        <td style="font-weight:normal; font-size:14px; padding:10px;">{{ $val->is_currently_working==1?"YES":"NO"}}</td>  
                                        @if($val->is_currently_working)   
                                            <tr>
                                                <td style="font-weight:normal; font-size:16px; padding:10px; border-bottom: 1px solid #c1c1c1;">{{date("d-m-Y", $val->from_date)}}</td>
                                            </tr>
                                            <tr>
                                                <td style="font-weight:normal; font-size:16px; padding:10px; border-bottom: 1px solid #c1c1c1;">{{date("d-m-Y", $val->to_date)}}</td>
                                            </tr>
                                        @endif                      
                                    </tr>    
                                @endforeach 
                                </tbody>             
                            </table> 
                        @endif
                    </td>
                </tr>
            </table>
        </div>
        <!-- main section end -->
    </body>
</html>
