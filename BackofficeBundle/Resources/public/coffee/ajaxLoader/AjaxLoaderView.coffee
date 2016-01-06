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
   * required options
   * {
   *  listenElement : {object} Jquery Element
   * }
   * @param {Object} options
  ###
  initialize: (options) ->
    viewContext = @
    @hideLoader(@);
    options.listenElement.bind 'ajaxStart', ->
        viewContext.showLoader(viewContext)
    options.listenElement.bind 'ajaxStop', ->
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
  ajaxLoaderView = new OpenOrchestra.AjaxLoader.AjaxLoaderView(
    listenElement: $(document)
  )
  $('body').append(ajaxLoaderView.render().$el);