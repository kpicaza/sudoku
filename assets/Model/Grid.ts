import { Value } from '../Types/Value';
import { Box } from '../Types/Box';

export class Grid {
  matrix: Array<Array<Box>>;

  constructor(matrix: Array<Array<Box>>) {
    this.matrix = matrix;
  }

  static fromPlainGrid(grid: Array<Array<string>>): Grid {
    const matrix: Array<Array<Box>> = [];
    let rowIndex = 0;
    for (const row of grid) {
      let colIndex = 0;
      matrix[rowIndex] = [];
      for (const value of row) {
        matrix[rowIndex][colIndex] = {
          value: {
            position: {
              row: rowIndex,
              col: colIndex,
              block: Grid.getBlockIndex(rowIndex, colIndex, 3),
            },
            value,
          },
          selected: false,
          fixed: value !== ' ',
          inlined: false,
        };
        colIndex += 1;
      }
      rowIndex += 1;
    }

    return new Grid(matrix);
  }

  static getBlockIndex(row: number, col: number, blockSize: number): number {
    return (
      Math.floor(row / blockSize) * blockSize + Math.floor(col / blockSize)
    );
  }

  selectWithSameValue(value: Value) {
    let rowIndex = 0;
    for (const row of this.matrix) {
      let colIndex = 0;
      for (const box of row) {
        if (box.value.value === value.value && value.value !== ' ') {
          box.selected = true;
        } else {
          box.selected = false;
        }
        if (
          colIndex === value.position.col ||
          rowIndex === value.position.row ||
          Grid.getBlockIndex(rowIndex, colIndex, 3) === value.position.block
        ) {
          box.inlined = true;
        } else {
          box.inlined = false;
        }

        this.matrix[rowIndex][colIndex] = box;
        colIndex += 1;
      }
      rowIndex += 1;
    }
  }

  setValue(value: Value) {
    const prevBox = this.matrix[value.position.row][value.position.col];
    if (prevBox.value.value === value.value) {
      return;
    }

    this.matrix[value.position.row][value.position.col] = {
      value: { position: prevBox.value.position, value: value.value },
      selected: true,
      fixed: prevBox.fixed,
      inlined: true,
    };
  }
}
