(function( $ ) {
	'use strict';

	/**
	 * All of the code for your admin-facing JavaScript source
	 * should reside in this file.
	 *
	 * Note: It has been assumed you will write jQuery code here, so the
	 * $ function reference has been prepared for usage within the scope
	 * of this function.
	 *
	 * This enables you to define handlers, for when the DOM is ready:
	 *
	 * $(function() {
	 *
	 * });
	 *
	 * When the window is loaded:
	 *
	 * $( window ).load(function() {
	 *
	 * });
	 *
	 * ...and/or other possibilities.
	 *
	 * Ideally, it is not considered best practise to attach more than a
	 * single DOM-ready or window-load handler for a particular page.
	 * Although scripts in the WordPress core, Plugins and Themes may be
	 * practising this, we should strive to set a better example in our own work.
	 */


    $(function() {
        $('.mur-multi-select').multiSelect({
            selectableHeader: "<div class='custom-header'>All</div>",
            selectionHeader: "<div class='custom-header'>Selected</div>",
            keepOrder: false
        });



        var select = $('.alsvin-mur-select2');


        $(select).select2({
            //minimumInputLength: 3,
            multiple: true,
            width: '50%',
            ajax: {
                url: ajaxurl,
                dataType: 'json',
                delay: 250, // delay in ms while typing when to perform a AJAX search
                data: function (params) {
                    var query = {
                        search: params.term,
                        page: params.page || 1,
                        action: 'mur_search_users',
                    };

                    return query;
                },
                processResults: function (data, params) {
                    params.page = params.page || 1;
                    var per_page_count = 10;
                    return {
                        results: data.data,
                        pagination: {
                            more: (params.page * per_page_count) < data.total_count
                        }
                    };
                }
            }
        });

        /*$(select).on('select2:select', function (e) {
            var data = e.params.data;
            var user_id = data.id;

            $('#mur_selected_user_id').val(user_id);
        });*/

        /*// Switches option sections
        $( '.group' ).hide();
        var activetab = '';
        if ( 'undefined' != typeof localStorage ) {
            activetab = localStorage.getItem( 'activetab' );
        }
        if ( '' != activetab && $( activetab ).length ) {
            $( activetab ).fadeIn();
        } else {
            $( '.group:first' ).fadeIn();
        }
        $( '.group .collapsed' ).each( function() {
            $( this )
                .find( 'input:checked' )
                .parent()
                .parent()
                .parent()
                .nextAll()
                .each( function() {
                    if ( $( this ).hasClass( 'last' ) ) {
                        $( this ).removeClass( 'hidden' );
                        return false;
                    }
                    $( this )
                        .filter( '.hidden' )
                        .removeClass( 'hidden' );
                });
        });

        if ( '' != activetab && $( activetab + '-tab' ).length ) {
            $( activetab + '-tab' ).addClass( 'nav-tab-active' );
        } else {
            $( '.nav-tab-wrapper a:first' ).addClass( 'nav-tab-active' );
        }
        $( '.nav-tab-wrapper a' ).click( function( evt ) {
            $( '.nav-tab-wrapper a' ).removeClass( 'nav-tab-active' );
            $( this )
                .addClass( 'nav-tab-active' )
                .blur();
            var clicked_group = $( this ).attr( 'href' );
            if ( 'undefined' != typeof localStorage ) {
                localStorage.setItem( 'activetab', $( this ).attr( 'href' ) );
            }
            $( '.group' ).hide();
            $( clicked_group ).fadeIn();
            evt.preventDefault();
        });

        $( '.wpsa-browse' ).on( 'click', function( event ) {
            event.preventDefault();

            var self = $( this );

            // Create the media frame.
            var file_frame = ( wp.media.frames.file_frame = wp.media({
                title: self.data( 'uploader_title' ),
                button: {
                    text: self.data( 'uploader_button_text' )
                },
                multiple: false
            }) );

            file_frame.on( 'select', function() {
                attachment = file_frame
                    .state()
                    .get( 'selection' )
                    .first()
                    .toJSON();

                self
                    .prev( '.wpsa-url' )
                    .val( attachment.url )
                    .change();
            });

            // Finally, open the modal
            file_frame.open();
        });

        $( 'input.wpsa-url' ).on( 'change keyup paste input', function() {
            var self = $( this );
            self
                .next()
                .parent()
                .children( '.wpsa-image-preview' )
                .children( 'img' )
                .attr( 'src', self.val() );
        }).change();*/
    });

})( jQuery );