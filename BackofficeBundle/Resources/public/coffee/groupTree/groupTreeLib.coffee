(($, OpenOrchestra) ->
  OpenOrchestra.toggleTreeNodeDisplay = (e) ->
    switcher = $(e.currentTarget)
    switcher.toggleClass 'open'
    nodeElement = switcher.closest('li')
    container = nodeElement.children('.child-node').first()
    if !switcher.hasClass('open')
      switcher.addClass 'fa-plus-square-o'
      switcher.removeClass 'fa-minus-square-o'
      container.hide()
      return
    switcher.addClass 'fa-minus-square-o'
    switcher.removeClass 'fa-plus-square-o'
    container.show()
    return
) jQuery, window.OpenOrchestra = window.OpenOrchestra or {}