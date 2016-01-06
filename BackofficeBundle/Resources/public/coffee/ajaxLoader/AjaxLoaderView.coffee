###*
 * @namespace OpenOrchestra:AjaxLoader
###
window.OpenOrchestra or= {}
window.OpenOrchestra.AjaxLoader or= {}

###*
 * @class AjaxLoaderView
###
class OpenOrchestra.AjaxLoader.AjaxLoaderView extends Backbone.View

  tagName : 'div'

  className: 'oo-ajax-loader'

  ###*
   * @param {Object} listenElement
  ###
  initialize: (listenElement) ->
    viewContext = @
    @hideLoader(@);
    listenElement.bind 'ajaxStart', ->
        viewContext.showLoader(viewContext)
    listenElement.bind 'ajaxStop', ->
        viewContext.hideLoader(viewContext)

  ###*
   * @return {this}
  ###
  render: ->
    return @

  ###*
   * Show loader
  ###
  showLoader : (viewContext) ->
    viewContext.$el.show()

  ###*
   * Hide loader
  ###
  hideLoader : (viewContext) ->
    viewContext.$el.hide()


jQuery ->
  ajaxLoaderView = new OpenOrchestra.AjaxLoader.AjaxLoaderView($(document))
  $('body').append(ajaxLoaderView.render().$el);
