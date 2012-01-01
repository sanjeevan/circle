var AddSourceView = Backbone.View.extend({
  initialize: function() {
    var view = this;
    this.template = this.options.template || JST['views/add_source_view.html'];
    this.el = $(this.template);

    // register events
    $("a#add-source").bind("click", function(evt) {
      evt.preventDefault();
      view.show();
    });
  },

  // Show the view
  show: function() {
    
  },

  // Render the view
  render: function() {
    
  }
});
