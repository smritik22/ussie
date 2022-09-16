<script type="text/javascript">
    var public_lang = "{{ @Helper::currentLanguage()->code }}";
    var public_folder_path = "{{ asset('') }}";
    var first_day_of_week = "{{ env("FIRST_DAY_OF_WEEK",0) }}";

</script>
@stack('before-scripts')
<!-- jQuery -->
<script src="{{ asset('assets/dashboard/js/jquery/dist/jquery.js') }}"></script>
<!-- Bootstrap -->
<script src="{{ asset('assets/dashboard/js/tether/dist/js/tether.min.js') }}" defer></script>
<script src="{{ asset('assets/dashboard/js/bootstrap/dist/js/bootstrap.js') }}" defer></script>
{{-- <script src="{{ asset('assets/dashboard/js/moment/moment.js') }}" defer></script> --}}
<script src="{{ asset('assets/dashboard/js/moment/moment.js') }}" defer></script>
<script src="{{ asset('assets/dashboard/js/moment/locale/'.@Helper::currentLanguage()->code.'.js') }}" defer></script>
<!-- core -->
<script src="{{ asset('assets/dashboard/js/underscore/underscore-min.js') }}" defer></script>
<script src="{{ asset('assets/dashboard/js/jQuery-Storage-API/jquery.storageapi.min.js') }}" defer></script>
<script src="{{ asset('assets/dashboard/js/pace/pace.min.js') }}" defer></script>
<script src="{{ asset('assets/dashboard/js/scripts/config.lazyload.js') }}" defer></script>

<script src="{{ asset('assets/dashboard/js/scripts/palette.js') }}" defer></script>
<script src="{{ asset('assets/dashboard/js/scripts/ui-load.js') }}" defer></script>
<script src="{{ asset('assets/dashboard/js/scripts/ui-jp.js') }}" defer></script>
<script src="{{ asset('assets/dashboard/js/scripts/ui-include.js') }}" defer></script>
<script src="{{ asset('assets/dashboard/js/scripts/ui-device.js') }}" defer></script>
<script src="{{ asset('assets/dashboard/js/scripts/ui-form.js') }}" defer></script>
<script src="{{ asset('assets/dashboard/js/scripts/ui-nav.js') }}" defer></script>
<script src="{{ asset('assets/dashboard/js/scripts/ui-screenfull.js') }}" defer></script>
<script src="{{ asset('assets/dashboard/js/scripts/ui-scroll-to.js') }}" defer></script>
<script src="{{ asset('assets/dashboard/js/scripts/ui-toggle-class.js') }}" defer></script>
{{-- <script src="{{ asset('assets/fa/js/all.min.js') }}" defer></script> --}}
{{-- <script src="{{ asset('assets/dashboard/fonts/materialize-v1.0.0/materialize/js/materialize.min.js') }}" defer></script> --}}


<script src="{{ asset('assets/dashboard/js/scripts/app.js') }}" defer></script>
<script type="text/javascript">
    // Restrict user input in a text field
    // create as many regular expressions here as you need:

    function restrictInput(myfield, e, restriction, checkdot){
        var digitsOnly = /[1234567890]/g;
        var integerOnly = /^[0-9\.]$/g;
        // var integerOnly = /^\d{0,15}(\.\d{1,4})?$/g;
        var alphaOnly = /[A-Za-z]/g;
        var usernameOnly = /[0-9A-Za-z\._-]/g;
        var latLong = /^[0-9\.]+$/g;

        if(restriction == 'digits'){
            restrictionType = digitsOnly;
        }

        if(restriction == 'latLong'){
            restrictionType = latLong;
        }

        if(restriction == 'integer'){
            restrictionType = integerOnly;
        }

        if(restriction == 'alpha'){
            restrictionType = alphaOnly;
        }

        if(restriction == 'username'){
            restrictionType = usernameOnly;
        }

        if (!e) var e = window.event
        if (e.keyCode) code = e.keyCode;
        else if (e.which) code = e.which;
        var character = String.fromCharCode(code);

        // if user pressed esc... remove focus from field...
        if (code==27) { this.blur(); return false; }
        // ignore if the user presses other keys
        // strange because code: 39 is the down key AND ' key...
        // and DEL also equals .
        if (!e.ctrlKey && code!=9 && code!=8 && code!=36 && code!=37 && code!=38 && (code!=39 || (code==39 && character=="'")) && code!=40) {
            if (character.match(restrictionType)) {
                if(checkdot == "checkdot"){
                    return !isNaN(myfield.value.toString() + character);
                } else {
                    return true;
                }
            } else {
                return false;
            }
        }
    }

    function isNumberKey(txt, evt) {
        var charCode = (evt.which) ? evt.which : evt.keyCode;
        if (charCode == 46) {
          //Check if the text already contains the . character
          if (txt.value.indexOf('.') === -1) {
            return true;
          } else {
            return false;
          }
        } else {
          if (charCode > 31 &&
            (charCode < 48 || charCode > 57))
            return false;
        }
        return true;
    }

    $(".decimal").on("input", function(evt) {
        var self = $(this);
        self.val(self.val().replace(/[^0-9\.]/g, ''));

        if ((evt.which != 46 || self.val().indexOf('.') != -1) && (evt.which < 48 || evt.which > 57)) 
        {
          evt.preventDefault();
        }
    });
    
</script>
{{-- {!! Helper::SaveVisitorInfo("Dashboard &raquo; ".trim($__env->yieldContent('title'))) !!} --}}
@stack('after-scripts')
