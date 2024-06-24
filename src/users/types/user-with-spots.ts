import { Spot } from '@/spots/types/spot';
import { User } from '@/users/types/user';

export type UserWithSpots = User & {
	spots: Spot[];
};
