###*
 * @namespace OpenOrchestra:FormBehavior
###
window.OpenOrchestra or= {}
window.OpenOrchestra.FormBehavior or= {}

###*
 * @class FormBehaviorLibrary
###
class OpenOrchestra.FormBehavior.FormBehaviorLibrary
  formBehaviors: []

  ###*
   * add
   * @param {Object} formBehavior
  ###
  add: (formBehavior) ->
    @formBehaviors.push(formBehavior)

  ###*
   * activateBehaviors
   * @param {Object} view
   * @param {Object} form
  ###
  activateBehaviors: (view, form) ->
    for formBehavior in @formBehaviors
      formBehavior.activateBehavior(view, form)

  ###*
   * deactivateBehaviors
   * @param {Object} view
   * @param {Object} form
  ###
  deactivateBehaviors: (view, form) ->
    for formBehavior in @formBehaviors
      formBehavior.deactivateBehavior(view, form)

jQuery ->
  OpenOrchestra.FormBehavior.formBehaviorLibrary = new OpenOrchestra.FormBehavior.FormBehaviorLibrary()
