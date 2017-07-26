import UserSubApplication from 'OpenOrchestra/Application/UserSubApplication'

$(() => {
    Backbone.Events.on('application:before:start', () => {
        UserSubApplication.run();
    });
});
