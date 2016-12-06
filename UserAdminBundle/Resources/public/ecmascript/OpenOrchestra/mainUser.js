import UserSubApplication from './Application/UserSubApplication'

$(() => {
    Backbone.Events.on('application:before:start', () => {
        UserSubApplication.run();
    });
});
