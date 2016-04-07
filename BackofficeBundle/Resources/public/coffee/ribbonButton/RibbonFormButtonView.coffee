###*
 * @namespace OpenOrchestra:RibbonButton
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
   * @param {Object} view
   * @param {String} container
  ###
  setFocusedView: (view, container) ->
    @container = container
    @resetAll(container)
    viewContext = this
    $('.btn-in-ribbon', view.$el).each ->
      viewContext.cloneButton $(this)
      return
    @setElement @container
    return

  ###*
   * Method call to clone and move button
   * @param {Object} button
  ###
  cloneButton: (button) ->
    button.uniqueId();
    clonedButton = button.clone().attr('data-clone', button.attr('id')).removeAttr('id')
    @buttonsList[button.attr('id')] = button
    button.hide()
    $(@container).append(clonedButton)

  ###*
   * Method call to click on original button
   * @param {Object} event
  ###
  clickClone: (event) ->
    event.preventDefault()
    key = $(event.currentTarget).data('clone')
    @buttonsList[key][0].click()

  ###*
   * Method call to reset Ribbbon
   * @param {string} container
  ###
  resetAll: (container) ->
    @container = container
    $(@container).html('')

jQuery ->
  appConfigurationView.setConfiguration('ribbon-form-button', 'createRibbonFormButton', OpenOrchestra.RibbonButton.RibbonFormButtonView)
