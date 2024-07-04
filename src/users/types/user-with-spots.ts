import { Spot } from '@/spots/types/spot';
import { SpotsStatistics } from '@/spots/types/spots-statistics';
import { User } from '@/users/types/user';

export type UserWithSpots = User & {
	spotsStatistics: SpotsStatistics;
	spots: Spot[];
};
