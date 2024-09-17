export class Coordinates {
    constructor(row, column) {
        this.row = row;
        this.column = column;
    }

    same(row, column) {
        return this.row === row && this.column === column;
    }
}
