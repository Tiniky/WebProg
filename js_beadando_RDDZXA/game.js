//Mező
const FieldState = {
    LIT : 1,
    UNLIT : 2,
    LIGHTBULB: 3
}

class Field{
    isObsticle = false
    state = FieldState.UNLIT
    bulbNeighbour = 0
    bulbsNextToIt = 0
    litByMultiple = 0
    isMessedUp = false
}

//Játék
const Difficulty = {
    EASY : {
        size : 7,
        obsticles : [0, 3, 1, 1, 1, 5, 3, 0, 3, 3, 3, 6, 5, 1, 5, 5, 6, 3],
        obsticle: 9,
        bulbDB : [1, 0, 2, -1, -1, -1, -1, 2, 3]
    },
    ADVANCED : {
        size : 7,
        obsticles : [0, 2, 0, 4, 2, 0, 2, 2, 2, 4, 2, 6, 3, 3, 4, 0, 4, 2, 4, 4, 4, 6, 6, 2, 6, 4],
        obsticle: 13,
        bulbDB : [0, -1, -1, -1, 3, -1, 1, 2, -1, -1, -1, -1, 2]
    },
    EXTREME : {
        size : 10,
        obsticles : [0, 1, 1, 5, 1, 7, 1, 9, 2, 1, 2, 2, 2, 7, 3, 4, 4, 1, 4, 4, 4, 5, 4, 6, 5, 3, 5, 4, 5, 5, 5, 8, 6, 5, 7, 2, 7, 7, 7, 8, 8, 0, 8, 2, 8, 4, 9, 8],
        obsticle: 24,
        bulbDB : [-1, 3, 2, -1, 0, -1, -1, -1, 1, -1, 1, -1, -1, -1, -1, 3, -1, 1, 0, -1, 3, -1, 0, 0]
    }
}

const Stage = {
    NOTSTARTED: 1,
    INGAME: 2,
    GG: 3
}

class Game{
    diff = ""
    board = []
    size = 0
    obsticles = []
    obsticle = 0
    bulbDB = []
    stage = Stage.NOTSTARTED

    init(setDiff){
        this.board = []
        if(setDiff == "ez"){
            this.diff = "Könnyű"
            this.size = Difficulty.EASY.size
            this.obsticles = Difficulty.EASY.obsticles
            this.obsticle = Difficulty.EASY.obsticle
            this.bulbDB = Difficulty.EASY.bulbDB
        } else if(setDiff == "soso"){
            this.diff = "Haladó"
            this.size = Difficulty.ADVANCED.size
            this.obsticles = Difficulty.ADVANCED.obsticles
            this.obsticle = Difficulty.ADVANCED.obsticle
            this.bulbDB = Difficulty.ADVANCED.bulbDB
        } else if(setDiff == "hardcore"){
            this.diff = "Extrém"
            this.size = Difficulty.EXTREME.size
            this.obsticles = Difficulty.EXTREME.obsticles
            this.obsticle = Difficulty.EXTREME.obsticle
            this.bulbDB = Difficulty.EXTREME.bulbDB
        }

        for(let x = 0; x < this.size; x++){
            this.board[x] = []
            for(let y = 0; y < this.size; y++){
                this.board[x][y] = new Field()
            }
        }

        for(let i = 0; i < this.obsticle*2; i+=2){
            let x = this.obsticles[i]
            let y = this.obsticles[i+1]
            this.board[x][y].isObsticle = true;
            this.board[x][y].bulbNeighbour = this.bulbDB[i/2]
        }

        this.stage = Stage.INGAME
    }

    lit(x,y){
        if(this.board[x][y].state != FieldState.LIGHTBULB){
            this.board[x][y].state = FieldState.LIT
        }
    }
    
    setBulb(x,y){
        this.board[x][y].state = FieldState.LIGHTBULB
        if(x+1 < this.size && this.board[x+1][y].isObsticle){
            this.board[x+1][y].bulbsNextToIt += 1;
        }
        if(y+1 < this.size && this.board[x][y+1].isObsticle){
            this.board[x][y+1].bulbsNextToIt += 1
        }
        if(x-1 >= 0 && this.board[x-1][y].isObsticle){
            this.board[x-1][y].bulbsNextToIt += 1;
        }
        if(y-1 >= 0 && this.board[x][y-1].isObsticle){
            this.board[x][y-1].bulbsNextToIt += 1
        }
    }

