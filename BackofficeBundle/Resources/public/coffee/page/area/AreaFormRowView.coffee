###*
 * @namespace OpenOrchestra:Page:Area
###
window.OpenOrchestra or= {}
window.OpenOrchestra.Page or= {}
window.OpenOrchestra.Page.Area or= {}

###*
 * @class AreaFormRowView
###
class OpenOrchestra.Page.Area.AreaFormRowView extends OpenOrchestra.Page.Area.AreaFormView

  events:
    'click ul.list-inline li': 'selectOptionLayout'

  ###*
   * @param {object} event
  ###
  selectOptionLayout: (event) ->
    layout = $(event.target).attr('data-layout')
    input = $('#area_row_columnLayout_layout', @$el)
    if (input.length > 0)
      input.val(layout)
