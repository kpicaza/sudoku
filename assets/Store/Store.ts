import { createStore } from '@stencil/store';
import { Grid } from '../Model/Grid';

const { state, onChange } = createStore({
  grid: Grid.fromPlainGrid([]),
  blockSize: 3,
  size: 9,
});

onChange('grid', () => {});

export default state;