    removeBulb(x,y){
        if(this.board[x][y].litByMultiple > 0){
            this.board[x][y].state = FieldState.LIT
        } else{
            this.board[x][y].state = FieldState.UNLIT
        }
        
        if(x+1 < this.size && this.board[x+1][y].isObsticle){
            this.board[x+1][y].bulbsNextToIt -= 1;
        }
        if(y+1 < this.size && this.board[x][y+1].isObsticle){
            this.board[x][y+1].bulbsNextToIt -= 1
        }
        if(x-1 >= 0 && this.board[x-1][y].isObsticle){
            this.board[x-1][y].bulbsNextToIt -= 1;
        }
        if(y-1 >= 0 && this.board[x][y-1].isObsticle){
            this.board[x][y-1].bulbsNextToIt -= 1
        }
    }

    unlit(x,y){
        if(this.board[x][y].state != FieldState.LIGHTBULB){
            this.board[x][y].state = FieldState.UNLIT
        }
    }

    checkIfWon(){
        let litDB = 0
        let wellLitObsticles = 0
        let allGood = true
        
        for(let row of this.board){
            for(let field of row){
                if(field.state == FieldState.LIT || field.state == FieldState.LIGHTBULB){
                    litDB++
                }

                if(field.isMessedUp){
                    allGood = false
                    break
                }

                if(field.isObsticle && field.bulbNeighbour == field.bulbsNextToIt){
                    wellLitObsticles++
                }
            }
        }

        let obsticlesWithSpecifiedNeighbours = this.obsticle - (this.bulbDB.filter(elem => elem == -1)).length
        if(allGood && litDB == (this.size*this.size)-this.obsticle && wellLitObsticles == obsticlesWithSpecifiedNeighbours){
            this.stage = Stage.GG
        }
    }
}

//Megjelenítés
const gameIsOn = document.querySelector('#game')
const newGame = document.querySelector('#new')
const diff = document.querySelector('#difficulty')
const easy = document.querySelector('#easy')
const medium = document.querySelector('#medium')
const hard = document.querySelector('#hard')
const after = document.querySelector('#after')
const again = document.querySelector('#again')

function render(gameState){
    let table = document.createElement('table')
    for(let i = 0; i<gameState.size; i++){
        let tr = document.createElement('tr')
        for(let j = 0; j<gameState.size; j++){
            let td = document.createElement('td')
            let btn = document.createElement('button')
            if(gameState.board[i][j].state == FieldState.UNLIT){
                btn.style.backgroundColor =  gameState.board[i][j].isObsticle ? 'black' : 'white'
                btn.innerText = gameState.board[i][j].isObsticle && gameState.board[i][j].bulbNeighbour > -1 ? gameState.board[i][j].bulbNeighbour : ''
                if(gameState.board[i][j].isObsticle && gameState.board[i][j].bulbNeighbour == gameState.board[i][j].bulbsNextToIt){
                    btn.style.color = "green"
                } else{
                    btn.style.color = "white"
                }
            } else if(gameState.board[i][j].state == FieldState.LIGHTBULB){
                btn.style.backgroundColor =  gameState.board[i][j].isObsticle ? '' : 'yellow'
                btn.innerText = gameState.board[i][j].isObsticle ? "" : "💡"

                if(gameState.board[i][j].isMessedUp){
                    btn.style.backgroundColor = 'red'
                }

            } else if(gameState.board[i][j].state == FieldState.LIT){
                btn.style.backgroundColor =  gameState.board[i][j].isObsticle ? 'black' : 'yellow'
                btn.innerText = gameState.board[i][j].isObsticle && gameState.board[i][j].bulbNeighbour > -1 ? gameState.board[i][j].bulbNeighbour : ''
            }

            td.appendChild(btn)
            tr.appendChild(td)
        }
        table.appendChild(tr)
    }
    gameIsOn.appendChild(table)
}

//Szimuláció part
const nameInput = document.querySelector('#name')
const before = document.querySelector('#before')
const labelDiff = document.querySelector('#label')
const labelPlayer = document.querySelector('#player')
const labelTime = document.querySelector('#time')
const leftIMG = document.querySelector('#left')
const rightIMG = document.querySelector('#right')
const afterPlayer = document.querySelector('#player2')

diff.style.display = "none"
gameIsOn.style.display = "none"
after.style.display = "none"
const welcome = "Hello :D"
const game = new Game()

function isEmpty(string){
    return !string.trim().length;
}

function showDifficulties(){
    if(isEmpty(nameInput.value)){
        return;
    } else{
        labelPlayer.innerText = welcome 
        before.style.display = "none"
        gameIsOn.style.display = "block"
        diff.style.display = "block"
        newGame.style.display = "none"
        labelTime.style.display = "none"
        leftIMG.style.display = "none"
        rightIMG.style.display = "none"
    }
}

newGame.addEventListener("click", showDifficulties)

