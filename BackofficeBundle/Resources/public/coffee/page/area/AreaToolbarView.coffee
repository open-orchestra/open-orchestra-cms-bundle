###*
 * @namespace OpenOrchestra:Page:Area
###
window.OpenOrchestra or= {}
window.OpenOrchestra.Page or= {}
window.OpenOrchestra.Page.Area or= {}

###*
 * @class AreaToolbarView
###
class OpenOrchestra.Page.Area.AreaToolbarView extends OrchestraView

  AREA_TYPE_ROW: 'row'
  AREA_TYPE_ROOT: 'root'

  extendView: ['OpenOrchestra.Page.Area.AddRow']

  events:
    'click .add-row-action': 'showFormAddRow'
    'click .edit-column': 'showFormColumn'
    'click .delete-column': 'deleteColumn'
    'click .delete-row': 'deleteRow'
    'click .edit-row': 'showFormRow'
    'click .move-row': 'activateMoveRow'
    'click .move-column': 'activateMoveColumn'
    'click .area-breadcrumb li' : 'triggerEditArea'

  ###*
   * @param {Object} options
  ###
  initialize: (options) ->
    @options = @reduceOption(options, [
      'area'
      'areaView'
      'domContainer'
    ])
    @options.entityType = 'area'
    @loadTemplates [
      "OpenOrchestraBackofficeBundle:BackOffice:Underscore/page/area/areaToolbarView"
      "OpenOrchestraBackofficeBundle:BackOffice:Underscore/page/area/areaBreadcrumbToolbar"
    ]

  ###*
   * Render area
  ###
  render: ->
    @setElement @renderTemplate('OpenOrchestraBackofficeBundle:BackOffice:Underscore/page/area/areaToolbarView', @options)
    @options.domContainer.html(@$el)
    context = @
    @updateToolbarPosition(@$el)
    @addBreadcrumb(@options.areaView)
    $(window).bind 'scroll', () ->
      context.updateToolbarPosition(context.$el)

  ###*
   * Activate edit area when click on breadcrumb
   * @param {object} event
  ###
  triggerEditArea: (event) ->
    areaId = $(event.target).attr('data-area-id')
    OpenOrchestra.Page.Area.Channel.trigger 'activateEditArea', areaId if areaId?

  ###*
   * Add breadcrumb on toolbar
   * @param {object} areaView
  ###
  addBreadcrumb : (areaView) ->
    if areaView.options.area.get('area_type')? and areaView.options.area.get('area_type') != @AREA_TYPE_ROW
      $('.area-breadcrumb', @$el).prepend(
        @renderTemplate('OpenOrchestraBackofficeBundle:BackOffice:Underscore/page/area/areaBreadcrumbToolbar',
          area: areaView.options.area
        )
      )
    if areaView.options.area.get('area_type')? and areaView.options.area.get('area_type') != @AREA_TYPE_ROOT
      @addBreadcrumb(areaView.options.parentAreaView)

  ###*
   * Activate sortable on row
  ###
  activateMoveRow: ->
    rowAreaView = @options.areaView.options.parentAreaView
    containerAreaId = @options.areaView.options.parentAreaView.options.parentAreaView.options.area.get('area_id')
    OpenOrchestra.Page.Area.Channel.trigger 'activateSortableArea', containerAreaId, rowAreaView

  ###*
   * Activate sortable on column
  ###
  activateMoveColumn: ->
    columnAreaView = @options.areaView
    containerAreaId = @options.areaView.options.parentAreaView.options.area.get('area_id')
    OpenOrchestra.Page.Area.Channel.trigger 'activateSortableArea', containerAreaId, columnAreaView

  ###*
   * Show form edit column
  ###
  showFormColumn: ->
    adminFormViewClass = appConfigurationView.setConfiguration(@options.entityType, 'showOrchestraModal', OpenOrchestra.Page.Area.AreaFormView)
    adminFormViewClass = appConfigurationView.getConfiguration(@options.entityType, 'showAdminForm')
    url = @options.area.get("links")._self_form_column
    title = @$el.attr('data-title-edit-column')
    if url?
      new adminFormViewClass(
        url: url
        entityType: @options.entityType
        title: title
      )

  ###*
   * Show form edit row
  ###
  showFormRow: ->
    url = @options.area.get("links")._self_form_row
    title = @$el.attr('data-title-edit-row')
    if url?
      @showFormWithSelectLayout(url, title)

  ###*
   * @param {Object} el Jquery element
  ###
  updateToolbarPosition: (el) ->
    el.removeClass("fixed")
    if $(window).scrollTop() > el.offset().top - el.height()
      el.addClass("fixed")
      el.width(el.parent().width())

  ###*
   * Delete column
   * @param {object} event Jquery event
  ###
  deleteColumn: (event) ->
    url = @options.area.get("links")._self_delete_column
    @deleteArea(event, url) if url?

  ###*
   * Delete row
   * @param {object} event Jquery event
  ###
  deleteRow: (event) ->
    url = @options.area.get("links")._self_delete_row
    @deleteArea(event, url) if url?

  ###*
   * Delete area
   * @param {object} event Jquery event
   * @param {string} url
  ###
  deleteArea: (event, url) ->
    button = $(event.target)
    smartConfirm(
      'fa-trash-o',
      button.attr('data-delete-confirm-question'),
      button.attr('data-delete-confirm-explanation'),
      callBackParams:
        url: url
        message: button.attr('data-delete-error-txt')
      yesCallback: (params) ->
        $.ajax
          url: url
          method: "DELETE"
          message: params.message
          success: () ->
            Backbone.history.loadUrl(Backbone.history.fragment);
    )

