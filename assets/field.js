import {getMonkOrNull} from "./monk";

export function fromHexagon(hexagon) {
    const row = parseInt(hexagon.dataset.jsHexagonRow);
    const column = parseInt(hexagon.dataset.jsHexagonColumn);

    return new Field(
        row,
        column,
        getMonkOrNull(hexagon),
        hexagon.style.backgroundColor
    );
}

class Field {
    constructor(row, column, monk, colour) {
        this.row = row;
        this.column = column;
        this.monk = monk;
        this.color = colour;
    }
}
