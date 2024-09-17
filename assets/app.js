/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you import will output into a single css file (app.css in this case)
import './styles/app.css';
import {fromHexagons, getNeighbourFieldWithMonk, getNeighbourHexagon, getNeighboursCoordinates} from './map.js'
import {isDefeated} from "./monk";
import {actualColumns, actualShorterColumns, basicColor, blockedColor, colors, deactivatedColor} from "./constants";
import {fromArray} from "./move";

let color = 'red';
let monkNumber = 1;
let isChainChainingTeam = false;
let moves = [];
let gameStarted = false;
let movesCounter = 0;
let messages = [];
let gameFinished = false;
let numberOfGames = 0;
let numberOfTurns = 0;
let winningTeam = 'red';
var tmp = [];
var counter = 0;

const hexagons = document.querySelectorAll('[data-js-hexagon-row]');
const colorCheckBoxes = document.getElementsByName('colorCheckBox');
const winningCheckBoxes = document.getElementsByName('winningCheckBox');
const deactivateFieldCheckBoxes = document.getElementById('deactivateFieldCheckBox');
const activateFieldCheckBoxes = document.getElementById('activateFieldCheckBox');
const playButton = document.getElementById('playButton');
const playLoopButton = document.getElementById('playLoopButton');
const playAgainButton = document.getElementById('playAgainButton');
const moveButton = document.getElementById('moveButton');
const moveAllButton = document.getElementById('moveAllButton');
const statisticsButton = document.getElementById('statisticsButton');
const globalStatisticsButton = document.getElementById('globalStatisticsButton');
const chainChangingTeamCheckBox = document.getElementById('chainChangingTeamCheckBox');

const startGame = () => {
    resetBlockedFields();
    playButton.disabled = true;
    playLoopButton.disabled = true;

    const id =  document.URL.substring(document.URL.lastIndexOf('/') + 1);
    var sendDate = (new Date()).getTime();
    return fetch('/model/' + id, {
            method: 'POST',
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                fields: fromHexagons(hexagons),
                numberOfTurns: numberOfTurns,
                numberOfGames: numberOfGames,
                winningTeam: winningTeam
            })
        })
        .then(response => {

            var receiveDate = (new Date()).getTime();

            var responseTimeMs = receiveDate - sendDate;

            tmp.push({
                number: tmp.length + 1,
                code: response.status,
                time: responseTimeMs
            });

            if (!response.ok) {
                counter++;

                throw new Error();
            }

            counter = 0;

            return response;
        })
        .catch(error => {
            // alert('Something went wrong, please try again');
            playButton.disabled = false;
            playLoopButton.disabled = false;

            return Promise.reject(error);
        })
        .then(response => response.json())
        .then(response => Object.keys(response).map((key) => response[key]))
        .then(response => moves = response.map((move) => fromArray(move)))
        .then(() => {
            if (moves.length > 0) {
                disableAfterGameStart();
                numberOfTurns++;
            }

            playButton.disabled = true;
            playLoopButton.disabled = true;
    });
};

function canMakeMove(moveTo) {
    return hexagons[getIndex(moveTo.row, moveTo.column)].innerHTML === '';
}

function getIndex(row, column) {
    return Math.ceil(row / 2) * actualColumns + Math.floor(row / 2) * actualShorterColumns + parseInt(column);
}

function showMessages() {
    // while (messages.length > 0) {
    //     alert(messages.shift());
    //     playAgainButton.disabled = false;
    // }
}

function enableAfterAllMoves() {
    moveButton.disabled = true;
    moveAllButton.disabled = true;
    playButton.disabled = false;
    playLoopButton.disabled = false;
    colorCheckBoxes.forEach((checkbox) => checkbox.disabled = false);
    winningCheckBoxes.forEach((checkbox) => checkbox.disabled = false);
    chainChangingTeamCheckBox.disabled = false;
    deactivateFieldCheckBoxes.disabled = false;
    activateFieldCheckBoxes.disabled = false;
    gameStarted = false;
}

