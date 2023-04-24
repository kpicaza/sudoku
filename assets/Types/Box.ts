import { Value } from './Value';

export type Box = {
  value: Value;
  selected: boolean;
  focused: boolean;
  inlined: boolean;
  fixed: boolean;
};
