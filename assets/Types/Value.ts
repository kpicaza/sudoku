import { Position } from './Position';
import { PencilMark } from './PencilMark';

export type Value = {
  position: Position;
  pencilMarks: Array<PencilMark>;
  value: string;
};