const restartGame = () => {
    gameFinished = false;
    gameStarted = false;
    enableAfterAllMoves();
    numberOfTurns = 1;
    numberOfGames++;
    playButton.disabled = true;
    playLoopButton.disabled = true;
    playAgainButton.disabled = true;
}

function blockAll() {
    moveButton.disabled = true;
    moveAllButton.disabled = true;
    playButton.disabled = true;
    playLoopButton.disabled = true;
    colorCheckBoxes.forEach((checkbox) => checkbox.disabled = true);
    winningCheckBoxes.forEach((checkbox) => checkbox.disabled = true);
    chainChangingTeamCheckBox.disabled = true;
    deactivateFieldCheckBoxes.disabled = true;
    activateFieldCheckBoxes.disabled = true;
    gameStarted = true;
}

const makeSingleMove = async () => {
    makeMove();
    await new Promise(r => setTimeout(r, 1000));

    if (moves.length === 0) {
        enableAfterAllMoves();
    }

    if (gameFinished) {
        blockAll();
    }

    showMessages();
}

const makeMove = () => {
    const move = moves.shift();

    if (!move) {
        return;
    }

    if (canMakeMove(move.moveTo)) {
        let index = getIndex(move.moveFrom.row, move.moveFrom.column);
        hexagons[index].style.backgroundColor = basicColor;
        hexagons[index].innerHTML = ``;

        index = getIndex(move.moveTo.row, move.moveTo.column);
        hexagons[index].style.backgroundColor = move.team;
        hexagons[index].innerHTML = `<div class="monk-number" data-js-monk-number="${move.id}">${move.id}</div>`;
        movesCounter++;
        chainChangeTeam(hexagons[index]);
    }
};

const makeAllMoves = async () => {
    movesCounter = 0;
    while (moves.length > 0) {
        makeMove();
        await new Promise(r => setTimeout(r, 1000));
        showMessages();
    }

    const movesSkipped = monkNumber - movesCounter - 1;

    if (movesSkipped > 0) {
        // alert(movesSkipped + " moves skipped");
    }
};

const moveAll = () => {
    moveButton.disabled = true;
    moveAllButton.disabled = true;

    makeAllMoves().then(() => {
        moveButton.disabled = false;
        moveAllButton.disabled = true;

        if (moves.length === 0) {
            enableAfterAllMoves();
        }

        if (gameFinished) {
            blockAll();
        }
    });
};

