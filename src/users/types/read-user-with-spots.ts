import { ReadSpot } from '@/spots/types/read-spot';
import { SpotsStatistics } from '@/spots/types/spots-statistics';
import { ReadUser } from '@/users/types/read-user';

export type ReadUserWithSpots = ReadUser & {
	spotsStatistics: SpotsStatistics;
	spots: ReadSpot[];
};
