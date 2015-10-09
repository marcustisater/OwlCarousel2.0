( function() {
    tinymce.PluginManager.add( 'owl_test', function( editor, url ) {
        editor.addButton( 'owl_test_button_key', {
          text: 'Owl Carousel',
          icon: false,
          onclick: function() {
              // Open window
              editor.windowManager.open( {
                  title: 'Enter the category name for the slideshow',
                  body: [{
                      type: 'textbox',
                      name: 'title',
                      label: 'Category name'
                  }],
                  onsubmit: function( e ) {
                  editor.insertContent( '[owl category="' + e.data.title + '"]');
                }
            } );
          }
      } );
    } );
} )();
