
jQuery( document ).ready(function() {

    //  Check the input field value , if there is no value show the warning message
    jQuery("#trello_board").click(function () {
        var trelloAccessCode = jQuery("#trello_access_code").val();
        if ( ! trelloAccessCode ) {
            jQuery("#save_settings").css("border-color", "red");
            jQuery("#trello_access_code").css("border-color", "red");
            alert( " Please save Trello access code, before selecting the Trello Board! ");
        }
    });

    jQuery("#trello_board").change(function () {

        // jQuery("#wt_loader").show();
        // jQuery("#wt_list").prop('disabled', true);

        var trello_board_id = this.value;
        if ( trello_board_id ) {

            var ajaxData = {
                'action': 'wootrello_ajax_response',
                'boardID': trello_board_id,
                'security': wootrello_data.security
            };

            jQuery.post( wootrello_data.wootrelloAjaxURL, ajaxData, function (trello_board_list) {
                var list = JSON.parse( trello_board_list );
                
                if ( list[0] ) {
                    jQuery("#wt_loader").hide();
                    jQuery("#wt_list").prop("disabled", false);
                    //
                    jQuery("#trello_list").empty();
                    jQuery("#trello_list").append(
                    '<option value=""> Select a List	</option>'
                    );
                    jQuery.each( list[1], function (key, value) {
                        jQuery("#trello_list").append(
                            '<option value="' + key + '">' + value + "</option>"
                        );
                    });
                } else {
                    alert("ERROR : " + list[1]);
                }

               
            });

        }

    });

});
// 