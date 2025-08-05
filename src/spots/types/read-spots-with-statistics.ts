import { ReadSpot } from '@/spots/types/read-spot';
import { SpotsStatistics } from '@/spots/types/spots-statistics';

export type ReadSpotsWithStatistics = {
	spotsStatistics: SpotsStatistics;
	spots: ReadSpot[];
};
