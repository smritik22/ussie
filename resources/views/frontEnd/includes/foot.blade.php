@stack('before-scripts')
    <script type="text/javascript" src="{{ asset('assets/frontend/js/jquery.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('assets/frontend/js/jquery.ui.js') }}"></script>
    <script type="text/javascript" src="{{ asset('assets/frontend/js/ui-slider.js') }}"></script>
    <script type="text/javascript" src="{{ asset('assets/frontend/js/popper.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('assets/frontend/js/bootstrap.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('assets/frontend/js/lightboxed.js')}}"></script>
    <script type="text/javascript" src="{{ asset('assets/frontend/js/owl.carousel.js') }}"></script>
    <script type="text/javascript" src="{{ asset('assets/frontend/js/menu.js') }}"></script>
    <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script type="text/javascript" src="{{ asset('assets/frontend/js/custom.js') }}"></script>

    <script>
        function addRemoveFav(element, is_fav, property_id, is_remove = 0) {
            let __this = $(element);
            let toChange = is_fav == 1 ? 0 : 1;
            __this.toggleClass('heart');
            loader_show();
            $.ajax({
                url: "{{ route('frontend.addRem_favProp') }}",
                type: "post",
                data: {
                    "property_id": property_id,
                    "is_fav": is_fav,
                },
                dataType: 'json',
                success: function(response) {
                    if (response.statusCode == 209) {
                        __this.removeClass('heart');

                        Swal.fire({
                            title: response.title,
                            showDenyButton: true,
                            showConfirmButton: true,
                            confirmButtonText: "{{ $labels['go_to_login'] }}",
                            denyButtonText: `{{ $labels['cancle'] }}`,
                            confirmButtonColor: "#2A4B9B",
                            heightAuto: false,
                        }).then((result) => {
                            if (result.isConfirmed) {
                                window.location.href = response.url;
                            } else if (result.isDenied) {

                            }
                        });
                    } else if (response.statusCode == 200) {

                        Swal.fire({
                            icon: 'success',
                            title: response.title,
                            toast: true,
                            position: 'top-end',
                            showConfirmButton: false,
                            timer: 3000,
                            timerProgressBar: true,
                            heightAuto: false,
                            didOpen: (toast) => {
                                toast.addEventListener('mouseenter', Swal.stopTimer)
                                toast.addEventListener('mouseleave', Swal.resumeTimer)
                            }
                        });

                        if(is_fav == 1 && is_remove == 1) {
                            __this.parents('.col-lg-6').remove();
                            if($('.row').find('.col-lg-6').length == 0) {
                                $('#something_went_wrong').removeClass('d-none');
                                $('#something_went_wrong').find('#message').text("{{$labels['no_data_is_available']}}");
                            } else{
                                $('#something_went_wrong').addClass('d-none');
                            }
                        } else {
                            __this.attr('onclick', `addRemoveFav(this, ${toChange}, ${property_id})`);
                        }

                    }
                    loader_hide();
                },
                error: function(error) {
                    loader_hide();
                }
            })
        }

        function logoutuser(element) {
            event.preventDefault();
            Swal.fire({
                title: "{{$labels['are_you_sure_you_want_to_logout']}}",
                showDenyButton: true,
                showConfirmButton: true,
                confirmButtonText: "{{ ucfirst($labels['yes']) }}",
                denyButtonText: `{{ ucfirst($labels['cancle']) }}`,
                confirmButtonColor: "#2A4B9B",
                heightAuto: false,
            }).then((result) => {
                if (result.isConfirmed) {
                    loader_show();
                    console.log('logging out...');
                    window.location.href = "{{route('frontend.logout')}}";
                }
            });
        }

        function delete_property(element, prop_id) {
            let __this = $(element);
            event.preventDefault();
            Swal.fire({
                title: "{{$labels['are_you_sure_you_want_to_delete']}}",
                showDenyButton: true,
                showConfirmButton: true,
                confirmButtonText: "{{ ucfirst($labels['yes']) }}",
                denyButtonText: `{{ ucfirst($labels['cancle']) }}`,
                confirmButtonColor: "#2A4B9B",
                heightAuto: false,
            }).then((result) => {
                if (result.isConfirmed) {
                    loader_show();
                    let __delete_url = "{{route('frontend.property.delete')}}";
                    console.log('Deleting property, please wait...');
                    {{--  window.location.href = "{{route('frontend.property.delete')}}/"+prop_id;  --}}

                    $.ajax({
                        url : __delete_url,
                        type : 'post',
                        data : {"_token" : "{{csrf_token()}}", "property_id" : prop_id},
                        dataType : 'json',
                        success : function (response) {
                            loader_hide();
                            if(response.statusCode == 200) {
                                console.log('property deleted.');
                                Swal.fire({
                                    icon: 'success',
                                    iconColor: '#2A4B9B',
                                    text: response.message,
                                    confirmButtonColor: "#2A4B9B",
                                    timer: 3000,
                                    timerProgressBar: true,
                                    heightAuto: false,
                                    willClose: () => {
                                        __this.parents('.propertyMainBoxClass').remove();
                                    }
                                }).then(function(result) {
                                    __this.parents('.propertyMainBoxClass').remove();
                                });
                            } else if(response.statusCode == 202) {
                                console.log('This is not good...');
                                Swal.fire({
                                    icon: 'error',
                                    iconColor: '#bb4f4f',
                                    text: response.message,
                                    showConfirmButton : true,
                                    confirmButtonText : "{{strtoupper($labels['ok'])}}",
                                    confirmButtonColor: "#2A4B9B",
                                    heightAuto: false,
                                    willClose: () => {
                                        __this.parents('.propertyMainBoxClass').remove();
                                    }
                                }).then(function(result) {
                                    __this.parents('.propertyMainBoxClass').remove();
                                });
                            } else  {
                                console.log('Something went wrong. Please try again or try refreshing the page');
                                Swal.fire({
                                    icon: 'error',
                                    iconColor: '#bb4f4f',
                                    text: response.message,
                                    timer: 3000,
                                    timerProgressBar: true,
                                    heightAuto: false,
                                    willClose: () => {
                                    }
                                }).then(function(result) {
                                });
                            }
                        },
                        error : function (err) {
                            console.error('Oopss... Something went wrong.');
                            console.error(err);
                            loader_hide();

                            Swal.fire({
                                icon: 'error',
                                iconColor: '#bb4f4f',
                                text: response.message,
                                timer: 3000,
                                timerProgressBar: true,
                                heightAuto: false,
                                willClose: () => {
                                }
                            }).then(function(result) {
                            });
                        }
                    });
                }
            });
        }

        var is_chat_ready = 1;
        function fetchChatLists() {
            if(is_chat_ready == 1) {
                is_chat_ready = 0;
                
                var __url = "{{route('frontend.chat.list.fetch')}}";
                let chat_page_no = $("#chatsAppend").data('page');
                let chat_user_id = $("#chat_user_id").val();

                $.ajax({
                    url : __url,
                    type : 'post',
                    dataType : 'json',
                    data : {"_token" : "{{csrf_token()}}", "chat_page_no":chat_page_no, "chat_user_id" : chat_user_id},
                    success : function (response) {
                        if(response.statusCode == 200) {

                            chat_page_no = parseInt(chat_page_no);
                            chat_page_no++;
                            $("#chatsAppend").data('page_no',chat_page_no);
                            if(chat_page_no > response.total_page) {
                                $('#chatsAppend').data('limit_exceeded',1);
                            }

                            if(response.html && response.total_records > 0) {
                                $('#something_went_wrong').addClass('d-none');
                                $("#chatsAppend").append(response.html);
                            } else {
                                $('#something_went_wrong').removeClass('d-none');
                                $('#something_went_wrong').find('#message').text("{{$labels['no_data_is_available']}}");
                            }
                        }
                        else {
                            $('#something_went_wrong').removeClass('d-none');
                            $('#something_went_wrong').find('#message').text("{{$labels['something_went_wrong']}}");
                        }

                        is_chat_ready = 1;
                    },
                    error : function(err) {
                        $('#something_went_wrong').removeClass('d-none');
                        $('#something_went_wrong').find('#message').text("{{$labels['something_went_wrong']}}");
                    }
                });
            }
        }

    </script>
@stack('after-scripts')
