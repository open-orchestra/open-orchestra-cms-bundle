import GroupSubApplication from './Application/GroupSubApplication'

$(() => {
    Backbone.Events.on('application:before:start', () => {
        GroupSubApplication.run();
    });
});
