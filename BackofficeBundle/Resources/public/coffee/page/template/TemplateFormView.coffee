###*
 * @namespace OpenOrchestra:Page:Template
###
window.OpenOrchestra or= {}
window.OpenOrchestra.Page or= {}
window.OpenOrchestra.Page.Template or= {}

###*
 * @class TemplateFormView
###
class OpenOrchestra.Page.Template.TemplateFormView extends OrchestraModalView

  ###*
   * Method call when view is ready
   * Redirect to  the template created when form is submitted and valid
  ###
  onViewReady: ->
    if @options.submitted
      displayRoute = appRouter.generateUrl "showTemplate",
        templateId: $('#oo_template_templateId', @$el).val()
      refreshMenu(displayRoute)
      Backbone.history.loadUrl(Backbone.history.fragment);

jQuery ->
  appConfigurationView.setConfiguration('template', 'showOrchestraModal', OpenOrchestra.Page.Template.TemplateFormView)
