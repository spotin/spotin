import { Spot } from '@/spots/types/spot';

export type UpdateSpot = Partial<
	Omit<Spot, 'id' | 'createdAt' | 'updatedAt' | 'deletedAt'>
>;
