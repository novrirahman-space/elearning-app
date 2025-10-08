import './bootstrap';

document.addEventListener('DOMContentLoaded', function () {
    const courseId = document.body.dataset.courseId;
    const list = document.querySelector('#discussion-list');

    window.Echo.private(`course.${courseId}`)
        .listen('.discussion.created', (e) => {
            const li = document.createElement('li');
            li.textContent = e.user.name + ': ' + e.content;
            list.appendChild(li);
        })
        .listen('.reply.created', (e) => {
            const li = document.createElement('li');
            li.textContent = '↳ ' + e.user.name + ': ' + e.content;
            list.appendChild(li);
        });
});
