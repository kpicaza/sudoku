import { Value } from './Value';

export type Box = {
  value: Value;
  selected: boolean;
  inlined: boolean;
  fixed: boolean;
};
