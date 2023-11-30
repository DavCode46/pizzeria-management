const text = document.querySelector('.animation');
const strText = text.textContent;
const splitText = strText.split("");
text.textContent = "";

for (let i = 0; i < splitText.length; i++) {
    let charClass = '';
    // Agrega clases diferentes a las letras basadas en la posición para simular la bandera de Italia
    if (splitText[i] === ' ') {
        text.innerHTML += " ";
    } else {
        if (i < 4) {
            charClass = 'green';
        } else if (i >= 4 && i <= 8) {
            charClass = 'white';
        } else {
            charClass = 'red';
        }
        text.innerHTML += "<span class='" + charClass + "'>" + splitText[i] + "</span>";
    }
}

let char = 0;
let timer = setInterval(onTick, 75);

export function onTick() {
    const spans = text.querySelectorAll('span');
    if (char < spans.length) {
        const span = spans[char];
        if (span) {
            span.classList.add('fade');
            char++;
        }
    } else {
        complete();
    }
}



function complete() {
    clearInterval(timer);
    timer = null;
}