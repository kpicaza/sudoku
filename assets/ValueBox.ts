import { html, css, LitElement } from 'lit';
import { property } from 'lit/decorators.js';
import {Position} from "./Types/Position";
import {Value} from "./Types/Value";
import {Box} from "./Types/Box";

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
        border: 1px solid #c6c8ca;
        font-size: 36px;
      }
      .border-right {
        border-right: 2px solid #2e3436;
      }
    .border-bottom {
      border-bottom: 2px solid #2e3436;

    }
    .sudoku-col {
      border: none;
      width: 100%;
      height: 100%;
      color: #005cbf;
      font-size: 36px;
      text-align: center;
      vertical-align: central;
      outline: none;
      caret-color: transparent;
    }
      .sudoku-col.fixed {
        color: #000;

      }
      .sudoku-col.selected {
        background: #d9edf7;
      } 
      .sudoku-col.inlined {
        background: #ebebeb;
      } 
      .sudoku-col:focus {
        background: #b8daff;
      }
  `;


    @property() position: Position = {row: 0, col: 0, block: 0}
    @property() box: Box = {value: {position: {row: 0, col: 0, block: 0}, value: ' '}, selected: false, fixed: false, inlined: false}
    @property() blockSize: number = 3;
    @property() size: number;


    constructor() {
        super();
        this.size = this.blockSize * this.blockSize

    }

    drawBorders() {
        let classNames: string = ''

        const col = (this.position.col + 1)
        const row = (this.position.row + 1)

        if (col % this.blockSize === 0 && col !== this.size) {
            classNames += 'border-right '
        }
        if (row % this.blockSize === 0 && row !== this.size) {
            classNames += 'border-bottom'
        }

        return classNames
    }

    boxFocused() {
        this.dispatchEvent(
            new CustomEvent('boxWasSelected', {
                bubbles: true,
                composed: true,
                detail: this.box.value,
            })
        );
    }

    getClassNames() {
        let classNames = ''
        if (this.box.selected) {
            classNames = 'selected'
        }
        if (this.box.inlined) {
            classNames += ' inlined'
        }

        return classNames
    }

    addValue(e: InputEvent) {
        this.box.value.value = e.data as string
        this.boxFocused()
    }

    drawCol(value: Value) {
        if (false === this.box.fixed) {
            return html`
             <input
                     @focus=${this.boxFocused}
                     @input=${this.addValue}
                     class="sudoku-col ${this.getClassNames()}" 
                     type="text" 
                     maxlength="1"
             >
         `
        }

        return html`
            <input 
                    @focus=${this.boxFocused}
                    readonly 
                    class="sudoku-col fixed ${this.getClassNames()}"
                    type="text" 
                    maxlength="1"
                    value="${value.value}"
            />
      `
    }

    protected render() {
        return html`
            <td class="${this.drawBorders()}">
                ${this.drawCol(this.box.value)}
            </td>
        `
    }
}