const playInLoop = () => {
    startGame()
        .catch(error => {
            if (counter >= 3 || gameFinished) {
                const id =  document.URL.substring(document.URL.lastIndexOf('/') + 1);

                fetch('/save/' + id, {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(tmp)
                })
                    .then(response => {
                        if (!response.ok) {
                            throw new Error();
                        }

                        return response;
                    })
                    .catch(error => {
                        alert('Something went wrong, please try again');
                    })
                    .then(window.location.href = '/game/new')
            }
            playInLoop();

            return Promise.reject(error);
        })
        .then(() => {
            playLoopButton.disabled = true;
            moveButton.disabled = true;
            moveAllButton.disabled = true;
        })
        .then(() => {
            makeAllMoves().then(() => {
                if (counter >= 3 || gameFinished) {
                    const id =  document.URL.substring(document.URL.lastIndexOf('/') + 1);

                    fetch('/save/' + id, {
                        method: 'POST',
                        headers: {
                            'Accept': 'application/json',
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify(tmp)
                     })
                    .then(response => {
                        if (!response.ok) {
                            throw new Error();
                        }

                        return response;
                    })
                    .catch(error => {
                        alert('Something went wrong, please try again');
                    })
                    .then(window.location.href = '/game/new')

                    return;
                }

                if (!gameFinished) {
                    playInLoop();
                    numberOfTurns++;

                    return;
                }

                blockAll();
            });
        });
};

const chainChangingTeam = () => {
    isChainChainingTeam = chainChangingTeamCheckBox.checked;
};

const showStatistics = () => {
    const id =  document.URL.substring(document.URL.lastIndexOf('/') + 1);

    window.location.href = '/model/' + id + '/statistics';
};

const showGlobalStatistics = () => {
    window.location.href = '/global-statistics';
};

function disableAfterGameStart() {
    moveButton.disabled = false;
    moveAllButton.disabled = false;
    playButton.disabled = true;
    playLoopButton.disabled = true;
    colorCheckBoxes.forEach((checkbox) => checkbox.disabled = true);
    winningCheckBoxes.forEach((checkbox) => checkbox.disabled = true);
    chainChangingTeamCheckBox.disabled = true;
    deactivateFieldCheckBoxes.disabled = true;
    activateFieldCheckBoxes.disabled = true;
    gameStarted = true;
    resetBlockedFields();
}

function detectWiningTeam(detectedColors) {
    const [firstTeamColor, secondTeamColor]  = detectedColors;

    switch (firstTeamColor) {
        case 'red':
            if (secondTeamColor === 'green') {
                return 'red';
            }

            return 'blue';
        case 'blue':
            if (secondTeamColor === 'red') {
                return 'blue';
            }

            return 'green';
        case 'green':
        default:
            if (secondTeamColor === 'blue') {
                return 'green';
            }

            return 'red';
    }
}

function detectPossibleGameStart() {
    const teams = new Set();
    hexagons.forEach((hexagon) => {
        if (colors.includes(hexagon.style.backgroundColor)) {
            teams.add(hexagon.style.backgroundColor);
        }
    });

    if (teams.size === 3) {
        playButton.disabled = false;
        playLoopButton.disabled = false;

        return;
    }

    playButton.disabled = true;
    playLoopButton.disabled = true;
}

function detectEndGame(color) {
    let wasOtherTeamDetected = false
    let detectedColors = new Set();
    detectedColors.add(color);

    hexagons.forEach((hexagon) => {
        const currentHexagonColor = hexagon.style.backgroundColor;
        if (colors.includes(currentHexagonColor) && currentHexagonColor !== color) {
            detectedColors.add(currentHexagonColor);
            wasOtherTeamDetected = true;
        }
    });

    if (!wasOtherTeamDetected) {
        handleGameFinish(color);
        return true;
    }

    if (detectedColors.size !== 3) {
        switch (detectedColors.size) {
            case 1:
                handleGameFinish(detectedColors.values().next().value);
                break;
            case 2:
            default:
                handleGameFinish(detectWiningTeam(detectedColors));
                break
        }

        return true;
    }

    return false;
}

function chainChangeTeam(hexagon) {
    const neighbours = [
        ...getNeighboursCoordinates(
            parseInt(hexagon.dataset.jsHexagonRow),
            parseInt(hexagon.dataset.jsHexagonColumn),
        ).neighbours
    ];

    const neighbourHexagons = [];

    neighbours.forEach((neighbour) => {
        let neighbourHexagon = getNeighbourFieldWithMonk(hexagons, neighbour.row, neighbour.column);
        if (neighbourHexagon) {
            neighbourHexagons.push(neighbourHexagon);
        }
    });

    neighbourHexagons.forEach((neighbourHexagon) => {
        if (neighbourHexagon.style.backgroundColor === hexagon.style.backgroundColor) {
            return;
        }

        if (isDefeated(hexagon, neighbourHexagon) || isChainChainingTeam) {
            const currentHexagonColor = hexagon.style.backgroundColor;
            neighbourHexagon.style.backgroundColor = currentHexagonColor;

            if (moves.length > 0) {
                const deletedMonkNumber = parseInt(neighbourHexagon.children.item(0).dataset.jsMonkNumber);
                moves = moves.filter((move) => move.id !== deletedMonkNumber);
            }

            if (!detectEndGame(currentHexagonColor)) {
                chainChangeTeam(neighbourHexagon);
            }

            return;
        }

        if (isDefeated(neighbourHexagon, hexagon)) {
            const currentHexagonColor = neighbourHexagon.style.backgroundColor;
            hexagon.style.backgroundColor = currentHexagonColor;
            detectEndGame(currentHexagonColor);

            if (!detectEndGame(currentHexagonColor)) {
                chainChangeTeam(hexagon);
            }
        }
    });
}

function removeMonk(deletedMonkNumber) {
    const monks = document.querySelectorAll('[data-js-monk-number]');

    monks.forEach((monk) => {
        const currentMonkNumber = monk.dataset.jsMonkNumber;
        if (parseInt(currentMonkNumber) > deletedMonkNumber) {
            const newId = currentMonkNumber - 1;
            monk.dataset.jsMonkNumber = newId.toString();
            monk.innerHTML = newId.toString();
        }
    });

    monkNumber--;
}

function resetColorCheckBoxes() {
    colorCheckBoxes.forEach((checkBox) => {
        checkBox.checked =  false;

    });
}

function resetWinningCheckBoxes() {
    winningCheckBoxes.forEach((checkBox) => {
        checkBox.checked =  false;

    });
}

function resetBlockedFields() {
    hexagons.forEach((hexagon) => {
        if (hexagon.style.backgroundColor === blockedColor) {
            hexagon.style.backgroundColor = basicColor;
            hexagon.style.cursor = 'pointer';
        }
    });
}

function blockFieldsForTeam() {
    let enemyMonks = [];

    hexagons.forEach((hexagon) => {
        if (colors.includes(hexagon.style.backgroundColor) && hexagon.style.backgroundColor !== color) {
            enemyMonks.push(...getNeighboursCoordinates(parseInt(hexagon.dataset.jsHexagonRow), parseInt(hexagon.dataset.jsHexagonColumn)).neighbours);
        }
    })

    let neighbourHexagons = [];

    enemyMonks.forEach((neighbour) => {
        let neighbourHexagon = getNeighbourHexagon(hexagons, neighbour.row, neighbour.column);
        if (neighbourHexagon) {
            neighbourHexagons.push(neighbourHexagon);
        }
    });

    neighbourHexagons.forEach((hexagon) => {
        if (hexagon.style.backgroundColor === basicColor) {
            hexagon.style.backgroundColor = blockedColor;
        }
        hexagon.style.cursor = 'default';
    });
}

function setMap() {
    const id =  document.URL.substring(document.URL.lastIndexOf('/') + 1);

    fetch('/game/' + id + '/map', {
        method: 'GET',
        headers: {
            'Accept': 'application/json',
            'Content-Type': 'application/json'
        },
    })
        .then(response => response.json())
        .then(response => {
            const fields = response['fields'];
            moves = Object.values(response['moves']);
            numberOfTurns = response['numberOfTurns'];
            numberOfGames = response['numberOfGames'];
            winningTeam = response['winningTeam'];

            winningCheckBoxes.forEach((winningCheckBox) => {
                if (winningTeam === winningCheckBox.value) {
                    resetWinningCheckBoxes();
                    winningCheckBox.checked =  true;
                }
            });

            hexagons.forEach((hexagon, index) =>  {
                hexagon.style.backgroundColor = fields[index].color;

                if(fields[index].monk) {
                    hexagon.innerHTML = `<div class="monk-number" data-js-monk-number="${fields[index].monk.id}">${fields[index].monk.id}</div>`;
                    monkNumber++;
                }

                hexagon.addEventListener('click', () => {
                    if (!gameStarted) {
                        if (hexagon.style.cursor === 'default') {
                            return;
                        }

                        if (color === deactivatedColor || color === basicColor) {
                            hexagon.style.backgroundColor = color;

                            if (hexagon.children.item(0)) {
                                const deletedMonkNumber = parseInt(hexagon.children.item(0).dataset.jsMonkNumber);

                                removeMonk(deletedMonkNumber);
                                hexagon.innerHTML = '';
                                detectPossibleGameStart();
                            }

                            return;
                        }

                        if (hexagon.style.backgroundColor !== color) {
                            if (hexagon.style.backgroundColor === basicColor || hexagon.style.backgroundColor === deactivatedColor) {
                                hexagon.innerHTML = `<div class="monk-number" data-js-monk-number="${monkNumber}">${monkNumber}</div>`;
                                monkNumber++;
                            }
                            hexagon.style.backgroundColor = color;
                            resetBlockedFields();
                            chainChangeTeam(hexagon);
                            blockFieldsForTeam();
                            detectPossibleGameStart();

                            return;
                        }

                        const deletedMonkNumber = parseInt(hexagon.children.item(0).dataset.jsMonkNumber);

                        removeMonk(deletedMonkNumber);
                        hexagon.innerHTML = '';
                        hexagon.style.backgroundColor = basicColor;
                        resetBlockedFields();
                        blockFieldsForTeam();
                        detectPossibleGameStart();
                    }
                })
            });

            resetBlockedFields();

            if(moves.length > 0) {
                disableAfterGameStart();
                numberOfTurns++;
            } else {
                moveButton.disabled = true;
                moveAllButton.disabled = true;
                playButton.disabled = false;
                playLoopButton.disabled = false;
                blockFieldsForTeam();
            }
        })
        .then(playInLoop);
}

function handleGameFinish(color) {
    moves = [];
    messages.push('Game finished: ' + color + ' wins!');
    gameFinished = true;

    const id =  document.URL.substring(document.URL.lastIndexOf('/') + 1);

    fetch('/model/' + id + '/endgame', {
        method: 'POST',
        headers: {
            'Accept': 'application/json',
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            teamThatWon: color,
            teamThatShouldHaveWon: winningTeam
        })
    }).then(response => {});
}

