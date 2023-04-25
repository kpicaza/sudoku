import { SudokuBoard } from './SudokuBoard';
import { ValueBox } from './ValueBox';
import { AppHeader } from './Components/AppHeader';
import { AnnotateButton } from './Components/AnnotateButton';
import { PencilMark } from './Components/PencilMark';

window.customElements.define('pencil-mark', PencilMark);
window.customElements.define('value-box', ValueBox);
window.customElements.define('app-header', AppHeader);
window.customElements.define('annotate-button', AnnotateButton);
window.customElements.define('sudoku-board', SudokuBoard);
