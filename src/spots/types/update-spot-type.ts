import { Spot } from './spot.type';

export type UpdateSpot = Omit<Spot, 'uuid'>;
