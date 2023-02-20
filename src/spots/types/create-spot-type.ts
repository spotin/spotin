import { Spot } from './spot.type';

export type CreateSpot = Omit<
  Spot,
  'uuid' | 'created_at' | 'updated_at' | 'deleted_at'
>;
