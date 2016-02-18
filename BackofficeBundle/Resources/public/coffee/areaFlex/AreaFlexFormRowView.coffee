###*
 * @namespace OpenOrchestra:AreaFlex
###
window.OpenOrchestra or= {}
window.OpenOrchestra.AreaFlex or= {}

###*
 * @class AreaFlexFormRowView
###
class OpenOrchestra.AreaFlex.AreaFlexFormRowView extends OpenOrchestra.AreaFlex.AreaFlexFormView

  events:
    'click ul.list-inline li': 'selectOptionLayout'

  ###*
   * @param {object} event
  ###
  selectOptionLayout: (event) ->
    layout = $(event.target).attr('data-layout')
    input = $('#area_flex_row_columnLayout_layout', @$el)
    if (input.length > 0)
      input.val(layout)
