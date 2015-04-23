duplicateChannel = Backbone.Wreqr.radio.channel('duplicate')

duplicateChannel.commands.setHandler 'ready', (view) ->
  new DuplicateView(
    domContainer: view.$el.find('#entity-duplicate')
    currentDuplicate: view.options.duplicate
  )
