import { Profile } from '@/profile/types/profile';
import { Spot } from '@/spots/types/spot';
import { SpotsStatistics } from '@/spots/types/spots-statistics';

export type ProfileWithPublicSpots = Omit<Profile, 'email'> & {
	spotsStatistics: SpotsStatistics;
	spots: Spot[];
};
