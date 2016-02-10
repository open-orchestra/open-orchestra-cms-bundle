###*
 * @namespace OpenOrchestra:TemplateFlex
###
window.OpenOrchestra or= {}
window.OpenOrchestra.TemplateFlex or= {}

###*
 * @class TemplateFlexFormView
###
class OpenOrchestra.TemplateFlex.TemplateFlexFormView extends OrchestraModalView

  ###*
   * Method call when view is ready
   * Redirect to  the template created when form is submitted and valid
  ###
  onViewReady: ->
    if @options.submitted
      displayRoute = appRouter.generateUrl "showTemplateFlex",
        templateId: $('#oo_template_flex_templateId', @$el).val()
      refreshMenu(displayRoute)

jQuery ->
  appConfigurationView.setConfiguration('template-flex', 'showOrchestraModal', OpenOrchestra.TemplateFlex.TemplateFlexFormView)
