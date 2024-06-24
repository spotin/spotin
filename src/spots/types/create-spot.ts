import { Spot } from '@/spots/types/spot';

export type CreateSpot = Omit<
	Spot,
	'id' | 'createdAt' | 'updatedAt' | 'deletedAt'
>;
