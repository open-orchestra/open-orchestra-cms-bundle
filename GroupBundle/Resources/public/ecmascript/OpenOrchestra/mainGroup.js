import GroupSubApplication from 'OpenOrchestra/Application/GroupSubApplication'

$(() => {
    Backbone.Events.on('application:before:start', () => {
        GroupSubApplication.run();
    });
});
