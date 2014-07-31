
$(function () {
    $('#autocomplete_search_terms').autocomplete({
        source: $('#autocomplete_search_terms').data('autocomplete-url'),
        minLength: $('#autocomplete_search_terms').data('min-length')
    });
});