function showDifficulties2(){
    time.reset()
    gameIsOn.innerHTML = ""
    gameIsOn.appendChild(labelPlayer)
    gameIsOn.appendChild(labelTime)
    gameIsOn.appendChild(labelDiff)
    labelDiff.style.display = "none"
    diff.style.display = "block"
    after.style.display = "none"
    labelTime.style.display = "none"
}
again.addEventListener("click", showDifficulties2)

easy.addEventListener("click", ()=>{
    time.start()
    diff.style.display = "none"
    labelTime.style.display = "block"
    gameIsOn.style.display = "block"
    labelDiff.style.display = "block"
    labelDiff.innerText = "Nehézség: Könnyű"
    labelPlayer.innerText = "Játékos: " + nameInput.value
    game.init("ez")
    render(game)
})

medium.addEventListener("click", ()=>{
    time.start()
    diff.style.display = "none"
    labelTime.style.display = "block"
    gameIsOn.style.display = "block"
    labelDiff.style.display = "block"
    labelDiff.innerText ="Nehézség: Haladó"
    labelPlayer.innerText = "Játékos: " + nameInput.value
    game.init("soso")
    render(game)
})

hard.addEventListener("click", ()=>{
    time.start()
    diff.style.display = "none"
    labelTime.style.display = "block"
    gameIsOn.style.display = "block"
    labelDiff.style.display = "block"
    labelDiff.innerText = "Nehézség: Extrém"
    labelPlayer.innerText = "Játékos: " + nameInput.value
    game.init("hardcore")
    render(game)
})

function handleFieldClick(event){
    if(!event.target.matches("button")){
        return
    }

    if(game.stage !== Stage.INGAME){
        return
    }

    const td = event.target.parentNode
    const tr = td.parentNode
    const y = td.cellIndex
    const x = tr.rowIndex
    if(game.board[x][y].isObsticle){
        return
    }

    if(game.board[x][y].state != FieldState.LIGHTBULB){
        game.setBulb(x,y)
        game.board[x][y].litByMultiple += 1

        for(let i = x-1; i>=0; i--){
            if(game.board[i][y].isObsticle){
                break
            } else if(game.board[i][y].state == FieldState.LIGHTBULB){
                game.board[i][y].isMessedUp = true
                game.board[x][y].isMessedUp = true
            } else{
                game.board[i][y].litByMultiple += 1
                if(game.board[i][y].state != FieldState.LIT){
                    game.lit(i, y)
                }
            }
        }

        for(let i = x+1; i<game.size; i++){
            if(game.board[i][y].isObsticle){
                break
            } else if(game.board[i][y].state == FieldState.LIGHTBULB){
                game.board[i][y].isMessedUp = true
                game.board[x][y].isMessedUp = true
            } else{
                game.board[i][y].litByMultiple += 1
                if(game.board[i][y].state != FieldState.LIT){
                    game.lit(i, y)
                }
            }
        }

        for(let j = y-1; j>=0; j--){
            if(game.board[x][j].isObsticle){
                break
            } else if(game.board[x][j].state == FieldState.LIGHTBULB){
                game.board[x][j].isMessedUp = true
                game.board[x][y].isMessedUp = true
            } else{
                game.board[x][j].litByMultiple += 1
                if(game.board[x][j].state != FieldState.LIT){
                    game.lit(x, j)
                }
            }
        }

        for(let j = y+1; j<game.size; j++){
            if(game.board[x][j].isObsticle){
                break
            } else if(game.board[x][j].state == FieldState.LIGHTBULB){
                game.board[x][j].isMessedUp = true
                game.board[x][y].isMessedUp = true
            } else{
                game.board[x][j].litByMultiple += 1
                if(game.board[x][j].state != FieldState.LIT){
                    game.lit(x, j)
                }
            }
        }

        gameIsOn.innerHTML = ""
        gameIsOn.appendChild(labelPlayer)
        gameIsOn.appendChild(labelTime)
        gameIsOn.appendChild(labelDiff)
        render(game)

    } else if(game.board[x][y].state == FieldState.LIGHTBULB){
        game.board[x][y].litByMultiple -= 1
        game.removeBulb(x,y)

        for(let i = x-1; i>=0; i--){
            if(game.board[i][y].isObsticle){
                break
            } else if(game.board[i][y].isMessedUp){
                game.board[i][y].isMessedUp = false
                game.board[x][y].isMessedUp = false
            } else{
                if(game.board[i][y].state == FieldState.LIT){
                    game.board[i][y].litByMultiple -= 1
                    if(game.board[i][y].litByMultiple != 1){
                        game.unlit(i, y)
                    }
                }
            }
        }

        for(let i = x+1; i<game.size; i++){
            if(game.board[i][y].isObsticle){
                break
            } else if(game.board[i][y].isMessedUp){
                game.board[i][y].isMessedUp = false
                game.board[x][y].isMessedUp = false
            } else{
                if(game.board[i][y].state == FieldState.LIT){
                    game.board[i][y].litByMultiple -= 1
                    if(game.board[i][y].litByMultiple != 1){
                        game.unlit(i, y)
                    }
                }
            }
        }

        for(let j = y-1; j>=0; j--){
            if(game.board[x][j].isObsticle){
                break
            } else if(game.board[x][j].isMessedUp){
                game.board[x][j].isMessedUp = false
                game.board[x][y].isMessedUp = false
            } else{
                if(game.board[x][j].state == FieldState.LIT){
                    game.board[x][j].litByMultiple -= 1
                    if(game.board[x][j].litByMultiple != 1){
                        game.unlit(x, j)
                    }
                }
            }
        }

        for(let j = y+1; j<game.size; j++){
            if(game.board[x][j].isObsticle){
                break
            } else if(game.board[x][j].isMessedUp){
                game.board[x][j].isMessedUp = false
                game.board[x][y].isMessedUp = false
            } else{
                if(game.board[x][j].state == FieldState.LIT){
                    game.board[x][j].litByMultiple -= 1
                    if(game.board[x][j].litByMultiple != 1){
                        game.unlit(x, j)
                    }
                } 
            }
        }

        gameIsOn.innerHTML = ""
        gameIsOn.appendChild(labelPlayer)
        gameIsOn.appendChild(labelTime)
        gameIsOn.appendChild(labelDiff)
        render(game)
    }
    
    game.checkIfWon()

    if(game.stage == Stage.GG){
        gameIsOn.style.display = "none"
        after.style.display = "block"
        afterPlayer.innerText = "Játékos: " + nameInput.value
        ingameTime = Math.round(time.getTime() / 1000)
        time.stop()
        finalTime.innerText = ingameTime

        /*
        let db = localStorage.getItem("db") + 1
        localStorage.removeItem("db")
        localStorage.setItem("db", db)

        localStorage.setItem("nev" + db, nameInput.value)
        localStorage.setItem("diff" + db, game.diff)
        localStorage.setItem("ido" + db, time.overallTime)
        */
    }
}

