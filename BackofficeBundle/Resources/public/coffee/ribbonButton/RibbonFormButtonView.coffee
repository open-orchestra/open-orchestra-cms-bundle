###*
 * @namespace OpenOrchestra:TemplateFlex
###
window.OpenOrchestra or= {}
window.OpenOrchestra.RibbonButton or= {}

###*
 * @class RibbonFormButtonView
###
class OpenOrchestra.RibbonButton.RibbonFormButtonView extends OrchestraView
  events:
    'click [data-clone]': 'clickClone'

  constructor: ->
    @buttonsList = []

  ###*
   * set focused view
  ###
  setFocusedView: (view, container) ->
    @container = if typeof container == 'undefined' then $('.ribbon-form-button') else container
    @container.html('')
    viewContext = this
    $('.btn-in-ribbon', view.$el).each ->
      viewContext.cloneButton $(this)
      return
    this.setElement @container
    return

  ###*$
   * Method call to clone and move button
  ###
  cloneButton: (button) ->
    button.uniqueId();
    clonedButton = button.clone().attr('data-clone', button.attr('id')).removeAttr('id')
    @buttonsList[button.attr('id')] = button
    button.hide()
    @container.append(clonedButton)

  clickClone: (event) ->
    event.preventDefault()
    key = $(event.currentTarget).data('clone')
    @buttonsList[key][0].click()

jQuery ->
  appConfigurationView.setConfiguration('ribbon-form-button', 'createRibbonFormButton', OpenOrchestra.RibbonButton.RibbonFormButtonView)
  ribbonFormButtonViewClass = appConfigurationView.getConfiguration('ribbon-form-button', 'createRibbonFormButton')
  window.ribbonFormButtonView = new ribbonFormButtonViewClass()
