import { css, html, LitElement } from 'lit';
import { property } from 'lit/decorators.js';
import './ValueBox';
import './Components/AnnotateButton';
import state from './Store/Store';
import { Grid } from './Model/Grid';
import { Value } from './Types/Value';
import { Box } from './Types/Box';
import { DrawMode } from './Types/DrawMode';

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

  @property({ type: Array<Array<string>> }) raw = [];

  @property() grid: Grid;

  @property() blockSize: number = 3;

  @property() store;

  @property() drawMode: DrawMode = DrawMode.Value;

  constructor() {
    super();
    this.store = state;
    this.grid = Grid.fromPlainGrid(this.raw);
  }

  connectedCallback() {
    super.connectedCallback();
    this.grid = Grid.fromPlainGrid(this.raw);
    this.store.grid = this.grid;
  }

  renderRows() {
    return html`
      ${this.grid.matrix.map(
        (row: Array<Box>, key: number) => html`
          <tr>
            ${this.renderCols(row, key)}
          </tr>
        `
      )}
    `;
  }

  renderCols(row: Array<Box>, rowKey: number) {
    return html`
      ${row.map(
        (col: Box, key: number) => html`
          <value-box
            .position=${{
              row: rowKey,
              col: key,
              block: Grid.getBlockIndex(rowKey, key, 3),
            }}
            .box=${col}
            block-size="${this.blockSize}"
            .drawMode=${this.drawMode}
          ></value-box>
        `
      )}
    `;
  }

  selectBox(e: CustomEvent<Value>) {
    this.store.grid.setValue(e.detail);
    this.store.grid.selectWithSameValue(e.detail);
    this.grid = this.store.grid;
    this.requestUpdate();
  }

  changeDrawMode(e: CustomEvent<DrawMode>) {
    this.store.drawMode = e.detail;
    this.drawMode = this.store.drawMode;
    this.requestUpdate();
  }

  render() {
    return html`
      <div class="sudoku-grid">
        <annotate-button
          @drawModeChanged=${this.changeDrawMode}
        ></annotate-button>
        <table
          @boxWasSelected="${this.selectBox}"
          @pencilMarkAdded="${this.selectBox}"
        >
          ${this.renderRows()}
        </table>
      </div>
    `;
  }
}
