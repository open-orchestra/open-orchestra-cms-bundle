import StatusSubApplication from './Application/StatusSubApplication'

$(() => {
    Backbone.Events.on('application:before:start', () => {
        StatusSubApplication.run();
    });
});
