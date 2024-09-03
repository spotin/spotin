import { Spot } from '@/spots/types/spot';

export type ConfigureSpot = Partial<
	Omit<
		Spot,
		'id' | 'public' | 'configured' | 'createdAt' | 'updatedAt' | 'deletedAt'
	>
>;
