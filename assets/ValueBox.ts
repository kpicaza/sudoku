import { css, html, LitElement } from 'lit';
import { property } from 'lit/decorators.js';
import { Position } from './Types/Position';
import { Value } from './Types/Value';
import { Box } from './Types/Box';
import { DrawMode } from './Types/DrawMode';

export class ValueBox extends LitElement {
  static styles = css`
    :host {
      display: inline-block;
      padding: 0;
      color: var(--sudoku-board-text-color, #000);
      font-family: Verdana, Arial;
    }
    td {
      padding: 0;
      text-align: center;
      height: 60px;
      width: 60px;
      border: 1.5px solid #c6c8ca;
      font-size: 36px;
    }
    .border-right {
      border-right: 2px solid #2e3436;
    }
    .border-bottom {
      border-bottom: 2px solid #2e3436;
      margin-top: 1px;
    }
    .border-left {
      border-left: 2px solid #2e3436;
      right: 1px;
    }
    .border-top {
      border-top: 2px solid #2e3436;
    }
    .sudoku-col {
      display: inline;
      border: none;
      width: 100%;
      height: 100%;
      color: #005cbf;
      font-size: 36px;
      text-align: center;
      vertical-align: central;
      outline: none;
      caret-color: transparent;
      position: relative;
      top: 0;
      left: 0;
      background: transparent;
    }
    .sudoku-col.fixed {
      color: #000;
      background: transparent;
    }
    .sudoku-col.selected {
      background: #d9edf7;
    }
    .sudoku-col.inlined {
      background: #ebebeb;
    }
    .sudoku-col:focus,
    .sudoku-col::selection {
      background: transparent;
    }
    .sudoku-col.bg-transparent {
      background: transparent;
    }
  `;

  @property() position: Position = { row: 0, col: 0, block: 0 };

  @property() box: Box = {
    value: {
      position: { row: 0, col: 0, block: 0 },
      value: ' ',
      validValue: ' ',
      pencilMarks: [],
    },
    selected: false,
    fixed: false,
    focused: false,
    inlined: false,
  };

  @property() blockSize: number = 3;

  @property() size: number;

  @property() drawMode: DrawMode = DrawMode.Value;

  constructor() {
    super();
    this.size = this.blockSize * this.blockSize;
  }

  drawBorders() {
    let classNames: string = '';

    const col = this.position.col + 1;
    const row = this.position.row + 1;

    if (col % this.blockSize === 0) {
      classNames += 'border-right ';
    }
    if (row % this.blockSize === 0) {
      classNames += 'border-bottom';
    }

    if ((col - 1) % this.blockSize === 0 && col - 1 !== this.size) {
      classNames += ' border-left ';
    }
    if ((row - 1) % this.blockSize === 0 && row - 1 !== this.size) {
      classNames += ' border-top';
    }

    return classNames;
  }

  boxFocused(e: InputEvent) {
    const input = e.target as HTMLInputElement;

    input.selectionStart = 0;
    input.selectionEnd = 1;

    this.dispatchEvent(
      new CustomEvent('boxWasSelected', {
        bubbles: true,
        composed: true,
        detail: {
          value: input.value,
          pencilMarks: this.box.value.pencilMarks,
          position: this.box.value.position,
        },
      })
    );
  }

  getClassNames() {
    let classNames = '';
    if (this.box.selected) {
      classNames = 'selected';
    }
    if (this.box.inlined) {
      classNames += ' inlined';
    }

    return classNames;
  }

  addValue(e: InputEvent) {
    let inputValue = e.data;
    if (inputValue === null) {
      inputValue = ' ';
    }
    const input = e.target as HTMLInputElement;

    input.selectionStart = 0;

    if (inputValue && inputValue.match(/[1-9\s]/) === null) {
      input.value = this.box.value.value;
      this.boxFocused(e);
      return;
    }

    if (this.drawMode === DrawMode.Note) {
      return;
    }

    input.value = inputValue;
    this.boxFocused(e);
  }

  drawCol(value: Value) {
    if (this.box.fixed) {
      return html`
        <pencil-mark
          style="z-index: -1"
          .inlined=${this.box.inlined}
          .focused=${this.box.focused}
          .box=${this.box}
          .drawMode=${this.drawMode}
          .pencilMark=${this.box.value.pencilMarks}
        ></pencil-mark>
        <input
          style="z-index: 1"
          @focus=${this.boxFocused}
          readonly
          class="sudoku-col fixed ${this.getClassNames()}"
          type="text"
          maxlength="1"
          .value="${value.value}"
        />
      `;
    }

    return html`
      <pencil-mark
        style="z-index: ${this.drawMode === DrawMode.Value ? '-1' : '1'}"
        .inlined=${this.box.inlined}
        .focused=${this.box.focused}
        .selected=${this.box.selected}
        .box=${this.box}
        .drawMode=${this.drawMode}
        .pencilMark=${this.box.value.pencilMarks}
      ></pencil-mark>
      <input
        style="z-index: ${value.value === ' ' && this.drawMode === DrawMode.Note
          ? '-1'
          : '1'}"
        @focus=${this.boxFocused}
        @input=${this.addValue}
        class="sudoku-col"
        type="text"
        maxlength="1"
        .value="${value.value}"
      />
    `;
  }

  protected render() {
    return html`
      <td class="${this.drawBorders()}">${this.drawCol(this.box.value)}</td>
    `;
  }
}
