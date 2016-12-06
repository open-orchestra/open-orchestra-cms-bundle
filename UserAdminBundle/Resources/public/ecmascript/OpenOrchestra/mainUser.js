import UserRouter from './Application/Router/User/UserRouter'

$(() => {
    Backbone.Events.on('application:before:start', () => {
        new UserRouter();
    });
});
