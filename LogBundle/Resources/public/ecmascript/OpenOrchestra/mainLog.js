import LogSubApplication from 'OpenOrchestra/Application/LogSubApplication'

$(() => {
    Backbone.Events.on('application:before:start', () => {
        LogSubApplication.run();
    });
});
