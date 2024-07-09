import { ReadProfile } from '@/profile/types/read-profile';
import { ReadSpot } from '@/spots/types/read-spot';
import { SpotsStatistics } from '@/spots/types/spots-statistics';

export type ReadProfileWithPublicSpots = Omit<ReadProfile, 'email'> & {
	spotsStatistics: SpotsStatistics;
	spots: ReadSpot[];
};
