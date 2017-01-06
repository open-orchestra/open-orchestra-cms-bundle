import WorkflowSubApplication from './Application/WorkflowSubApplication'

$(() => {
    Backbone.Events.on('application:before:start', () => {
        WorkflowSubApplication.run();
    });
});
