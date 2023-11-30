import { onTick } from "./heading-animation.js";

const imgageContainers = document.querySelectorAll('.figure');

const observer = new IntersectionObserver(callback, {
    threshols: .8
});

function callback(entries){
    entries.forEach(entry => {
        const image = entry.target.querySelector('img');
        image.classList.toggle('unset', entry.isIntersecting);
    })
}

imgageContainers.forEach(container => {
    observer.observe(container);
})

onTick();

const message = document.getElementById('message');

setTimeout(() => {
    if(message){
        message.style.display = 'none';
    }
}, 3000);
