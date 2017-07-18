import WorkflowSubApplication from 'OpenOrchestra/Application/WorkflowSubApplication'

$(() => {
    Backbone.Events.on('application:before:start', () => {
        WorkflowSubApplication.run();
    });
});
