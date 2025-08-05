import { ReadProfile } from '@/profile/types/read-profile';
import { ReadSpotsWithStatistics } from '@/spots/types/read-spots-with-statistics';

export type ReadProfileWithPublicSpots = Omit<ReadProfile, 'email'> &
	ReadSpotsWithStatistics;
