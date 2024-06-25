import { Spot } from '@/spots/types/spot';

export type ConfigureSpot = Partial<
	Omit<
		Spot,
		'id' | 'referenced' | 'configured' | 'createdAt' | 'updatedAt' | 'deletedAt'
	>
>;