gameIsOn.addEventListener("click", handleFieldClick)

//Időzítő
class Timer {
    constructor () {
      this.isRunning = false;
      this.startTime = 0;
      this.overallTime = 0;
    }
  
    secPassed() {
      if (!this.startTime) {
        return 0;
      }
    
      return Date.now() - this.startTime;
    }
  
    start() {
      if (this.isRunning) {
        return console.error('Timer is already running');
      }
  
      this.isRunning = true;
  
      this.startTime = Date.now();
    }
  
    stop() {
      if (!this.isRunning) {
        return console.error('Timer is already stopped');
      }
  
      this.isRunning = false;
  
      this.overallTime = this.overallTime + this.secPassed();
    }
  
    reset() {
      this.overallTime = 0;
  
      if (this.isRunning) {
        this.startTime = Date.now();
        return;
      }
  
      this.startTime = 0;
    }
  
    getTime() {
      if (!this.startTime) {
        return 0;
      }
  
      if (this.isRunning) {
        return this.overallTime + this.secPassed();
      }
  
      return this.overallTime;
    }
}

const time = new Timer
const secondsPassed = document.querySelector('#secondsPassed')
const finalTime = document.querySelector('#secondsPassed2')
setInterval(() => {
    const timeInSeconds = Math.round(time.getTime() / 1000);
    secondsPassed.innerText = timeInSeconds;
  }, 100)

//localstore jk nvm
/*const ldrboard = document.querySelector('#leaderboard')
localStorage.setItem("db", 0)
localStorage.setItem("nev", "Játékos neve")
localStorage.setItem("diff", "Nehézség")
localStorage.setItem("ido", "Idő")

let data = ["nev", "diff", "ido"]

let test = document.createElement('table')
let htr = document.createElement('tr')
for(let i = 0; i<3; i++){
    let td = document.createElement('td')
    td.innerText = localStorage.getItem(data[i])
    htr.appendChild(td)
}
test.appendChild(htr)

for(let i = 0; i<localStorage.getItem("db"); i++){
    let tr = document.createElement('tr')
    for(let j=0; j<3; j++){
        let td = document.createElement('td')
        td.innerText = localStorage.getItem(data[i] + i)
        tr.appendChild(td)
    }
    test.appendChild(tr)
}

ldrboard.appendChild(test)*/