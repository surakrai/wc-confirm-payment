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

  $(document).ready(function(){

    $( '.wcp_input_table tbody' ).sortable({
      items: 'tr',
      cursor: 'move',
      axis: 'y',
      opacity: 0.65,
      placeholder: 'wcp-metabox-sortable-placeholder',
    });

  });


  // $( '.wcp_input_table .remove_rows' ).click( function() {
  //  var $tbody = $( this ).closest( '.wc_input_table' ).find( 'tbody' );
  //  if ( $tbody.find( 'tr.current' ).length > 0 ) {
  //    var $current = $tbody.find( 'tr.current' );
  //    $current.each( function() {
  //      $( this ).remove();
  //    });
  //  }
  //  return false;
  // });


  $(document).on( 'click', '.wcp-remove_rows', function(event){

    $('tbody.accounts tr.is-focus').remove();

  });

  $(document).on( 'click', '.wcp-add_rows', function(event){


    event.preventDefault();

    var key = $('tr.account-item').length;

    $('tbody.accounts').append(
      '<tr class="account-item">\
        <td class="sort"><i class="dashicons dashicons-menu"></i></td>\
        <td>\
          <div class="wcp-upload-image-wrapper">\
            <input type="hidden" class="widefat bank_logo upload-image" name="bank_logo[' + key + ']" value="">\
            <div class="wcp-image-preview">\
              <span class="no-image"></span>\
            </div>\
            <a href="#" class="wcp-button-upload-image">'+ WCP.i18n.upload +'</a>\
            <a href="#" class="wcp-button-remove-image">&times;</a>\
          </div>\
        </td>\
        <td>\
          <input type="text" class="widefat bank_name" name="bank_name[' + key + ']" value="">\
        </td>\
        <td>\
          <input type="text" class="widefat account_number" name="account_number[' + key + ']" value="">\
        </td>\
        <td>\
          <input type="text" class="widefat account_name" name="account_name[' + key + ']" value="">\
        </td>\
      </tr>'
    );

  });

  $( document ).on('focus', 'table.wcp_input_table input', function(event) {

    event.preventDefault();

    $(this).closest('tr').addClass('is-focus').siblings().removeClass('is-focus');

  });

  $(document).on( 'click', '.wcp-button-remove-image', function(event){

    $(this).closest('.wcp-upload-image-wrapper').removeClass('has-logo').find('input').val('').next().empty();

  });

  $(document).on( 'click', '.wcp-button-upload-image', function(event){

    event.preventDefault();

    var $this = $(this);

    var $wrapper = $this.closest('.wcp-upload-image-wrapper');

    var uploader;

    uploader = wp.media.frames.file_frame = wp.media({

      title: WCP.i18n.select_logo,
      button: {
        text: WCP.i18n.select
      },
      multiple: false

    });

    uploader.state( 'library' ).on( 'select', function(){

      var image = this.get( 'selection' ).first().toJSON();

      //console.log(image);
      $wrapper.addClass('has-logo');
      $wrapper.find('input.upload-image').val( image.id );
      $wrapper.find('.wcp-image-preview').html('<img src="'+ image.sizes.thumbnail.url +'">')

    });

    uploader.open();

  });

})( jQuery );
