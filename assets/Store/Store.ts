import { createStore } from '@stencil/store';
import { Grid } from '../Model/Grid';
import { DrawMode } from '../Types/DrawMode';

const { state, onChange } = createStore({
  grid: Grid.fromPlainGrid([], []),
  blockSize: 3,
  size: 9,
  drawMode: DrawMode.Value,
});

onChange('grid', () => {});

export default state;
