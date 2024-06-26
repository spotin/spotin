import { ReadSpot } from '@/spots/types/read-spot';
import { ReadUser } from '@/users/types/read-user';

export type ReadSpotWithUser = ReadSpot & {
	user: ReadUser;
};
