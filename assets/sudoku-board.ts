import { SudokuBoard } from './SudokuBoard';
import { ValueBox } from './ValueBox';
import { AnnotateButton } from './Components/AnnotateButton';
import { PencilMark } from './Components/PencilMark';

window.customElements.define('pencil-mark', PencilMark);
window.customElements.define('value-box', ValueBox);
window.customElements.define('annotate-button', AnnotateButton);
window.customElements.define('sudoku-board', SudokuBoard);
