###*
 * @namespace OpenOrchestra:FormBehavior
###
window.OpenOrchestra or= {}
window.OpenOrchestra.FormBehavior or= {}

window.OpenOrchestra.FormBehavior.channel = new (Backbone.Wreqr.EventAggregator)

window.OpenOrchestra.FormBehavior.channel.bind 'activate', (view, form) ->
  if typeof OpenOrchestra != 'undefined' and typeof OpenOrchestra.FormBehavior != 'undefined' and typeof OpenOrchestra.FormBehavior.formBehaviorLibrary != 'undefined'
    OpenOrchestra.FormBehavior.formBehaviorLibrary.activateBehaviors view, form
  $("[data-prototype]", form).each ->
    PO.formPrototypes.addPrototype $(@), view
  loadExtendView(view, 'contentTypeSelector') if (elements = $(".contentTypeSelector", form)) && elements.length > 0

window.OpenOrchestra.FormBehavior.channel.bind 'deactivate', (view, form) ->
  if typeof OpenOrchestra != 'undefined' and typeof OpenOrchestra.FormBehavior != 'undefined' and typeof OpenOrchestra.FormBehavior.formBehaviorLibrary != 'undefined'
    OpenOrchestra.FormBehavior.formBehaviorLibrary.deactivateBehaviors view, form
