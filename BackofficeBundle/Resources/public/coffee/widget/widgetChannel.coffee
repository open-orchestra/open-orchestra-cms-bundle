widgetChannel = new (Backbone.Wreqr.EventAggregator)

widgetChannel.bind 'ready', (view) ->
  el = view.$el
  if $(".select2", el).length > 0
    activateSelect2($(".select2", el))
  if $(".orchestra-node-choice", el).length > 0
    activateOrchestraNodeChoice($(".orchestra-node-choice", el))
  if $(".colorpicker", el).length > 0
    activateColorPicker($(".colorpicker", el))
  if $(".helper-block", el).length > 0
    activateHelper($(".helper-block", el))
  if $(".widget-grid", el).length > 0
    console.log 'load widgets widgetChannel.coffee'
    setup_widgets_desktop()
  if $(".page-title", el).length > 0
    renderPageTitle()
  if $(".contentTypeSelector", el).length > 0
    loadExtendView(view, 'contentTypeSelector')
  if $(".content_type_change_type", el).length > 0
    loadExtendView(view, 'contentTypeChange')
  if $(".configuration-change", el).length > 0
    loadExtendView(view, 'configurationChange')
