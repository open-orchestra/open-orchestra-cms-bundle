###
 * @namespace OpenOrchestra:Page
###
window.OpenOrchestra or= {}
window.OpenOrchestra.Page or= {}

###
 * @class NodeLayoutButtonView
###
class OpenOrchestra.Page.NodeLayoutButtonView extends OpenOrchestra.Page.PageLayoutButtonView

  ###
   * Called when user click on edit span
  ###
  configurationPage: () ->
    title = @options.configuration.get('name')+' (#'+@options.configuration.get('version')+" - "+@options.configuration.get('language')+")"
    options =
      url: @options.configuration.get('links')._self_form
      title: title
      entityType: @options.entityType
    if @options.configuration.attributes.alias is ''
      $.extend options, extendView: [ 'generateId']
    adminFormViewClass = appConfigurationView.getConfiguration(@options.entityType, 'showAdminForm')
    new adminFormViewClass(options)

jQuery ->
  appConfigurationView.setConfiguration('node', 'addPageLayoutButton', OpenOrchestra.Page.NodeLayoutButtonView)
