import { html, css, LitElement } from 'lit';
import { property } from 'lit/decorators.js';
import './ValueBox'
import state from "./Store/Store";
import {Grid} from "./Model/Grid";
import {Value} from "./Types/Value";
import {Box} from "./Types/Box";

export class SudokuBoard extends LitElement {
  static styles = css`
    :host {
      display: block;
      padding: 25px;
      color: var(--sudoku-board-text-color, #000);
    }

    table {
      border-spacing: 0;
      border-color: #1b1e21;
      border-style: solid;
    }
    tr {
      padding: 0;
      margin: 0;
    }
    .sudoku-grid {
      margin-left: auto;
      margin-right: auto;
    }
  `;

  @property({ type: Array<Array<string>> }) grid = [];
  @property() blockSize: number = 3;
  @property() store;

  constructor() {
      super();
      this.store = state
  }

    connectedCallback() {
        super.connectedCallback();
        this.store.grid = Grid.fromPlainGrid(this.grid)
    }
  renderRows()
  {
      return html`
          ${this.store.grid.matrix.map((row: Array<Box>, key: number) => html`
              <tr>
                ${this.renderCols(row, key)}
              </tr>
        `)}
      `
  }

  renderCols(row: Array<Box>, rowKey: number)
  {

      return html`
              ${row.map((col: Box, key: number) => html`
                <value-box
                        .position=${{row: rowKey, col: key, block: Grid.getBlockIndex(rowKey, key, 3)}}
                        .box=${col}
                        block-size="${this.blockSize}"
                ></value-box>
            `)}
      `
  }

    boxSelected(e: CustomEvent<Value>) {
        this.store.grid.setValue(e.detail)
        this.store.grid.selectWithSameValue(e.detail)
        this.requestUpdate()
    }

  render() {
    return html`
      <table
              class="sudoku-grid"              
             @boxWasSelected="${this.boxSelected}"
      >${this.renderRows()}</table>
    `;
  }
}
