import { Profile } from '@/profile/types/profile';
import { Spot } from '@/spots/types/spot';

export type ProfileWithPublicSpots = Omit<Profile, 'email'> & {
	spots: Spot[];
};
