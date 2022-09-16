<!DOCTYPE html>
<html lang="en" dir="ltr">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    {{--  <link rel="icon" href="{{ asset('assets/frontend/logo/gemology_icon_color.png')}}">  --}}
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link rel="icon" href="{{asset('assets/img/favicon.png')}}" type="image/gif">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="{{asset('assets/css/bootstrap.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('assets/css/owl.carousel.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('assets/css/menu.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('assets/css/style.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('assets/css/arabic.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('assets/css/responsive.css')}}">
    <!-- stylesheet ends -->
    <title>@yield('title')</title>
    @yield('style')
    @yield('head')
    {{--  <link rel="stylesheet" type="text/css" href="{{asset('assets/frontend/css/custom.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('assets/frontend/css/responsive_custom.css')}}">  --}}
    <style>
    .loader {
        position: fixed;
        left: 0px;
        top: 0px;
        width: 100%;
        height: 100%;
        z-index: 9999;
        background: url("{{ asset('uploads/Loader.gif') }}") 50% 50% no-repeat rgb(249,249,249);
    }

/*{{ asset('assets/frontend/images/preview.gif')}}*/
</style>
</head>
<body>
    <div class="loader"></div>


    
    @yield('page')
    <!-- Scripts -->
    <div class="modal fade" id="alert_confirm" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                  <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Confirm</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                  </div>
                  <div class="modal-body">
                    <p class="alert_dynamic_message">
                    </p>
                  </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="default_confirm" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Confirm</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
              </div>
              <div class="modal-body">
                <p class="dynamic_message">
                    Are you sure ?
                </p>
                <input type="hidden" name="checkbox_data" class="checkbox_data">
                <input type="hidden" name="checkbox_type" class="checkbox_type">
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">No</button>
                <button type="button" class="btn btn-danger yes_click">Yes</button>
              </div>
            </div>
        </div>
    </div>
    <script type="text/javascript" type="text/js" src="{{ asset('assets/frontend/js/jquery.min.js')}}"></script>
    <script type="text/javascript" type="text/js" src="{{ asset('assets/frontend/js/slick.js')}}"></script>
    <script type="text/javascript" type="text/js" src="{{ asset('assets/frontend/js/bootstrap5.min.js')}}"></script>
    <script type="text/javascript" type="text/js" src="{{ asset('assets/frontend/js/custom.js')}}"></script>

    <script type="text/javascript" type="text/js" src="{{ asset('assets/frontend/custom/js/jquery.dataTables.min.js') }}"></script>
    <script type="text/javascript" type="text/js" src="{{ asset('assets/frontend/custom/js/dataTables.bootstrap5.min.js') }}"></script>
    <script src="{{ asset('assets/frontend/js/bootstrap-datepicker.js') }}"></script>
    <script src="{{ asset('assets/frontend/js/bootstrap-select.min.js') }}"></script>
    <script src="{{ asset('assets/frontend/js/jquery-ui.js') }}"></script>
     <script src="https://www.gstatic.com/firebasejs/7.23.0/firebase.js"></script>
    <script type="text/javascript">
        

        
        {{--  $(window).scroll(function () {
            jQuery(this).scrollTop() > 10 ? jQuery(".content-header").addClass("sticky") : jQuery(".content-header").removeClass("sticky")
        });
        // package toggle --------------------------------------------------------------------------------------
        $('h3.toggle-btn').on("click", function () {
            if ($(window).width() < 1900) {
                $('.active-toggle').slideToggle();
            }
            $('.active-toggle').toggleClass('toggled-on');
        });  --}}
        {{--  function messages(classname, msg)
        {
           return '<div class="alert ' + classname + ' alert-dismissible fade show" role="alert">' + msg + '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button> </div>';
        }  --}}

        // Resize Function

        $(window).on("load resize", function (e) {
            if ($(window).width() > 1900) {
                $(".active-toggle").show();
            }
            // else {
            //  $(".active-toggle").hide();
            // }
        });

        $(window).on('load', function(){ 
            $('.loader').fadeOut();
        });
        /*$(document).ajaxStart(function(){
            $(".loader").fadeIn();
        }).ajaxStop(function(){
            $('.loader').fadeOut();
        });*/

        
    
    {{--  const firebaseConfig = {
      apiKey: "AIzaSyC9T-MbVJD7xHJqPp3dU8oB9nVn8OvAbCY",
      //databaseURL: "https://XXXX.firebaseio.com",
      authDomain: "wonup-653fd.firebaseapp.com",
      projectId: "wonup-653fd",
      storageBucket: "wonup-653fd.appspot.com",
      messagingSenderId: "4372952293",
      appId: "1:4372952293:web:2215e70ec11e5cacb1900c",
      measurementId: "G-9V3YELCHB8"
    };
      
    firebase.initializeApp(firebaseConfig);
    const messaging = firebase.messaging();
    $( document ).ready(function() {
        initFirebaseMessagingRegistration();
    });
    function initFirebaseMessagingRegistration() {
            messaging
            .requestPermission()
            .then(function () {
                return messaging.getToken()
            })
            .then(function(token) {
   
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                 var device_token = "{{ isset($device_token) ? $device_token : ''}}";
                 if(device_token != token)
                 {
                    $.ajax({
                        url: '{{ route("frontend.savetoken") }}',
                        type: 'POST',
                        data: {
                            token: token
                        },
                        dataType: 'JSON',
                        success: function (response) {
                            console.log('Token saved successfully.');
                            localStorage.setItem('popState','shown')
                        },
                        error: function (err) {
                            console.log('User Chat Token Error'+ err);
                        },
                    });

                 }
                
            }).catch(function (err) {
                console.log('User Chat Token Error'+ err);
            });
     }  
      
        messaging.onMessage(function(payload) {
            const noteTitle = payload.notification.title;
            const noteOptions = {
                body: payload.notification.body,
                icon: payload.notification.icon,
            };
            new Notification(noteTitle, noteOptions);
        });  --}}
        

        {{--  //hide show ul li
        $(document).on('click', '.show-dropdown-list', function() {
            var $this = $(this);
           $(document).find('.all-action-dropdown .ul-action').addClass('d-none');
            $this.parents('.all-action-dropdown').find('.ul-action').removeClass('d-none');
           if($this.parents('.all-action-dropdown').find('.ul-action').hasClass('show'))
           {
                $this.parents('.all-action-dropdown').find('.ul-action').removeClass('d-none');

           }
           else
           {
                $this.parents('.all-action-dropdown').find('.ul-action').addClass('d-none');
           }
            
        });
        $('body').on('click',function(){
            $(document).find('.all-action-dropdown .ul-action').addClass('d-none');
        });  --}}

    </script>
    @yield('script')
</body>
</html>
