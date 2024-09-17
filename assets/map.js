import {fromHexagon} from "./field";
import {basicColor, colors, columns, deactivatedColor, rows, shorterColumns} from "./constants";

export function fromHexagons(hexagons) {
    let fields = [];

    hexagons.forEach((hexagon) => {
        fields.push(fromHexagon(hexagon));
    });

    return new Map(fields);
}

export function getNeighbourFieldWithMonk(hexagons, row, column) {
    let neighbour = undefined;

    hexagons.forEach((hexagon) => {
        let currentRow = parseInt(hexagon.dataset.jsHexagonRow);
        let currentColumn = parseInt(hexagon.dataset.jsHexagonColumn);

        if (
            row === currentRow &&
            column === currentColumn &&
            colors.includes(hexagon.style.backgroundColor)
        ) {
            neighbour = hexagon;
        }
    });

    return neighbour
}

export function getNeighboursCoordinates(row, column) {
    let neighbours = {};
    let arrayOfCoordinates = [
        [
            row - 1,
            column,
        ],
        [
            row,
            column - 1,
        ],
        [
            row,
            column + 1,
        ],
        [
            row + 1,
            column,
        ],
    ];
    neighbours['neighbours'] = [];

    switch (row % 2) {
        case 0:
            arrayOfCoordinates.push(
                [
                    row - 1,
                    column - 1,
                ],
                [
                    row + 1,
                    column - 1,
                ],
            );
            break;
        case 1:
            arrayOfCoordinates.push(
                [
                    row - 1,
                    column + 1,
                ],
                [
                    row + 1,
                    column + 1,
                ],
            );
            break;
    }

    arrayOfCoordinates.forEach((coordinates) => {
        if (coordinates[0] < 0 || coordinates[0] > rows) {
            return;
        }
        if (coordinates[1] < 0 || coordinates[1] > columns) {
            return;
        }
        if (1 === coordinates[0] % 2 && coordinates[1] > shorterColumns) {
            return;
        }

        neighbours['neighbours'].push({
            row: coordinates[0], column: coordinates[1]
        });
    });

    return neighbours;
}

export function getNeighbourHexagon(hexagons, row, column) {
    let neighbour = undefined;

    hexagons.forEach((hexagon) => {
        let currentRow = parseInt(hexagon.dataset.jsHexagonRow);
        let currentColumn = parseInt(hexagon.dataset.jsHexagonColumn);

        if (
            row === currentRow &&
            column === currentColumn &&
            (hexagon.style.backgroundColor === basicColor ||
                hexagon.style.backgroundColor === deactivatedColor)
        ) {
            neighbour = hexagon;
        }
    });

    return neighbour
}

class Map {
    constructor(fields) {
        this.fields = fields;
    }

    toJSON() {
        return this.fields;
    }
}
