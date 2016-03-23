###*
 * @namespace OpenOrchestra:WebSite
###
window.OpenOrchestra or= {}
window.OpenOrchestra.WebSite or= {}

###*
 * @class WebSiteFormView
###
class OpenOrchestra.WebSite.WebSiteFormView extends FullPageFormView

  ###*
   * On Element Created
  ###
  onElementCreated: ->
    document.location.reload true

jQuery ->
  appConfigurationView.setConfiguration('websites', 'addEntity', OpenOrchestra.WebSite.WebSiteFormView)
