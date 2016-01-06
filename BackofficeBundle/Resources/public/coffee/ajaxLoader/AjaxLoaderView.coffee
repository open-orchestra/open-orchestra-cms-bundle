###*
 * @class AjaxLoaderView
###
class AjaxLoaderView extends Backbone.View

  tagName : 'div'
  className: 'pace'

  ###*
   * @return {this}
  ###
  render: ->
    console.log @el
    console.log @$el
    @$el.append('<div class="pace-activity"></div>');
    _this = @
    $(document).bind 'ajaxStart', {$loader: @$el } , _this.showLoader
    $(document).bind 'ajaxStop',  {$loader: @$el } , _this.hideLoader

    return @

  ###*
   * Show loader
  ###
  showLoader : (event) ->
    console.log event
    event.data.$loader.show()

  ###*
   * Hide loader
  ###
  hideLoader : (event) ->
    console.log event
    event.data.$loader.hide()

jQuery ->
  ajaxLoaderView = new AjaxLoaderView()
  console.log "loader"
  console .log ajaxLoaderView.render().$el
  $('body').append(ajaxLoaderView.render().$el);