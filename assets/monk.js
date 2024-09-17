export function getMonkOrNull(hexagon) {
    return !hexagon.children.item(0) ?
        null :
        new Monk(
            parseInt(hexagon.children.item(0).dataset.jsMonkNumber),
            hexagon.style.backgroundColor,
        );
}

export function isDefeated(monk, oppositeMonk) {
    return (monk.style.backgroundColor === 'red' && oppositeMonk.style.backgroundColor === 'green') ||
        (monk.style.backgroundColor === 'green' && oppositeMonk.style.backgroundColor === 'blue') ||
        (monk.style.backgroundColor === 'blue' && oppositeMonk.style.backgroundColor === 'red');
}

class Monk {
    constructor(id, team) {
        this.id = id;
        this.team = team;
    }
}
