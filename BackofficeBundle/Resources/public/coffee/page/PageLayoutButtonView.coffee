###
 * @namespace OpenOrchestra:Page
###
window.OpenOrchestra or= {}
window.OpenOrchestra.Page or= {}

###
 * @class PageLayoutButtonView
###
class OpenOrchestra.Page.PageLayoutButtonView extends OrchestraView

  events:
    'click span': 'configurationPage'
    'click .ajax-delete': 'deleteButton'

  initialize: (options) ->
    @options = @reduceOption(options, [
      'configuration'
      'viewContainer'
      'entityType'
      'widget_index'
      'deleteUrl'
      'confirmText'
      'confirmTitle'
      'redirectUrl'
    ])
    @loadTemplates [
      "OpenOrchestraBackofficeBundle:BackOffice:Underscore/widgetPageLayoutButton"
    ]
    return

  ###
   * Render area
  ###
  render: ->
    @setElement @renderTemplate('OpenOrchestraBackofficeBundle:BackOffice:Underscore/widgetPageLayoutButton',
      deleteUrl: @options.deleteUrl
      confirmText: @options.confirmText
      confirmTitle: @options.confirmTitle
      redirectUrl: @options.redirectUrl
    )
    @$el.attr('data-widget-index', @options.widget_index)
    OpenOrchestra.RibbonButton.ribbonFormButtonView.setFocusedView(@, '.ribbon-form-button')
    return

  ###
   * Called when user click on edit span
  ###
  configurationPage: () ->
    options =
      url: @options.configuration.get('links')._self_form
      title: @options.configuration.get('name')
      entityType: @options.entityType
    if @options.configuration.attributes.alias is ''
      $.extend options, extendView: [ 'generateId']
    adminFormViewClass = appConfigurationView.getConfiguration(@options.entityType, 'showAdminForm')
    new adminFormViewClass(options)

  ###
   * @param {Object} event
  ###
  deleteButton: (event) ->
    event.preventDefault()
    target = $(event.currentTarget)
    url = target.data("delete-url")
    confirm_text = target.data("confirm-text")
    confirm_title = target.data("confirm-title")
    redirectUrl = target.data('redirect-url')
    smartConfirm(
      'fa-trash-o',
      confirm_title,
      confirm_text,
      callBackParams:
        url: url
      yesCallback: (params) ->
        $.ajax
          url: params.url
          method: 'DELETE'
          success: (response) ->
            if redirectUrl != undefined
              refreshMenu(redirectUrl)
            else
              redirectUrl = appRouter.generateUrl 'showDashboard'
              refreshMenu(redirectUrl)
            return
      )
