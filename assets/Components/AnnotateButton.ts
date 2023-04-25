import { html, css, LitElement } from 'lit';
import { property } from 'lit/decorators.js';
import '@material/mwc-button';
import { DrawMode } from '../Types/DrawMode';

export class AnnotateButton extends LitElement {
  static styles = css`
    :host {
      display: block;
      margin: 15px;
    }
    mwc-button {
      --mdc-theme-primary: #363636;
      --mdc-theme-on-primary: white;
    }
  `;

  @property() enabled: boolean = false;

  private enableValueMode() {
    this.enabled = false;
    this.dispatchEvent(
      new CustomEvent('drawModeChanged', {
        bubbles: true,
        composed: true,
        detail: DrawMode.Value,
      })
    );
  }

  private enableNoteMode() {
    this.enabled = true;
    this.dispatchEvent(
      new CustomEvent('drawModeChanged', {
        bubbles: true,
        composed: true,
        detail: DrawMode.Note,
      })
    );
  }

  protected render() {
    if (this.enabled) {
      return html`
        <mwc-button @click=${this.enableValueMode} icon="edit_square" raised
          >Add numbers</mwc-button
        >
      `;
    }
    return html`
      <mwc-button @click=${this.enableNoteMode} icon="edit_square" outlined
        >Take Notes</mwc-button
      >
    `;
  }
}
