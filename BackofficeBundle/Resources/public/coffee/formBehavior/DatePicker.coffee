###*
 * @namespace OpenOrchestra:FormBehavior
###
window.OpenOrchestra or= {}
window.OpenOrchestra.FormBehavior or= {}

###*
 * @class DatePicker
###
class OpenOrchestra.FormBehavior.DatePicker extends OpenOrchestra.FormBehavior.AbstractFormBehavior
  convertFormatDay:
    'EEEE' : 'DD'
    'EE' : 'D'
    'E' : 'D'
    'D' : 'o'
  convertFormatMonth:
    'MMMM' : 'MM'
    'MMM' : 'M'
    'MM' : 'mm'
    'M' : 'm'
  convertFormatYear:
    'Y' : 'yy'
    'yyyy': 'yy'
    'y' : 'yy'

  ###*
   * activateBehaviorOnElements
   * @param {Array} elements
   * @param {Object} view
   * @param {Object} form
  ###
  activateBehaviorOnElements: (elements, view, form) ->
    if $.fn.datepicker
      for element in elements
        element = $(element)

        dataDateFormat = element.data('dateformat') or 'yyyy-mm-dd'
        dataDateFormat = @convertFormat(@convertFormatYear, dataDateFormat);
        dataDateFormat = @convertFormat(@convertFormatMonth, dataDateFormat);
        dataDateFormat = @convertFormat(@convertFormatDay, dataDateFormat);

        element.datepicker
          dateFormat: dataDateFormat
          prevText: '<i class="fa fa-chevron-left"></i>'
          nextText: '<i class="fa fa-chevron-right"></i>'

  ###*
   * convertFormat
   * @param {Array} formats
   * @param {dateFormat} view
  ###
  convertFormat: (formats, dateFormat) ->
    for format of formats
      dateReplace = dateFormat.replace(new RegExp(format, 'g'), formats[format]);
      return dateReplace if dateReplace != dateFormat
    return dateFormat

jQuery ->
  OpenOrchestra.FormBehavior.formBehaviorLibrary.add(new OpenOrchestra.FormBehavior.DatePicker(".datepicker"))
