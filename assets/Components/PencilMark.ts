import { css, html, LitElement } from 'lit';
import { property } from 'lit/decorators.js';
import { DrawMode } from '../Types/DrawMode';
import { PencilMark as PMark } from '../Types/PencilMark';
import { Box } from '../Types/Box';
import { Value } from '../Types/Value';

export class PencilMark extends LitElement {
  static styles = css`
    :host {
      display: block;
      padding: 0;
      position: relative;
      top: 0;
      left: 0;
    }
    textarea {
      border: none;
      overflow: auto;
      outline: none;

      -webkit-box-shadow: none;
      -moz-box-shadow: none;
      box-shadow: none;

      resize: none; /*remove the resize handle on the bottom right*/
    }
    .notes-input {
      display: inline;
      height: 58px;
      width: 60px;
      padding: 0;
      border: none;
      color: #005cbf;
      font-size: 16px;
      text-align: center;
      vertical-align: central;
      outline: none;
      caret-color: transparent;
      background: transparent;
      position: absolute;
      top: 0px;
      left: 0px;
      overflow-wrap: break-word;
      letter-spacing: 8px;
      padding-left: 6px;
      overflow-y: hidden;
    }

    .notes-input.selected {
      background: #d9edf7;
    }
    .notes-input.inlined {
      background: #ebebeb;
    }
    .notes-input:focus,
    .notes-input::selection,
    .notes-input.focused {
      background: #b8daff;
    }
  `;

  @property() box: Box = {
    value: {
      position: { row: 0, col: 0, block: 0 },
      value: ' ',
      pencilMarks: [],
    },
    selected: false,
    fixed: false,
    focused: false,
    inlined: false,
  };

  @property() pencilMark: Array<PMark> = [];

  @property() drawMode: DrawMode = DrawMode.Value;

  @property() inlined: boolean = false;

  @property() focused: boolean = false;

  @property() selected: boolean = false;

  addNote(e: InputEvent) {
    const input = e.target as HTMLTextAreaElement;
    const pencilMark: PMark = {
      value: e.data as string,
    };

    if (!pencilMark.value || pencilMark.value.match(/[1-9]/) === null) {
      input.value = input.value.replace(pencilMark.value, '');
      return;
    }

    const inputValue = this.getValue();

    if (inputValue.indexOf(pencilMark.value) !== -1) {
      input.value = inputValue.replace(pencilMark.value, '');
    }

    const pencilMarks: Array<PMark> = [];
    for (const noteValue of input.value) {
      pencilMarks.push({
        value: noteValue,
      });
    }

    const value: Value = {
      value: ' ',
      position: this.box.value.position,
      pencilMarks,
    };

    this.dispatchEvent(
      new CustomEvent('pencilMarkAdded', {
        bubbles: true,
        composed: true,
        detail: value,
      })
    );
  }

  getValue() {
    let inputValue = '';
    for (const notes of this.pencilMark) {
      inputValue += notes.value;
    }

    return inputValue.split('').join('');
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
    let classNames = '';
    if (this.inlined) {
      classNames = 'inlined';
    }

    if (this.focused) {
      classNames = ' focused';
    }

    if (this.selected && !this.focused) {
      classNames = ' selected';
    }

    return classNames;
  }

  protected render() {
    return html`
      <textarea
        class="notes-input ${this.getClassNames()}"
        type="text"
        @focus=${this.boxFocused}
        @input=${this.addNote}
        maxlength="10"
        .value=${this.getValue()}
      ></textarea>
    `;
  }
}
