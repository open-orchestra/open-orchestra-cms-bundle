import LogSubApplication from './Application/LogSubApplication'

$(() => {
    Backbone.Events.on('application:before:start', () => {
        LogSubApplication.run();
    });
});
