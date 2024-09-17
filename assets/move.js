import {Coordinates} from "./cordinates";

export function fromArray(move) {
    return new Move(
        move['id'],
        move['team'],
        new Coordinates(
            move['moveFrom']['row'],
            move['moveFrom']['column'],
        ),
        new Coordinates(
            move['moveTo']['row'],
            move['moveTo']['column'],
        ),
        move['direction'],
    );
}

class Move {
    constructor(id, team, moveFrom, moveTo, direction) {
        this.id = id;
        this.team = team;
        this.moveFrom = moveFrom;
        this.moveTo = moveTo;
        this.direction = direction;
    }
}
