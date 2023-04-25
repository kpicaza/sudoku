import { css, html, LitElement } from 'lit';
import '@material/mwc-top-app-bar';
import '@material/mwc-icon-button';

export class AppHeader extends LitElement {
  static styles = css`
    :host {
      display: block;
      padding: 0;
      margin: 0;
    }
    mwc-top-app-bar {
      --mdc-theme-primary: #363636;
      --mdc-theme-on-primary: white;
    }
    .site-title {
      padding: 5px;
    }
    .site-title img {
      display: inline-block;
      vertical-align: middle;
      padding-bottom: 10px;
    }
    .site-title h1 {
      display: inline-block;
      font-size: 36px;
    }
  `;

  protected render(): unknown {
    return html`
      <mwc-top-app-bar>
        <div class="site-title" slot="title">
          <img alt="logo" src="/images/evil-sudoku-logo-90x90.png" width="52" />
          <h1>Evil Sudoku</h1>
        </div>
        <mwc-icon-button
          icon="emoji_events"
          slot="actionItems"
        ></mwc-icon-button>
        <mwc-icon-button icon="print" slot="actionItems"></mwc-icon-button>
        <mwc-icon-button icon="favorite" slot="actionItems"></mwc-icon-button>
        <div><!-- content --></div>
      </mwc-top-app-bar>
    `;
  }
}
