<script type="text/javascript" src="{{ asset('assets/frontend/js/jquery.min.js') }}"></script>
<script>
    $().ready(function(){
        var response = {!! json_encode($response) !!};
        opener.resultFetched(response);
    })
</script>