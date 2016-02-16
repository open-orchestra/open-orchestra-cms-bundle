viewportChannel = Backbone.Wreqr.radio.channel('viewport')

viewportChannel.commands.setHandler 'init', (blockpanel) ->
  $(this).data 'blockpanel', blockpanel if typeof blockpanel != 'undefined'
  ghostPanel = $('#ghost-blockpanel', $(this).data('blockpanel'))
  if (ghostPanel[0])
    ghostPanel.show()
    $(this).data 'fixedtop', ghostPanel[0].getBoundingClientRect().top
    ghostPanel.hide()
    $(this).data('blockpanel').height $(window).height() - $(this).data('fixedtop')
    Backbone.Wreqr.radio.commands.execute 'viewport', 'scroll'
  return

viewportChannel.commands.setHandler 'scroll', ->
  $(this).data('blockpanel').removeClass('panel-fixed')
  try
    if $(this).data('blockpanel')[0].getBoundingClientRect().top + 2 < $(this).data('fixedtop')
      $(this).data('blockpanel').addClass('panel-fixed')
  return
