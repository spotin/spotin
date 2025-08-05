import { Profile } from '@/profile/types/profile';
import { SpotsWithStatistics } from '@/spots/types/spots-with-statistics';

export type ProfileWithPublicSpots = Omit<Profile, 'email'> &
	SpotsWithStatistics;
