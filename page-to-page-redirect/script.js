jQuery(document).ready(function ($) {
    jQuery('.page_to_page_redirect_form').submit(function (e) {
        e.preventDefault();

        let $form = $(this);

        let from_id = $form.find('select[name="from_id"]').val();
        let to_id = $form.find('select[name="to_id"]').val();

        var data = {
            action: 'action_add_update_redirect',
            from_id: from_id,
            to_id: to_id,
        };

        jQuery.post(ajaxurl, data, function (response) {
            if (response) {
                print_redirects_data(response);
                reset_select2();
            } else {
                console.warn(response);
            }
        });

    });

    function get_redirects_data() {

        var data = {
            action: 'action_get_redirects_data',
        };

        jQuery.post(ajaxurl, data, function (response) {

            if (response) {
                print_redirects_data(response)
            } else {
                console.error(response);
                console.log(typeof (response));
            }

        });
    }

    function print_redirects_data(data){
        let $res_container = jQuery('#redirects');
        let $res_table = jQuery('#redirects_table');
        
        if(Object.keys(data).length > 0){
            $res_container.empty();

            for (var key in data) {
                $res_container.append('<tr><td>' + key + '</td><td>' + data[key] + '</td><td> <button class="remov_redir" data-from-id="'+key+'">x</button></td></tr>');
            }

            $res_table.fadeIn(100);
        } else {
            $res_table.fadeOut(50);
        }
    }

    jQuery('.select2').select2({
        minimumInputLength: 1,
        ajax: {
            url: ajaxurl,
            type: "POST",
            dataType: 'json',
            data: function (params) {
                return {
                    search_keyword: params.term,
                    action: 'action_search_posts',
                };
            },
            processResults: function (data) {

                var options = [];
                if (data) {

                    for (var key in data) {

                        let this_children = [];

                        $.each(data[key], function (index, post) {
                            this_children.push({"id": post['id'], "text": post['title']});
                        });

                        options.push(
                            {
                                "children": this_children,
                                "text": key
                            }
                        );
                    }
                }
                return {
                    results: options
                };
            },
            cache: true
        }
    });

    function reset_select2(){
        $(".select2").val('').trigger('change')
    }

    function remove_one_redirect(from_id){
        var data = {
            action: 'action_remove_redirect',
            from_id: from_id,
        };

        jQuery.post(ajaxurl, data, function (response) {
            if (response) {
                print_redirects_data(response);
            } else {
                console.warn(response);
            }
        });
    }

    jQuery(document).on('click', '.remov_redir', function () {
        remove_one_redirect(jQuery(this).data('from-id'));
    })

    get_redirects_data();

});
