###*
 * @namespace OpenOrchestra:TemplateFlex
###
window.OpenOrchestra or= {}
window.OpenOrchestra.RibbonButton or= {}

###*
 * @class RibbonFormButtonView
###
class OpenOrchestra.RibbonButton.RibbonFormButtonView extends OrchestraView

  className : 'ribbon-form-button'

  ###*
   * set focused view
  ###
  setFocusedView: (view) ->
    @$el.html('')
    viewContext = this
    $('.btn-in-ribbon', view.$el).each ->
      viewContext.cloneButton $(this)
      return
    return

  ###*
   * Method call to clone and move button
  ###
  cloneButton: (button) ->
    button = button.clone().attr('data-clone', button.attr('id')).removeAttr('id')
    button.hide()
    @$el.append(button)

jQuery ->
  appConfigurationView.setConfiguration('ribbon-form-button', 'createRibbonFormButton', OpenOrchestra.RibbonButton.RibbonFormButtonView)
  ribbonFormButtonViewClass = appConfigurationView.getConfiguration('ribbon-form-button', 'createRibbonFormButton')
  window.ribbonFormButtonView = new ribbonFormButtonViewClass()
