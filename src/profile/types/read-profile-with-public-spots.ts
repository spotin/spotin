import { ReadProfile } from '@/profile/types/read-profile';
import { ReadSpot } from '@/spots/types/read-spot';

export type ReadProfileWithPublicSpots = Omit<ReadProfile, 'email'> & {
	spots: ReadSpot[];
};