setMap();

deactivateFieldCheckBoxes.addEventListener('click', () => {
    color = deactivatedColor;
    resetColorCheckBoxes();
    resetBlockedFields();
    deactivateFieldCheckBoxes.checked = true;
    activateFieldCheckBoxes.checked = false;
    hexagons.forEach((hexagon) => {
        hexagon.style.cursor = 'pointer';
    });
});

activateFieldCheckBoxes.addEventListener('click', () => {
    color = basicColor;
    resetColorCheckBoxes();
    resetBlockedFields();
    deactivateFieldCheckBoxes.checked = false;
    activateFieldCheckBoxes.checked = true;
    hexagons.forEach((hexagon) => {
        hexagon.style.cursor = 'pointer';
    });
});

colorCheckBoxes.forEach((colorCheckBox) => {
    colorCheckBox.addEventListener('click', () =>  {
        resetBlockedFields();
        color = colorCheckBox.value;
        blockFieldsForTeam();

        deactivateFieldCheckBoxes.checked = false;
        activateFieldCheckBoxes.checked = false;
        resetColorCheckBoxes();
        colorCheckBox.checked =  true;
    });
});

winningCheckBoxes.forEach((winningCheckBox) => {
    winningCheckBox.addEventListener('click', () =>  {
        winningTeam = winningCheckBox.value;
        resetWinningCheckBoxes();
        winningCheckBox.checked =  true;
    });
});

playButton.addEventListener('click', startGame);
playLoopButton.addEventListener('click', playInLoop);
playAgainButton.addEventListener('click', restartGame);
moveButton.addEventListener('click', makeSingleMove);
moveAllButton.addEventListener('click', moveAll);
chainChangingTeamCheckBox.addEventListener('click', chainChangingTeam);
statisticsButton.addEventListener('click', showStatistics);
globalStatisticsButton.addEventListener('click', showGlobalStatistics);
playAgainButton.disabled = true;
