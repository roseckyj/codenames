let fields = [];
let game_id;
let selected = [];
let red = 0;
let blue = 0;

function select(index, send) {
    //console.log(game_id);
    //console.log(index);
    if (!selected.includes(index)) {
        selected.push(index);
        const res = fields[index];
        const obj = document.getElementById('card_' + index);
        obj.innerHTML += '<div class="solved sol' + res + '" id="solved_' + index + '"></div>';
        if (res == 1) {
            red--;
        }
        if (res == 2) {
            blue--;
        }
        document.getElementById('remaining-red').innerHTML = red;
        document.getElementById('remaining-blue').innerHTML = blue;
    }
    if (send) {
        const Http = new XMLHttpRequest();
        const url='./submit_click.php?game_id=' + game_id + '&i=' + index;
        Http.open("GET", url);
        Http.send();
        
        Http.onreadystatechange = () => {
            if (Http.readyState == 4 && Http.status == 200) {
                //console.log(Http.responseText)
            }
        }
    }
}

function startGame(type) {
    document.body.removeChild(document.getElementById("blindfold"));

    if (type == 1) {
        console.log("Spymaster");
        fields.forEach((res, index) => {
            const obj = document.getElementById('card_' + index).classList.add("hint" + res);
        })
    }
}

function init(game_idLocal, fieldsLocal) {
    game_id = game_idLocal;
    fields = fieldsLocal;
    fields.forEach((f) => {
        if (f == 1) {
            red++;
        }
        if (f == 2) {
            blue++;
        }
    })

    setInterval(()=> {
        resize();
        const Http = new XMLHttpRequest();
        const url='./get_status.php?game_id=' + game_id;
        Http.open("GET", url);
        Http.send();
        
        Http.onreadystatechange = () => {
            if (Http.readyState == 4 && Http.status == 200) {
                activeTiles = JSON.parse(Http.responseText);
                //console.log(activeTiles);
                for (let i = 0; i < activeTiles.length; i++) {
                    select(activeTiles[i], false);
                }
                document.getElementById('remaining-red').innerHTML = red;
                document.getElementById('remaining-blue').innerHTML = blue;
            }
        }
    }, 300)
}

function resize() {
    const scaleW = window.innerWidth / (220*5);
    const scaleH = window.innerHeight / (155*5);
    const scale = Math.min((scaleW > scaleH) ? scaleH : scaleW, 1);

    const elements = document.getElementsByClassName("card_wrapper");
    for(let i = 0; i < elements.length; i++) {
        elements[i].style.transform = "scale(" + (scale) + ")";
        if (scale < 0.5) {
            elements[i].classList.add("small");
        } else {
            elements[i].classList.remove("small");
        }
    }

    const flags = document.getElementsByClassName("remaining");
    for(let i = 0; i < flags.length; i++) {
        if (scale < 0.5) {
            flags[i].classList.add("small");
        } else {
            flags[i].classList.remove("small");
        }
    }
}

window.addEventListener('resize', () => {
    resize();
}, true);